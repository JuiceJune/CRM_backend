<?php

namespace App\Http\Controllers\Api\Google;

use App\Http\Controllers\Controller;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Google\Service\Gmail;
use PHPUnit\Exception;
use PHPUnit\Framework\Error;
use App\Models\Mailbox;
use Google\Client;

class GoogleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getClient($accessToken): Client
    {
        $client = new Client();
        $client->setApplicationName('My App');
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT'));
        $client->setScopes([Gmail::GMAIL_READONLY, Gmail::GMAIL_COMPOSE]);
        $client->setAccessToken($accessToken);
        return $client;
    }

    public function login()
    {
        try {
            $parameters = ['access_type' => 'offline', "approval_prompt" => "force"];
            $scopes = array(
                'https://www.googleapis.com/auth/plus.business.manage'
            );
            return Socialite::driver('google')
                ->scopes([Gmail::GMAIL_READONLY, Gmail::GMAIL_COMPOSE])
                ->with($parameters)
                ->stateless()
                ->redirect()
                ->getTargetUrl();
        } catch (Exception $error) {
            Log::channel('development')->error('Error: ' . $error);
            return $error;
        }
    }

    public function callback()
    {
        try {
            $user = Socialite::driver('google')->user();

            if (!$user) {
                throw new Error('User connection failed');
            }

            if (Mailbox::where('email', $user->getEmail())->exists()) {
                throw new Error('User already exist');
            }
            $mailboxe = Mailbox::create([
                "email" => $user->getEmail(),
                "name" => $user->getName(),
                "avatar" => $user->getAvatar(),
                "domain" => $user->getRaw()["hd"],
                "password" => 'password',
                "create_date" => '2023-10-10',
                "email_provider_id" => 1,
                "token" => $user->token,
                "refresh_token" => $user->refreshToken,
                "expires_in" => $user->expiresIn
            ]);

//            if ($mailboxe && $mailboxe->id) {
//                Log::channel('development')->info('Mailbox created');
//                $res = (new \Illuminate\Console\Scheduling\Schedule)
//                    ->command(RefreshGoogleAccessToken::class, [$mailboxe->id])
//                    ->everyFourHours();
//                Log::channel('development')->info('Res command: ' . json_encode($res));
//            }

//            $client = $this->getClient($user->token);
//            $sender = 'Oleg';
//            $recipient = 'oleg.bortovsky@gmail.com'; // Адреса отримувача
//            $subject = 'Test Gmail API';
//            $messageText = 'Test gmail api text';
//            $service = new Gmail($client);
//            $message = $this->createMessage($sender, $recipient, $subject, $messageText);
//            $response = $service->users_messages->send('me', $message);
//            dd($response);

        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function createMessage($sender, $to, $subject, $messageText)
    {
        $message = new Google_Service_Gmail_Message();
        $rawMessageString = "From: <{$sender}>\r\n";
        $rawMessageString .= "To: <{$to}>\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
        $rawMessageString .= "{$messageText}\r\n";
        $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $message->setRaw($rawMessage);
        return $message;
    }
}
