<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // Check if the column exists first
            if (Schema::hasColumn('offers', 'product_id')) {
                // Check if the foreign key exists using DB facade
                $foreignKeys = DB::select(
                    "SELECT * FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'offers'
                    AND COLUMN_NAME = 'product_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL"
                );
                
                // If foreign key exists, drop it
                if (!empty($foreignKeys)) {
                    $table->dropForeign(['product_id']);
                }
                
                // Drop the column
                $table->dropColumn('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            // Add the product_id column back
            if (!Schema::hasColumn('offers', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained();
            }
        });
    }
};
