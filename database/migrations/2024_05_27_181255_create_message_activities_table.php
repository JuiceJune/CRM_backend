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
        Schema::create('message_activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->dateTime('date_time');
            $table->string('type');
            $table->string('ip')->nullable();
            $table->foreignId('campaign_message_id')->constrained('campaign_messages')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_activities');
    }
};
