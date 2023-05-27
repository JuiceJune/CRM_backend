<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        if(!Hash::check($validated["current_password"], auth()->user()->password)){
            return response("Old Password Doesn't match", 400);
        }

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response("Password Updated", 200);
    }
}
