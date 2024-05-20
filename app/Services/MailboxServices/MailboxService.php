<?php

namespace App\Services\MailboxServices;

interface MailboxService
{
    public function getClient();
    public function setClient($client);
    public function connectAccount($accountUuid, $url, $project);
    public function initializeClient($token);
    public function generateMessage($senderName, $senderEmail, $prospectEmail, $subject, $message,
                                    $signature, $messageUuid, $threadId, $messageStringId);
    public function setSnippets($snippets, $messageText, $subject);
    public function sendMessage($campaignMessage);
    public function getHistory($token, $historyId);
    public function getMessage($token, $messageId);
    public function getMessageStringId($token, $messageId);
    public function getThread($token, $threadId);
    public function refreshToken($token, $refreshToken);
}
