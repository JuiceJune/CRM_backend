<?php

namespace App\Services\CampaignMessageService;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CampaignProspect;
use App\Models\CampaignStep;
use App\Models\Prospect;
use App\Services\CampaignJobServices\CampaignJobService;
use App\Services\MailboxServices\MailboxService;
use App\Services\MessagesStatusServices\MessageStatusService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignMessageService
{
    private CampaignMessage $campaignMessage;
    private Campaign $campaign;
    private CampaignStep $campaignStep;
    private Prospect $prospect;
    private Carbon $currentTime;
    private CampaignProspect $campaignProspect;

    public function __construct(CampaignMessage $campaignMessage)
    {
        $this->campaignMessage = $campaignMessage;
        $this->campaign = $campaignMessage->campaign;
        $this->campaignStep = $campaignMessage->campaignStep;
        $this->prospect = $campaignMessage->prospect;
        $this->currentTime = Carbon::now($this->campaign->timzone);
        $this->campaignProspect = $campaignMessage->campaignProspect;
    }

    public function getCampaignMessages(): CampaignMessage
    {
        return $this->campaignMessage;
    }

    public function setCampaignMessage(CampaignMessage $campaignMessage): void
    {
        $this->campaignMessage = $campaignMessage;
    }

    public function sent($sentResponse, MailboxService $mailboxService): void
    {
        try {
            $messageId = $sentResponse['messageResponse']->id;
            $threadId = $sentResponse['messageResponse']->threadId;
            $token = $sentResponse["token"];

            $messageStringId = $mailboxService->getMessageStringId($token, $messageId);

            $this->campaignMessage->update([
                'status' => "sent",
                'sent_time' => now(),
                'message_id' => $messageId,
                'message_string_id' => $messageStringId,
                'thread_id' => $threadId,
                'subject' => $sentResponse['subject'],
                'message' => $sentResponse['message'],
                'from' => $sentResponse['from'],
                'to' => $sentResponse['to'],
            ]);

            $this->campaignMessage->redisJob->delete();

            $this->setupNextMessage();

        } catch (Exception $error) {
            Log::error("CampaignMessageService->sent(): " . $error->getMessage());
        }
    }

    public function setupNextMessage(): void
    {
        try {
            $nextStep = $this->campaign->step($this->campaignStep->step + 1);

            if ($nextStep) {
                $startAfter = $this->campaignStep->start_after;

                // "Days" is one single type. There are no others. So it is mb redundant
                if ($startAfter["time_type"] === "days") {
                    $this->currentTime->addDays($startAfter["time"]);
                }

                // Change step field in CampaignProspect element
                $this->campaignProspect->update(['step' => $this->campaignStep->step + 1]);

                // Create CampaignMessage element
                CampaignMessage::create([
                    'account_id' => $this->campaign->account_id,
                    'campaign_id' => $this->campaign->id,
                    'campaign_step_id' => $nextStep->id,
                    'campaign_step_version_id' => $nextStep->version('A')->id,
                    'prospect_id' => $this->prospect->id,
                    'available_at' => $this->currentTime->startOfDay()->toDateTimeString(),
                ]);
            } else {
                // If there are no more steps -> set status of CampaignProspect to 'end'
                $this->campaignProspect->update(['status' => 'end', 'step' => $this->campaignStep->step + 1]);
            }
        } catch (Exception $error) {
            Log::error("CampaignMessageService->setupNextMessage(): " . $error->getMessage());
        }
    }

    public function opened(): void
    {
        try {
            $this->campaignMessage->update(['status' => 'opened']);
            $this->campaignProspect->update(['status' => 'opened']);
        } catch (Exception $error) {
            Log::error('Opened: ' . $error->getMessage());
        }
    }

    public function unsubscribe(): void
    {
        DB::beginTransaction();
        try {
            $this->campaignMessage->update(['status' => 'unsubscribe']);
            $this->campaignProspect->update(['status' => 'unsubscribe']);

            $this->deleteNextMessages();

            DB::commit();
        } catch (Exception $error) {
            Log::error('Unsubscribe: ' . $error->getMessage());
            DB::rollBack();
        }
    }

    public function replayed(): void
    {
        DB::beginTransaction();
        try {
            $this->campaignMessage->update(['status' => 'replayed']);
            $this->campaignProspect->update(['status' => 'replayed']);

            $this->deleteNextMessages();

            DB::commit();
        } catch (Exception $error) {
            Log::error('Replayed: ' . $error->getMessage());
            DB::rollBack();
        }
    }

    public function bounced(): void
    {
        DB::beginTransaction();
        try {
            $this->campaignMessage->update(['status' => 'bounced']);
            $this->campaignProspect->update(['status' => 'bounced']);

            $this->deleteNextMessages();

            DB::commit();
        } catch (Exception $error) {
            Log::error('Bounced: ' . $error->getMessage());
            DB::rollBack();
        }
    }

    private function deleteNextMessages(): void
    {
        try {
            CampaignMessage::where('campaign_id', $this->campaign->id)
                ->where('prospect_id', $this->prospect->id)
                ->where('status', 'in', ['pending', 'scheduled'])
                ->delete();

            $campaignJobService = new CampaignJobService();
            $campaignJobService->deleteProspectJobs($this->campaign, $this->prospect);
        } catch (Exception $error) {
            Log::error("CampaignMessageService->deleteNextMessages(): " . $error->getMessage());
        }
    }

    public function checkMessageStatus(CampaignMessage $campaignMessage, MailboxService $mailboxService): int
    {
        try {
            $checkMessageStatus = new MessageStatusService($campaignMessage, $mailboxService);
            $statusCheckResponse = $checkMessageStatus->checkAllMessageHistory();

            if($statusCheckResponse['status'] === 'error' || $statusCheckResponse['status'] === 'not-send') {
                Log::alert('Message has not be sent: ' . $statusCheckResponse['data']);
                return 0;
            }
            return 1;
        } catch (Exception $error) {
            Log::error("CampaignMessageService -> CheckMessageStatus: " . $error->getMessage());
            return 0;
        }
    }
}
