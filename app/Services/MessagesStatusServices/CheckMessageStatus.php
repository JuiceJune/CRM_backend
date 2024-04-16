<?php

namespace App\Services\MessagesStatusServices;

use App\Http\Controllers\Api\Google\GoogleController;
use App\Models\CampaignSentProspect;
use App\Models\CampaignStepProspect;
use App\Models\CampaignProspectMessage;
use App\Models\Mailbox;
use Exception;
use Illuminate\Support\Facades\Log;

class CheckMessageStatus
{
    public function index($campaignStepProspectId, Mailbox $mailbox)
    {
        try {
            $campaignStepProspect = CampaignStepProspect::find($campaignStepProspectId);

            $campaignStepProspectPrevious =CampaignStepProspect::where('campaign_id', $campaignStepProspect->campaign_id)
                ->where('prospect_id', $campaignStepProspect->prospect_id)
                ->where('id', '!=', $campaignStepProspect->id)
                ->orderBy('id', 'desc') // сортування за спаданням id
                ->first(); // вибір першого рядка

            $campaignSentProspect = CampaignSentProspect::where('campaign_id', $campaignStepProspect['campaign_id'])
                ->where('prospect_id', $campaignStepProspect['prospect_id'])
                ->orderBy('id', 'desc') // сортування за спаданням id
                ->first(); // вибір першого рядка

            Log::channel('development')->error("CampaignStepProspect: " . json_encode($campaignStepProspect));
            Log::channel('development')->error("CampaignSentProspect: " . json_encode($campaignSentProspect));

            if ($campaignSentProspect) {
                Log::channel('development')->error("СampaignSentProspect: " . json_encode($campaignSentProspect));
                if ($campaignSentProspect['status'] != 'replayed' && $campaignSentProspect['status'] != 'unsubscribe' && $campaignSentProspect['status'] != 'bounced') {
                    $campaignProspectMessage = CampaignProspectMessage::where('campaign_sent_prospect_id', $campaignSentProspect['id'])->first();

                    $google = new GoogleController();
                    $client = $google->getClient($mailbox['token']);
                    $threadResponse = $google->getThread($client, $campaignProspectMessage['thread_id']);

                    if ($threadResponse['status'] === 'success') {
                        $messages = $threadResponse['data']->messages;
                        Log::channel('development')->error("Messages: " . json_encode($messages));

                        if (count($messages) > 1) {

                            $bouncedFlag = false;

                            foreach ($messages as $messageKey => $message) {
                                foreach ($message->payload->headers as $header) {
                                    if ($header->name === 'From' && str_contains($header->value, 'mailer-daemon@googlemail.com')) {
                                        $bouncedFlag = true;
                                        break;
                                    }
                                }
                            }

                            if ($bouncedFlag) {
                                $campaignStepProspectPrevious->bounced();
                                $campaignStepProspect->delete();
                                return [
                                    'status' => 'success',
                                    'data' => 'bounced'
                                ];
                            }

                            $replayedFlag = false;

                            foreach ($messages as $messageKey => $message) {
                                foreach ($message->payload->headers as $header) {
                                    if ($header->name === 'From' && !str_contains($header->value, $mailbox->email)) {
                                        $replayedFlag = true;
                                        break;
                                    }
                                }
                            }

                            if ($replayedFlag) {
                                $campaignStepProspectPrevious->replayed();
                                $campaignStepProspect->delete();
                                return [
                                    'status' => 'success',
                                    'data' => 'replayed'
                                ];
                            }

                            return [
                                'status' => 'success',
                                'data' => 'good'
                            ];
                        } else {
                            return [
                                'status' => 'success',
                                'data' => 'good'
                            ];
                        }
                    } else {
                        Log::channel('development')->error("Thread ERROR: " . json_encode($threadResponse['data']));
                        $campaignStepProspect->delete();
                        return [
                            'status' => 'error',
                            'data' => $threadResponse['data']
                        ];
                    }
                } else {
                    $campaignStepProspect->delete();
                    return [
                        'status' => 'error',
                        'data' => 'replayed or unsubscribe'
                    ];
                }
            } else {
                return [
                    'status' => 'success',
                    'data' => 'good'
                ];
            }
        } catch (Exception $error) {
            Log::channel('development')->error("Error: " . $error->getMessage());
            return [
                'status' => 'error',
                'data' => $error
            ];
        }
    }
}
