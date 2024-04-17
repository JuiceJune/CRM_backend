<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use App\Services\Mailbox\GmailService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshMailboxAccessTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:mailbox-access-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Mailbox Access Token';

    public function handle()
    {
        try {
            $mailboxes = Mailbox::all();
            foreach ($mailboxes as $mailbox) {
                if($mailbox->email_provider === 'gmail') {
                    $gmailService = new GmailService();
                    $response = $gmailService->refreshToken($mailbox['token'], $mailbox['refresh_token']);

                    if($response) {
                        $mailbox->update([
                            'token' => $response['access_token'],
                            'expires_in' => $response['expires_in'],
                            'scopes' => $response['scope'],
                            'last_token_refresh' => Carbon::createFromTimestamp($response['created']),
                        ]);
                        Log::alert("Token for mailbox [{$mailbox->email}] refreshed successfully");
                    }
                }
            }
        } catch (Exception $error) {
            Log::error('Refresh Token: ' . $error->getMessage());
        }
    }
}
