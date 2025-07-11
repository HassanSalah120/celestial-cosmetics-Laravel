@extends('layouts.admin')

@section('title', 'About Page - Simple Editor')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">About Page - Simple Editor</h1>
        <p class="mt-1 text-sm text-gray-600">This is a simplified editor for the about page.</p>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-5">
        @include('admin.partials.alerts')
        
        <form action="{{ route('admin.about.simple.update') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Main Content
                    </h3>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <input type="hidden" name="about_page_id" value="{{ $aboutPage->id ?? 1 }}">
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- English Content -->
                        <div class="sm:col-span-6">
                            <h4 class="text-md font-medium mb-4 text-gray-700">English Content</h4>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Page Title
                            </label>
                            <div class="mt-1">
                                <input type="text" name="title" id="title"
                                       value="{{ old('title', $aboutPage->title ?? 'About Us') }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="subtitle" class="block text-sm font-medium text-gray-700">
                                Page Subtitle
                            </label>
                            <div class="mt-1">
                                <input type="text" name="subtitle" id="subtitle"
                                       value="{{ old('subtitle', $aboutPage->subtitle ?? 'Learn about our journey, values, and the team behind Celestial Cosmetics.') }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="our_story" class="block text-sm font-medium text-gray-700">
                                Our Story Content
                            </label>
                            <div class="mt-1">
                                <textarea name="our_story" id="our_story" rows="6"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old('our_story', $aboutPage->our_story ?? '') }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Write the full story of your company. This supports Markdown formatting.</p>
                        </div>
                        
                        <!-- Arabic Content -->
                        <div class="sm:col-span-6 border-t pt-6 mt-6">
                            <h4 class="text-md font-medium mb-4 text-gray-700">Arabic Content</h4>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="title_ar" class="block text-sm font-medium text-gray-700">
                                Page Title (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="title_ar" id="title_ar" dir="rtl"
                                       value="{{ old('title_ar', $aboutPage->title_ar ?? '') }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="subtitle_ar" class="block text-sm font-medium text-gray-700">
                                Page Subtitle (Arabic)
                            </label>
                            <div class="mt-1">
                                <input type="text" name="subtitle_ar" id="subtitle_ar" dir="rtl"
                                       value="{{ old('subtitle_ar', $aboutPage->subtitle_ar ?? '') }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="our_story_ar" class="block text-sm font-medium text-gray-700">
                                Our Story Content (Arabic)
                            </label>
                            <div class="mt-1">
                                <textarea name="our_story_ar" id="our_story_ar" rows="6" dir="rtl"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old('our_story_ar', $aboutPage->our_story_ar ?? '') }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Arabic version of your company story.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Section Visibility
                    </h3>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
                        <div>
                            <div class="flex items-center">
                                <input id="show_hero" name="show_hero" type="checkbox" 
                                    {{ ($sectionVisibility->show_hero ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="show_hero" class="ml-2 block text-sm text-gray-900">
                                    Hero Section
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center">
                                <input id="show_story" name="show_story" type="checkbox" 
                                    {{ ($sectionVisibility->show_story ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="show_story" class="ml-2 block text-sm text-gray-900">
                                    Our Story
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center">
                                <input id="show_values" name="show_values" type="checkbox" 
                                    {{ ($sectionVisibility->show_values ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="show_values" class="ml-2 block text-sm text-gray-900">
                                    Corporate Values
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center">
                                <input id="show_team" name="show_team" type="checkbox" 
                                    {{ ($sectionVisibility->show_team ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="show_team" class="ml-2 block text-sm text-gray-900">
                                    Team Members
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center">
                                <input id="show_certifications" name="show_certifications" type="checkbox" 
                                    {{ ($sectionVisibility->show_certifications ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="show_certifications" class="ml-2 block text-sm text-gray-900">
                                    Certifications
                                </label>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex items-center">
                                <input id="show_cta" name="show_cta" type="checkbox" 
                                    {{ ($sectionVisibility->show_cta ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                <label for="show_cta" class="ml-2 block text-sm text-gray-900">
                                    Call to Action
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-5 sm:p-6 rounded-md">
                <div class="flex justify-between">
                    <a href="{{ route('admin.about.edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Back to Full Editor
                    </a>
                    
                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 