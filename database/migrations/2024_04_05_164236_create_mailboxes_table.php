<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mailboxes', function (Blueprint $table) {
            $table->id('id');
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained();
            $table->string("name");
            $table->string("email");
            $table->string("password")->nullable();
            $table->string("domain")->nullable();
            $table->string("avatar")->default('mailboxes/avatars/default.png');
            $table->string('token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->string('expires_in')->nullable();
            $table->integer('send_limit')->default(200);
            $table->text("signature")->nullable();
            $table->string("status")->default('inactive');
            $table->string("scopes")->nullable();
            $table->dateTime("last_token_refresh")->nullable();
            $table->json("errors")->nullable();
            $table->string("email_provider")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mailboxes');
    }
};
