<?php

namespace App\Services\Mailbox;

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

    public function connectAccount($accountUuid)
    {
        return $this->getAuthUrl($accountUuid);
    }

    public function getAuthUrl($accountUuid)
    {
        return Socialite::driver('google')
            ->scopes([Gmail::GMAIL_READONLY, Gmail::GMAIL_COMPOSE])
            ->with(['access_type' => 'offline', "prompt" => "consent",
                'state' => json_encode(['driver' => 'google', 'account' => $accountUuid])])
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
            Log::error('Initialize Client | Google | Token: ' . $token . ' | Error: ' . $error->getMessage());
        }
    }

    public function generateMessage($senderName, $senderEmail, $prospectEmail, $subject, $message,
                                    $signature, $messageUuid, $threadId, $messageStringId): ?Google_Service_Gmail_Message
    {
        try {
            $url = env('APP_URL');
            $gmailMessage = new Google_Service_Gmail_Message();

            // Constructing message headers
            $headers = [
                "From: {$senderName} <{$senderEmail}>",
                "To: <{$prospectEmail}>",
                'Subject: =?utf-8?B?' . base64_encode($subject) . "?=",
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=utf-8",
                $messageStringId !== null ? "References: {$messageStringId}" : '',
                $messageStringId !== null ? "In-Reply-To: {$messageStringId}" : '',
                'Content-Transfer-Encoding: 7bit',
                '', // Empty line before message body
                "{$message}", // Message body
                "<br/>", // HTML break line
                "{$signature}", // Signature
                "<img src='{$url}/api/image/{$messageUuid}' alt='-- ' height='1'/>" // Image
            ];

            // Constructing the raw message string
            $rawMessageString = implode("\r\n", $headers);

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
            Log::error('GenerateMessage: ' . $error->getMessage());
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
            Log::error('SendMessage: ' . $error->getMessage());
            return null;
        }
    }
    public function sendMessage($campaignMessage)
    {
        try {
            $prospect = $campaignMessage->prospect;
            $snippets = $prospect->toArray();
            [$message, $subject] = $this->setSnippets($snippets, $campaignMessage['message'], $campaignMessage['subject']);

            $mailbox = $campaignMessage->campaign->mailbox;
            $senderName = $mailbox['name'];
            $senderEmail = $mailbox['email'];
            $signature = $mailbox['signature'];
            $token = $mailbox['token'];

            $frontendUrl = env('FRONTEND_URL');
            $signature = str_replace('{{UNSUBSCRIBE}}', "{$frontendUrl}/unsubscribe/{$campaignMessage['uuid']}", $signature);

            $message = $this->generateMessage($senderName, $senderEmail, $prospect['email'], $subject, $message,
                $signature, $campaignMessage['uuid'], $campaignMessage['thread_id'], $campaignMessage['message_string_id']);

            if(!$message) {
                throw new Error('Generate message problem');
            }

            $this->initializeClient($token);

            $service = new Gmail($this->client);

            return $service->users_messages->send('me', $message);

        } catch (Exception $error) {
            Log::error('SendMessage: ' . $error->getMessage());
            return null;
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
            Log::alert('History: ' . json_encode($res));
        } catch(Exception $e) {
            Log::error('GetHistory: ' . $e->getMessage());
        }
    }

    public function getMessage($token, $messageId): ?Gmail\Message
    {
        try {
            $this->initializeClient($token);
            $gmail = new Gmail($this->client);
            $res = $gmail->users_messages->get('me', $messageId);
            Log::alert('Message: ' . json_encode($res));
            return $res;
        } catch(Exception $e) {
            Log::error('GetMessage: ' . $e->getMessage());
            return null;
        }
    }

    public function getThread($token, $threadId): ?Gmail\Thread
    {
        try {
            $this->initializeClient($token);
            $gmail = new Gmail($this->client);
            $res = $gmail->users_threads->get('me', $threadId);
            Log::alert('Thread: ' . json_encode($res));
            return $res;
        } catch(Exception $e) {
            Log::error('GetThread: ' . $e->getMessage());
            return null;
        }
    }
}

