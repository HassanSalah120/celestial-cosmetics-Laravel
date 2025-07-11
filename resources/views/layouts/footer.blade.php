<!-- Footer -->
<footer class="bg-primary shadow-md border-t border-primary-dark/20">
    <!-- Main Footer -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="flex flex-col">
                <div class="mb-6 flex items-center">
                    @if(Settings::get('site_logo'))
                        <img src="{{ asset('storage/' . Settings::get('site_logo')) }}" alt="{{ Settings::get('site_name') }}" class="h-10 {{ is_rtl() ? 'ml-2' : 'mr-2' }}">
                    @else
                        <div class="w-10 h-10 flex items-center justify-center rounded-full bg-white {{ is_rtl() ? 'ml-2' : 'mr-2' }}">
                            <svg class="w-8 h-8 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-2xl font-display text-accent">
                        @if(is_rtl())
                            سيليستيال كوزمتكس
                        @else
                            {{ Settings::get('site_name') }}
                        @endif
                    </span>
                </div>
                <p class="text-white/90 mb-6">
                    {{ is_rtl() && !empty($footerSettings['footer_tagline_ar']->value) ? $footerSettings['footer_tagline_ar']->value : (isset($footerSettings['footer_tagline']) ? $footerSettings['footer_tagline']->value : (is_rtl() ? 'ارتقِ بجمالك مع مجموعتنا الكونية من مستحضرات التجميل الفاخرة.' : 'Elevate your beauty with our cosmic collection of premium cosmetics.')) }}
                </p>
                <div class="flex space-x-4 {{ is_rtl() ? 'space-x-reverse' : '' }} mt-auto">
                    @if(Settings::get('facebook_url'))
                    <a href="{{ Settings::get('facebook_url') }}" target="_blank" rel="noopener" class="text-white/90 hover:text-accent p-2 rounded-full transition-all duration-200 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    @endif
                    @if(Settings::get('instagram_url'))
                    <a href="{{ Settings::get('instagram_url') }}" target="_blank" rel="noopener" class="text-white/90 hover:text-accent p-2 rounded-full transition-all duration-200 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    @endif
                    @if(Settings::get('twitter_url'))
                    <a href="{{ Settings::get('twitter_url') }}" target="_blank" rel="noopener" class="text-white/90 hover:text-accent p-2 rounded-full transition-all duration-200 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path>
                        </svg>
                    </a>
                    @endif
                    @if(Settings::get('pinterest_url'))
                    <a href="{{ Settings::get('pinterest_url') }}" target="_blank" rel="noopener" class="text-white/90 hover:text-accent p-2 rounded-full transition-all duration-200 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Footer Sections -->
            @if(isset($footerSections) && count($footerSections) > 0)
                @foreach($footerSections as $section)
                    <div>
                        <h3 class="text-xl font-display mb-5 text-accent">{{ $section->localized_title }}</h3>
                        @if($section->links->count() > 0)
                            <ul class="space-y-3">
                                @foreach($section->links as $link)
                                    <li>
                                        <a href="{{ $link->url }}" class="text-white/90 hover:text-accent transition-colors duration-200 flex items-center">
                                            <svg class="h-4 w-4 {{ is_rtl() ? 'ml-2' : 'mr-2' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $link->localized_title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        
                        @if($section->type === 'newsletter')
                            <p class="text-white/90 mb-5">{{ isset($footerSettings['footer_newsletter_desc']) ? $footerSettings['footer_newsletter_desc']->value : (is_rtl() ? 'اشترك للحصول على تحديثات حول المنتجات الجديدة والعروض الخاصة.' : 'Subscribe to receive updates on new products and special offers.') }}</p>
                            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="flex">
                                    <input type="email" name="email" placeholder="{{ isset($footerSettings['footer_newsletter_placeholder']) ? $footerSettings['footer_newsletter_placeholder']->value : (is_rtl() ? 'بريدك الإلكتروني' : 'Your email') }}" class="px-4 py-3 w-full rounded-l-md focus:outline-none focus:ring-2 focus:ring-accent" required>
                                    <button type="submit" class="bg-accent hover:opacity-90 text-white font-bold px-4 py-3 rounded-r-md transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        @endif
                        
                        @if($section->type === 'contact')
                            <div class="space-y-2 text-white/90">
                                @if(Settings::get('contact_address'))
                                <p class="flex items-center text-sm">
                                    <svg class="h-5 w-5 {{ is_rtl() ? 'ml-3' : 'mr-3' }} text-accent" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ Settings::get('contact_address') }}
                                </p>
                                @endif
                                @if(Settings::get('contact_phone'))
                                <p class="flex items-center text-sm">
                                    <svg class="h-5 w-5 {{ is_rtl() ? 'ml-3' : 'mr-3' }} text-accent" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    {{ Settings::get('contact_phone') }}
                                </p>
                                @endif
                                @if(Settings::get('contact_email'))
                                <p class="flex items-center text-sm">
                                    <svg class="h-5 w-5 {{ is_rtl() ? 'ml-3' : 'mr-3' }} text-accent" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    {{ Settings::get('contact_email') }}
                                </p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <!-- Fallback Footer Content -->
                <!-- Shop Links -->
                <div>
                    <h3 class="text-xl font-display mb-5 text-accent">{{ Settings::get('footer_quick_links_title', is_rtl() ? 'روابط سريعة' : 'Quick Links') }}</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('home') }}" class="text-white/90 hover:text-accent transition-colors duration-200 flex items-center">
                                <svg class="h-4 w-4 {{ is_rtl() ? 'ml-2' : 'mr-2' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ is_rtl() ? 'الرئيسية' : 'Home' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('products.index') }}" class="text-white/90 hover:text-accent transition-colors duration-200 flex items-center">
                                <svg class="h-4 w-4 {{ is_rtl() ? 'ml-2' : 'mr-2' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ is_rtl() ? 'المنتجات' : 'Products' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}" class="text-white/90 hover:text-accent transition-colors duration-200 flex items-center">
                                <svg class="h-4 w-4 {{ is_rtl() ? 'ml-2' : 'mr-2' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ is_rtl() ? 'من نحن' : 'About Us' }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="text-white/90 hover:text-accent transition-colors duration-200 flex items-center">
                                <svg class="h-4 w-4 {{ is_rtl() ? 'ml-2' : 'mr-2' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                {{ is_rtl() ? 'اتصل بنا' : 'Contact' }}
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="py-6 border-t border-primary-dark/20 shadow-inner">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <p class="text-white/70 text-center md:text-left">
                    {{ isset($footerSettings['footer_copyright']) ? $footerSettings['footer_copyright']->value : ('© ' . date('Y') . ' ' . Settings::get('site_name') . '. ' . (is_rtl() ? 'جميع الحقوق محفوظة.' : 'All rights reserved.')) }}
                </p>
                <div class="flex flex-wrap justify-center md:justify-end mt-4 md:mt-0 space-x-4 {{ is_rtl() ? 'space-x-reverse' : '' }}">
                    <a href="{{ route('terms') }}" class="text-sm text-white/70 hover:text-accent transition-colors duration-200">{{ isset($footerSettings['footer_terms_text']) ? $footerSettings['footer_terms_text']->value : (is_rtl() ? 'الشروط والأحكام' : 'Terms & Conditions') }}</a>
                    <a href="{{ route('privacy') }}" class="text-sm text-white/70 hover:text-accent transition-colors duration-200">{{ isset($footerSettings['footer_privacy_text']) ? $footerSettings['footer_privacy_text']->value : (is_rtl() ? 'سياسة الخصوصية' : 'Privacy Policy') }}</a>
                    <a href="{{ route('shipping') }}" class="text-sm text-white/70 hover:text-accent transition-colors duration-200">{{ isset($footerSettings['footer_shipping_text']) ? $footerSettings['footer_shipping_text']->value : (is_rtl() ? 'سياسة الشحن' : 'Shipping Policy') }}</a>
                    <a href="{{ route('refunds') }}" class="text-sm text-white/70 hover:text-accent transition-colors duration-200">{{ isset($footerSettings['footer_refunds_text']) ? $footerSettings['footer_refunds_text']->value : (is_rtl() ? 'سياسة الاسترداد' : 'Refunds') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer> 