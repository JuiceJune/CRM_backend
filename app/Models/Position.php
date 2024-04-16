<?php

namespace App\Models;

use App\Http\Resources\User\UserResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Position extends Model
{
    use HasFactory, UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getUsersByPosition($position_id): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->users()
                ->where('position_id', $position_id)
                ->get()
        );
    }
}
