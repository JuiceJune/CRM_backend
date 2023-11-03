<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        "token",
        "refresh_token",
        "expires_in",
        "signature"
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'mailboxes_projects', 'mailbox_id', 'project_id');
    }
}
