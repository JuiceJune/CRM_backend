<?php

namespace App\Services\MessagesStatusServices;

use App\Models\CampaignMessage;
use App\Models\Mailbox;
use App\Services\CampaignMessageService\CampaignMessageService;
use App\Services\MailboxServices\MailboxService;
use Exception;
use Illuminate\Support\Facades\Log;

class MessageStatusService
{
    protected CampaignMessage $campaignMessage;
    protected MailboxService $mailboxService;
    protected Mailbox $mailbox;

    public function __construct(CampaignMessage $campaignMessage, MailboxService $mailboxService)
    {
        $this->campaignMessage = $campaignMessage;
        $this->mailboxService = $mailboxService;
        $this->mailbox = $campaignMessage->campaign->mailbox;
    }

    public function checkAllMessageHistory(): array
    {
        try {
            $campaignProspect = $this->campaignMessage->campaignProspect;
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

            $previousMessages = CampaignMessage::where('campaign_id', $this->campaignMessage->campaign_id)
                ->where('prospect_id', $this->campaignMessage->prospect_id)
                ->whereNotNull('message_id')
                ->where('type', 'from me')
                ->orderBy('id', 'desc')
                ->get();

            if (!$previousMessages) {
                return [
                    "status" => "send",
                    "data" => "No previous message found"
                ];
            }

            $campaign = $this->campaignMessage->campaign;
            $mailbox = $campaign->mailbox;

            if(!$mailbox) {
                return [
                    "status" => "error",
                    "data" => "Mailbox not found in Campaign"
                ];
            }

            return $this->checkPreviousMessages($previousMessages);

        } catch (Exception $error) {
            Log::error("checkAllMessageHistory: " . $error->getMessage());
            return [
                'status' => 'error',
                'data' => $error->getMessage()
            ];
        }
    }

    protected function checkPreviousMessages($previousMessages): array {
    try {
        foreach($previousMessages as $previousMessage) {
            if($previousMessage['status'] != 'replayed' && $previousMessage['status'] != 'unsubscribe' && $previousMessage['status'] != 'bounced') {

                $campaignMessageService = new CampaignMessageService($previousMessage);

                $threadResponse = $this->mailboxService->getThread($this->mailbox['token'], $previousMessage['thread_id']);

                if ($threadResponse['status'] === 'success') {
                    $messages = $threadResponse['data']->messages;
                    Log::alert('checkPreviousMessages Messages: ' . json_encode($messages));

                    if (count($messages) > 1) {

                        foreach ($messages as $messageKey => $message) {
                            foreach ($message->payload->headers as $header) {
                                if ($header->name === 'From' && str_contains($header->value, 'mailer-daemon@googlemail.com')) {
                                    $campaignMessageService->bounced($message);
                                    return [
                                        'status' => 'not-send',
                                        'data' => 'Bounced'
                                    ];
                                }
                            }
                        }

                        foreach ($messages as $messageKey => $message) {
                            foreach ($message->payload->headers as $header) {
                                if ($header->name === 'From' && !str_contains($header->value, $this->mailbox->email)) {
                                    $campaignMessageService->replayed($message);
                                    return [
                                        'status' => 'not-send',
                                        'data' => 'Replayed'
                                    ];
                                }
                            }
                        }
                    }
                } else {
                    throw new \Error($threadResponse['data']);
                }
            } else {
                return [
                    "status" => "not-send",
                    "data" => "replayed|unsubscribed|bounced"
                ];
            }
        }

        return [
            "status" => "send",
            "data" => "send"
        ];
    } catch (Exception $error) {
        Log::error("CheckMessageStatus->checkPreviousMessages: " . $error->getMessage());
        return [
            'status' => 'error',
            'data' => $error->getMessage()
        ];
    }
}

    public function checkMessageStatus(): array
    {
        try {
            $campaignMessageService = new CampaignMessageService($this->campaignMessage);

            $threadResponse = $this->mailboxService->getThread($this->mailbox['token'], $this->campaignMessage['thread_id']);

            if ($threadResponse['status'] === 'success') {
                $messages = $threadResponse['data']->messages;

                if (count($messages) > 1) {

                    foreach ($messages as $messageKey => $message) {
                        foreach ($message->payload->headers as $header) {
                            if ($header->name === 'From' && str_contains($header->value, 'mailer-daemon@googlemail.com')) {
                                $campaignMessageService->bounced($message);
                                return [
                                    'status' => 'success',
                                    'data' => 'Bounced'
                                ];
                            }
                        }
                    }

                    foreach ($messages as $messageKey => $message) {
                        foreach ($message->payload->headers as $header) {
                            if ($header->name === 'From' && !str_contains($header->value, $this->mailbox->email)) {
                                $campaignMessageService->replayed($message);
                                return [
                                    'status' => 'success',
                                    'data' => 'Replayed'
                                ];
                            }
                        }
                    }
                }
            } else {
                throw new \Error($threadResponse['data']);
            }
            return [
                "status" => "success",
                "data" => "Empty"
            ];
        } catch (Exception $error) {
            Log::error("CheckMessageStatus->checkMessageStatus: " . $error->getMessage());
            return [
                'status' => 'error',
                'data' => $error->getMessage()
            ];
        }
    }
}
