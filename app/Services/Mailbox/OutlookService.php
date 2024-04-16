<?php

namespace App\Services\Mailbox;

class OutlookService implements MailboxService
{
    public function connect(array $data)
    {
        return 2;
    }
}
