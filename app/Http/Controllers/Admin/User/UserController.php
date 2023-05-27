<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $positions = Position::all();
        return view('admin.user.create', compact('roles', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        if(isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'users/avatars', 'public'
            );
        } else {
            $validated["avatar"] = "users/avatars/default.png";
        }

        User::create($validated);
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $positions = Position::all();
        return view('admin.user.edit', compact('user', 'roles', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::findOrFail($id);
        if(isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'users/avatars', 'public'
            );
            if(File::exists(public_path('storage/'.$user->avatar))  && $user->avatar != "users/avatars/default.png") {
                File::delete(public_path('storage/'.$user->avatar));
            }
        }
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);
            if($user->delete()) {
                return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
            } else {
                return redirect()->route('admin.users.index')->with('error', 'User not deleted.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.users.index')->with('error', 'This user cannot be deleted due to existing dependencies (client or user)');
        }
    }
}
