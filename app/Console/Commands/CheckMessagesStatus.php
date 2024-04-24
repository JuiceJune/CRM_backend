<?php

namespace App\Console\Commands;

use App\Models\CampaignMessage;
use App\Services\MailboxServices\GmailService;
use App\Services\MessagesStatusServices\MessageStatusService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class CheckMessagesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check messages status';

    public function handle()
    {
        try {
            Log::alert('=======================CHECK MESSAGES STATUS START=======================');

            $allSentMessages = CampaignMessage::where('type', 'from me')
                ->whereNotIn('status', ['bounced', 'unsubscribe', 'replayed'])->get();

            $gmailService = new GmailService();

            foreach ($allSentMessages as $sentMessage) {

                $messageStatusService = new MessageStatusService($sentMessage, $gmailService);
                $res = $messageStatusService->checkMessageStatus();

                if($res['status'] === 'success') {
                    Log::alert('CampaignMessage: ' . $sentMessage['id'] . " | Status success: " . $res['data']);
                } else {
                    Log::error('CampaignMessage: ' . $sentMessage['id'] . " | Status Error: " . $res['data']);
                }
            }
        } catch (Exception $error) {
            Log::error('Command: messages:check-status: ' . $error->getMessage());
        } finally {
            Log::alert('=======================CHECK MESSAGES STATUS END=======================');
        }
    }
}
