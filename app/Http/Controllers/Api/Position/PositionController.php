<?php

namespace App\Http\Controllers\Api\Position;

use App\Http\Requests\Position\PositionUpdateRequest;
use App\Http\Requests\Position\PositionStoreRequest;
use App\Http\Requests\StorePositionRequest;
use App\Http\Resources\Position\PositionResource;
use App\Http\Controllers\Controller;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
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

            $query = Position::query()->skip($offset)->take($limit);

            $position = $query->get();

            return response(PositionResource::collection($position));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionStoreRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $position = Position::create($validated);
            return $this->respondCreated(new PositionResource($position));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new PositionResource($position));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PositionUpdateRequest $request, Position $position): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $position->update($validated);
            return $this->respondWithSuccess(new PositionResource($position));
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
//            return response($error, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position): \Illuminate\Http\JsonResponse
    {
        try {
            $position->delete();
            return $this->respondOk("Position [{$position->title}] deleted successfully");
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
