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
        Schema::table('store_hours', function (Blueprint $table) {
            $table->integer('day_number')->default(0)->after('hours');
        });

        // Update existing records with correct day numbers
        $dayNumbers = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];

        foreach ($dayNumbers as $day => $number) {
            DB::table('store_hours')
                ->where('day', $day)
                ->update(['day_number' => $number]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_hours', function (Blueprint $table) {
            $table->dropColumn('day_number');
        });
    }
}; 