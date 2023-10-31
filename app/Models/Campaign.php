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
        'message',
        'sending_time_json',
        'status',
        'period'
    ];

    protected $casts = [
        'sending_time_json' => 'json'
    ];

    protected $attributes = [
        'period' => 60,
        'status' => 'stopped',
        'sending_time_json' => '{
        "Mon": [true, "08:00", "15:00"],
        "Tues": [true, "08:00", "15:00"],
        "Wed": [true, "08:00", "15:00"],
        "Thurs": [true, "08:00", "15:00"],
        "Fri": [true, "08:00", "15:00"],
        "Sat": [false, "08:00", "15:00"],
        "Sun": [false, "08:00", "15:00"]
    }',
    ];

    public function mailbox() {
        return $this->belongsTo(Mailbox::class);
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }
}
