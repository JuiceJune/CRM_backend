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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('account_id')->constrained();
            $table->string('name');
            $table->foreignId('mailbox_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('project_id')->constrained();
            $table->string('status')->default('stopped');
            $table->string('timezone')->default('Europe/Kyiv');
            $table->dateTime('start_date')->nullable();
            $table->integer('send_limit')->default(100);
            $table->json('priority_config');
            $table->string('setup_campaign_job_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
