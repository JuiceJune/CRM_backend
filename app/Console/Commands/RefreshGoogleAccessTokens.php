<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;
use PHPUnit\Framework\Error;

class RefreshGoogleAccessTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:google-access-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Google Access Token';

    public function handle()
    {
        try {
            $mailboxes = Mailbox::whereNotNull("refresh_token")->get();
            foreach ($mailboxes as $mailbox) {
                $client = (new \App\Http\Controllers\Admin\Google\GoogleController)->getClient($mailbox['token']);
                $client->fetchAccessTokenWithRefreshToken($mailbox['refresh_token']);
                $newAccessToken = $client->getAccessToken();
                if(!array_key_exists('access_token', $newAccessToken) || !array_key_exists('refresh_token', $newAccessToken)) {
                    throw new Error('There are not access_token and refresh_tokens');
                }
                $updated = $mailbox->update([
                    "token" => $newAccessToken["access_token"],
                    "refresh_token" => $newAccessToken["refresh_token"],
                ]);
                if($updated) {
                    Log::channel('development')->info("Token updated for " . $mailbox['id'] . ":" . $mailbox["email"]);
                } else {
                    Log::channel('development')->critical("Token not updated for " . $mailbox['id'] . ":" . $mailbox["email"]);
                }
            }
        } catch (Exception $error) {
            Log::channel('development')->error('Error: ' . $error);
        }
    }
}
