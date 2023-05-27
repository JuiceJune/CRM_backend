<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'title'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getUsersByPosition($position_id)
    {
        return $this->users()->where('position_id', $position_id)->get();
    }
}
