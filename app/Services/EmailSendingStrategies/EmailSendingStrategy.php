<?php

namespace App\Services\EmailSendingStrategies;

interface EmailSendingStrategy
{
    public function send($campaign, $prospect, $version, $campaignStepProspectId, $step);

    public function setSnippets($snippets, $messageText, $subject);
}
