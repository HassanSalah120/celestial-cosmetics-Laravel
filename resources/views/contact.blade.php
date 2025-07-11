@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
use App\Helpers\SettingsHelper as Settings;
@endphp

@section('meta_tags')
    <x-seo :title="Settings::get('contact_meta_title') ?? (is_rtl() ? 'اتصل بنا' : 'Contact Us') . ' | ' . config('app.name')"
           :description="Settings::get('contact_meta_description') ?? (is_rtl() ? 'تواصل مع فريق Celestial Cosmetics للاستفسارات والدعم ومعلومات المنتج.' : 'Get in touch with the Celestial Cosmetics team for inquiries, support, and product information.')"
           :keywords="Settings::get('contact_meta_keywords') ?? (is_rtl() ? 'اتصل بنا، دعم العملاء، مستحضرات التجميل، استفسارات' : 'contact us, customer support, cosmetics, inquiries')"
           :ogImage="Settings::get('contact_og_image')"
           type="website" />
@endsection

@section('content')
<div class="bg-background min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary-dark to-primary pt-16 pb-24 relative overflow-hidden">
        <!-- Animated stars background with improved animation -->
        <div class="absolute inset-0 opacity-40 overflow-hidden">
            <div class="stars-container h-full w-full">
                <!-- Mobile-optimized stars (fewer on small screens) -->
                <span class="star-icon text-xl sm:text-2xl animate-twinkle hidden sm:block" style="top: 15%; left: 10%; animation-delay: 0.5s;">✦</span>
                <span class="star-icon text-2xl sm:text-3xl animate-twinkle" style="top: 25%; left: 20%; animation-delay: 1.2s;">✧</span>
                <span class="star-icon text-lg sm:text-xl animate-twinkle hidden sm:block" style="top: 10%; left: 30%; animation-delay: 2.3s;">✦</span>
                <span class="star-icon text-xl sm:text-2xl animate-twinkle" style="top: 30%; left: 40%; animation-delay: 0.8s;">✧</span>
                <span class="star-icon text-lg sm:text-xl animate-twinkle" style="top: 20%; left: 50%; animation-delay: 1.6s;">✦</span>
                <span class="star-icon text-2xl sm:text-3xl animate-twinkle hidden md:block" style="top: 15%; left: 60%; animation-delay: 2.5s;">✧</span>
                <span class="star-icon text-xl sm:text-2xl animate-twinkle hidden sm:block" style="top: 25%; left: 70%; animation-delay: 0.7s;">✦</span>
                <span class="star-icon text-lg sm:text-xl animate-twinkle hidden md:block" style="top: 30%; left: 80%; animation-delay: 1.9s;">✧</span>
                <span class="star-icon text-2xl sm:text-3xl animate-twinkle hidden lg:block" style="top: 20%; left: 90%; animation-delay: 2.1s;">✦</span>
                <span class="moon-icon text-3xl sm:text-4xl animate-orbit" style="top: 70%; left: 15%; animation-delay: 1.1s;">☾</span>
                <span class="cosmic-icon text-2xl sm:text-3xl animate-spin-slow" style="top: 75%; left: 85%; animation-delay: 0.3s;">✯</span>
            </div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl font-display font-bold text-accent mb-4 drop-shadow-md" data-aos="fade-down" data-aos-delay="100">{{ is_rtl() ? 'اتصل بنا' : 'Contact Us' }}</h1>
                <p class="text-white text-opacity-90 text-lg sm:text-xl max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    {{ is_rtl() ? 'نحن نحب أن نسمع منك! سواء كان لديك سؤال حول منتجاتنا، أو تحتاج إلى مساعدة في طلب ما، أو ترغب في استكشاف فرص الشراكة، فريقنا هنا للمساعدة.' : 'We\'d love to hear from you! Whether you have a question about our products, need assistance with an order, or want to explore partnership opportunities, our team is here to help.' }}
                </p>
                
                <div class="w-16 sm:w-20 md:w-24 h-1 bg-accent mx-auto mt-6 sm:mt-8 rounded-full" data-aos="zoom-in" data-aos-delay="300"></div>
            </div>
        </div>
    </div>
    
    <!-- Contact Information -->
    <div class="bg-white py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Phone -->
                <div class="bg-background rounded-2xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ is_rtl() ? 'اتصل بنا' : 'Call Us' }}</h3>
                    <p class="text-gray-600 mb-3">{{ is_rtl() ? 'متاح من 9 صباحًا - 5 مساءً، من الاثنين إلى الجمعة' : 'Available 9am - 5pm, Mon-Fri' }}</p>
                    <a href="tel:{{ Settings::get('contact_phone', '+1 (555) 123-4567') }}" class="text-primary font-medium hover:text-primary-dark">
                        {{ Settings::get('contact_phone', '+1 (555) 123-4567') }}
                    </a>
            </div>
            
            <!-- Email -->
                <div class="bg-background rounded-2xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ is_rtl() ? 'راسلنا عبر البريد الإلكتروني' : 'Email Us' }}</h3>
                    <p class="text-gray-600 mb-3">{{ is_rtl() ? 'نرد خلال 24 ساعة' : 'We respond within 24 hours' }}</p>
                    <a href="mailto:{{ Settings::get('contact_email', 'info@celestial-cosmetics.com') }}" class="text-primary font-medium hover:text-primary-dark">
                        {{ Settings::get('contact_email', 'info@celestial-cosmetics.com') }}
                    </a>
            </div>
            
            <!-- Visit -->
                <div class="bg-background rounded-2xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ is_rtl() ? 'زورنا' : 'Visit Us' }}</h3>
                    <p class="text-gray-600 mb-3">{{ is_rtl() ? '123 طريق كوزميك، مدينة ستارداست' : '123 Cosmic Lane, Stardust City' }}</p>
                    <a href="#map" class="text-primary font-medium hover:text-primary-dark">{{ is_rtl() ? 'احصل على الاتجاهات' : 'Get Directions' }}</a>
            </div>
            
            <!-- Social -->
                <div class="bg-background rounded-2xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ is_rtl() ? 'تابعنا' : 'Follow Us' }}</h3>
                    <p class="text-gray-600 mb-3">{{ is_rtl() ? 'ابق على اطلاع بأحدث منتجاتنا' : 'Stay updated with our latest products' }}</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ Settings::get('social_instagram') }}" class="text-primary hover:text-primary-dark" target="_blank">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="{{ Settings::get('social_facebook') }}" class="text-primary hover:text-primary-dark" target="_blank">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                        <a href="{{ Settings::get('social_twitter') }}" class="text-primary hover:text-primary-dark" target="_blank">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                        </svg>
                    </a>
                    </div>
                </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Form Section -->
    <div class="bg-background py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Contact Form -->
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-2xl shadow-md p-8">
                        <h2 class="text-3xl font-display text-primary mb-4">{{ is_rtl() ? 'أرسل رسالة' : 'Send a Message' }}</h2>
                        <p class="text-gray-600 mb-6">{{ is_rtl() ? 'نحن نحب أن نسمع منك! املأ النموذج وسيعاود فريقنا الاتصال بك في أقرب وقت ممكن.' : 'We\'d love to hear from you! Fill out the form and our team will get back to you as soon as possible.' }}</p>
                        
                        <div class="lg:flex gap-10 mb-8">
                            <div class="lg:w-1/3 mb-6 lg:mb-0">
                                <h3 class="font-medium text-gray-900 mb-2">{{ is_rtl() ? 'لماذا تتصل بنا؟' : 'Why Contact Us?' }}</h3>
                                <ul class="space-y-3 text-sm">
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                        <span>{{ is_rtl() ? 'أسئلة المنتج أو التوصيات' : 'Product questions or recommendations' }}</span>
                            </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                        <span>{{ is_rtl() ? 'مساعدة الطلب والتتبع' : 'Order assistance and tracking' }}</span>
                            </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                        <span>{{ is_rtl() ? 'فرص الشراكة' : 'Partnership opportunities' }}</span>
                            </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                        <span>{{ is_rtl() ? 'دعم العملاء والتعليقات' : 'Customer support and feedback' }}</span>
                            </li>
                        </ul>
                    </div>
                        
                            <div class="lg:w-2/3">
                                <form id="contact-form" class="space-y-4">
                        <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">{{ is_rtl() ? 'الاسم' : 'Name' }}</label>
                                        <div class="mt-1">
                                            <input type="text" name="name" id="name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                                        </div>
                        </div>
                        
                        <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">{{ is_rtl() ? 'البريد الإلكتروني' : 'Email' }}</label>
                                        <div class="mt-1">
                                            <input type="email" name="email" id="email" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                                        </div>
                        </div>
                        
                        <div>
                                        <label for="subject" class="block text-sm font-medium text-gray-700">{{ is_rtl() ? 'الموضوع' : 'Subject' }}</label>
                                        <div class="mt-1">
                                            <input type="text" name="subject" id="subject" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
                                        </div>
                        </div>
                        
                        <div>
                                        <label for="message" class="block text-sm font-medium text-gray-700">{{ is_rtl() ? 'الرسالة' : 'Message' }}</label>
                                        <div class="mt-1">
                                            <textarea id="message" name="message" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required></textarea>
                                        </div>
                        </div>
                        
                        <div>
                                        <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white py-3 px-6 rounded-lg font-semibold transition-colors duration-300">
                                            {{ is_rtl() ? 'إرسال رسالة' : 'Send Message' }}
                            </button>
                        </div>
                    </form>
                            </div>
                </div>
            </div>
        </div>

                <!-- Map & Store Hours -->
                <div class="lg:w-1/3">
                    <!-- Map -->
                    <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ is_rtl() ? 'موقعنا' : 'Our Location' }}</h3>
                        <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center">
                            <!-- Map would go here -->
                            <p class="mt-2 text-gray-600">{{ is_rtl() ? 'ستظهر الخريطة التفاعلية هنا' : 'Interactive map would appear here' }}</p>
                        </div>
                    </div>
                    
                    <!-- Store Hours -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-2xl font-display text-primary">{{ is_rtl() ? 'ساعات المتجر' : 'Store Hours' }}</h3>
                            <div class="flex items-center">
                                <span class="inline-block w-3 h-3 rounded-full {{ $isOpen ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                                <span class="text-sm font-medium {{ $isOpen ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $isOpen 
                                        ? (is_rtl() ? 'مفتوح الآن' : 'Open Now') 
                                        : (is_rtl() ? 'مغلق الآن' : 'Closed Now') 
                                    }}
                                </span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 -mt-2 mb-3">
                            {{ is_rtl() ? 'جميع الأوقات بتوقيت القاهرة (مصر)' : 'All times in Egypt/Cairo timezone' }}
                        </div>
                        
                    <div class="space-y-3">
                            @foreach($storeHours as $storeHour)
                                <div class="flex justify-between {{ $storeHour->day === $currentDayName ? 'bg-primary/5 p-2 rounded-lg font-medium' : '' }}">
                                    <span class="{{ $storeHour->day === $currentDayName ? 'text-primary-dark' : 'text-gray-600' }}">
                                        {{ $storeHour->translated_day }}
                                        @if($storeHour->day === $currentDayName)
                                            <span class="text-xs ml-1 text-primary-dark">({{ is_rtl() ? 'اليوم' : 'Today' }})</span>
                                        @endif
                                    </span>
                                    <span class="{{ $storeHour->day === $currentDayName ? 'text-primary-dark' : '' }}">
                                        {{ $storeHour->translated_hours }}
                                    </span>
                                </div>
                            @endforeach
                    </div>
                    
                        <div class="mt-6 p-4 bg-primary/5 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">{{ is_rtl() ? 'ساعات العطلات' : 'Holiday Hours' }}</h4>
                            <p class="text-gray-600 mb-3">{{ is_rtl() ? 'قد تختلف الساعات خلال العطلات. يرجى التحقق من وسائل التواصل الاجتماعي الخاصة بنا للحصول على أحدث المعلومات.' : 'Hours may vary during holidays. Please check our social media for the most up-to-date information.' }}</p>
                        <a href="#" class="inline-flex items-center text-primary hover:text-primary-dark">
                                <span>{{ is_rtl() ? 'عرض جدول العطلات' : 'View Holiday Schedule' }}</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    const form = document.getElementById('contact-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would normally send the form data to your server
            // For demo purposes, we'll just show a success message
            
            // Get form data
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            // Log the data (for demo)
            console.log('Form submitted:', { name, email, subject, message });
            
            // Show success message
            form.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">${is_rtl() ? 'شكرًا لتواصلك معنا!' : 'Thank you for contacting us!'}</h3>
                    <p class="text-gray-600">${is_rtl() ? 'لقد تلقينا رسالتك وسنرد عليك في أقرب وقت ممكن.' : 'We have received your message and will get back to you as soon as possible.'}</p>
                </div>
            `;
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Enhanced cosmic animations */
    .stars-container {
        position: relative;
    }
    
    .star-icon, .moon-icon, .cosmic-icon {
        position: absolute;
        color: rgb(var(--color-accent) / 0.6);
        opacity: 0.7;
        animation-duration: 3s;
        animation-iteration-count: infinite;
    }
    
    @keyframes twinkle {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }
    
    .animate-twinkle {
        animation: twinkle 3s ease-in-out infinite;
    }
    .animate-twinkle-slow {
        animation: twinkle 5s ease-in-out infinite;
    }
    .animate-spin-slow {
        animation: spin 12s linear infinite;
    }
    .animate-float-slow {
        animation: float 8s ease-in-out infinite;
    }
    .animate-float-reverse {
        animation: float 6s ease-in-out infinite reverse;
    }
    .animate-orbit {
        animation: orbit 15s linear infinite;
    }
    .animate-pulse-slow {
        animation: pulse 7s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    .animate-bounce-slow {
        animation: bounce 2s infinite;
    }
    @keyframes orbit {
        0% { transform: rotate(0deg) translateX(20px) rotate(0deg); }
        100% { transform: rotate(360deg) translateX(20px) rotate(-360deg); }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>
@endpush
@endsection 