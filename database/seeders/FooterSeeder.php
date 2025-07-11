<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FooterSection;
use App\Models\FooterLink;
use App\Models\Setting;

class FooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding footer content...');

        // Seed Footer Sections
        $sections = [
            [
                'title' => 'About Us',
                'title_ar' => 'من نحن',
                'type' => 'links',
                'sort_order' => 1,
                'is_active' => true,
                'links' => [
                    ['title' => 'Our Story', 'url' => '/about', 'sort_order' => 1],
                    ['title' => 'Blog', 'url' => '/blog', 'sort_order' => 2],
                    ['title' => 'Contact Us', 'url' => '/contact', 'sort_order' => 3],
                ]
            ],
            [
                'title' => 'Customer Service',
                'title_ar' => 'خدمة العملاء',
                'type' => 'links',
                'sort_order' => 2,
                'is_active' => true,
                'links' => [
                    ['title' => 'FAQ', 'url' => '/faq', 'sort_order' => 1],
                    ['title' => 'Shipping & Returns', 'url' => '/shipping-returns', 'sort_order' => 2],
                    ['title' => 'Privacy Policy', 'url' => '/privacy-policy', 'sort_order' => 3],
                ]
            ],
            [
                'title' => 'Newsletter',
                'title_ar' => 'النشرة الإخبارية',
                'type' => 'newsletter',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sections as $sectionData) {
            $section = FooterSection::firstOrCreate(
                ['title' => $sectionData['title']],
                [
                    'title_ar' => $sectionData['title_ar'],
                    'type' => $sectionData['type'],
                    'sort_order' => $sectionData['sort_order'],
                    'is_active' => $sectionData['is_active'],
                ]
            );

            if (isset($sectionData['links'])) {
                foreach ($sectionData['links'] as $linkData) {
                    FooterLink::firstOrCreate(
                        ['title' => $linkData['title'], 'column_id' => $section->id],
                        [
                            'url' => $linkData['url'],
                            'sort_order' => $linkData['sort_order'],
                        ]
                    );
                }
            }
        }

        // Seed Footer Settings
        $settings = [
            ['key' => 'copyright_text', 'value' => '© ' . date('Y') . ' Celestial Cosmetics. All rights reserved.'],
            ['key' => 'copyright_text_ar', 'value' => '© ' . date('Y') . ' سيليستيال كوزمتكس. جميع الحقوق محفوظة.'],
            ['key' => 'tagline', 'value' => 'Elevate your beauty with our cosmic collection of premium cosmetics.'],
            ['key' => 'tagline_ar', 'value' => 'ارتقِ بجمالك مع مجموعتنا الكونية من مستحضرات التجميل الفاخرة.'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com'],
            ['key' => 'show_newsletter', 'value' => '1'],
            ['key' => 'show_social_icons', 'value' => '1'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => 'footer']
            );
        }

        $this->command->info('Footer content seeded successfully!');
    }
}
