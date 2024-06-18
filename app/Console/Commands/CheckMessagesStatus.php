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
            Log::channel('dev-check-message-status')->alert('=======================CHECK MESSAGES STATUS START=======================');

            $allSentMessages = CampaignMessage::where('type', 'from me')
                ->whereNotIn('status', ['bounced', 'unsubscribe', 'replayed', 'pending'])->get();

            $gmailService = new GmailService();

            foreach ($allSentMessages as $sentMessage) {

                $messageStatusService = new MessageStatusService($sentMessage, $gmailService);
                $res = $messageStatusService->checkMessageStatus();

                if($res['status'] === 'success') {
                    Log::channel('dev-check-message-status')->alert('CampaignMessage: ' . $sentMessage['id'] . " | Status success: " . $res['data']);
                } else {
                    Log::channel('dev-check-message-status')->error('CampaignMessage: ' . $sentMessage['id'] . " | Status Error: " . $res['data']);
                }
            }
        } catch (Exception $error) {
            Log::channel('dev-check-message-status')->error('Command: messages:check-status: ' . $error->getMessage());
        } finally {
            Log::channel('dev-check-message-status')->alert('=======================CHECK MESSAGES STATUS END=======================');
        }
    }
}
