<?php

namespace App\Services\MailboxServices;

class OutlookService implements MailboxService
{
    public function getClient()
    {
        // TODO: Implement getClient() method.
    }

    public function setClient($client)
    {
        // TODO: Implement setClient() method.
    }

    public function connectAccount($accountUuid, $url, $project)
    {
        // TODO: Implement connectAccount() method.
    }

    public function initializeClient($token)
    {
        // TODO: Implement initializeClient() method.
    }

    public function sendMessage($campaignMessage)
    {
        // TODO: Implement sendMessage() method.
    }

    public function generateMessage($senderName, $senderEmail, $prospectEmail, $subject, $message, $signature, $messageUuid, $threadId, $messageStringId)
    {
        // TODO: Implement generateMessage() method.
    }

    public function setSnippets($snippets, $messageText, $subject)
    {
        // TODO: Implement setSnippets() method.
    }

    public function getHistory($token, $historyId)
    {
        // TODO: Implement getHistory() method.
    }

    public function getMessage($token, $messageId)
    {
        // TODO: Implement getMessage() method.
    }

    public function getThread($token, $threadId)
    {
        // TODO: Implement getThread() method.
    }

    public function refreshToken($token, $refreshToken)
    {
        // TODO: Implement refreshToken() method.
    }

    public function getMessageStringId($token, $messageId)
    {
        // TODO: Implement getMessageStringId() method.
    }
}
