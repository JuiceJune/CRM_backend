<?php

namespace App\Services\CampaignMessageService;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\CampaignProspect;
use App\Models\CampaignStep;
use App\Models\Prospect;
use App\Services\MailboxServices\MailboxService;
use Carbon\Carbon;
use Exception;
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
            $messageId = $sentResponse->messageResponse->id;
            $threadId = $sentResponse->messageResponse->threadId;
            $token = $sentResponse->token;

            $messageStringId = $mailboxService->getMessageStringId($token, $messageId);

            $this->campaignMessage->update([
                'status' => "sent",
                'sent_time' => now(),
                'message_id' => $messageId,
                'message_string_id' => $messageStringId,
                'thread_id' => $threadId,
                'subject' => $sentResponse->subject,
                'message' => $sentResponse->message,
                'from' => $sentResponse->from,
                'to' => $sentResponse->to,
            ]);

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
                    'campaign_step_version_id' => $nextStep->version->id,
                    'prospect_id' => $this->prospect->id,
                    'available_at' => $this->currentTime->startOfDay()->toDateTimeString(),
                ]);
            }
            else {
                // If there are no more steps -> set status of CampaignProspect to 'end'
                $this->campaignProspect->update(['status' => 'end']);
            }
        } catch (Exception $error) {
            Log::error("CampaignMessageService->setupNextMessage(): " . $error->getMessage());
        }
    }
}
