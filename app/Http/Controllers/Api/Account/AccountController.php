<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Requests\Account\AccountUpdateRequest;
use App\Http\Requests\Account\AccountStoreRequest;
use App\Http\Resources\Account\AccountResource;
use App\Http\Controllers\Controller;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $offset = $request->input('offset', 0);

            $query = Account::query()->skip($offset)->take($limit);

            $account = $query->get();

            return response(AccountResource::collection($account));
        } catch (\Exception $error) {
            //            return $this->respondError($error->getMessage());
            return response($error, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $account = Account::create($validated);
            return $this->respondCreated(new AccountResource($account));
        } catch (\Exception $error) {
//            return $this->respondError($error->getMessage());
            return response($error, 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        try {
            return $this->respondWithSuccess(new AccountResource($account));
        } catch (\Exception $error) {
//            return $this->respondError($error->getMessage());
            return response($error, 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountUpdateRequest $request, Account $account)
    {
        try {
            $validated = $request->validated();
            $account->update($validated);
            return $this->respondWithSuccess(new AccountResource($account));
        } catch (\Exception $error) {
//            return $this->respondError($error->getMessage());
            return response($error, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        try {
            $account->delete();
            return $this->respondOk('Account deleted successfully');
        } catch (\Exception $error) {
            //            return $this->respondError($error->getMessage());
            return response($error, 400);
        }
    }
}
