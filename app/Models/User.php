<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Collections\UserCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role_id',
        'position_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role->title === 'admin';
    }

    public function newCollection(array $models = [])
    {
        return new UserCollection($models);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function position() {
        return $this->belongsTo(Position::class);
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'users_projects', 'user_id', 'project_id');
    }

    public static function getUsersByPosition($position)
    {
        return self::whereHas('position', function ($query) use ($position) {
            $query->where('title', $position);
        })->get();
    }
}
