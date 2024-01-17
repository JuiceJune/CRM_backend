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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('mailbox_id')->nullable()->constrained();
            $table->foreignId('project_id')->constrained();
            $table->string('status')->default('stopped');
            $table->string('timezone')->default('Europe/Kyiv');
            $table->dateTime('start_date')->nullable();
            $table->integer('send_limit')->default(100);
            $table->json('priority_config');
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
        Schema::dropIfExists('campaigns');
    }
};
