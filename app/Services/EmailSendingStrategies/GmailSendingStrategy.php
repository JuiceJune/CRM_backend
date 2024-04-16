<?php

namespace App\Services\EmailSendingStrategies;

use App\Http\Controllers\Api\Google\GoogleController;
use App\Models\CampaignSentProspect;
use App\Models\CampaignStepProspect;
use App\Models\CampaignProspectMessage;
use App\Models\Mailbox;
use Google\Service\Gmail;
use App\Services\MessagesStatusServices\CheckMessageStatus;
use Exception;
use Illuminate\Support\Facades\Log;

class GmailSendingStrategy implements EmailSendingStrategy
{
    public function send($campaign, $prospect, $version, $campaignStepProspectId, $step)
    {
        try {
            $mailbox = $campaign->mailbox;
            $message = $version->message;
            $subject = $version->subject;
            $email = $prospect->email;

            if ($mailbox) {
                $checkMessageStatusService = new CheckMessageStatus();
                $res = $checkMessageStatusService->index($campaignStepProspectId, $mailbox);

                if ($res['status'] === 'success' && $res['data'] === 'good') {
                    $snippets = $prospect->toArray();

                    [$messageText, $subject] = $this->setSnippets($snippets, $message, $subject);

                    $sender_name = $mailbox['name'];
                    $sender_email = $mailbox['email'];
                    $signature = $mailbox['signature'];
                    $signature = str_replace('{{UNSUBSCRIBE}}', "https://outnash.io/unsubscribe/{$campaignStepProspectId}", $signature);

                    $client = (new \App\Http\Controllers\Api\Google\GoogleController)->getClient($mailbox["token"]);
                    $recipient = $email; // Адреса отримувача
                    $service = new Gmail($client);

                    $messageId = null;
                    $threadId = null;
                    Log::channel('development')->alert("Step: " . json_encode($step));

                    if ($step->reply_to_exist_thread['reply']) {
                        $replyStep = $campaign->step($step->reply_to_exist_thread['step']);

                        $campaignSentProspect = CampaignSentProspect::where('campaign_step_id', $replyStep->id)
                            ->where('campaign_id', $campaign->id)
                            ->where('prospect_id', $prospect->id)
                            ->first();

                        $campaignProspectMessage = CampaignProspectMessage::where('campaign_sent_prospect_id', $campaignSentProspect['id'])->first();

                        $messageId = $campaignProspectMessage['message_id'];

                        if($messageId && $messageId[0] != '<') {
                            $google = new GoogleController();
                            $client = $google->getClient($campaign->mailbox["token"]);
                            $message = $google->getMessage($client, $messageId);

                            foreach ($message['payload']['headers'] as $header) {
                                if ($header['name'] === 'Message-Id') {
                                    $messageId = $header['value'];
                                    break;
                                }
                            }
                        }

                        $threadId = $campaignProspectMessage['thread_id'];
                    }

                    $message = (new \App\Http\Controllers\Api\Google\GoogleController)->createMessage(
                        $sender_name, $sender_email, $recipient, $subject, $messageText, $signature,
                        $campaignStepProspectId, $messageId, $messageId, $threadId);

                    $response = $service->users_messages->send('me', $message);

                    return [
                        'status' => 'success',
                        'data' => $response
                    ];
                } else {
                    return [
                        'status' => 'error',
                        'data' => 'status not available: ' . $res['data']
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'data' => 'Mailbox not found'
                ];
            }
        } catch (Exception $error) {
            return [
                'status' => 'error',
                'data' => $error
            ];
        }
    }

    /**
     * @throws Exception
     */
    public function setSnippets($snippets, $messageText, $subject): array
    {
        try {
            if (count($snippets) > 0) {
                foreach ($snippets as $key => $snippet) {
                    $messageText = str_ireplace('{{' . $key . '}}', $snippet, $messageText);
                    $subject = str_ireplace('{{' . $key . '}}', $snippet, $subject);
                }
            }
            return [
                $messageText,
                $subject,
            ];
        } catch (Exception $error) {
            throw new Exception($error->getMessage());
        }
    }
}
