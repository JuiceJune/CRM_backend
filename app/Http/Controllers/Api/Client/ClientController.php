<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientStoreRequest;
use App\Http\Requests\Client\ClientUpdateRequest;
use App\Http\Resources\Client\ClientResource;
use App\Models\Client;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Exception;

class ClientController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 20);
            $offset = $request->input('offset', 0);

            $query = Client::query()->skip($offset)->take($limit);

            $clients = $query->get();

            return response(ClientResource::collection($clients));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClientStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();
            $validated['account_id'] = $user->account_id;

            if(!isset($validated['email'])) {
                $validated['email'] = 'client@email.com';
            }

            $client = Client::create($validated);

            return $this->respondCreated(new ClientResource($client));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        try {
            return $this->respondWithSuccess(new ClientResource($client));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): \Illuminate\Http\JsonResponse
    {
        try {
            return $this->respondWithSuccess(new ClientResource($client));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientUpdateRequest $request, Client $client): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();

            $client->update($validated);

            return $this->respondWithSuccess(new ClientResource($client));
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): \Illuminate\Http\JsonResponse
    {
        try {
            $client->delete();
            return $this->respondOk($client->name);
        } catch (Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }
}
