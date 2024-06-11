<?php

use App\Http\Controllers\Api\Account\AccountController;
use App\Http\Controllers\Api\Campaign\CampaignController;
use App\Http\Controllers\Api\Client\ClientController;
use App\Http\Controllers\Api\Mailbox\MailboxController;
use App\Http\Controllers\Api\Position\PositionController;
use App\Http\Controllers\Api\Project\ProjectController;
use App\Http\Controllers\Api\Prospect\ProspectController;
use App\Http\Controllers\Api\Role\RoleController;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/image/{campaignMessage:uuid}', [CampaignController::class, 'openEmail'])->name('openEmail');

Route::get('/unsubscribe/{campaignMessage:uuid}', [CampaignController::class, 'unsubscribe'])->name('unsubscribe');

Route::group(['prefix' => ''], function () {
    Route::post('login', [AuthController::class, 'login']);
//    Route::post('register', [AuthController::class, 'register']);
});

Route::get('mailboxes/store', [MailboxController::class, 'store'])->name('mailboxes.store');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);

    Route::group(['prefix' => 'accounts'], function () {
        Route::get('/', [AccountController::class, 'index']);
        Route::get('/{account:uuid}', [AccountController::class, 'show']);
        Route::post('/', [AccountController::class, 'store']);
        Route::put('/{account:uuid}', [AccountController::class, 'update']);
        Route::delete('/{account:uuid}', [AccountController::class, 'destroy']);
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/{role:uuid}', [RoleController::class, 'show']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{role:uuid}', [RoleController::class, 'update']);
        Route::delete('/{role:uuid}', [RoleController::class, 'destroy']);
    });

    Route::group(['prefix' => 'positions'], function () {
        Route::get('/', [PositionController::class, 'index']);
        Route::get('/{position:uuid}', [PositionController::class, 'show']);
        Route::post('/', [PositionController::class, 'store']);
        Route::put('/{position:uuid}', [PositionController::class, 'update']);
        Route::delete('/{position:uuid}', [PositionController::class, 'destroy']);
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/create', [UserController::class, 'create']);
        Route::get('/{user:uuid}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user:uuid}/edit', [UserController::class, 'edit']);
        Route::put('/{user:uuid}', [UserController::class, 'update']);
        Route::delete('/{user:uuid}', [UserController::class, 'destroy']);
    });

    Route::group(['prefix' => 'mailboxes'], function () {
        Route::post('/connect', [MailboxController::class, 'connect']);
        Route::get('/', [MailboxController::class, 'index']);
        Route::get('/{mailbox:uuid}', [MailboxController::class, 'show']);
//        Route::post('/store', [MailboxController::class, 'store']);
        Route::put('/{mailbox:uuid}', [MailboxController::class, 'update']);
        Route::delete('/{mailbox:uuid}', [MailboxController::class, 'destroy']);
    });

    Route::group(['prefix' => 'clients'], function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::get('/{client:uuid}', [ClientController::class, 'show']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{client:uuid}/edit', [ClientController::class, 'edit']);
        Route::put('/{client:uuid}', [ClientController::class, 'update']);
        Route::delete('/{client:uuid}', [ClientController::class, 'destroy']);
    });

    Route::group(['prefix' => 'projects'], function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::get('/create', [ProjectController::class, 'create']);
        Route::get('/{project:uuid}', [ProjectController::class, 'show']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/{project:uuid}/edit', [ProjectController::class, 'edit']);
        Route::put('/{project:uuid}', [ProjectController::class, 'update']);
        Route::delete('/{project:uuid}', [ProjectController::class, 'destroy']);
    });

    Route::group(['prefix' => 'prospects'], function () {
        Route::get('/', [ProspectController::class, 'index']);
        Route::get('/{prospect:uuid}', [ProspectController::class, 'show']);
        Route::post('/', [ProspectController::class, 'store']);
        Route::post('/csv-upload', [ProspectController::class, 'csvUpload']);
        Route::put('/{prospect:uuid}', [ProspectController::class, 'update']);
        Route::delete('/{prospect:uuid}', [ProspectController::class, 'destroy']);
        Route::post('/csv-upload', [ProspectController::class, 'csvUpload']);
    });

    Route::group(['prefix' => 'campaigns'], function () {
        Route::get('/create', [CampaignController::class, 'create']);
        Route::get('/', [CampaignController::class, 'index']);
        Route::get('/{campaign:uuid}', [CampaignController::class, 'show']);
        Route::post('/', [CampaignController::class, 'store']);
        Route::post('/sendTestEmail', [CampaignController::class, 'sendTestEmail']);
        Route::get('/{campaign:uuid}/edit', [CampaignController::class, 'edit']);
        Route::put('/{campaign:uuid}', [CampaignController::class, 'update']);
        Route::delete('/{campaign:uuid}', [CampaignController::class, 'destroy']);

        Route::get('/{campaign:uuid}/start', [CampaignController::class, 'startCampaign']);
        Route::get('/{campaign:uuid}/stop', [CampaignController::class, 'stopCampaign']);
        Route::get('/{campaign:uuid}/report', [CampaignController::class, 'generateReport']);
    });

});
