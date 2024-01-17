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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('status')->default('active');
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->dateTime('date_contacted')->nullable();
            $table->dateTime('date_responded')->nullable();
            $table->dateTime('date_added')->nullable();
            $table->string('phone')->nullable();
            $table->string('title')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('timezone')->nullable();
            $table->string('industry')->nullable();
            $table->json('tags')->nullable();
            $table->string('snippet_1')->nullable();
            $table->string('snippet_2')->nullable();
            $table->string('snippet_3')->nullable();
            $table->string('snippet_4')->nullable();
            $table->string('snippet_5')->nullable();
            $table->string('snippet_6')->nullable();
            $table->string('snippet_7')->nullable();
            $table->string('snippet_8')->nullable();
            $table->string('snippet_9')->nullable();
            $table->string('snippet_10')->nullable();
            $table->string('snippet_11')->nullable();
            $table->string('snippet_12')->nullable();
            $table->string('snippet_13')->nullable();
            $table->string('snippet_14')->nullable();
            $table->string('snippet_15')->nullable();
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
        Schema::dropIfExists('prospects');
    }
};
