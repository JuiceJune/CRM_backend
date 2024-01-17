<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_sent_prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_step_version_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_step_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active');
            $table->dateTime('sent_time')->nullable();
            $table->dateTime('response_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_sent_prospects');
    }
};
