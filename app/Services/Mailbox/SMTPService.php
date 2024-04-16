<?php

namespace App\Services\Mailbox;

class SMTPService implements MailboxService
{
    public function connect(array $data)
    {
        return 3;
    }
}
