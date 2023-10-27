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
use Illuminate\Http\Request;
use PHPUnit\Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Отримуємо значення параметрів limit, offset та fields з запиту, або встановлюємо їх за замовчуванням.
            $limit = $request->input('limit', 10); // За замовчуванням виводимо 10 користувачів.
            $offset = $request->input('offset', 0); // За замовчуванням починаємо з першого користувача.

            // Створюємо запит до бази даних з врахуванням обмежень та зсуву.
            $query = User::skip($offset)->take($limit);

            // Витягуємо користувачів та серіалізуємо їх за допомогою ресурсу UserResource.
            $users = $query->get();

            return response(UserResource::collection($users)); // Використовуємо ресурс для серіалізації результату.
        } catch (Exception $error) {
            return response($error, 400);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = Role::all();
            $positions = Position::all();
            return response(["roles" => $roles, "positions" => $positions]);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['password'] = Hash::make($validated['password']);

            if (isset($validated["avatar"])) {
                $validated["avatar"] = $request->file('avatar')->store(
                    'users/avatars', 'public'
                );
            } else {
                $validated["avatar"] = "users/avatars/default.png";
            }

            $user = User::create($validated);
            return response($user);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        try {
            $currentUser = new UserResource($user);
            return response()->json($currentUser);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $roles = Role::all();
            $positions = Position::all();
            return response(["" => $user, "roles" => $roles, "positions" => $positions]);
        } catch (Exception $error) {
            return response($error, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        try {
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
            return response($user);
        } catch (Exception $error) {
            return response($error, 400);
        }
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
            $user->delete();
            return response('User deleted successfully');
        } catch (Exception $error) {
            return response($error, 400);
        }
    }
}
