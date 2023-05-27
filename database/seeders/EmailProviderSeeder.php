<?php

namespace Database\Seeders;

use App\Models\EmailProvider;
use Illuminate\Database\Seeder;

class EmailProviderSeeder extends Seeder
{
    protected array $email_providers = [
        ["title" => "Gmail", "logo" => "email_providers/logos/gmail.png"],
        ["title" => "Outlook", "logo" => "email_providers/logos/outlook.png"],
        ["title" => "Yahoo", "logo" => "email_providers/logos/yahoo.png"],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->email_providers as $email_provider) {
            EmailProvider::create($email_provider);
        }
    }
}
