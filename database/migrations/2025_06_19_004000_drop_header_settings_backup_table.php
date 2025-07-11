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
        // Create a backup of the backup table data (just in case)
        if (Schema::hasTable('header_settings_backup')) {
            // Get all data from the backup table
            $settings = DB::table('header_settings_backup')->get();
            
            // Store the settings data in a JSON file
            $jsonData = json_encode($settings, JSON_PRETTY_PRINT);
            file_put_contents(storage_path('header_settings_backup_' . date('Y_m_d_His') . '.json'), $jsonData);
            
            // Drop the backup table
            Schema::dropIfExists('header_settings_backup');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't restore the data automatically, but we can recreate the table structure
        if (!Schema::hasTable('header_settings_backup')) {
            Schema::create('header_settings_backup', function (Blueprint $table) {
                $table->id();
                $table->string('key');
                $table->text('value')->nullable();
                $table->string('display_name')->nullable();
                $table->string('type')->default('string');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }
};
