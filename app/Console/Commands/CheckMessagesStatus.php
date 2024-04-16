<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Google\GoogleController;
use App\Models\CampaignProspect;
use App\Models\CampaignSentProspect;
use App\Models\CampaignStepProspect;
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
    protected $description = 'Check messages status by gmail api';

    public function handle()
    {
        try {
            Log::channel('development')->alert("=======================CHECK MESSAGES STATUS START=======================");

            $allSentProspects = CampaignSentProspect::whereNotIn('status', ['bounced', 'unsubscribe', 'replayed'])->get();

            foreach ($allSentProspects as $key => $sentProspect) {
                $messageInfo = $sentProspect->messageInfo;
                $campaign = $sentProspect->campaign;
                $mailbox = $campaign->mailbox;

                $google = new GoogleController();
                $client = $google->getClient($mailbox['token']);

                $threadResponse = $google->getThread($client, $messageInfo['thread_id']);

                if ($threadResponse['status'] === 'success') {
                    $messages = $threadResponse['data']->messages;
                    if (count($messages) > 1) {
                        $bouncedFlag = false;

                        foreach ($messages as $messageKey => $message) {
                            foreach ($message->payload->headers as $header) {
                                if ($header->name === 'From'
                                    && (str_contains($header->value, 'mailer-daemon@googlemail.com') || str_contains($header->value, 'postmaster'))) {
                                    $bouncedFlag = true;
                                    break;
                                }

                            }
                        }

                        $replayedFlag = false;

                        foreach ($messages as $messageKey => $message) {
                            foreach ($message->payload->headers as $header) {
                                if ($header->name === 'From'
                                    && !str_contains($header->value, $mailbox->email)) {
                                    $replayedFlag = true;
                                    break;
                                }
                            }
                        }

                        if($bouncedFlag || $replayedFlag) {
                            $campaignStepProspect = CampaignStepProspect::where('campaign_id', $sentProspect->campaign_id)
                                ->where('prospect_id', $sentProspect->prospect_id)
                                ->where('campaign_step_id', $sentProspect->campaign_step_id)
                                ->first();

                            if ($campaignStepProspect) {
                                if ($bouncedFlag) {
                                    $campaignStepProspect->bounced();
                                } else {
                                    $campaignStepProspect->replayed();
                                }
                            }
                        }
                    }
                } else {
                    Log::channel('development')->error("Thread ERROR: " . json_encode($threadResponse['data']));

                }
            }
        } catch (Exception $error) {
            Log::channel('development')->error('Error: ' . $error);
        } finally {
            Log::channel('development')->alert("=======================CHECK MESSAGES STATUS END=======================");
        }
    }
}
