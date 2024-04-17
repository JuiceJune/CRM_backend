<?php

namespace App\Services\Mail;

use App\Services\Mailbox\GmailService;
use App\Models\CampaignMessage;
use App\Models\Mailbox;
use Error;
use Illuminate\Support\Facades\Log;

class SetupMailService
{
    private CampaignMessage $campaignMessage;
    private Mailbox $mailbox;
    public function __construct(CampaignMessage $campaignMessage)
    {
        $this->campaignMessage = $campaignMessage;
        $this->mailbox = $campaignMessage->campaign->mailbox;
    }
    public function setup()
    {
        try{
            Log::alert('Send Message Start');

            if($this->mailbox->email_provider === 'gmail') {

                $gmailService = new GmailService();

                $response = $gmailService->sendMessage($this->campaignMessage);

                if(!$response) {
                    throw new Error('Message not sent');
                }

                Log::alert('SendMessage Response: ' . json_encode($response));
            }
        } catch (\Exception $error) {
            Log::error($error->getMessage());
        }
    }
}
