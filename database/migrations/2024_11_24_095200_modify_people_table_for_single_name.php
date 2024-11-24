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
        Schema::table('people', function (Blueprint $table) {
            // Drop the old columns
            $table->dropColumn(['first_name', 'last_name', 'phone', 'date_of_birth', 'address']);
            
            // Add the new name column
            $table->string('name')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Remove the new column
            $table->dropColumn('name');
            
            // Add back the old columns
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
        });
    }
};
