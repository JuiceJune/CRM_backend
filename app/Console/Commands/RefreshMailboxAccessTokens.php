<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use App\Services\Mailbox\GmailService;
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
                    $newToken = $gmailService->refreshToken($mailbox['token'], $mailbox['refresh_token']);
                    Log::alert('Token: ' . json_encode($newToken));
                }
            }
        } catch (Exception $error) {
            Log::error('Refresh Token: ' . $error->getMessage());
        }
    }
}
