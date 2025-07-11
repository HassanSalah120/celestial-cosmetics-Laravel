@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
use App\Helpers\SettingsHelper as Settings;
@endphp

@section('meta_tags')
    <x-seo :title="$aboutPage->meta_title ?? ($aboutPage->seo_title ?? (is_rtl() && $aboutPage->title_ar ? $aboutPage->title_ar : $aboutPage->title)) . ' | ' . config('app.name')"
           :description="$aboutPage->meta_description ?? ($aboutPage->seo_description ?? (is_rtl() && $aboutPage->subtitle_ar ? $aboutPage->subtitle_ar : $aboutPage->subtitle))"
           :keywords="$aboutPage->meta_keywords ?? ($aboutPage->seo_keywords ?? '')"
           :ogImage="isset($aboutPage) && isset($aboutPage->og_image) ? asset('storage/' . $aboutPage->og_image) : null"
           type="website" />
@endsection

@section('content')
<div class="bg-background min-h-screen">
    <!-- Hero Section -->
    @if(!isset($sectionVisibility) || $sectionVisibility->show_hero)
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
                <h1 class="text-4xl sm:text-5xl font-display font-bold text-accent mb-4 drop-shadow-md" data-aos="fade-down" data-aos-delay="100">{{ is_rtl() && $aboutPage->title_ar ? $aboutPage->title_ar : $aboutPage->title }}</h1>
                <p class="text-white text-opacity-90 text-lg sm:text-xl max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    {{ is_rtl() && $aboutPage->subtitle_ar ? $aboutPage->subtitle_ar : $aboutPage->subtitle }}
                </p>
                
                <div class="w-16 sm:w-20 md:w-24 h-1 bg-accent mx-auto mt-6 sm:mt-8 rounded-full" data-aos="zoom-in" data-aos-delay="300"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Our Story Section -->
    @if(!isset($sectionVisibility) || $sectionVisibility->show_story)
    <div class="bg-white py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2">
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl font-display text-primary mb-4">{{ TranslationHelper::get('messages.our_story', is_rtl() ? 'قصتنا' : 'Our Story') }}</h2>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e(is_rtl() && !empty($aboutPage->our_story_ar) ? $aboutPage->our_story_ar : $aboutPage->our_story)) !!}
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        @if(isset($aboutPage) && isset($aboutPage->story_image))
                            <img src="{{ asset('storage/' . $aboutPage->story_image) }}" alt="{{ TranslationHelper::get('messages.our_story', is_rtl() ? 'قصتنا' : 'Our Story') }}" class="w-full h-auto">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Our Values Section -->
    @if(!isset($sectionVisibility) || $sectionVisibility->show_values)
    <div class="bg-background py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-display text-primary mb-4">{{ TranslationHelper::get('messages.our_values', is_rtl() ? 'قيمنا' : 'Our Values') }}</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">{{ TranslationHelper::get('messages.values_description', is_rtl() ? 'المبادئ الأساسية التي توجه كل ما نقوم به.' : 'The core principles that guide everything we do.') }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @if(isset($ourValues) && count($ourValues) > 0)
                    @foreach($ourValues as $value)
                        <div class="bg-white rounded-2xl shadow-md p-6 text-center">
                            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    @if($value['icon'] == 'sparkles')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    @elseif($value['icon'] == 'shield-check')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    @elseif($value['icon'] == 'leaf')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                    @endif
                                </svg>
                            </div>
                            <h3 class="text-xl font-display text-primary mb-2">{{ $value['title'] }}</h3>
                            <p class="text-gray-600">{{ $value['description'] }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Team Section -->
    @if(!isset($sectionVisibility) || $sectionVisibility->show_team)
    <div class="bg-white py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-display text-primary mb-4">{{ TranslationHelper::get('messages.meet_our_team', is_rtl() ? 'تعرف على فريقنا' : 'Meet Our Team') }}</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">{{ TranslationHelper::get('messages.team_description', is_rtl() ? 'الأفراد المتفانين الذين يحققون رؤيتنا الكونية.' : 'The dedicated individuals who bring our cosmic vision to life.') }}</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @if(isset($teamMembers) && count($teamMembers) > 0)
                    @foreach($teamMembers as $member)
                        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                            @if(isset($member['image']) && $member['image'])
                                <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="w-full h-64 object-cover">
                            @else
                                <div class="w-full h-64 bg-gray-200"></div>
                            @endif
                            <div class="p-6">
                                <h3 class="text-xl font-display text-primary mb-1">{{ $member['name'] }}</h3>
                                <p class="text-accent mb-3">{{ $member['title'] }}</p>
                                <p class="text-gray-600 text-sm">{{ $member['bio'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Certifications Section -->
    @if(!isset($sectionVisibility) || $sectionVisibility->show_certifications)
    <div class="bg-background py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-display text-primary mb-4">{{ TranslationHelper::get('messages.our_certifications', is_rtl() ? 'شهاداتنا' : 'Our Certifications') }}</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">{{ TranslationHelper::get('messages.certifications_description', is_rtl() ? 'نحن ملتزمون بالحفاظ على أعلى معايير الجودة والممارسات الأخلاقية.' : 'We are committed to maintaining the highest standards of quality and ethical practices.') }}</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Certification 1: Cruelty-Free -->
                <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            @if($aboutPage->certification_1_icon == 'sun')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            @elseif($aboutPage->certification_1_icon == 'shield-check')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            @elseif($aboutPage->certification_1_icon == 'leaf')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            @elseif($aboutPage->certification_1_icon == 'sparkles')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            @elseif($aboutPage->certification_1_icon == 'cube')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            @elseif($aboutPage->certification_1_icon == 'scale')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            @endif
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-primary mb-1">{{ is_rtl() && $aboutPage->certification_1_title_ar ? $aboutPage->certification_1_title_ar : $aboutPage->certification_1_title }}</h3>
                    <p class="text-sm text-gray-600">{{ is_rtl() && $aboutPage->certification_1_description_ar ? $aboutPage->certification_1_description_ar : $aboutPage->certification_1_description }}</p>
                </div>
                
                <!-- Certification 2: Organic -->
                <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            @if($aboutPage->certification_2_icon == 'sun')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            @elseif($aboutPage->certification_2_icon == 'shield-check')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            @elseif($aboutPage->certification_2_icon == 'leaf')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            @elseif($aboutPage->certification_2_icon == 'sparkles')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            @elseif($aboutPage->certification_2_icon == 'cube')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            @elseif($aboutPage->certification_2_icon == 'scale')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            @endif
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-primary mb-1">{{ is_rtl() && $aboutPage->certification_2_title_ar ? $aboutPage->certification_2_title_ar : $aboutPage->certification_2_title }}</h3>
                    <p class="text-sm text-gray-600">{{ is_rtl() && $aboutPage->certification_2_description_ar ? $aboutPage->certification_2_description_ar : $aboutPage->certification_2_description }}</p>
                </div>
                
                <!-- Certification 3: Sustainable Packaging -->
                <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            @if($aboutPage->certification_3_icon == 'sun')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            @elseif($aboutPage->certification_3_icon == 'shield-check')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            @elseif($aboutPage->certification_3_icon == 'leaf')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            @elseif($aboutPage->certification_3_icon == 'sparkles')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            @elseif($aboutPage->certification_3_icon == 'cube')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            @elseif($aboutPage->certification_3_icon == 'scale')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            @endif
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-primary mb-1">{{ is_rtl() && $aboutPage->certification_3_title_ar ? $aboutPage->certification_3_title_ar : $aboutPage->certification_3_title }}</h3>
                    <p class="text-sm text-gray-600">{{ is_rtl() && $aboutPage->certification_3_description_ar ? $aboutPage->certification_3_description_ar : $aboutPage->certification_3_description }}</p>
                </div>
                
                <!-- Certification 4: Vegan -->
                <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            @if($aboutPage->certification_4_icon == 'sun')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            @elseif($aboutPage->certification_4_icon == 'shield-check')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            @elseif($aboutPage->certification_4_icon == 'leaf')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            @elseif($aboutPage->certification_4_icon == 'sparkles')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            @elseif($aboutPage->certification_4_icon == 'cube')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            @elseif($aboutPage->certification_4_icon == 'scale')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            @endif
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-primary mb-1">{{ is_rtl() && $aboutPage->certification_4_title_ar ? $aboutPage->certification_4_title_ar : $aboutPage->certification_4_title }}</h3>
                    <p class="text-sm text-gray-600">{{ is_rtl() && $aboutPage->certification_4_description_ar ? $aboutPage->certification_4_description_ar : $aboutPage->certification_4_description }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

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