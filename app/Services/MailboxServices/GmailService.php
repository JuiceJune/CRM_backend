<?php

namespace App\Services\MailboxServices;

use App\Models\CampaignMessage;
use Error;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Google\Service\Gmail;
use Google\Client;
use Exception;

class GmailService implements MailboxService
{
    protected Client $client;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient($client): void
    {
        $this->client = $client;
    }

    public function connectAccount($accountUuid, $url, $project)
    {
        return $this->getAuthUrl($accountUuid, $url, $project);
    }

    public function getAuthUrl($accountUuid, $url, $project)
    {
        return Socialite::driver('google')
            ->scopes([Gmail::GMAIL_READONLY, Gmail::GMAIL_COMPOSE])
            ->with(['access_type' => 'offline', "prompt" => "consent",
                'state' => json_encode(
                    ['driver' => 'google', 'account' => $accountUuid, 'url' => $url, 'project' => $project]
                )])
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    }

    public function initializeClient($token): void
    {
        try {
            $client = new Client();
            $client->setApplicationName(env('GOOGLE_APPLICATION_NAME'));
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri(env('GOOGLE_REDIRECT'));
            $client->setAccessType("offline");
            $client->setApprovalPrompt("force");
            $client->setAccessToken($token);
            $this->setClient($client);
        } catch (Exception $error) {
            Log::channel('dev-mailbox')->error('Initialize Client | Google | Token: ' . $token . ' | Error: ' . $error->getMessage());
        }
    }

    public function generateMessage($senderName, $senderEmail, $prospectEmail, $subject, $message,
                                    $signature, $messageUuid = null, $threadId = null, $messageStringId = null): ?Google_Service_Gmail_Message
    {
        try {
            $url = env('APP_URL');
            $gmailMessage = new Google_Service_Gmail_Message();

            $rawMessageString = "From: {$senderName} <{$senderEmail}>\r\n";
            $rawMessageString .= "To: <{$prospectEmail}>\r\n";
            $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
            $rawMessageString .= "MIME-Version: 1.0\r\n";
            $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
            $rawMessageString .= $messageStringId !== null ? "References: {$messageStringId}\r\n" : '';
            $rawMessageString .= $messageStringId !== null ? "In-Reply-To: {$messageStringId}\r\n" : '';
            $rawMessageString .= 'Content-Transfer-Encoding: 7bit' . "\r\n\r\n";
            $rawMessageString .= "{$message}\r\n";
            $rawMessageString .= "<br/>\r\n";
            $rawMessageString .= "{$signature}\r\n";
            $rawMessageString .= $messageUuid ? "<img src='{$url}/api/image/{$messageUuid}' alt='-- ' height='1'/>\r\n" : null;

            // Encoding the raw message string
            $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));

            // Setting the raw message to the Gmail message object
            $gmailMessage->setRaw($rawMessage);

            // Setting thread id if available
            if ($threadId !== null) {
                $gmailMessage->setThreadId($threadId);
            }

