<?php

namespace App\Services\SendEmailServices;

use App\Services\CampaignMessageService\CampaignMessageService;
use App\Services\MailboxServices\GmailService;
use App\Models\CampaignMessage;
use App\Models\Mailbox;
use App\Services\MessagesStatusServices\CheckMessageStatus;
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
    public function setup(): void
    {
        try{
            Log::alert('Send Message Start');

            if($this->mailbox->email_provider === 'gmail') {

                $gmailService = new GmailService();

                $checkMessageStatus = new CheckMessageStatus();
                $statusCheckResponse = $checkMessageStatus->checkStatus($this->campaignMessage, $gmailService);

                if($statusCheckResponse['status'] === 'error' || $statusCheckResponse['data'] === 'not-send') {
                    Log::alert('Send Message Error');
                    throw new Error($statusCheckResponse['data']);
                }

                $response = $gmailService->sendMessage($this->campaignMessage);

                if(!$response) {
                    throw new Error('Message not sent');
                }

                $campaignMessageService = new CampaignMessageService($this->campaignMessage);
                $campaignMessageService->sent($response, $gmailService);

                Log::alert('SendMessage Response: ' . json_encode($response));
            }
        } catch (\Exception $error) {
            Log::error("Setup Mail Service: " . $error->getMessage());
        }
    }
}