<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('admin');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('/', function () {
        return view('admin.main');
    })->name('admin.main');

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\Role\RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/create', [\App\Http\Controllers\Admin\Role\RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/', [\App\Http\Controllers\Admin\Role\RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\Role\RoleController::class, 'show'])->name('admin.roles.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\Role\RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\Role\RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\Role\RoleController::class, 'destroy'])->name('admin.roles.destroy');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\User\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/create', [\App\Http\Controllers\Admin\User\UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [\App\Http\Controllers\Admin\User\UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\User\UserController::class, 'show'])->name('admin.users.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\User\UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\User\UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\User\UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    Route::group(['prefix' => 'positions'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\Position\PositionController::class, 'index'])->name('admin.positions.index');
        Route::get('/create', [\App\Http\Controllers\Admin\Position\PositionController::class, 'create'])->name('admin.positions.create');
        Route::post('/', [\App\Http\Controllers\Admin\Position\PositionController::class, 'store'])->name('admin.positions.store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\Position\PositionController::class, 'show'])->name('admin.positions.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\Position\PositionController::class, 'edit'])->name('admin.positions.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\Position\PositionController::class, 'update'])->name('admin.positions.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\Position\PositionController::class, 'destroy'])->name('admin.positions.destroy');
    });

    Route::group(['prefix' => 'clients'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\Client\ClientController::class, 'index'])->name('admin.clients.index');
        Route::get('/create', [\App\Http\Controllers\Admin\Client\ClientController::class, 'create'])->name('admin.clients.create');
        Route::post('/', [\App\Http\Controllers\Admin\Client\ClientController::class, 'store'])->name('admin.clients.store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\Client\ClientController::class, 'show'])->name('admin.clients.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\Client\ClientController::class, 'edit'])->name('admin.clients.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\Client\ClientController::class, 'update'])->name('admin.clients.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\Client\ClientController::class, 'destroy'])->name('admin.clients.destroy');
    });

    Route::group(['prefix' => 'mailboxes'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\Mailbox\MailboxController::class, 'index'])->name('admin.mailboxes.index');
        Route::get('/create', [\App\Http\Controllers\Admin\Mailbox\MailboxController::class, 'create'])->name('admin.mailboxes.create');
        Route::post('/', [\App\Http\Controllers\Admin\Mailbox\MailboxController::class, 'store'])->name('admin.mailboxes.store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\Mailbox\MailboxController::class, 'show'])->name('admin.mailboxes.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\Mailbox\MailboxController::class, 'edit'])->name('admin.mailboxes.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\Mailbox\MailboxController::class, 'update'])->name('admin.mailboxes.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\Mailbox\MailboxController::class, 'destroy'])->name('admin.mailboxes.destroy');
    });

    Route::group(['prefix' => 'projects'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\Project\ProjectController::class, 'index'])->name('admin.projects.index');
        Route::get('/create', [\App\Http\Controllers\Admin\Project\ProjectController::class, 'create'])->name('admin.projects.create');
        Route::post('/', [\App\Http\Controllers\Admin\Project\ProjectController::class, 'store'])->name('admin.projects.store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\Project\ProjectController::class, 'show'])->name('admin.projects.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\Project\ProjectController::class, 'edit'])->name('admin.projects.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\Project\ProjectController::class, 'update'])->name('admin.projects.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\Project\ProjectController::class, 'destroy'])->name('admin.projects.destroy');
    });

    Route::group(['prefix' => 'campaigns'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\Campaign\CampaignController::class, 'index'])->name('admin.campaigns.index');
        Route::get('/create', [\App\Http\Controllers\Admin\Campaign\CampaignController::class, 'create'])->name('admin.campaigns.create');
        Route::post('/', [\App\Http\Controllers\Admin\Campaign\CampaignController::class, 'store'])->name('admin.campaigns.store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\Campaign\CampaignController::class, 'show'])->name('admin.campaigns.show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\Campaign\CampaignController::class, 'edit'])->name('admin.campaigns.edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\Campaign\CampaignController::class, 'update'])->name('admin.campaigns.update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\Campaign\CampaignController::class, 'destroy'])->name('admin.campaigns.destroy');
    });

    Route::group(['prefix' => 'google'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\Google\GoogleController::class, 'index'])->name('admin.google.index');
        Route::get('/login', [\App\Http\Controllers\Admin\Google\GoogleController::class, 'login'])->name('admin.google.login');
        Route::get('/callback', [\App\Http\Controllers\Admin\Google\GoogleController::class, 'callback'])->name('admin.google.callback');
    });
});
