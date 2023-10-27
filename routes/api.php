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
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::get('/google/callback', [\App\Http\Controllers\Api\Google\GoogleController::class, 'callback']);

Route::middleware('auth:sanctum')->group(function() {
    Route::group(['prefix' => 'google'], function () {
        Route::get('/login', [\App\Http\Controllers\Api\Google\GoogleController::class, 'login']);
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [\App\Http\Controllers\Api\User\UserController::class, 'index']);
        Route::get('/create', [\App\Http\Controllers\Api\User\UserController::class, 'create']);
        Route::post('/', [\App\Http\Controllers\Api\User\UserController::class, 'store']);
        Route::get('/{user}', [\App\Http\Controllers\Api\User\UserController::class, 'show']);
        Route::get('/{user}/edit', [\App\Http\Controllers\Api\User\UserController::class, 'edit']);
        Route::put('/{user}', [\App\Http\Controllers\Api\User\UserController::class, 'update']);
        Route::delete('/{user}', [\App\Http\Controllers\Api\User\UserController::class, 'destroy']);
    });


    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

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
    Route::prefix('projects')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Project\ProjectController::class, 'index']);
//        Route::middleware("project.access")->get('/{project}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'show']);
        Route::get('/{project}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\Project\ProjectController::class, 'store']);
        Route::put('/{project}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'update']);
        Route::delete('/{project}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'destroy']);
    });
    Route::prefix('campaigns')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'index']);
        Route::get('/create', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'create']);
        Route::post('/', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'store']);
        Route::get('/{campaign}', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'show']);
        Route::get('/{campaign}/edit', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'edit']);
        Route::put('/{campaign}', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'update']);
        Route::delete('/{campaign}', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'destroy']);
        Route::post('/sendTestEmail', [\App\Http\Controllers\Api\Campaign\CampaignController::class, 'sendTestEmail']);
    });
    Route::prefix('prospects')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Prospect\ProspectController::class, 'index']);
        Route::get('/create', [\App\Http\Controllers\Api\Prospect\ProspectController::class, 'create']);
        Route::post('/', [\App\Http\Controllers\Api\Prospect\ProspectController::class, 'store']);
        Route::get('/{prospect}', [\App\Http\Controllers\Api\Prospect\ProspectController::class, 'show']);
        Route::get('/{prospect}/edit', [\App\Http\Controllers\Api\Prospect\ProspectController::class, 'edit']);
        Route::put('/{prospect}', [\App\Http\Controllers\Api\Prospect\ProspectController::class, 'update']);
        Route::delete('/{prospect}', [\App\Http\Controllers\Api\Prospect\ProspectController::class, 'destroy']);
    });

    Route::get('user-projects/{user}', [\App\Http\Controllers\Api\Project\ProjectController::class, 'getAllByUser']);
});
