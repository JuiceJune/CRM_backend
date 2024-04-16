<?php

namespace App\Services\Mailbox;

use Laravel\Socialite\Facades\Socialite;
use Google\Service\Gmail;

class GmailService implements MailboxService
{
    public function connect()
    {
        return $this->getAuthUrl();
    }

    public function getAuthUrl()
    {
        return Socialite::driver('google')
            ->scopes([Gmail::GMAIL_READONLY, Gmail::GMAIL_COMPOSE])
            ->with(['access_type' => 'offline', "prompt" => "consent"])
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    }
}
