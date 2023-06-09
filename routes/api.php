<?php

use App\Http\Controllers\Api\Mailbox\MailboxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware("auth:sanctum")->group(function() {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('users', \App\Http\Controllers\Api\User\UserController::class);

    Route::prefix('profile')->group(function () {
        Route::post('/update-password/{user}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'update']);
    });

    Route::prefix('mailboxes')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Mailbox\MailboxController::class, 'index']);
        Route::middleware("mailbox.access")->get('/{mailbox}', [\App\Http\Controllers\Api\Mailbox\MailboxController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\Mailbox\MailboxController::class, 'store']);
        Route::put('/{mailbox}', [\App\Http\Controllers\Api\Mailbox\MailboxController::class, 'update']);
        Route::delete('/{mailbox}', [\App\Http\Controllers\Api\Mailbox\MailboxController::class, 'destroy']);
    });
    Route::prefix('linkedin-accounts')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Linkedin\LinkedinController::class, 'index']);
        Route::middleware("linkedin.access")->get('/{linkedin}', [\App\Http\Controllers\Api\Linkedin\LinkedinController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\Linkedin\LinkedinController::class, 'store']);
        Route::put('/{linkedin}', [\App\Http\Controllers\Api\Linkedin\LinkedinController::class, 'update']);
        Route::delete('/{linkedin}', [\App\Http\Controllers\Api\Linkedin\LinkedinController::class, 'destroy']);
    });
    Route::prefix('projects')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Project\ProjectController::class, 'index']);
        Route::middleware("project.access")->get('/{project}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\Project\ProjectController::class, 'store']);
        Route::put('/{project}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'update']);
        Route::delete('/{project}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'destroy']);
    });

    Route::get('user-projects/{user}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'getAllByUser']);
});

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
