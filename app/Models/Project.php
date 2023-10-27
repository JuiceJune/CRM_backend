<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        "logo",
        "name",
        "client_id",
        "start_date",
        "end_date",
        "price",
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
    public function mailboxes() {
        return $this->belongsToMany(Mailbox::class, 'mailboxes_projects', 'project_id', 'mailbox_id');
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
