<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use App\Services\MailboxServices\GmailService;
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
            Log::channel('dev-user-token-update')->alert('=======================REFRESH TOKENs START=======================');

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
                        Log::channel('dev-user-token-update')->alert("Token for mailbox [{$mailbox->email}] refreshed successfully");
                    }
                }
            }
        } catch (Exception $error) {
            Log::channel('dev-user-token-update')->error('Refresh Token: ' . $error->getMessage());
        } finally {
            Log::channel('dev-user-token-updatedev-user-token-update')->alert('=======================REFRESH TOKENs END=======================');
        }
    }
}
