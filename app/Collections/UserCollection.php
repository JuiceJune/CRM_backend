<?php
namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class UserCollection extends Collection
{
    public function getUsersByPosition($position)
    {
        return $this->filter(function ($user) use ($position) {
            return $user->position->title === $position;
        });
    }
}
