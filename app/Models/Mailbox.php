<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailbox extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "phone",
        "email",
        "domain",
        "avatar",
        "password",
        "create_date",
        "app_password",
        "for_linkedin",
        "email_provider_id",
    ];

    public function email_provider()
    {
        return $this->belongsTo(EmailProvider::class);
    }

    public function linkedin()
    {
        return $this->hasOne(Linkedin::class);
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'mailboxes_projects', 'mailbox_id', 'project_id');
    }
}
