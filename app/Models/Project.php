<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "logo",
        "client_id",
        "start_date",
        "end_date",
        "period",
        "price",
        "description",
    ];

    public function mailboxes() {
        return $this->belongsToMany(Mailbox::class, 'mailboxes_projects', 'project_id', 'mailbox_id');
    }

    public function linkedin_accounts() {
        return $this->belongsToMany(Linkedin::class, 'linkedin_accounts_projects', 'project_id', 'linkedin_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'users_projects', 'project_id', 'user_id');
    }

    public function usersWithPosition($position_title)
    {
        return $this->users()->whereHas('position', function($query) use ($position_title) {
            $query->where('title', $position_title);
        })->get();
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
