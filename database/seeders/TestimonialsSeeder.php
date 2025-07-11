<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First check if we have users to associate with testimonials
        $user = User::where('role', 'customer')->first();
        if (!$user) {
            // Create a customer if none exists
            $user = User::create([
                'name' => 'Customer User',
                'email' => 'customer@celestialcosmetics.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);
        }
        
        $testimonials = [
            [
                'customer_name' => 'Sarah N.',
                'customer_name_ar' => 'سارة ن.',
                'email' => 'sarah@example.com',
                'avatar' => null,
                'customer_role' => 'Skincare Enthusiast',
                'customer_role_ar' => 'خبير العناية بالبشرة',
                'title' => 'Life-changing products',
                'title_ar' => 'منتجات غيرت حياتي',
                'message' => "I've tried so many skincare products, but Celestial Cosmetics' Moon Glow Serum has transformed my skin. The subtle shimmer gives me a radiant glow that lasts all day. It's truly out of this world!",
                'message_ar' => "لقد جربت الكثير من منتجات العناية بالبشرة، لكن مصل القمر المتوهج من سيليستيال كوزمتكس غير بشرتي تمامًا. يمنحني البريق الخفيف توهجًا مشعًا يدوم طوال اليوم. إنه حقًا من عالم آخر!",
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true,
                'user_id' => $user->id,
            ],
            [
                'customer_name' => 'Michael J.',
                'customer_name_ar' => 'مايكل ج.',
                'email' => 'michael@example.com',
                'avatar' => null,
                'customer_role' => 'Regular Customer',
                'customer_role_ar' => 'عميل منتظم',
                'title' => 'Quality you can feel',
                'title_ar' => 'جودة يمكنك الشعور بها',
                'message' => "As someone with sensitive skin, I'm always cautious about trying new products. The Cosmic Bath Elixir was gentle yet effective, leaving my skin feeling rejuvenated and nourished. Celestial Cosmetics clearly prioritizes quality ingredients.",
                'message_ar' => "كشخص يعاني من البشرة الحساسة، أنا دائمًا حذر من تجربة منتجات جديدة. كان إكسير الاستحمام الكوني لطيفًا ولكنه فعال، مما ترك بشرتي تشعر بالتجدد والتغذية. سيليستيال كوزمتكس بوضوح تعطي الأولوية للمكونات ذات الجودة العالية.",
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true,
                'user_id' => $user->id,
            ],
            [
                'customer_name' => 'Amina L.',
                'customer_name_ar' => 'أمينة ل.',
                'email' => 'amina@example.com',
                'avatar' => null,
                'customer_role' => 'Beauty Blogger',
                'customer_role_ar' => 'مدونة جمال',
                'title' => 'Stellar customer service',
                'title_ar' => 'خدمة عملاء متميزة',
                'message' => "Not only are the products amazing, but the customer service is stellar too! When I had questions about which products would work best for my skin type, the team was incredibly helpful and responsive. I'm a customer for life!",
                'message_ar' => "ليست المنتجات رائعة فحسب، بل إن خدمة العملاء متميزة أيضًا! عندما كانت لدي أسئلة حول أي المنتجات ستعمل بشكل أفضل لنوع بشرتي، كان الفريق مفيدًا جدًا وسريع الاستجابة. أنا عميل مدى الحياة!",
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => true,
                'user_id' => $user->id,
            ],
            [
                'customer_name' => 'David R.',
                'customer_name_ar' => 'داود ر.',
                'email' => 'david@example.com',
                'avatar' => null,
                'customer_role' => 'Gift Shopper',
                'customer_role_ar' => 'مشتري هدايا',
                'title' => 'Perfect gift',
                'title_ar' => 'هدية مثالية',
                'message' => "I purchased the Starlight Collection as a gift for my wife, and she absolutely loves it! The packaging is beautiful and the products are high-quality. Will definitely be shopping here again.",
                'message_ar' => "اشتريت مجموعة ضوء النجوم كهدية لزوجتي، وهي تحبها تمامًا! العبوة جميلة والمنتجات عالية الجودة. سأتسوق هنا مرة أخرى بالتأكيد.",
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false,
                'user_id' => $user->id,
            ],
            [
                'customer_name' => 'Elena T.',
                'customer_name_ar' => 'إيلينا ت.',
                'email' => 'elena@example.com',
                'avatar' => null,
                'customer_role' => 'Satisfied Customer',
                'customer_role_ar' => 'عميل راضٍ',
                'title' => 'Worth every penny',
                'title_ar' => 'تستحق كل قرش',
                'message' => "The Celestial Glow Serum is a bit pricey, but absolutely worth the investment. I've been using it for a month now, and the improvement in my skin texture and tone is remarkable. My friends keep asking what I'm doing differently!",
                'message_ar' => "مصل التوهج السماوي باهظ الثمن قليلاً، لكنه يستحق الاستثمار تمامًا. لقد استخدمته لمدة شهر الآن، والتحسن في ملمس بشرتي ولونها ملحوظ. أصدقائي يسألون باستمرار عما أفعله بشكل مختلف!",
                'rating' => 5,
                'is_approved' => true,
                'is_featured' => false,
                'user_id' => $user->id,
            ],
            [
                'customer_name' => 'James W.',
                'customer_name_ar' => 'جيمس و.',
                'email' => 'james@example.com',
                'avatar' => null,
                'customer_role' => 'Loyal Customer',
                'customer_role_ar' => 'عميل مخلص',
                'title' => 'Fast shipping',
                'title_ar' => 'شحن سريع',
                'message' => "I was impressed by how quickly my order arrived. Everything was beautifully packaged and exactly as described on the website. The Moon Dust Bath Bombs are amazing!",
                'message_ar' => "لقد انبهرت بمدى سرعة وصول طلبي. كان كل شيء معبأ بشكل جميل وتمامًا كما هو موصوف على الموقع. قنابل الاستحمام بغبار القمر مذهلة!",
                'rating' => 4,
                'is_approved' => true,
                'is_featured' => false,
                'user_id' => $user->id,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
