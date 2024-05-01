<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'account_id',
        "name",
        "logo",
        "client_id",
        "start_date",
        "end_date",
        "status"
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function mailboxes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Mailbox::class, 'mailboxes_projects', 'project_id', 'mailbox_id');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_projects', 'project_id', 'user_id');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function usersWithPosition($position_title)
    {
        return $this->users()->whereHas('position', function($query) use ($position_title) {
            $query->where('title', $position_title);
        })->get();
    }


}
