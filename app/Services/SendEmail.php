<?php

namespace App\Services;

use App\Services\EmailSendingStrategies\EmailSendingStrategy;

class SendEmail
{
    private $emailSendingStrategy;

    public function __construct(EmailSendingStrategy $emailSendingStrategy)
    {
        $this->emailSendingStrategy = $emailSendingStrategy;
    }

    public function send($campaign, $prospect, $version, $campaignStepProspectId, $step)
    {
        return $this->emailSendingStrategy->send($campaign, $prospect, $version, $campaignStepProspectId, $step);
    }
}
