<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Resources\Position\PositionResource;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\User\UserCreateResource;
use App\Http\Resources\User\UserResource;
use App\Models\Position;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = User::query()->skip($offset)->take($limit);

            $users = $query->get();

            return response()->json(UserResource::collection($users));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Http\JsonResponse
    {
        try {
            $roles = Role::all();
            $positions = Position::all();
            return response()->json([
                "roles" => RoleResource::collection($roles),
                "positions" => PositionResource::collection($positions)
            ]);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = $request->user();
            $validated['account_id'] = $user->account_id;

            $validated['password'] = Hash::make($validated['password']);
            $validated['avatar'] = 'users/avatars/default.png';

            $user = User::query()->create($validated);
            return $this->respondCreated(new UserResource($user));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new UserResource($user));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): \Illuminate\Http\JsonResponse
    {
        try {
            $roles = Role::all();
            $positions = Position::all();
            return $this->respondWithSuccess([
                "user" => new UserResource($user),
                "roles" => RoleResource::collection($roles),
                "positions" => PositionResource::collection($positions)
            ]);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);
            return $this->respondWithSuccess(new UserCreateResource($user));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): \Illuminate\Http\JsonResponse
    {
        try {
            $user->delete();
            return $this->respondOk($user->name);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
