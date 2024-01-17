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
        Schema::create('campaign_steps', function (Blueprint $table) {
            $table->id();
            $table->integer('step');
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->json('sending_time_json');
            $table->integer('period')->default(120);
            $table->json('start_after')->nullable();
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
        Schema::dropIfExists('campaign_steps');
    }
};
