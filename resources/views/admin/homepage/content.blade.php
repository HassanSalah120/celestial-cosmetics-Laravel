@extends('layouts.admin')

@section('title', 'Homepage Content Settings')

@push('scripts')
<script>
    // Initialize section ordering on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing section order functionality');
        
        // Initialize the order on page load
        if (typeof updateSectionOrder === 'function') {
            updateSectionOrder();
        } else {
            console.error('updateSectionOrder function not found');
        }
        
        // Normal form submission
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                // Update order one more time before submitting
                if (typeof updateSectionOrder === 'function') {
                    updateSectionOrder();
                }
            });
        }
    });
</script>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Homepage Content</h1>
        <p class="mt-1 text-sm text-gray-600">Manage the content sections displayed on your homepage.</p>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-5">
        {{-- Removed duplicated alerts include --}}
        
        <!-- Inline scripts for immediate definition -->
        <script>
            function updateSectionOrder() {
                const sectionInputs = document.querySelectorAll('input[name^="section_order"]');
                const sectionsOrderInput = document.getElementById('sections-order-input');
                
                // Create an array to hold the sections in the new order
                const orderMap = new Map();
                
                // First map each section to its order value
                sectionInputs.forEach(input => {
                    const sectionKey = input.name.match(/section_order\[(.*?)\]/)[1];
                    const orderValue = parseInt(input.value);
                    orderMap.set(sectionKey, orderValue);
                });
                
                // Sort by order value and create the array
                const sortedSections = Array.from(orderMap.entries())
                    .sort((a, b) => a[1] - b[1])
                    .map(entry => entry[0]);
                
                // Update the hidden input with properly formatted JSON
                if (sortedSections.length > 0) {
                    sectionsOrderInput.value = JSON.stringify(sortedSections);
                    console.log('Section order updated:', sortedSections);
                    console.log('JSON value:', sectionsOrderInput.value);
                    
                    if (document.getElementById('order-status')) {
                        document.getElementById('order-status').textContent = 'Order updated but not saved. Click "Apply Order" to save.';
                    }
                } else {
                    console.error('No sections found to order');
                    if (document.getElementById('order-status')) {
                        document.getElementById('order-status').textContent = 'Error: No sections found to order';
                    }
                }
            }
            
            function applySectionOrder() {
                // Update the section order
                updateSectionOrder();
                
                // Get the current value
                const sectionsOrderInput = document.getElementById('sections-order-input');
                const currentOrder = sectionsOrderInput.value;
                
                // Create a form to submit just the section order
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('admin.homepage-content.update') }}";
                form.style.display = 'none';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = "{{ csrf_token() }}";
                form.appendChild(csrfToken);
                
                // Add method field
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);
                
                // Add the section order
                const orderInput = document.createElement('input');
                orderInput.type = 'hidden';
                orderInput.name = 'settings[homepage_sections_order]';
                orderInput.value = currentOrder;
                form.appendChild(orderInput);
                
                // Add hidden inputs to ensure required fields are submitted when using Apply Order button
                const hiddenInputs = [
                    'featured_products_count',
                    'new_arrivals_count',
                    'testimonials_count',
                    'featured_categories_count',
                    'new_product_days',
                    'hero_title',
                    'hero_description',
                    'hero_button_text',
                    'hero_secondary_button_text',
                    'hero_button_url',
                    'hero_secondary_button_url'
                ];
                
                hiddenInputs.forEach(name => {
                    const input = document.querySelector(`input[name="${name}"]`);
                    if (input) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = name;
                        hiddenInput.value = input.value;
                    form.appendChild(hiddenInput);
                    } else {
                        console.warn(`Input with name ${name} not found`);
                    }
                });
                
                // Add the form to the page and submit it
                document.body.appendChild(form);
                
                if (document.getElementById('order-status')) {
                    document.getElementById('order-status').textContent = 'Saving section order...';
                }
                
                if (document.getElementById('apply-order-btn')) {
                    document.getElementById('apply-order-btn').disabled = true;
                    document.getElementById('apply-order-btn').textContent = 'Saving...';
                }
                
                form.submit();
            }
        </script>
        
        <form action="{{ route('admin.homepage-content.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            
            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Validation errors:</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Section Order & Visibility
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Arrange the order of sections and control which sections appear on the homepage.
                    </p>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <!-- Section Order -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Homepage Sections Order
                            </label>
                            <p class="text-sm text-gray-500 mb-3">Enter a number for each section (1 being first, 2 being second, etc). Lower numbers appear first on the homepage.</p>
                            
                            <input type="hidden" name="settings[homepage_sections_order]" 
                                id="sections-order-input" 
                                value="{{ json_encode($sectionOrder) }}" 
                                class="form-control">
                            
                            <!-- Hidden inputs for Apply Order button functionality -->
                            <input type="hidden" name="hero_title" value="{{ $settings['homepage_hero_title']->value ?? 'Discover Celestial Beauty' }}">
                            <input type="hidden" name="hero_description" value="{{ $settings['homepage_hero_description']->value ?? 'Explore our range of premium cosmetics inspired by the cosmos.' }}">
                            <input type="hidden" name="hero_button_text" value="{{ $settings['homepage_hero_button_text']->value ?? 'Shop Now' }}">
                            <input type="hidden" name="hero_secondary_button_text" value="{{ $settings['homepage_hero_secondary_button_text']->value ?? 'Learn More' }}">
                            <input type="hidden" name="hero_button_url" value="{{ $settings['homepage_hero_button_url']->value ?? '/products' }}">
                            <input type="hidden" name="hero_secondary_button_url" value="{{ $settings['homepage_hero_secondary_button_url']->value ?? '/about' }}">
                            
                            <div class="space-y-3">
                                @foreach($sectionOrder as $index => $sectionKey)
                                    @php
                                        $settingKey = 'homepage_show_' . $sectionKey;
                                        $checked = false;
                                        
                                        // For existing sections with dedicated columns
                                        if ($sectionKey === 'our_story' && isset($homepageSettings->show_our_story)) {
                                            $checked = (bool)$homepageSettings->show_our_story;
                                        } elseif ($sectionKey === 'testimonials' && isset($homepageSettings->show_testimonials)) {
                                            $checked = (bool)$homepageSettings->show_testimonials;
                                        } 
                                        // For new sections added via migration
                                        elseif (isset($homepageSettings) && property_exists($homepageSettings, 'show_' . $sectionKey)) {
                                            $propName = 'show_' . $sectionKey;
                                            $checked = (bool)$homepageSettings->$propName;
                                        }
                                        // Fallback to settings array if needed
                                        elseif (isset($settings[$settingKey])) {
                                            $checked = filter_var($settings[$settingKey]->value, FILTER_VALIDATE_BOOLEAN);
                                        }
                                        // Default to true for new sections
                                        else {
                                            $checked = true;
                                        }
                                    @endphp
                                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-md flex items-center" 
                                        data-section="{{ $sectionKey }}">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-primary text-white mr-3">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </span>
                                        <span class="flex-1 font-medium">{{ $availableSections[$sectionKey] ?? ucfirst(str_replace('_', ' ', $sectionKey)) }}</span>
                                        
                                        <div class="w-20 mr-4">
                                            <input type="number" 
                                                   name="section_order[{{ $sectionKey }}]" 
                                                   value="{{ $index + 1 }}" 
                                                   min="1" 
                                                   max="{{ count($sectionOrder) }}" 
                                                   class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md"
                                                   onchange="updateSectionOrder()">
                                        </div>
                                        
                                            <div class="relative inline-block ml-2">
                                                <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="show_{{ $sectionKey }}" value="1" 
                                                           class="sr-only peer" 
                                                        {{ $checked ? 'checked' : '' }}>
                                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary peer-focus:ring-2 peer-focus:ring-primary-light/30 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                                    <span class="ml-3 text-sm font-medium text-gray-600">Show Section</span>
                                                </label>
                                            </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Section Order Apply Button -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Section Order Status</h4>
                                        <p class="text-xs text-gray-500" id="order-status">Ready to save section order</p>
                                    </div>
                                    <button type="button" id="apply-order-btn" onclick="applySectionOrder()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        Apply Order
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section Counts -->
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="featured-count" class="block text-sm font-medium text-gray-700">
                                    Featured Products Count
                                </label>
                                <div class="mt-1 flex items-center">
                                    <input type="number" name="featured_products_count" id="featured-count"
                                           value="{{ $settings['homepage_featured_products_count']->value ?? 6 }}"
                                           min="1" step="1"
                                            class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Number of featured products to display. Recommended: 4-8 for best layout.</p>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="new-arrivals-count" class="block text-sm font-medium text-gray-700">
                                    New Arrivals Count
                                </label>
                                <div class="mt-1 flex items-center">
                                    <input type="number" name="new_arrivals_count" id="new-arrivals-count"
                                           value="{{ $settings['homepage_new_arrivals_count']->value ?? 4 }}"
                                           min="1" step="1"
                                            class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Number of new arrivals to display. Recommended: 4-8 for optimal grid layout.</p>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="featured-categories-count" class="block text-sm font-medium text-gray-700">
                                    Featured Categories Count
                                </label>
                                <div class="mt-1 flex items-center">
                                    <input type="number" name="featured_categories_count" id="featured-categories-count"
                                           value="{{ $settings['homepage_featured_categories_count']->value ?? 3 }}"
                                           min="1" step="1"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Number of categories to display. Recommended: 3-6 for best visual balance.</p>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="new-product-days" class="block text-sm font-medium text-gray-700">
                                    New Product Days
                                </label>
                                <div class="mt-1 flex items-center">
                                    <input type="number" name="new_product_days" id="new-product-days"
                                           value="{{ $settings['homepage_new_product_days']->value ?? 30 }}"
                                            min="1" max="90" step="1"
                                            class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Number of days a product is considered "new" after creation</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="testimonials-count" class="block text-sm font-medium text-gray-700">
                                    Testimonials Count
                                </label>
                                <div class="mt-1 flex items-center">
                                    <input type="number" name="testimonials_count" id="testimonials-count"
                                           value="{{ $settings['homepage_testimonials_count']->value ?? 3 }}"
                                           min="1" step="1"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Number of testimonials to display on the homepage. Recommended: 3-6 for best visual balance.</p>
                            </div>
                        </div>
                        
                        <!-- Animations -->
                        <div>
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="animation-enabled" name="animation_enabled" 
                                           type="checkbox" value="1"
                                           {{ isset($homepageSettings) && isset($homepageSettings->animation_enabled) && (bool)$homepageSettings->animation_enabled ? 'checked' : '' }}
                                           class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="animation-enabled" class="font-medium text-gray-700">Enable Animations</label>
                                    <p class="text-gray-500">Add subtle animations to homepage elements for a more dynamic experience.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Section -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Hero Section
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        The main banner section displayed at the top of your homepage.
                    </p>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="hero-title" class="block text-sm font-medium text-gray-700">
                                Hero Title
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_title" id="hero-title"
                                       value="{{ $settings['homepage_hero_title']->value ?? 'Discover Celestial Beauty' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-title-ar" class="block text-sm font-medium text-gray-700">
                                Hero Title (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_title_ar" id="hero-title-ar"
                                       value="{{ $settings['homepage_hero_title_ar']->value ?? 'اكتشف جمال سيليستيال' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-headtag" class="block text-sm font-medium text-gray-700">
                                Hero Pretitle Tag
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_headtag" id="hero-headtag"
                                       value="{{ $settings['homepage_hero_headtag']->value ?? 'Experience the Cosmos' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">This is the small tag displayed above the main hero title</p>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-headtag-ar" class="block text-sm font-medium text-gray-700">
                                Hero Pretitle Tag (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_headtag_ar" id="hero-headtag-ar"
                                       value="{{ $settings['homepage_hero_headtag_ar']->value ?? 'استكشف الكون' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Arabic version of the pretitle tag</p>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="hero-description" class="block text-sm font-medium text-gray-700">
                                Hero Description
                            </label>
                            <div class="mt-1">
                                <textarea name="hero_description" id="hero-description" rows="3"
                                          class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_hero_description']->value ?? 'Explore our range of premium cosmetics inspired by the cosmos.' }}</textarea>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="hero-description-ar" class="block text-sm font-medium text-gray-700">
                                Hero Description (Arabic)
                            </label>
                            <div class="mt-1">
                                <textarea name="hero_description_ar" id="hero-description-ar" rows="3"
                                          dir="rtl"
                                          class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_hero_description_ar']->value ?? 'استكشف مجموعتنا من مستحضرات التجميل الفاخرة المستوحاة من الكون.' }}</textarea>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-button-text" class="block text-sm font-medium text-gray-700">
                                Primary Button Text
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_button_text" id="hero-button-text"
                                       value="{{ $settings['homepage_hero_button_text']->value ?? 'Shop Now' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-button-text-ar" class="block text-sm font-medium text-gray-700">
                                Primary Button Text (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_button_text_ar" id="hero-button-text-ar"
                                       value="{{ $settings['homepage_hero_button_text_ar']->value ?? 'تسوق الآن' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-secondary-button-text" class="block text-sm font-medium text-gray-700">
                                Secondary Button Text
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_secondary_button_text" id="hero-secondary-button-text"
                                       value="{{ $settings['homepage_hero_secondary_button_text']->value ?? 'Learn More' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-secondary-button-text-ar" class="block text-sm font-medium text-gray-700">
                                Secondary Button Text (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_secondary_button_text_ar" id="hero-secondary-button-text-ar"
                                       value="{{ $settings['homepage_hero_secondary_button_text_ar']->value ?? 'اعرف المزيد' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-button-url" class="block text-sm font-medium text-gray-700">
                                Primary Button URL
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_button_url" id="hero-button-url"
                                       value="{{ $settings['homepage_hero_button_url']->value ?? '/products' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-secondary-button-url" class="block text-sm font-medium text-gray-700">
                                Secondary Button URL
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_secondary_button_url" id="hero-secondary-button-url"
                                       value="{{ $settings['homepage_hero_secondary_button_url']->value ?? '/about' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-scroll-indicator-text" class="block text-sm font-medium text-gray-700">
                                Scroll Indicator Text
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_scroll_indicator_text" id="hero-scroll-indicator-text"
                                       value="{{ $settings['homepage_hero_scroll_indicator_text']->value ?? 'Scroll to explore' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Text displayed at the bottom of the hero section</p>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-scroll-indicator-text-ar" class="block text-sm font-medium text-gray-700">
                                Scroll Indicator Text (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_scroll_indicator_text_ar" id="hero-scroll-indicator-text-ar"
                                       value="{{ $settings['homepage_hero_scroll_indicator_text_ar']->value ?? 'مرر للاستكشاف' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Arabic version of the scroll indicator text</p>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-tags" class="block text-sm font-medium text-gray-700">
                                Hero Tags (comma separated)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_tags" id="hero-tags"
                                       value="{{ isset($homepageHero->hero_tags) ? implode(', ', json_decode($homepageHero->hero_tags)) : 'Cruelty-Free, 100% Natural' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">These are the trust indicators shown below the hero buttons</p>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="hero-tags-ar" class="block text-sm font-medium text-gray-700">
                                Hero Tags (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="hero_tags_ar" id="hero-tags-ar"
                                       value="{{ isset($homepageHero->hero_tags_ar) ? implode(', ', json_decode($homepageHero->hero_tags_ar)) : 'خالي من القسوة, 100٪ طبيعي' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Arabic version of the trust indicators</p>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Hero Image
                            </label>
                            
                            <div class="mt-2 flex items-center">
                                @php
                                    $heroImage = '';
                                    if(isset($settings['homepage_hero_image'])) {
                                        $heroImage = $settings['homepage_hero_image']->value;
                                    } elseif(isset($homepageHero) && isset($homepageHero->image)) {
                                        $heroImage = $homepageHero->image;
                                    }
                                @endphp
                                
                                @if(!empty($heroImage))
                                    <div class="mr-4 flex-shrink-0 w-32 h-32 bg-gray-100 rounded-md overflow-hidden">
                                        <img src="{{ str_starts_with($heroImage, '/storage/') ? $heroImage : asset($heroImage) }}" alt="Hero Image" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                
                                <input type="file" name="hero_image" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary-light/80">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Offers Section -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Offers Section
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Manage the special offers section displayed on your homepage.
                    </p>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="offers-title" class="block text-sm font-medium text-gray-700">
                                Offers Title
                            </label>
                            <div class="mt-1">
                                <input type="text" name="settings[homepage_offers_title]" id="offers-title"
                                       value="{{ $settings['homepage_offers_title']->value ?? 'Special Offers' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="offers-title-ar" class="block text-sm font-medium text-gray-700">
                                Offers Title (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="settings[homepage_offers_title_ar]" id="offers-title-ar"
                                       value="{{ $settings['homepage_offers_title_ar']->value ?? 'عروض خاصة' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="offers-description" class="block text-sm font-medium text-gray-700">
                                Offers Description
                            </label>
                            <div class="mt-1">
                                <textarea name="settings[homepage_offers_description]" id="offers-description" rows="3"
                                          class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_offers_description']->value ?? 'Take advantage of these limited-time special offers and exclusive deals.' }}</textarea>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="offers-description-ar" class="block text-sm font-medium text-gray-700">
                                Offers Description (Arabic)
                            </label>
                            <div class="mt-1">
                                <textarea name="settings[homepage_offers_description_ar]" id="offers-description-ar" rows="3"
                                          dir="rtl"
                                          class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_offers_description_ar']->value ?? 'استفد من هذه العروض الخاصة المحدودة والصفقات الحصرية.' }}</textarea>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="offers-enabled" name="settings[homepage_show_offers]" 
                                           type="checkbox" value="1"
                                           {{ isset($settings['homepage_show_offers']) && $settings['homepage_show_offers']->value == '1' ? 'checked' : '' }}
                                           class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="offers-enabled" class="font-medium text-gray-700">Always Show Offers Section</label>
                                    <p class="text-gray-500">If checked, the offers section will be shown even if there are no active offers. Otherwise, it only appears when there are active offers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <!-- Featured Products -->
                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-base font-medium text-gray-900 mb-4">Featured Products</h4>
                            
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="featured-title" class="block text-sm font-medium text-gray-700">
                                        Title
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" name="settings[homepage_featured_products_title]" id="featured-title"
                                               value="{{ $settings['homepage_featured_products_title']->value ?? 'Featured Products' }}"
                                               class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="featured-title-ar" class="block text-sm font-medium text-gray-700">
                                        Title (Arabic)
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" name="settings[homepage_featured_products_title_ar]" id="featured-title-ar"
                                               value="{{ $settings['homepage_featured_products_title_ar']->value ?? 'منتجات مميزة' }}"
                                               dir="rtl"
                                               class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="featured-description" class="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>
                                    <div class="mt-1">
                                        <textarea name="settings[homepage_featured_products_description]" id="featured-description" rows="2"
                                                  class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_featured_products_description']->value ?? 'Discover our carefully selected products that highlight the best of our collection.' }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="featured-description-ar" class="block text-sm font-medium text-gray-700">
                                        Description (Arabic)
                                    </label>
                                    <div class="mt-1">
                                        <textarea name="settings[homepage_featured_products_description_ar]" id="featured-description-ar" rows="2"
                                                  dir="rtl"
                                                  class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_featured_products_description_ar']->value ?? 'اكتشف منتجاتنا المختارة بعناية والتي تسلط الضوء على أفضل ما في مجموعتنا.' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- New Arrivals -->
                        <div class="border-b border-gray-200 pb-6">
                            <h4 class="text-base font-medium text-gray-900 mb-4">New Arrivals</h4>
                            
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="new-arrivals-title" class="block text-sm font-medium text-gray-700">
                                        Title
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" name="settings[homepage_new_arrivals_title]" id="new-arrivals-title"
                                               value="{{ $settings['homepage_new_arrivals_title']->value ?? 'New Arrivals' }}"
                                               class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="new-arrivals-title-ar" class="block text-sm font-medium text-gray-700">
                                        Title (Arabic)
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" name="settings[homepage_new_arrivals_title_ar]" id="new-arrivals-title-ar"
                                               value="{{ $settings['homepage_new_arrivals_title_ar']->value ?? 'وصل حديثاً' }}"
                                               dir="rtl"
                                               class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="new-arrivals-tag" class="block text-sm font-medium text-gray-700">
                                        Tag Line
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" name="settings[homepage_new_arrivals_tag]" id="new-arrivals-tag"
                                               value="{{ $settings['homepage_new_arrivals_tag']->value ?? 'Just Arrived' }}"
                                               class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="new-arrivals-tag-ar" class="block text-sm font-medium text-gray-700">
                                        Tag Line (Arabic)
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" name="settings[homepage_new_arrivals_tag_ar]" id="new-arrivals-tag-ar"
                                               value="{{ $settings['homepage_new_arrivals_tag_ar']->value ?? 'وصل للتو' }}"
                                               dir="rtl"
                                               class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="new-arrivals-description" class="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>
                                    <div class="mt-1">
                                        <textarea name="settings[homepage_new_arrivals_description]" id="new-arrivals-description" rows="2"
                                                  class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_new_arrivals_description']->value ?? 'Check out our latest products added to our collection, bringing you the newest trends and innovations.' }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="new-arrivals-description-ar" class="block text-sm font-medium text-gray-700">
                                        Description (Arabic)
                                    </label>
                                    <div class="mt-1">
                                        <textarea name="settings[homepage_new_arrivals_description_ar]" id="new-arrivals-description-ar" rows="2"
                                                  dir="rtl"
                                                  class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['homepage_new_arrivals_description_ar']->value ?? 'تحقق من أحدث منتجاتنا المضافة إلى مجموعتنا، والتي تجلب لك أحدث الاتجاهات والابتكارات.' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Our Story Section -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mt-8">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Our Story Section
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Manage the "Our Story" section content displayed on your homepage.
                    </p>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Title fields -->
                        <div class="sm:col-span-3">
                            <label for="our-story-title" class="block text-sm font-medium text-gray-700">
                                Section Title
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_title" id="our-story-title"
                                       value="{{ $ourStoryContent->title ?? 'Beauty Inspired by the Cosmos' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-title-ar" class="block text-sm font-medium text-gray-700">
                                Section Title (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_title_ar" id="our-story-title-ar"
                                       value="{{ $ourStoryContent->title_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <!-- Subtitle fields -->
                        <div class="sm:col-span-3">
                            <label for="our-story-subtitle" class="block text-sm font-medium text-gray-700">
                                Section Tag
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_subtitle" id="our-story-subtitle"
                                       value="{{ $ourStoryContent->subtitle ?? 'About Us' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Small tag displayed above the title</p>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-subtitle-ar" class="block text-sm font-medium text-gray-700">
                                Section Tag (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_subtitle_ar" id="our-story-subtitle-ar"
                                       value="{{ $ourStoryContent->subtitle_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <!-- Description fields -->
                        <div class="sm:col-span-6">
                            <label for="our-story-description" class="block text-sm font-medium text-gray-700">
                                Main Description
                            </label>
                            <div class="mt-1">
                                <textarea name="our_story_description" id="our-story-description" rows="4"
                                          class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $ourStoryContent->description ?? 'Celestial Cosmetics began as a dream to create beauty products that capture the wonder of the cosmos. Founded on the belief that beauty should be as unique as the stars themselves, we combine innovative science with celestial inspiration to create products that help you shine.' }}</textarea>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="our-story-description-ar" class="block text-sm font-medium text-gray-700">
                                Main Description (Arabic)
                            </label>
                            <div class="mt-1">
                                <textarea name="our_story_description_ar" id="our-story-description-ar" rows="4"
                                          dir="rtl"
                                          class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $ourStoryContent->description_ar ?? '' }}</textarea>
                            </div>
                        </div>
                        
                        <!-- Feature 1 -->
                        <div class="sm:col-span-6">
                            <h4 class="text-base font-medium text-gray-900">Feature 1</h4>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="our-story-feature1-icon" class="block text-sm font-medium text-gray-700">
                                Icon
                            </label>
                            <div class="mt-1">
                                <select name="our_story_feature1_icon" id="our-story-feature1-icon"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="check-circle" {{ ($ourStoryContent->feature1_icon ?? '') == 'check-circle' ? 'selected' : '' }}>Check Circle</option>
                                    <option value="star" {{ ($ourStoryContent->feature1_icon ?? '') == 'star' ? 'selected' : '' }}>Star</option>
                                    <option value="heart" {{ ($ourStoryContent->feature1_icon ?? '') == 'heart' ? 'selected' : '' }}>Heart</option>
                                    <option value="badge-check" {{ ($ourStoryContent->feature1_icon ?? '') == 'badge-check' ? 'selected' : '' }}>Badge Check</option>
                                    <option value="sparkles" {{ ($ourStoryContent->feature1_icon ?? '') == 'sparkles' ? 'selected' : '' }}>Sparkles</option>
                                    <option value="beaker" {{ ($ourStoryContent->feature1_icon ?? '') == 'beaker' ? 'selected' : '' }}>Beaker (Science)</option>
                                    <option value="leaf" {{ ($ourStoryContent->feature1_icon ?? '') == 'leaf' ? 'selected' : '' }}>Leaf (Organic)</option>
                                    <option value="globe" {{ ($ourStoryContent->feature1_icon ?? '') == 'globe' ? 'selected' : '' }}>Globe (Earth)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="our-story-feature1-title" class="block text-sm font-medium text-gray-700">
                                Title
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature1_title" id="our-story-feature1-title"
                                       value="{{ $ourStoryContent->feature1_title ?? 'Cruelty-Free' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="our-story-feature1-title-ar" class="block text-sm font-medium text-gray-700">
                                Title (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature1_title_ar" id="our-story-feature1-title-ar"
                                       value="{{ $ourStoryContent->feature1_title_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-feature1-text" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature1_text" id="our-story-feature1-text"
                                       value="{{ $ourStoryContent->feature1_text ?? 'All our products are ethically made and never tested on animals' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-feature1-text-ar" class="block text-sm font-medium text-gray-700">
                                Description (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature1_text_ar" id="our-story-feature1-text-ar"
                                       value="{{ $ourStoryContent->feature1_text_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <!-- Feature 2 -->
                        <div class="sm:col-span-6">
                            <h4 class="text-base font-medium text-gray-900">Feature 2</h4>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="our-story-feature2-icon" class="block text-sm font-medium text-gray-700">
                                Icon
                            </label>
                            <div class="mt-1">
                                <select name="our_story_feature2_icon" id="our-story-feature2-icon"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="check-circle" {{ ($ourStoryContent->feature2_icon ?? '') == 'check-circle' ? 'selected' : '' }}>Check Circle</option>
                                    <option value="star" {{ ($ourStoryContent->feature2_icon ?? '') == 'star' ? 'selected' : '' }}>Star</option>
                                    <option value="heart" {{ ($ourStoryContent->feature2_icon ?? '') == 'heart' ? 'selected' : '' }}>Heart</option>
                                    <option value="badge-check" {{ ($ourStoryContent->feature2_icon ?? '') == 'badge-check' ? 'selected' : '' }}>Badge Check</option>
                                    <option value="sparkles" {{ ($ourStoryContent->feature2_icon ?? '') == 'sparkles' ? 'selected' : '' }}>Sparkles</option>
                                    <option value="beaker" {{ ($ourStoryContent->feature2_icon ?? '') == 'beaker' ? 'selected' : '' }}>Beaker (Science)</option>
                                    <option value="leaf" {{ ($ourStoryContent->feature2_icon ?? '') == 'leaf' ? 'selected' : '' }}>Leaf (Organic)</option>
                                    <option value="globe" {{ ($ourStoryContent->feature2_icon ?? '') == 'globe' ? 'selected' : '' }}>Globe (Earth)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="our-story-feature2-title" class="block text-sm font-medium text-gray-700">
                                Title
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature2_title" id="our-story-feature2-title"
                                       value="{{ $ourStoryContent->feature2_title ?? 'Innovative Formulas' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-2">
                            <label for="our-story-feature2-title-ar" class="block text-sm font-medium text-gray-700">
                                Title (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature2_title_ar" id="our-story-feature2-title-ar"
                                       value="{{ $ourStoryContent->feature2_title_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-feature2-text" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature2_text" id="our-story-feature2-text"
                                       value="{{ $ourStoryContent->feature2_text ?? 'Advanced ingredients inspired by celestial elements' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-feature2-text-ar" class="block text-sm font-medium text-gray-700">
                                Description (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_feature2_text_ar" id="our-story-feature2-text-ar"
                                       value="{{ $ourStoryContent->feature2_text_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <!-- Primary Button -->
                        <div class="sm:col-span-3">
                            <label for="our-story-button-text" class="block text-sm font-medium text-gray-700">
                                Primary Button Text
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_button_text" id="our-story-button-text"
                                       value="{{ $ourStoryContent->button_text ?? 'Learn more about our journey' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-button-text-ar" class="block text-sm font-medium text-gray-700">
                                Primary Button Text (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_button_text_ar" id="our-story-button-text-ar"
                                       value="{{ $ourStoryContent->button_text_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-button-url" class="block text-sm font-medium text-gray-700">
                                Primary Button URL
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_button_url" id="our-story-button-url"
                                       value="{{ $ourStoryContent->button_url ?? '/about' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <!-- Secondary Button -->
                        <div class="sm:col-span-3">
                            <label for="our-story-secondary-button-text" class="block text-sm font-medium text-gray-700">
                                Secondary Button Text
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_secondary_button_text" id="our-story-secondary-button-text"
                                       value="{{ $ourStoryContent->secondary_button_text ?? 'Explore Products' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-secondary-button-text-ar" class="block text-sm font-medium text-gray-700">
                                Secondary Button Text (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_secondary_button_text_ar" id="our-story-secondary-button-text-ar"
                                       value="{{ $ourStoryContent->secondary_button_text_ar ?? '' }}"
                                       dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-secondary-button-url" class="block text-sm font-medium text-gray-700">
                                Secondary Button URL
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_secondary_button_url" id="our-story-secondary-button-url"
                                       value="{{ $ourStoryContent->secondary_button_url ?? '/products' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Brand Image
                            </label>
                            
                            <div class="mt-2 flex items-center">
                                @if(!empty($ourStoryContent->image))
                                    <div class="mr-4 flex-shrink-0 w-32 h-32 bg-gray-100 rounded-md overflow-hidden">
                                        <img src="{{ str_starts_with($ourStoryContent->image, '/storage/') ? $ourStoryContent->image : asset($ourStoryContent->image) }}" alt="Our Story Image" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                
                                <input type="file" name="our_story_image" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary-light/80">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Recommended size: 400x400px</p>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="our-story-year" class="block text-sm font-medium text-gray-700">
                                Year Founded
                            </label>
                            <div class="mt-1">
                                <input type="text" name="our_story_year" id="our-story-year"
                                       value="{{ $ourStoryContent->year_founded ?? '2023' }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Displayed in "Est. YYYY" format</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="window.history.back()" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Cancel
                </button>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 