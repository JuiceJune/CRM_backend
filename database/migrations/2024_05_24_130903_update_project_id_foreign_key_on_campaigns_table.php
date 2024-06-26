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
        Schema::table('campaigns', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['project_id']);
            // Add the foreign key constraint with onDelete cascade
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Drop the new foreign key constraint with onDelete cascade
            $table->dropForeign(['project_id']);
            // Add the old foreign key constraint without onDelete cascade
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }
};
