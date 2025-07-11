<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update the about_page record with certification data
        DB::table('about_page')
            ->where('id', 1)
            ->update([
                // Certification 1
                'certification_1_title' => 'Leaping Bunny',
                'certification_1_title_ar' => 'ليبينج باني',
                'certification_1_description' => 'Cruelty-Free Certified',
                'certification_1_description_ar' => 'معتمد خالي من القسوة',
                'certification_1_icon' => 'sun',
                
                // Certification 2
                'certification_2_title' => 'Ecocert',
                'certification_2_title_ar' => 'إيكوسيرت',
                'certification_2_description' => 'Organic Certified',
                'certification_2_description_ar' => 'معتمد عضوي',
                'certification_2_icon' => 'cube',
                
                // Certification 3
                'certification_3_title' => 'FSC',
                'certification_3_title_ar' => 'شهادة FSC',
                'certification_3_description' => 'Sustainable Packaging',
                'certification_3_description_ar' => 'تغليف مستدام',
                'certification_3_icon' => 'scale',
                
                // Certification 4
                'certification_4_title' => 'Vegan',
                'certification_4_title_ar' => 'نباتي',
                'certification_4_description' => 'Vegan Friendly Products',
                'certification_4_description_ar' => 'منتجات صديقة للنباتيين',
                'certification_4_icon' => 'sparkles',
            ]);
    }
} 