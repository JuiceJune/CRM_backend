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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string("name", 100);
            $table->string("email", 50);
            $table->string("password");
            $table->string("avatar");
            $table->string("location");
            $table->date("birthday");
            $table->date("start_date");
            $table->foreignId('role_id')->constrained();
            $table->foreignId('position_id')->constrained();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