            return $gmailMessage;
        } catch (Exception $error) {
            Log::channel('dev-sent-message')->error('GenerateMessage: ' . $error->getMessage());
            return null;
        }
    }

    public function setSnippets($snippets, $messageText, $subject): ?array
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
            Log::channel('dev-mailbox')->error('SendMessage: ' . $error->getMessage());
            return null;
        }
    }

    public function sendMessage($campaignMessage): ?array
    {
        try {
            $prospect = $campaignMessage->prospect;

            $campaignStep = $campaignMessage->campaignStep;
            $respondedThreadId = null;
            $respondedMessageStringId = null;
            if($campaignStep['reply_to_exist_thread']['reply'] && $campaignStep['reply_to_exist_thread']['step']) {
                $campaign = $campaignMessage->campaign;
                $respondedStep = $campaign->step($campaignStep['reply_to_exist_thread']['step']);
                $respondedCampaignMessage = CampaignMessage::where('campaign_id', $campaign->id)
                    ->where('campaign_step_id', $respondedStep->id)
                    ->where('prospect_id', $prospect->id)
                    ->where('type', 'from me')
                    ->first();
                if($respondedCampaignMessage) {
                    $respondedThreadId = $respondedCampaignMessage['thread_id'];
                    $respondedMessageStringId = $respondedCampaignMessage['message_string_id'];
                } else {
                    throw new Error('Not found message to followup');
                }
            }

            $version = $campaignMessage->campaignStepVersion;

            $snippets = $prospect->toArray();
            [$message, $subject] = $this->setSnippets($snippets, $version['message'], $version['subject']);

            $mailbox = $campaignMessage->campaign->mailbox;
            $senderName = $mailbox['name'];
            $senderEmail = $mailbox['email'];
            $signature = $mailbox['signature'];
            $token = $mailbox['token'];

            $frontendUrl = env('FRONTEND_URL');
            $signature = str_replace('{{UNSUBSCRIBE}}', "{$frontendUrl}/unsubscribe/{$campaignMessage['uuid']}", $signature);

            $messageObj = $this->generateMessage($senderName, $senderEmail, $prospect['email'], $subject, $message,
                $signature, $campaignMessage['uuid'], $respondedThreadId, $respondedMessageStringId);

            if(!$messageObj) {
                throw new Error('Generate message problem');
            }

            $this->initializeClient($token);

            $service = new Gmail($this->client);

            $response = $service->users_messages->send('me', $messageObj);

            $messageResponse = $this->getMessage($token, $response->id);

            return [
                'messageResponse' => $messageResponse,
                'from' => $senderEmail,
                'to' => $prospect['email'],
                'subject' => $subject,
                'message' => $message,
                'token' => $token
            ];
        } catch (Exception $error) {
            Log::channel('dev-sent-message')->error('SendMessage: ' . $error->getMessage());
            return null;
        }
    }

    public function sendTestMessage($mailbox, $message, $subject, $testEmail, $snippets): array
    {
        try {
            $this->initializeClient($mailbox['token']);

            if (count($snippets) > 0) {
                foreach ($snippets as $key => $snippet) {
                    $message = str_replace('{{' . $key . '}}', $snippet, $message);
                    $subject = str_replace('{{' . $key . '}}', $snippet, $subject);
                }
            }
            $senderName = $mailbox['name'];
            $senderEmail = $mailbox['email'];
            $signature = str_replace('{{UNSUBSCRIBE}}', '#', $mailbox['signature']);;

            $messageObj = $this->generateMessage($senderName, $senderEmail, $testEmail, $subject, $message, $signature);

            if(!$messageObj) {
                throw new Error('Generate message problem');
            }

            $service = new Gmail($this->client);

            $service->users_messages->send('me', $messageObj);

            return [
                'status' => 'success',
                'data' => 'success'
            ];
        } catch (Exception $error) {
            Log::channel('dev-campaign-process')->error('SendTestMessage: ' . $error->getMessage());
            return [
                'status' => 'error',
                'data' => $error->getMessage(),
            ];
        }
    }

    public function getHistory($token, $historyId): void
    {
        try {
            $this->initializeClient($token);
            $gmail = new Gmail($this->client);
            $res = $gmail->users_history->listUsersHistory('me', [
                'startHistoryId' => $historyId
            ]);
            Log::channel('dev-mailbox')->alert('History: ' . json_encode($res));
        } catch(Exception $e) {
            Log::channel('dev-mailbox')->error('GetHistory: ' . $e->getMessage());
        }
    }

    public function getMessage($token, $messageId): ?Gmail\Message
    {
        try {
            $this->initializeClient($token);
            $gmail = new Gmail($this->client);
            $res = $gmail->users_messages->get('me', $messageId);
            return $res;
        } catch(Exception $e) {
            Log::channel('dev-mailbox')->error('GetMessage: ' . $e->getMessage());
            return null;
        }
    }

    public function getMessageStringId($token, $messageId)
    {
        try {
            $message = $this->getMessage($token, $messageId);
            $message_id = null;

            foreach ($message['payload']['headers'] as $header) {
                if ($header['name'] === 'Message-Id') {
                    $message_id = $header['value'];
                    break;
                }
            }
            return $message_id;
        } catch(Exception $e) {
            Log::channel('dev-mailbox')->error('GetMessageStringId: ' . $e->getMessage());
            return null;
        }
    }

    public function getThread($token, $threadId): array
    {
        try {
            $this->initializeClient($token);
            $gmail = new Gmail($this->client);
            $res = $gmail->users_threads->get('me', $threadId);
            return [
                'status' => 'success',
                'data' => $res
            ];
        } catch(Exception $e) {
            Log::channel('dev-mailbox')->error('GetThread: ' . $e->getMessage());
            return [
                'status' => 'error',
                'data' => $e->getMessage()
            ];
        }
    }

    public function refreshToken($token, $refreshToken): ?array
    {
        try {
            $this->initializeClient($token);

            $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

            return $this->client->getAccessToken();

        } catch (Exception $error) {
            Log::channel('dev-user-token-update')->error('RefreshToken: ' . $error->getMessage());
            return null;
        }
    }
}

