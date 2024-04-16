<?php

namespace App\Http\Controllers\Api\Role;

use App\Http\Requests\Role\RoleUpdateRequest;
use App\Http\Requests\Role\RoleStoreRequest;
use App\Http\Resources\Role\RoleResource;
use App\Http\Controllers\Controller;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = Role::query()->skip($offset)->take($limit);

            $role = $query->get();

            return response(RoleResource::collection($role));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $role = Role::create($validated);
            return $this->respondCreated(new RoleResource($role));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new RoleResource($role));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUpdateRequest $request, Role $role): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $role->update($validated);
            return $this->respondWithSuccess(new RoleResource($role));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
//            return response($error, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): \Illuminate\Http\JsonResponse
    {
        try {
            $role->delete();
            return $this->respondOk("Role [{$role->title}] deleted successfully");
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
