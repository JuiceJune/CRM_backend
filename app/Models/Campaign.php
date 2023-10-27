<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mailbox_id',
        'project_id',
        'subject',
        'message'
    ];

    public function mailbox() {
        return $this->belongsTo(Mailbox::class);
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }
}
