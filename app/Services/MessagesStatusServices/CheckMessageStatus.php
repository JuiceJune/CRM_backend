<?php

namespace App\Services\MessagesStatusServices;

use App\Http\Controllers\Api\Google\GoogleController;
use App\Models\CampaignMessage;
use App\Models\Mailbox;
use App\Services\CampaignMessageService\CampaignMessageService;
use App\Services\MailboxServices\MailboxService;
use Exception;
use Illuminate\Support\Facades\Log;

class CheckMessageStatus
{
    protected CampaignMessage $campaignMessage;
    protected MailboxService $mailboxService;

    public function __construct(CampaignMessage $campaignMessage, MailboxService $mailboxService)
    {
        $this->campaignMessage = $campaignMessage;
        $this->mailboxService = $mailboxService;
    }

    public function checkStatus(CampaignMessage $campaignMessage, MailboxService $mailboxService): array
    {
        try {
            $campaignProspect = $campaignMessage->campaignProspect;
            $currentStatus = $campaignProspect->status;
            $nextStep = $campaignProspect->step;

            if($currentStatus === 'end') {
                return [
                    "status" => "not-send",
                    "data" => "Current status end"
                ];
            }

            if($nextStep === 1) {
                return [
                    "status" => "send",
                    "data" => "First message"
                ];
            }

            $previousMessages = CampaignMessage::where('campaign_id', $campaignMessage->campaign_id)
                ->where('prospect_id', $campaignMessage->prospect_id)
                ->whereNotNull('message_id')
                ->where('type', 'from me')
                ->orderBy('id', 'desc')
                ->get();

            $campaign = $campaignMessage->campaign;
            $mailbox = $campaign->mailbox;

            if(!$mailbox) {
                return [
                    "status" => "error",
                    "data" => "Mailbox not found in Campaign"
                ];
            }

            foreach($previousMessages as $previousMessage) {

            }

            return [
                'status' => 'send',
                'data' => 'send'
            ];
        } catch (Exception $error) {
            Log::error("CheckStatus: " . $error->getMessage());
            return [
                'status' => 'error',
                'data' => $error->getMessage()
            ];
        }
    }

    protected function checkMessageStatus($previousMessage) {
        try {
            if($previousMessage['status'] != 'replayed' && $previousMessage['status'] != 'unsubscribe' && $previousMessage['status'] != 'bounced') {

                $campaignMessageService = new CampaignMessageService($previousMessage);

                $threadResponse = $mailboxService->getThread($mailbox['token'], $previousMessage['thread_id']);

                if ($threadResponse['status'] === 'success') {
                    $messages = $threadResponse['data']->messages;
                    Log::alert('CheckStatus Messages: ' . json_encode($messages));

                    if (count($messages) > 1) {

                        $bouncedFlag = false;

                        foreach ($messages as $messageKey => $message) {
                            foreach ($message->payload->headers as $header) {
                                if ($header->name === 'From' && str_contains($header->value, 'mailer-daemon@googlemail.com')) {
                                    $bouncedFlag = true;
                                    break;
                                }
                            }
                        }

                        if ($bouncedFlag) {
                            $campaignMessageService->bounced();
                            return [
                                'status' => 'not-send',
                                'data' => 'Bounced'
                            ];
                        }

                        $replayedFlag = false;

                        foreach ($messages as $messageKey => $message) {
                            foreach ($message->payload->headers as $header) {
                                if ($header->name === 'From' && !str_contains($header->value, $mailbox->email)) {
                                    $replayedFlag = true;
                                    break;
                                }
                            }
                        }

                        if ($replayedFlag) {
                            $campaignMessageService->replayed();
                            return [
                                'status' => 'not-send',
                                'data' => 'Replayed'
                            ];
                        }
                    }
                } else {
                    throw new \Error($threadResponse['data']);
                }
            } else {
                return [
                    "status" => "success",
                    "data" => "not-send"
                ];
            }
        } catch (Exception $error) {
            Log::error("CheckMessageStatus->checkMessageStatus: " . $error->getMessage());
        }
    }
}
