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
        Schema::create('emails_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_step_version_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_step_id')->constrained()->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('prospect_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('emails_jobs');
    }
};
