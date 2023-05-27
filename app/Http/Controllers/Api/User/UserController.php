<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Position;
use App\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(UserResource::collection(
            User::all()
        ));
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

        if (isset($validated["avatar"])) {
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
    public function show(User $user)
    {
        return new UserResource($user);
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
        //TODO add services
        $validated = $request->validated();

        $user = User::findOrFail($id);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::findOrFail($user->user_id);
        if (isset($validated["avatar"])) {
            $validated["avatar"] = $request->file('avatar')->store(
                'users/avatars', 'public'
            );
            if (File::exists(public_path('storage/' . $user->avatar)) && $user->avatar != "users/avatars/default.png") {
                File::delete(public_path('storage/' . $user->avatar));
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
            //TODO delete avatar file
            $user = User::find($id);
            $user->projects()->detach();
            if ($user->delete()) {
                return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
            } else {
                return redirect()->route('admin.users.index')->with('error', 'User not deleted.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.mailboxes.index')->with('error', 'This user cannot be deleted due to existing dependencies (project)');
        }
    }
}
