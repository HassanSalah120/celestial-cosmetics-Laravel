<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreHour;
use Illuminate\Support\Facades\DB;

class StoreHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing store hours
        DB::table('store_hours')->truncate();

        // Define default store hours
        $storeHours = [
            ['day' => 'Monday', 'hours' => '9:00 AM - 5:00 PM', 'day_number' => 1],
            ['day' => 'Tuesday', 'hours' => '9:00 AM - 5:00 PM', 'day_number' => 2],
            ['day' => 'Wednesday', 'hours' => '9:00 AM - 5:00 PM', 'day_number' => 3],
            ['day' => 'Thursday', 'hours' => '9:00 AM - 5:00 PM', 'day_number' => 4],
            ['day' => 'Friday', 'hours' => '9:00 AM - 5:00 PM', 'day_number' => 5],
            ['day' => 'Saturday', 'hours' => '10:00 AM - 4:00 PM', 'day_number' => 6],
            ['day' => 'Sunday', 'hours' => 'Closed', 'day_number' => 7],
        ];

        // Insert store hours
        foreach ($storeHours as $hours) {
            StoreHour::create([
                'day' => $hours['day'],
                'hours' => $hours['hours'],
                'day_number' => $hours['day_number'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Store hours seeded successfully!');
    }
} 