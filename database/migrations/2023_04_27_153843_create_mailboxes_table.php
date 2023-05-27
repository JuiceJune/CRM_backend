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
            $table->id();
            $table->string("email");
            $table->string("name");
            $table->string("phone")->nullable();
            $table->string("avatar");
            $table->string("domain");
            $table->string("password");
            $table->date("create_date");
            $table->string("app_password")->nullable();
            $table->boolean("for_linkedin")->default(false);
            $table->foreignId("email_provider_id")->constrained();
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
