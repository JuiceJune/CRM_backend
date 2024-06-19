<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponseHelpers;
    public function register(Request $request){
        //
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $credentials = $request->validated();

            if(!Auth::attempt($credentials)) {
                return $this->respondFailedValidation('Provided email or password is incorrect');
            }

            /** @var User $user */
            $user = Auth::user();

            $ipAddress = $request->ip();

            if ($user->ip !== $ipAddress) {
                $user->update(['ip' => $ipAddress]);
            }

            $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;

            return $this->respondWithSuccess([
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            /** @var User $user */
            $user = $request->user();

            $user->currentAccessToken()->delete();

            return $this->respondWithSuccess();
        } catch (\Exception $error) {
            return $this->respondError($error->getMessage());
        }
    }

    public function user(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($request->user());
    }
}
