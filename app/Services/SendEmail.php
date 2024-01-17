<?php

namespace App\Services;

use App\Models\Mailbox;
use Google\Service\Gmail;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class SendEmail
{
    public function send($campaign, $prospect)
    {
        try {
            $mailbox_id = $campaign->mailbox_id;
            $message = $campaign->message;
            $subject = $campaign->subject;
            $email = $prospect->email;
            $mailbox = Mailbox::find($mailbox_id);
            if ($mailbox) {
                $messageText = $message;
                $snippets = [
                    "first_name" => $prospect->first_name,
                    'last_name' => $prospect->last_name,
                    'location' => $prospect->location
                ];
                if (count($snippets) > 0) {
                    foreach ($snippets as $key => $snippet) {
                        Log::channel('development')->alert("key: " . $key);
                        Log::channel('development')->alert("Snippet: " . $snippet);

                        $key = strtoupper($key);
                        $messageText = str_replace('{{' . $key . '}}', $snippet, $messageText);
                        $subject = str_replace('{{' . $key . '}}', $snippet, $subject);
                    }
                }
                $client = (new \App\Http\Controllers\Api\Google\GoogleController)->getGoogleClient($mailbox["token"]);
                $sender_name = $mailbox['name'];
                $sender_email = $mailbox['email'];
                $signature = $mailbox['signature'];
                $recipient = $email; // Адреса отримувача
                $service = new Gmail($client);
                $message = (new \App\Http\Controllers\Api\Google\GoogleController)->createMessage($sender_name, $sender_email, $recipient, $subject, $messageText, $signature);
                $response = $service->users_messages->send('me', $message);
                Log::channel('development')->alert("Email send successfully");
                Log::channel('development')->alert("response: " . json_encode($response));
            } else {
                Log::channel('development')->alert("Mailbox not found");
            }
        } catch (Exception $error) {
            Log::channel('development')->alert("ERROR:" . $error);
        }
    }
}
