<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'type' => 'terms',
                'title' => 'Terms and Conditions',
                'title_ar' => 'الشروط والأحكام',
                'content' => '<h2>1. Introduction</h2><p>Welcome to Celestial Cosmetics. By accessing our website, you agree to these terms and conditions.</p>',
                'content_ar' => '<h2>١. مقدمة</h2><p>مرحباً بكم في سيليستيال كوزمتكس. باستخدامك لموقعنا الإلكتروني، فإنك توافق على هذه الشروط والأحكام.</p>',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'type' => 'privacy',
                'title' => 'Privacy Policy',
                'title_ar' => 'سياسة الخصوصية',
                'content' => '<h2>1. Data Collection</h2><p>We collect information to provide better services to our users.</p>',
                'content_ar' => '<h2>١. جمع البيانات</h2><p>نقوم بجمع المعلومات لتقديم خدمات أفضل لمستخدمينا.</p>',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'type' => 'shipping',
                'title' => 'Shipping Policy',
                'title_ar' => 'سياسة الشحن',
                'content' => '<h2>1. Delivery Times</h2><p>We aim to deliver all orders within 3-5 business days.</p>',
                'content_ar' => '<h2>١. مواعيد التسليم</h2><p>نهدف إلى تسليم جميع الطلبات خلال ٣-٥ أيام عمل.</p>',
                'last_updated' => now(),
                'is_active' => true
            ],
            [
                'type' => 'refunds',
                'title' => 'Refund Policy',
                'title_ar' => 'سياسة الاسترجاع',
                'content' => '<h2>1. Return Period</h2><p>You may return unused items within 14 days of delivery.</p>',
                'content_ar' => '<h2>١. فترة الإرجاع</h2><p>يمكنك إرجاع المنتجات غير المستخدمة خلال ١٤ يوماً من التسليم.</p>',
                'last_updated' => now(),
                'is_active' => true
            ]
        ];

        foreach ($pages as $page) {
            LegalPage::updateOrCreate(
                ['type' => $page['type']], // Find by type
                $page // Update or create with all data
            );
        }
    }
} 