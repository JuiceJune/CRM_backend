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
        Schema::create('redis_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained();
            $table->string('type');
            $table->string('redis_job_id');
            $table->string('campaign_id')->constrained();
            $table->string('prospect_id')->nullable()->constrained();
            $table->string('campaign_step_id')->nullable()->constrained();
            $table->string('campaign_step_version_id')->nullable()->constrained();
            $table->string('status');
            $table->dateTime('date_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redis_jobs');
    }
};
