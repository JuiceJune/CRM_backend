<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'location',
        'status',
        'campaign_id',
    ];

    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }

    public static function insertProspects($prospects)
    {
        return self::insert($prospects);
    }
}
