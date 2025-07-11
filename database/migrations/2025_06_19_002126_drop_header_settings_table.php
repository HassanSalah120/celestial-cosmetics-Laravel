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
        // Backup the header_settings data before dropping the table
        if (Schema::hasTable('header_settings')) {
            $settings = DB::table('header_settings')->get();
            
            // Store the settings in a backup table
            Schema::create('header_settings_backup', function (Blueprint $table) {
                $table->id();
                $table->string('key');
                $table->text('value')->nullable();
                $table->string('display_name')->nullable();
                $table->string('type')->default('string');
                $table->text('description')->nullable();
                $table->timestamps();
            });
            
            // Copy data to backup table
            foreach ($settings as $setting) {
                DB::table('header_settings_backup')->insert([
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'display_name' => $setting->display_name,
                    'type' => $setting->type,
                    'description' => $setting->description,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at,
                ]);
            }
            
            // Drop the original table
            Schema::dropIfExists('header_settings');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the header_settings table if it doesn't exist
        if (!Schema::hasTable('header_settings') && Schema::hasTable('header_settings_backup')) {
            Schema::create('header_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key');
                $table->text('value')->nullable();
                $table->string('display_name')->nullable();
                $table->string('type')->default('string');
                $table->text('description')->nullable();
                $table->timestamps();
            });
            
            // Restore data from backup
            $backupSettings = DB::table('header_settings_backup')->get();
            
            foreach ($backupSettings as $setting) {
                DB::table('header_settings')->insert([
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'display_name' => $setting->display_name,
                    'type' => $setting->type,
                    'description' => $setting->description,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at,
                ]);
            }
        }
    }
};
