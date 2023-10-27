<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mailbox extends Model
{
    use HasFactory;

    protected $fillable = [
        "email",
        "name",
        "domain",
        "avatar",
        "phone",
        "password",
        "app_password",
        "email_provider",
        "token",
        "refresh_token",
        "expires_in"
    ];

    public function projects() {
        return $this->belongsToMany(Project::class, 'mailboxes_projects', 'mailbox_id', 'project_id');
    }
}
