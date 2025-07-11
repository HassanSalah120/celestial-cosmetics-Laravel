<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AboutPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert About Page content
        DB::table('our_story_content')->insert([
            'title' => 'About Us',
            'title_ar' => 'معلومات عنا',
            'subtitle' => 'Learn about our journey, values, and the team behind Celestial Cosmetics.',
            'subtitle_ar' => 'تعرف على رحلتنا وقيمنا والفريق وراء سيليستيال كوزمتيكس.',
            'description' => "At Celestial Cosmetics, we believe beauty should be as boundless as the cosmos. Founded in 2018 by Dr. Stella Nova, a former astrophysicist with a passion for skincare, our journey began with a simple vision: to create beauty products inspired by the wonders of the universe.\n\nWhat started as experiments in a small lab has grown into a beloved brand known for innovative formulations that combine science with sustainable practices. Each product in our collection draws inspiration from celestial bodies, infusing your beauty routine with a touch of cosmic magic.\n\nWe're committed to ethical sourcing, cruelty-free testing, and eco-friendly packaging. Our ingredients are carefully selected for both effectiveness and environmental impact, ensuring that you can look good while doing good for the planet.\n\nToday, we're proud to offer a constellation of products that celebrate diversity, sustainability, and the magic that happens when science meets beauty. Join us on our cosmic journey—because your beauty is written in the stars.",
            'description_ar' => "في سيليستيال كوزمتيكس، نؤمن بأن الجمال يجب أن يكون بلا حدود مثل الكون. تأسست في عام 2018 من قبل د. ستيلا نوفا، عالمة فيزياء فلكية سابقة لديها شغف بالعناية بالبشرة، بدأت رحلتنا برؤية بسيطة: إنشاء منتجات تجميل مستوحاة من عجائب الكون.\n\nما بدأ كتجارب في مختبر صغير نما ليصبح علامة تجارية محبوبة معروفة بتركيباتها المبتكرة التي تجمع بين العلم والممارسات المستدامة. يستمد كل منتج في مجموعتنا الإلهام من الأجرام السماوية، مما يضفي لمسة من السحر الكوني على روتين الجمال الخاص بك.\n\nنحن ملتزمون بالمصادر الأخلاقية والاختبار الخالي من القسوة والتغليف الصديق للبيئة. يتم اختيار مكوناتنا بعناية من حيث الفعالية والتأثير البيئي، مما يضمن أنك تبدو جيدًا أثناء القيام بالخير للكوكب.\n\nاليوم، نحن فخورون بتقديم كوكبة من المنتجات التي تحتفل بالتنوع والاستدامة والسحر الذي يحدث عندما يلتقي العلم بالجمال. انضم إلينا في رحلتنا الكونية - لأن جمالك مكتوب في النجوم.",
            'image' => '/images/our-story.jpg',
            'button_text' => 'Read Our Full Story',
            'button_text_ar' => 'اقرأ قصتنا الكاملة',
            'button_url' => '/about',
            'feature1_icon' => 'fa-leaf',
            'feature1_title' => 'Natural Ingredients',
            'feature1_title_ar' => 'مكونات طبيعية',
            'feature1_text' => 'We use only the finest natural ingredients',
            'feature1_text_ar' => 'نستخدم فقط أفضل المكونات الطبيعية',
            'feature2_icon' => 'fa-heart',
            'feature2_title' => 'Cruelty Free',
            'feature2_title_ar' => 'خالي من القسوة',
            'feature2_text' => 'We never test on animals',
            'feature2_text_ar' => 'نحن لا نختبر أبدًا على الحيوانات',
            'secondary_button_text' => 'Contact Us',
            'secondary_button_text_ar' => 'اتصل بنا',
            'secondary_button_url' => '/contact',
            'year_founded' => '2018',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert values into about_section_visibility with the updated structure
        DB::table('about_section_visibility')->insert([
            'show_hero' => true,
            'show_story' => true,
            'show_values' => true,
            'show_team' => true,
            'show_certifications' => true,
            'show_cta' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Insert corporate values
        DB::table('corporate_values')->insert([
            [
                'title' => 'Cosmic Innovation',
                'title_ar' => 'الابتكار الكوني',
                'description' => 'We push boundaries and explore new frontiers in beauty, constantly seeking innovative formulations inspired by the cosmos.',
                'description_ar' => 'نحن نتجاوز الحدود ونستكشف آفاقًا جديدة في الجمال، ونبحث باستمرار عن تركيبات مبتكرة مستوحاة من الكون.',
                'icon' => 'sparkles',
                'sort_order' => 1,
            ],
            [
                'title' => 'Stellar Quality',
                'title_ar' => 'الجودة النجمية',
                'description' => 'Like the precision of celestial movements, we maintain exacting standards in every product we create.',
                'description_ar' => 'مثل دقة الحركات السماوية، نحافظ على معايير دقيقة في كل منتج نصنعه.',
                'icon' => 'shield-check',
                'sort_order' => 2,
            ],
            [
                'title' => 'Universal Sustainability',
                'title_ar' => 'الاستدامة الكونية',
                'description' => 'We treat our planet with the same care as we do our skin, choosing sustainable practices and ingredients.',
                'description_ar' => 'نحن نعامل كوكبنا بنفس العناية التي نوليها لبشرتنا، ونختار الممارسات والمكونات المستدامة.',
                'icon' => 'leaf',
                'sort_order' => 3,
            ],
        ]);

        // Insert team members
        DB::table('team_members')->insert([
            [
                'name' => 'Dr. Stella Nova',
                'name_ar' => 'د. ستيلا نوفا',
                'position' => 'Founder & Chief Formulator',
                'position_ar' => 'المؤسس والمصمم الرئيسي',
                'bio' => 'Former astrophysicist with a passion for merging science and beauty. Stella brings precision and innovation to every formula.',
                'bio_ar' => 'عالمة فيزياء فلكية سابقة لديها شغف بدمج العلم والجمال. تجلب ستيلا الدقة والابتكار لكل تركيبة.',
                'image' => 'team/stella-nova.jpg',
                'sort_order' => 1,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Luna Celeste',
                'name_ar' => 'لونا سيليست',
                'position' => 'Creative Director',
                'position_ar' => 'المدير الإبداعي',
                'bio' => 'With 15 years in beauty and fashion, Luna infuses our brand with artistic vision and ensures our cosmic aesthetic is consistent across all touchpoints.',
                'bio_ar' => 'مع 15 عامًا في مجال الجمال والأزياء، تضفي لونا علامتنا التجارية برؤية فنية وتضمن أن جمالياتنا الكونية متسقة عبر جميع نقاط الاتصال.',
                'image' => 'team/luna-celeste.jpg',
                'sort_order' => 2,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Orion Martinez',
                'name_ar' => 'أوريون مارتينيز',
                'position' => 'Sustainability Officer',
                'position_ar' => 'مسؤول الاستدامة',
                'bio' => 'Environmental scientist dedicated to making our operations and products as eco-friendly as possible. Orion leads our zero-waste initiatives.',
                'bio_ar' => 'عالم بيئي مكرس لجعل عملياتنا ومنتجاتنا صديقة للبيئة قدر الإمكان. يقود أوريون مبادراتنا للحد من النفايات.',
                'image' => 'team/orion-martinez.jpg',
                'sort_order' => 3,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 