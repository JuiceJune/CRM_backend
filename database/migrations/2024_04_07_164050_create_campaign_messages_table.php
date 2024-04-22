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
        Schema::create('campaign_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_step_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_step_version_id')->constrained()->onDelete('cascade');
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            $table->foreignId('redis_job_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('pending');
            $table->dateTime('available_at');
            $table->dateTime('sent_time')->nullable();
            $table->string('message_id')->nullable();
            $table->string('message_string_id')->nullable();
            $table->string('thread_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('type')->default('from me');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_messages');
    }
};
