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
        Schema::create('campaign_prospect_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_sent_prospect_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('message');
            $table->string('from');
            $table->string('to');
            $table->dateTime('sent_time')->nullable();
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
        Schema::dropIfExists('campaign_prospect_messages');
    }
};
