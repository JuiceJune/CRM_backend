<?php

namespace App\Http\Controllers\Api\Google;

use Laravel\Socialite\Facades\Socialite;
use Google\Service\Gmail\WatchRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Google_Service_Gmail_Message;
use PHPUnit\Framework\Error;
use Google\Service\Gmail;
use App\Models\Mailbox;
use PHPUnit\Exception;
use Google\Client;

class GoogleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getClient($accessToken): ?Client
    {
        try {
            $client = new Client();
            $client->setApplicationName(env('GOOGLE_APPLICATION_NAME'));
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri(env('GOOGLE_REDIRECT'));
            $client->setAccessType("offline");
            $client->setApprovalPrompt("force");
            $client->setAccessToken($accessToken);
            return $client;
        } catch (Exception $error) {
            return null;
        }
    }

    public function login()
    {
        try {
            return Socialite::driver('google')
                ->scopes([Gmail::GMAIL_READONLY, Gmail::GMAIL_COMPOSE])
                ->with(['access_type' => 'offline', "prompt" => "consent"])
                ->stateless()
                ->redirect()
                ->getTargetUrl();
        } catch (Exception $error) {
            return response([
                "message" => "Problem with getting Google login url",
                "error_message" => $error->getMessage(),
            ], 500);
        }
    }

    public function callback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            if (!$user) {
                throw new Error('User connection failed');
            }
            Log::channel('development')->info("USer: " . json_encode($user));

            if (Mailbox::where('email', $user->getEmail())->exists()) {
                $mailbox = Mailbox::where('email', $user->getEmail())->first();
                $mailbox->update([
                    "email" => $user->getEmail(),
                    "name" => $user->getName(),
                    "avatar" => $user->getAvatar(),
                    "token" => $user->token,
                    "refresh_token" => $user->refreshToken,
                    "expires_in" => $user->expiresIn
                ]);
                return redirect()->to(env('FRONTEND_URL') . '/mailboxes/' . $mailbox->id);
            }

            $raw = $user->getRaw();
            Log::channel('development')->info("Row: " . json_encode($raw));

            $mailbox = Mailbox::create([
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "avatar" => $user->getAvatar(),
                "domain" => ($raw && array_key_exists("hd", $raw)) ? $raw["hd"] : "gmail",
                "password" => 'password',
                "create_date" => '2023-10-10',
                "email_provider_id" => 1,
                "token" => $user->token,
                "refresh_token" => $user->refreshToken,
                "expires_in" => $user->expiresIn
            ]);

            $response = [
                'status' => "success",
                'message' => 'Mailbox created successfully',
                'email' => $mailbox->email
            ];

            return redirect()->to(env('FRONTEND_URL') . '/mailboxes/' . $mailbox->id);
        } catch (Exception $error) {
            Log::channel('development')->error('Google CallBack method error: ' . $error);
            $response = [
                'status' => "error",
                'message' => $error->getMessage(),
                'email' => ""
            ];
            return redirect()->to(env('FRONTEND_URL') . '/mailboxes?' . http_build_query($response));
        }
    }

    public function createMessage($sender_name, $sender_email, $to,
                                  $subject, $messageText, $signature,
                                  $campaignStepProspectId,
                                  $references = null, $inReplyTo = null, $threadId = null): Google_Service_Gmail_Message
    {
        $message = new Google_Service_Gmail_Message();
        $rawMessageString = "From: {$sender_name} <{$sender_email}>\r\n";
        $rawMessageString .= "To: <{$to}>\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessageString .= $references !== null ? "References: {$references}\r\n" : '';
        $rawMessageString .= $inReplyTo !== null ? "In-Reply-To: {$inReplyTo}\r\n" : '';
        $rawMessageString .= 'Content-Transfer-Encoding: 7bit' . "\r\n\r\n";
        $rawMessageString .= "{$messageText}\r\n";
        $rawMessageString .= "<br/>\r\n";
        $rawMessageString .= "{$signature}\r\n";
        $rawMessageString .= $campaignStepProspectId ? "<img src='https://api.outnash.io/api/image/{$campaignStepProspectId}' alt='-- ' height='1'/>\r\n" : null;
        $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $message->setRaw($rawMessage);

        if ($threadId !== null) {
            $message->setThreadId($threadId);
        }

        return $message;
    }

    public function connectPubSub(Client $client): void
    {
        try {
            $watchRequest = new WatchRequest();
            $watchRequest->setTopicName(env('POP_SUB_TOPIC_NAME'));
            $watchRequest->setLabelIds(['INBOX']);

            $gmail = new Gmail($client);
            $res = $gmail->users->watch('me', $watchRequest);

            Log::channel('development')->error('Connect PubSub res: ' . json_encode($res));
        } catch (Exception $error) {
            Log::channel('development')->error('Connect PubSub error: ' . $error->getMessage());
        }
    }

    public function getHistory(Client $client, String $historyId): void
    {
        try {
            $gmail = new Gmail($client);
            $res = $gmail->users_history->listUsersHistory('me', [
                'startHistoryId' => $historyId
            ]);

            Log::channel('development')->alert("History: : " . json_encode($res));
        } catch(Exception $e) {
            Log::channel('development')->alert("GetMessage Error: " . $e->getMessage());
        }
    }

    public function getMessage(Client $client, String $messageId): ?Gmail\Message
    {
        try {
            $gmail = new Gmail($client);
            $res = $gmail->users_messages->get('me', $messageId);

            Log::channel('development')->alert("Message: " . json_encode($res));
            return $res;
        } catch(Exception $e) {
            Log::channel('development')->alert("GetMessage Error: " . $e->getMessage());
            return null;
        }
    }

    public function getThread(Client $client, String $threadId)
    {
        try {
            $gmail = new Gmail($client);
            $res = $gmail->users_threads->get('me', $threadId);
            return [
                "status" => 'success',
                "data" => $res
            ];
        } catch(Exception $e) {
            return [
                "status" => 'error',
                "data" => $e
            ];
        }
    }
}

