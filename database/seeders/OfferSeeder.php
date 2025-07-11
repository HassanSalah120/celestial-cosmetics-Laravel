<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample offers
        $offers = [
            [
                'title' => 'Summer Sale: 25% Off All Skincare',
                'title_ar' => 'تخفيضات الصيف: خصم 25٪ على جميع منتجات العناية بالبشرة',
                'subtitle' => 'Limited time offer for premium skincare products',
                'subtitle_ar' => 'عرض لفترة محدودة على منتجات العناية بالبشرة الفاخرة',
                'description' => 'Take advantage of our biggest sale of the season with 25% off our entire skincare collection. Stock up on your favorites or try something new!',
                'description_ar' => 'استفد من أكبر تخفيضاتنا لهذا الموسم مع خصم 25٪ على مجموعة العناية بالبشرة بأكملها. اشترِ المنتجات المفضلة لديك أو جرّب شيئًا جديدًا!',
                'image' => '/storage/images/offers/summer-sale.jpg',
                'tag' => 'SUMMER SALE',
                'tag_ar' => 'تخفيضات الصيف',
                'original_price' => 199.99,
                'discounted_price' => 149.99,
                'discount_text' => '25% OFF',
                'discount_text_ar' => 'خصم 25٪',
                'button_text' => 'Shop Now',
                'button_text_ar' => 'تسوق الآن',
                'button_url' => '/products?category=skincare',
                'promo_code' => 'SUMMER25',
                'is_active' => true,
                'starts_at' => Carbon::now()->subDays(5),
                'expires_at' => Carbon::now()->addDays(25),
                'sort_order' => 1,
            ],
            [
                'title' => 'New Customer Special: $15 Off First Order',
                'title_ar' => 'عرض خاص للعملاء الجدد: خصم 15 دولارًا على الطلب الأول',
                'subtitle' => 'Welcome to Celestial Cosmetics',
                'subtitle_ar' => 'مرحبًا بكم في سيليستيال كوزمتكس',
                'description' => 'New to Celestial Cosmetics? We\'d like to welcome you with a special discount on your first order. Use code WELCOME15 at checkout.',
                'description_ar' => 'هل أنت جديد في سيليستيال كوزمتكس؟ نود أن نرحب بك بخصم خاص على طلبك الأول. استخدم الرمز WELCOME15 عند الدفع.',
                'image' => '/storage/images/offers/new-customer.jpg',
                'tag' => 'NEW CUSTOMER',
                'tag_ar' => 'عميل جديد',
                'original_price' => null,
                'discounted_price' => null,
                'discount_text' => '$15 OFF',
                'discount_text_ar' => 'خصم 15 دولارًا',
                'button_text' => 'Start Shopping',
                'button_text_ar' => 'ابدأ التسوق',
                'button_url' => '/products',
                'promo_code' => 'WELCOME15',
                'is_active' => true,
                'starts_at' => Carbon::now()->subDays(30),
                'expires_at' => null, // No expiration
                'sort_order' => 2,
            ],
            [
                'title' => 'Buy One Get One 50% Off - Celestial Collection',
                'title_ar' => 'اشترِ واحدة واحصل على الثانية بخصم 50٪ - مجموعة سيليستيال',
                'subtitle' => 'Mix and match from our signature collection',
                'subtitle_ar' => 'اختر وطابق من مجموعتنا المميزة',
                'description' => 'Purchase any full-priced item from our Celestial Collection and get a second item at 50% off. The perfect opportunity to complete your set!',
                'description_ar' => 'اشترِ أي منتج كامل السعر من مجموعة سيليستيال واحصل على المنتج الثاني بخصم 50٪. فرصة مثالية لإكمال مجموعتك!',
                'image' => '/storage/images/offers/bogo-celestial.jpg',
                'tag' => 'BOGO',
                'tag_ar' => 'اشتر واحد واحصل على الثاني',
                'original_price' => null,
                'discounted_price' => null,
                'discount_text' => 'BUY 1 GET 1 50% OFF',
                'discount_text_ar' => 'اشتر 1 واحصل على 1 بخصم 50٪',
                'button_text' => 'Shop Collection',
                'button_text_ar' => 'تسوق المجموعة',
                'button_url' => '/products?collection=celestial',
                'promo_code' => 'BOGO50',
                'is_active' => true,
                'starts_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addDays(14),
                'sort_order' => 3,
            ],
            [
                'title' => 'Weekend Flash Sale - 30% Off',
                'title_ar' => 'تخفيضات نهاية الأسبوع - خصم 30٪',
                'subtitle' => 'Saturday & Sunday Only',
                'subtitle_ar' => 'السبت والأحد فقط',
                'description' => 'Don\'t miss our weekend flash sale with 30% off sitewide! This offer is only valid for 48 hours, so shop now while supplies last.',
                'description_ar' => 'لا تفوت تخفيضات نهاية الأسبوع مع خصم 30٪ على جميع المنتجات! هذا العرض صالح لمدة 48 ساعة فقط، لذا تسوق الآن بينما الكميات متوفرة.',
                'image' => '/storage/images/offers/weekend-flash.jpg',
                'tag' => 'FLASH SALE',
                'tag_ar' => 'تخفيضات سريعة',
                'original_price' => null,
                'discounted_price' => null,
                'discount_text' => '30% OFF',
                'discount_text_ar' => 'خصم 30٪',
                'button_text' => 'Shop Now',
                'button_text_ar' => 'تسوق الآن',
                'button_url' => '/products',
                'promo_code' => 'FLASH30',
                'is_active' => true,
                'starts_at' => Carbon::now()->next('Saturday'),
                'expires_at' => Carbon::now()->next('Sunday')->endOfDay(),
                'sort_order' => 4,
            ],
        ];

        // Insert offers
        foreach ($offers as $offerData) {
            Offer::create($offerData);
        }
    }
}
