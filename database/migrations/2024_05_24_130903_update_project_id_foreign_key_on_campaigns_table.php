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
            $table->dropForeign(['project_id']); // Drop the existing foreign key constraint
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Add the new foreign key constraint with onDelete cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['project_id']); // Drop the new foreign key constraint
            $table->foreignId('project_id')->constrained(); // Restore the old foreign key constraint
        });
    }
};
