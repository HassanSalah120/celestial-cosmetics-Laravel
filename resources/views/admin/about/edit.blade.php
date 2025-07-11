@extends('layouts.admin')

@section('title', 'About Page Management')

@push('styles')
<style>
    .sortable-item {
        cursor: move;
    }
    .sortable-ghost {
        opacity: 0.5;
        background: #c8e6c9;
    }
    .required-label::after {
        content: " *";
        color: red;
    }
    
    .section-divider {
        border-top: 1px solid #e5e7eb;
        margin: 2rem 0;
        padding-top: 2rem;
    }
    
    .section-header {
        margin-bottom: 1.5rem;
    }
    
    .back-to-top {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 100;
        background-color: rgba(79, 70, 229, 0.9);
        color: white;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
    }
    
    .back-to-top:hover {
        background-color: rgba(67, 56, 202, 1);
    }
    
    .sticky-save {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.95);
        padding: 1rem;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        z-index: 99;
        display: flex;
        justify-content: flex-end;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }
    
    .sticky-save.visible {
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">About Page Management</h1>
        <p class="mt-1 text-sm text-gray-600">Configure the content displayed on your About Us page.</p>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 mt-5">
        @include('admin.partials.alerts')
        
        <!-- Simple Editor Link -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded relative mb-4">
            <p class="font-bold">Having trouble saving changes?</p>
            <p>Try our simplified editor instead:</p>
            <div class="mt-2">
                <a href="{{ route('admin.about.simple') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Open Simplified Editor
                </a>
                <p class="mt-1 text-xs">The simplified editor has fewer features but is more reliable for saving changes.</p>
            </div>
        </div>
        
        <!-- Emergency Form Submission -->
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative mb-4">
            <p class="font-bold">EMERGENCY SAVE OPTION</p>
            <p>If nothing else works, try this emergency save method:</p>
            <form action="{{ url('/emergency-about-update') }}" method="POST" class="mt-2">
                @csrf
                <input type="hidden" name="about_page_id" value="{{ $aboutPage->id ?? 1 }}">
                <input type="hidden" name="title" value="{{ old('title', $aboutPage->title ?? 'About Us') }}">
                <input type="hidden" name="subtitle" value="{{ old('subtitle', $aboutPage->subtitle ?? 'Learn about our journey, values, and the team behind Celestial Cosmetics.') }}">
                <input type="hidden" name="our_story" value="{{ old('our_story', $aboutPage->our_story ?? '') }}">
                <input type="hidden" name="title_ar" value="{{ old('title_ar', $aboutPage->title_ar ?? '') }}">
                <input type="hidden" name="subtitle_ar" value="{{ old('subtitle_ar', $aboutPage->subtitle_ar ?? '') }}">
                <input type="hidden" name="our_story_ar" value="{{ old('our_story_ar', $aboutPage->our_story_ar ?? '') }}">
                
                @if($sectionVisibility && $sectionVisibility->show_hero)
                    <input type="hidden" name="show_hero" value="on">
                @endif
                @if($sectionVisibility && $sectionVisibility->show_story)
                    <input type="hidden" name="show_story" value="on">
                @endif
                @if($sectionVisibility && $sectionVisibility->show_values)
                    <input type="hidden" name="show_values" value="on">
                @endif
                @if($sectionVisibility && $sectionVisibility->show_team)
                    <input type="hidden" name="show_team" value="on">
                @endif
                @if($sectionVisibility && $sectionVisibility->show_certifications)
                    <input type="hidden" name="show_certifications" value="on">
                @endif
                @if($sectionVisibility && $sectionVisibility->show_cta)
                    <input type="hidden" name="show_cta" value="on">
                @endif
                
                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    EMERGENCY SAVE (Minimal Data)
                </button>
                <p class="mt-1 text-xs">This will save only the basic page content and section visibility settings.</p>
            </form>
        </div>
        
        <!-- Direct Form Submission (No JavaScript) -->
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded relative mb-4">
            <p class="font-bold">Having trouble saving changes?</p>
            <p>Try using this direct form submission instead:</p>
            <form action="{{ url('/admin/about/direct-fix-update') }}" method="POST" enctype="multipart/form-data" class="mt-2">
                @csrf
                <input type="hidden" name="about_page_id" value="{{ $aboutPage->id ?? 1 }}">
                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    Save All Changes (Direct Method)
                </button>
                <p class="mt-1 text-xs">This will submit the form directly without using JavaScript.</p>
            </form>
        </div>
        
        <!-- Top Action Button -->
        <div class="flex justify-end mb-4">
            <button type="submit" form="direct-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Save All Changes (Direct Method)
            </button>
        </div>
        
        <form action="{{ url('/admin/about/direct-fix-update') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="aboutPageForm">
            @csrf
            
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
            
            <!-- Section Navigation Guide -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        About Page Sections
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        All sections are displayed below. Scroll down to edit each section.
                    </p>
                </div>
                
                <div class="px-4 py-3">
                    <div class="flex flex-wrap gap-2">
                        <a href="#main-content-section" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium">Main Content</a>
                        <a href="#values-section" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium">Corporate Values</a>
                        <a href="#team-section" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium">Team Members</a>
                        <a href="#certifications-section" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium">Certifications</a>
                        <a href="#visibility-section" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium">Section Visibility</a>
                    </div>
                </div>
            </div>
            
            <!-- Main About Page Content -->
            <div id="main-content-section" class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Main Page Content
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Edit the primary content sections of your About page.
                    </p>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <input type="hidden" name="about_page_id" value="{{ $aboutPage->id ?? 1 }}">
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- English Content -->
                        <div class="sm:col-span-6">
                            <h4 class="text-md font-medium mb-4 text-gray-700">English Content</h4>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="title" class="block text-sm font-medium text-gray-700 required-label">
                                Page Title
                            </label>
                            <div class="mt-1">
                                <input type="text" name="title" id="title"
                                       value="{{ old('title', $aboutPage->title ?? 'About Us') }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="subtitle" class="block text-sm font-medium text-gray-700 required-label">
                                Page Subtitle
                            </label>
                            <div class="mt-1">
                                <input type="text" name="subtitle" id="subtitle"
                                       value="{{ old('subtitle', $aboutPage->subtitle ?? 'Learn about our journey, values, and the team behind Celestial Cosmetics.') }}"
                                       class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="our_story" class="block text-sm font-medium text-gray-700 required-label">
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
            
            <!-- Corporate Values -->
            <div id="values-section" class="section-divider"></div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Corporate Values
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Manage the values displayed on your About page.
                        </p>
                    </div>
                    <button type="button" id="add-value-btn" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Add New Value
                    </button>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <!-- Existing Values -->
                    <div id="values-container" class="space-y-4">
                        @forelse($corporateValues as $index => $value)
                            <div class="bg-gray-50 p-4 rounded-md sortable-item" data-id="{{ $value->id }}">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <span class="grip-handle mr-2 text-gray-400 cursor-move">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.20l-.8 2H12a1 1 0 110 2H8.2l-.8 2H10a1 1 0 110 2H7.2l-.8 2H9a1 1 0 010 2H5a1 1 0 01-.77-.37l-4-5a1 1 0 010-1.27l4-5A1 1 0 015 2h2z" />
                                                <path d="M13.5 13a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 10a1 1 0 110-2h5a1 1 0 110 2h-5z" />
                                            </svg>
                                        </span>
                                        <span class="font-medium">Value #{{ $index + 1 }}</span>
                                    </div>
                                    <form action="{{ route('admin.about.values.delete', $value->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this value?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                                
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-2">
                                        <label for="values[{{ $value->id }}][title]" class="block text-sm font-medium text-gray-700 required-label">
                                            Title
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="values[{{ $value->id }}][title]" 
                                                value="{{ old("values.{$value->id}.title", $value->title) }}"
                                                class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                    
                                    <div class="sm:col-span-1">
                                        <label for="values[{{ $value->id }}][icon]" class="block text-sm font-medium text-gray-700">
                                            Icon
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="values[{{ $value->id }}][icon]" 
                                                value="{{ old("values.{$value->id}.icon", $value->icon) }}"
                                                class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Examples: star, leaf, globe, shield-check</p>
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="values[{{ $value->id }}][description]" class="block text-sm font-medium text-gray-700 required-label">
                                            Description
                                        </label>
                                        <div class="mt-1">
                                            <textarea name="values[{{ $value->id }}][description]" rows="3"
                                                class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old("values.{$value->id}.description", $value->description) }}</textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="values[{{ $value->id }}][title_ar]" class="block text-sm font-medium text-gray-700">
                                            Title (Arabic)
                                        </label>
                                        <div class="mt-1">
                                            <input type="text" name="values[{{ $value->id }}][title_ar]" dir="rtl"
                                                value="{{ old("values.{$value->id}.title_ar", $value->title_ar) }}"
                                                class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                    
                                    <div class="sm:col-span-3">
                                        <label for="values[{{ $value->id }}][description_ar]" class="block text-sm font-medium text-gray-700">
                                            Description (Arabic)
                                        </label>
                                        <div class="mt-1">
                                            <textarea name="values[{{ $value->id }}][description_ar]" rows="3" dir="rtl"
                                                class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ old("values.{$value->id}.description_ar", $value->description_ar) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 bg-gray-50 rounded-md">
                                <p class="text-gray-500">No corporate values defined yet. Add your first value using the button above.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- New Value Form (Hidden by Default) -->
                    <div id="new-value-form" class="bg-blue-50 p-4 rounded-md mt-4 hidden">
                        <h4 class="text-md font-medium text-blue-800 mb-3">Add New Value</h4>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="new_value[title]" class="block text-sm font-medium text-gray-700 required-label">
                                    Title
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_value[title]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <label for="new_value[icon]" class="block text-sm font-medium text-gray-700">
                                    Icon
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_value[icon]" value="star"
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Examples: star, leaf, globe, shield-check</p>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="new_value[description]" class="block text-sm font-medium text-gray-700 required-label">
                                    Description
                                </label>
                                <div class="mt-1">
                                    <textarea name="new_value[description]" rows="3"
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="new_value[title_ar]" class="block text-sm font-medium text-gray-700">
                                    Title (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_value[title_ar]" dir="rtl"
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="new_value[description_ar]" class="block text-sm font-medium text-gray-700">
                                    Description (Arabic)
                                </label>
                                <div class="mt-1">
                                    <textarea name="new_value[description_ar]" rows="3" dir="rtl"
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-right">
                            <button type="button" id="cancel-value-btn" class="mr-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Team Members -->
            <div id="team-section" class="section-divider"></div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Team Members
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Manage the team members displayed on your About page.
                        </p>
                    </div>
                    <button type="button" id="add-member-btn" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Add New Member
                    </button>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <!-- Existing Team Members -->
                    <div id="members-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($teamMembers as $index => $member)
                            <div class="bg-gray-50 p-4 rounded-md sortable-item" data-id="{{ $member->id }}">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <span class="grip-handle mr-2 text-gray-400 cursor-move">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.20l-.8 2H12a1 1 0 110 2H8.2l-.8 2H10a1 1 0 110 2H7.2l-.8 2H9a1 1 0 010 2H5a1 1 0 01-.77-.37l-4-5a1 1 0 010-1.27l4-5A1 1 0 015 2h2z" />
                                                <path d="M13.5 13a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 10a1 1 0 110-2h5a1 1 0 110 2h-5z" />
                                            </svg>
                                        </span>
                                        <span class="font-medium">{{ $member->name }}</span>
                                    </div>
                                    <form action="{{ route('admin.about.members.delete', $member->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this team member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                    </form>
                                </div>
                                
                                <div class="space-y-4">
                                    @if(!empty($member->image))
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $member->image) }}" 
                                                alt="{{ $member->name }}"
                                                class="h-24 w-24 object-cover rounded-full mx-auto">
                                        </div>
                                    @else
                                        <div class="mb-2">
                                            <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mx-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-6">
                                        <div class="sm:col-span-3">
                                            <label for="members[{{ $member->id }}][name]" class="block text-sm font-medium text-gray-700 required-label">
                                                Name (English)
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" name="members[{{ $member->id }}][name]" 
                                                    value="{{ $member->name }}"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" required>
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-3">
                                            <label for="members[{{ $member->id }}][name_ar]" class="block text-sm font-medium text-gray-700">
                                                Name (Arabic)
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" name="members[{{ $member->id }}][name_ar]" 
                                                    value="{{ $member->name_ar }}"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-3">
                                            <label for="members[{{ $member->id }}][title]" class="block text-sm font-medium text-gray-700 required-label">
                                                Job Title (English)
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" name="members[{{ $member->id }}][title]" 
                                                    value="{{ $member->title ?? $member->position }}"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" required>
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-3">
                                            <label for="members[{{ $member->id }}][title_ar]" class="block text-sm font-medium text-gray-700">
                                                Job Title (Arabic)
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" name="members[{{ $member->id }}][title_ar]" 
                                                    value="{{ $member->title_ar ?? $member->position_ar }}"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-6">
                                            <label for="members[{{ $member->id }}][bio]" class="block text-sm font-medium text-gray-700">
                                                Bio (English)
                                            </label>
                                            <div class="mt-1">
                                                <textarea name="members[{{ $member->id }}][bio]" rows="3"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $member->bio }}</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-6">
                                            <label for="members[{{ $member->id }}][bio_ar]" class="block text-sm font-medium text-gray-700">
                                                Bio (Arabic)
                                            </label>
                                            <div class="mt-1">
                                                <textarea name="members[{{ $member->id }}][bio_ar]" rows="3"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $member->bio_ar }}</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-3">
                                            <label for="member_image_{{ $member->id }}" class="block text-sm font-medium text-gray-700">
                                                Profile Image
                                            </label>
                                            <div class="mt-1">
                                                @if(!empty($member->image))
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $member->image) }}" alt="{{ $member->name }}" class="h-16 w-16 object-cover rounded-full border border-gray-200">
                                                    </div>
                                                @endif
                                                <input type="file" name="member_image_{{ $member->id }}" id="member_image_{{ $member->id }}"
                                                    accept="image/jpeg,image/jpg,image/png,image/gif"
                                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary-light">
                                                <p class="mt-1 text-xs text-gray-500">Leave empty to keep current image</p>
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">Visibility</label>
                                            <div class="mt-2">
                                                <div class="flex items-center">
                                                    <input id="members[{{ $member->id }}][is_visible]" name="members[{{ $member->id }}][is_visible]" type="checkbox" 
                                                        {{ $member->is_visible ? 'checked' : '' }}
                                                        class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                                    <label for="members[{{ $member->id }}][is_visible]" class="ml-2 block text-sm text-gray-900">
                                                        Visible on website
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-2">
                                            <label for="members[{{ $member->id }}][social_linkedin]" class="block text-sm font-medium text-gray-700">
                                                LinkedIn URL
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" name="members[{{ $member->id }}][social_linkedin]" 
                                                    value="{{ $member->social_linkedin }}"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-2">
                                            <label for="members[{{ $member->id }}][social_twitter]" class="block text-sm font-medium text-gray-700">
                                                X/Twitter URL
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" name="members[{{ $member->id }}][social_twitter]" 
                                                    value="{{ $member->social_twitter }}"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                        
                                        <div class="sm:col-span-2">
                                            <label for="members[{{ $member->id }}][social_instagram]" class="block text-sm font-medium text-gray-700">
                                                Instagram URL
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" name="members[{{ $member->id }}][social_instagram]" 
                                                    value="{{ $member->social_instagram }}"
                                                    class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 bg-gray-50 rounded-md col-span-full">
                                <p class="text-gray-500">No team members defined yet. Add your first team member using the button above.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- New Team Member Form (Hidden by Default) -->
                    <div id="new-member-form" class="bg-blue-50 p-4 rounded-md mt-4 hidden">
                        <h4 class="text-md font-medium text-blue-800 mb-3">Add New Team Member</h4>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="new_member[name]" class="block text-sm font-medium text-gray-700 required-label">
                                    Name (English)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_member[name]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="new_member[name_ar]" class="block text-sm font-medium text-gray-700">
                                    Name (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_member[name_ar]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="new_member[title]" class="block text-sm font-medium text-gray-700 required-label">
                                    Job Title (English)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_member[title]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="new_member[title_ar]" class="block text-sm font-medium text-gray-700">
                                    Job Title (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_member[title_ar]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label for="new_member[bio]" class="block text-sm font-medium text-gray-700">
                                    Bio (English)
                                </label>
                                <div class="mt-1">
                                    <textarea name="new_member[bio]" rows="3"
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label for="new_member[bio_ar]" class="block text-sm font-medium text-gray-700">
                                    Bio (Arabic)
                                </label>
                                <div class="mt-1">
                                    <textarea name="new_member[bio_ar]" rows="3"
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="new_member_image" class="block text-sm font-medium text-gray-700">
                                    Profile Image
                                </label>
                                <div class="mt-1">
                                    <input type="file" name="new_member_image" 
                                        accept="image/jpeg,image/jpg,image/png,image/gif"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary-light">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Visibility</label>
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <input id="new_member[is_visible]" name="new_member[is_visible]" type="checkbox" checked
                                            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                        <label for="new_member[is_visible]" class="ml-2 block text-sm text-gray-900">
                                            Visible on website
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="new_member[social_linkedin]" class="block text-sm font-medium text-gray-700">
                                    LinkedIn URL
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_member[social_linkedin]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="new_member[social_twitter]" class="block text-sm font-medium text-gray-700">
                                    X/Twitter URL
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_member[social_twitter]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label for="new_member[social_instagram]" class="block text-sm font-medium text-gray-700">
                                    Instagram URL
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="new_member[social_instagram]" 
                                        class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-right">
                            <button type="button" id="cancel-member-btn" class="mr-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Certifications -->
            <div id="certifications-section" class="section-divider"></div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Certifications
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Manage the certifications displayed on your About page.
                    </p>
                </div>
                
                <div class="px-4 py-5 sm:p-6">
                    <!-- Certification 1 -->
                    <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-medium text-gray-700">Certification 1</h4>
                            <form action="{{ route('admin.about.certifications.delete', 1) }}" method="POST" onsubmit="return confirm('Are you sure you want to clear this certification? This will remove all data for this certification.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Clear</button>
                            </form>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- English Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_1_title" class="block text-sm font-medium text-gray-700">
                                    Title
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_1_title" id="certification_1_title"
                                           value="{{ old('certification_1_title', $aboutPage->certification_1_title ?? 'Leaping Bunny') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <label for="certification_1_icon" class="block text-sm font-medium text-gray-700">
                                    Icon
                                </label>
                                <div class="mt-1">
                                    <select name="certification_1_icon" id="certification_1_icon"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select an icon</option>
                                        <option value="badge-check" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'badge-check' ? 'selected' : '' }}>Badge Check</option>
                                        <option value="check-circle" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'check-circle' ? 'selected' : '' }}>Check Circle</option>
                                        <option value="shield-check" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'shield-check' ? 'selected' : '' }}>Shield Check</option>
                                        <option value="star" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'star' ? 'selected' : '' }}>Star</option>
                                        <option value="sparkles" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'sparkles' ? 'selected' : '' }}>Sparkles</option>
                                        <option value="leaf" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'leaf' ? 'selected' : '' }}>Leaf</option>
                                        <option value="globe" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'globe' ? 'selected' : '' }}>Globe</option>
                                        <option value="heart" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'heart' ? 'selected' : '' }}>Heart</option>
                                        <option value="cube" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'cube' ? 'selected' : '' }}>Cube</option>
                                        <option value="beaker" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'beaker' ? 'selected' : '' }}>Beaker</option>
                                        <option value="scale" {{ old('certification_1_icon', $aboutPage->certification_1_icon ?? '') == 'scale' ? 'selected' : '' }}>Scale</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Select an icon for this certification</p>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="certification_1_description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_1_description" id="certification_1_description"
                                           value="{{ old('certification_1_description', $aboutPage->certification_1_description ?? 'Cruelty-Free Certified') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <!-- Arabic Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_1_title_ar" class="block text-sm font-medium text-gray-700">
                                    Title (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_1_title_ar" id="certification_1_title_ar" dir="rtl"
                                           value="{{ old('certification_1_title_ar', $aboutPage->certification_1_title_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3 sm:col-start-4">
                                <label for="certification_1_description_ar" class="block text-sm font-medium text-gray-700">
                                    Description (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_1_description_ar" id="certification_1_description_ar" dir="rtl"
                                           value="{{ old('certification_1_description_ar', $aboutPage->certification_1_description_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Certification 2 -->
                    <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-medium text-gray-700">Certification 2</h4>
                            <form action="{{ route('admin.about.certifications.delete', 2) }}" method="POST" onsubmit="return confirm('Are you sure you want to clear this certification? This will remove all data for this certification.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Clear</button>
                            </form>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- English Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_2_title" class="block text-sm font-medium text-gray-700">
                                    Title
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_2_title" id="certification_2_title"
                                           value="{{ old('certification_2_title', $aboutPage->certification_2_title ?? 'Ecocert') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <label for="certification_2_icon" class="block text-sm font-medium text-gray-700">
                                    Icon
                                </label>
                                <div class="mt-1">
                                    <select name="certification_2_icon" id="certification_2_icon"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select an icon</option>
                                        <option value="badge-check" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'badge-check' ? 'selected' : '' }}>Badge Check</option>
                                        <option value="check-circle" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'check-circle' ? 'selected' : '' }}>Check Circle</option>
                                        <option value="shield-check" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'shield-check' ? 'selected' : '' }}>Shield Check</option>
                                        <option value="star" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'star' ? 'selected' : '' }}>Star</option>
                                        <option value="sparkles" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'sparkles' ? 'selected' : '' }}>Sparkles</option>
                                        <option value="leaf" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'leaf' ? 'selected' : '' }}>Leaf</option>
                                        <option value="globe" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'globe' ? 'selected' : '' }}>Globe</option>
                                        <option value="heart" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'heart' ? 'selected' : '' }}>Heart</option>
                                        <option value="cube" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'cube' ? 'selected' : '' }}>Cube</option>
                                        <option value="beaker" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'beaker' ? 'selected' : '' }}>Beaker</option>
                                        <option value="scale" {{ old('certification_2_icon', $aboutPage->certification_2_icon ?? '') == 'scale' ? 'selected' : '' }}>Scale</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Select an icon for this certification</p>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="certification_2_description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_2_description" id="certification_2_description"
                                           value="{{ old('certification_2_description', $aboutPage->certification_2_description ?? 'Organic Certified') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <!-- Arabic Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_2_title_ar" class="block text-sm font-medium text-gray-700">
                                    Title (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_2_title_ar" id="certification_2_title_ar" dir="rtl"
                                           value="{{ old('certification_2_title_ar', $aboutPage->certification_2_title_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3 sm:col-start-4">
                                <label for="certification_2_description_ar" class="block text-sm font-medium text-gray-700">
                                    Description (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_2_description_ar" id="certification_2_description_ar" dir="rtl"
                                           value="{{ old('certification_2_description_ar', $aboutPage->certification_2_description_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Certification 3 -->
                    <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-medium text-gray-700">Certification 3</h4>
                            <form action="{{ route('admin.about.certifications.delete', 3) }}" method="POST" onsubmit="return confirm('Are you sure you want to clear this certification? This will remove all data for this certification.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Clear</button>
                            </form>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- English Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_3_title" class="block text-sm font-medium text-gray-700">
                                    Title
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_3_title" id="certification_3_title"
                                           value="{{ old('certification_3_title', $aboutPage->certification_3_title ?? 'FSC') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <label for="certification_3_icon" class="block text-sm font-medium text-gray-700">
                                    Icon
                                </label>
                                <div class="mt-1">
                                    <select name="certification_3_icon" id="certification_3_icon"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select an icon</option>
                                        <option value="badge-check" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'badge-check' ? 'selected' : '' }}>Badge Check</option>
                                        <option value="check-circle" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'check-circle' ? 'selected' : '' }}>Check Circle</option>
                                        <option value="shield-check" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'shield-check' ? 'selected' : '' }}>Shield Check</option>
                                        <option value="star" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'star' ? 'selected' : '' }}>Star</option>
                                        <option value="sparkles" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'sparkles' ? 'selected' : '' }}>Sparkles</option>
                                        <option value="leaf" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'leaf' ? 'selected' : '' }}>Leaf</option>
                                        <option value="globe" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'globe' ? 'selected' : '' }}>Globe</option>
                                        <option value="heart" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'heart' ? 'selected' : '' }}>Heart</option>
                                        <option value="cube" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'cube' ? 'selected' : '' }}>Cube</option>
                                        <option value="beaker" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'beaker' ? 'selected' : '' }}>Beaker</option>
                                        <option value="scale" {{ old('certification_3_icon', $aboutPage->certification_3_icon ?? '') == 'scale' ? 'selected' : '' }}>Scale</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Select an icon for this certification</p>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="certification_3_description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_3_description" id="certification_3_description"
                                           value="{{ old('certification_3_description', $aboutPage->certification_3_description ?? 'Sustainable Packaging') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <!-- Arabic Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_3_title_ar" class="block text-sm font-medium text-gray-700">
                                    Title (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_3_title_ar" id="certification_3_title_ar" dir="rtl"
                                           value="{{ old('certification_3_title_ar', $aboutPage->certification_3_title_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3 sm:col-start-4">
                                <label for="certification_3_description_ar" class="block text-sm font-medium text-gray-700">
                                    Description (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_3_description_ar" id="certification_3_description_ar" dir="rtl"
                                           value="{{ old('certification_3_description_ar', $aboutPage->certification_3_description_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Certification 4 -->
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-md font-medium text-gray-700">Certification 4</h4>
                            <form action="{{ route('admin.about.certifications.delete', 4) }}" method="POST" onsubmit="return confirm('Are you sure you want to clear this certification? This will remove all data for this certification.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Clear</button>
                            </form>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- English Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_4_title" class="block text-sm font-medium text-gray-700">
                                    Title
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_4_title" id="certification_4_title"
                                           value="{{ old('certification_4_title', $aboutPage->certification_4_title ?? 'Vegan') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-1">
                                <label for="certification_4_icon" class="block text-sm font-medium text-gray-700">
                                    Icon
                                </label>
                                <div class="mt-1">
                                    <select name="certification_4_icon" id="certification_4_icon"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="">Select an icon</option>
                                        <option value="badge-check" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'badge-check' ? 'selected' : '' }}>Badge Check</option>
                                        <option value="check-circle" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'check-circle' ? 'selected' : '' }}>Check Circle</option>
                                        <option value="shield-check" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'shield-check' ? 'selected' : '' }}>Shield Check</option>
                                        <option value="star" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'star' ? 'selected' : '' }}>Star</option>
                                        <option value="sparkles" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'sparkles' ? 'selected' : '' }}>Sparkles</option>
                                        <option value="leaf" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'leaf' ? 'selected' : '' }}>Leaf</option>
                                        <option value="globe" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'globe' ? 'selected' : '' }}>Globe</option>
                                        <option value="heart" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'heart' ? 'selected' : '' }}>Heart</option>
                                        <option value="cube" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'cube' ? 'selected' : '' }}>Cube</option>
                                        <option value="beaker" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'beaker' ? 'selected' : '' }}>Beaker</option>
                                        <option value="scale" {{ old('certification_4_icon', $aboutPage->certification_4_icon ?? '') == 'scale' ? 'selected' : '' }}>Scale</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Select an icon for this certification</p>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="certification_4_description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_4_description" id="certification_4_description"
                                           value="{{ old('certification_4_description', $aboutPage->certification_4_description ?? 'Vegan Friendly Products') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <!-- Arabic Content -->
                            <div class="sm:col-span-2">
                                <label for="certification_4_title_ar" class="block text-sm font-medium text-gray-700">
                                    Title (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_4_title_ar" id="certification_4_title_ar" dir="rtl"
                                           value="{{ old('certification_4_title_ar', $aboutPage->certification_4_title_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3 sm:col-start-4">
                                <label for="certification_4_description_ar" class="block text-sm font-medium text-gray-700">
                                    Description (Arabic)
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="certification_4_description_ar" id="certification_4_description_ar" dir="rtl"
                                           value="{{ old('certification_4_description_ar', $aboutPage->certification_4_description_ar ?? '') }}"
                                           class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section Visibility -->
            <div id="visibility-section" class="section-divider"></div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Section Visibility
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Control which sections are visible on the About page.
                    </p>
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
                            <p class="mt-1 text-xs text-gray-500">The animated hero banner at the top</p>
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
                            <p class="mt-1 text-xs text-gray-500">The main story content</p>
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
                            <p class="mt-1 text-xs text-gray-500">Company values and principles</p>
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
                            <p class="mt-1 text-xs text-gray-500">Team member profiles and bios</p>
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
                            <p class="mt-1 text-xs text-gray-500">Company certifications and badges</p>
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
                            <p class="mt-1 text-xs text-gray-500">Bottom call-to-action banner</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="bg-gray-50 px-4 py-5 sm:p-6 rounded-md">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.about.edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Save Changes
                    </button>
                    <button type="submit" form="direct-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Save (Direct Method)
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Hidden direct form that copies all form data -->
        <form id="direct-form" action="{{ url('/admin/about/direct-fix-update') }}" method="POST" enctype="multipart/form-data" class="hidden">
            @csrf
            <input type="hidden" name="about_page_id" value="{{ $aboutPage->id ?? 1 }}">
            <!-- Include all basic fields from the main form -->
            <input type="hidden" name="title" value="{{ old('title', $aboutPage->title ?? 'About Us') }}">
            <input type="hidden" name="subtitle" value="{{ old('subtitle', $aboutPage->subtitle ?? 'Learn about our journey, values, and the team behind Celestial Cosmetics.') }}">
            <input type="hidden" name="our_story" value="{{ old('our_story', $aboutPage->our_story ?? '') }}">
            <input type="hidden" name="title_ar" value="{{ old('title_ar', $aboutPage->title_ar ?? '') }}">
            <input type="hidden" name="subtitle_ar" value="{{ old('subtitle_ar', $aboutPage->subtitle_ar ?? '') }}">
            <input type="hidden" name="our_story_ar" value="{{ old('our_story_ar', $aboutPage->our_story_ar ?? '') }}">
            
            <!-- Section visibility -->
            @if($sectionVisibility && $sectionVisibility->show_hero)
                <input type="hidden" name="show_hero" value="on">
            @endif
            @if($sectionVisibility && $sectionVisibility->show_story)
                <input type="hidden" name="show_story" value="on">
            @endif
            @if($sectionVisibility && $sectionVisibility->show_values)
                <input type="hidden" name="show_values" value="on">
            @endif
            @if($sectionVisibility && $sectionVisibility->show_team)
                <input type="hidden" name="show_team" value="on">
            @endif
            @if($sectionVisibility && $sectionVisibility->show_certifications)
                <input type="hidden" name="show_certifications" value="on">
            @endif
            @if($sectionVisibility && $sectionVisibility->show_cta)
                <input type="hidden" name="show_cta" value="on">
            @endif
            
            <!-- The rest of the form data will be copied by JavaScript -->
        </form>
    </div>
</div>

<!-- Back to Top Button -->
<div class="back-to-top" id="backToTop">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</div>

<!-- Sticky Save Button -->
<div class="sticky-save" id="stickySave">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <span class="font-medium">Unsaved changes</span>
            </div>
            <div class="space-x-3">
                <button type="submit" form="aboutPageForm" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Save Changes
                </button>
                <button type="submit" form="direct-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Save (Direct Method)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Back to Top Button
        const backToTopButton = document.getElementById('backToTop');
        
        // Show button when page is scrolled down
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
            
            // Show sticky save button when scrolled past a certain point
            if (window.pageYOffset > 500) {
                document.getElementById('stickySave').classList.add('visible');
            } else {
                document.getElementById('stickySave').classList.remove('visible');
            }
        });
        
        // Form change detection
        const formInputs = document.querySelectorAll('#aboutPageForm input, #aboutPageForm textarea, #aboutPageForm select');
        formInputs.forEach(input => {
            input.addEventListener('change', () => {
                document.getElementById('stickySave').classList.add('visible');
            });
        });
        
        // Smooth scroll to top when button is clicked
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Direct form submission handling
        const directForm = document.getElementById('direct-form');
        const mainForm = document.getElementById('aboutPageForm');
        
        // Copy all form inputs from main form to direct form before submission
        directForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear any previous inputs
            const existingInputs = directForm.querySelectorAll('input:not([name="_token"]):not([name="about_page_id"])');
            existingInputs.forEach(input => input.remove());
            
            // Copy all inputs from the main form
            const inputs = mainForm.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name && input.name !== '') {
                    // Handle checkboxes differently
                    if (input.type === 'checkbox') {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = input.name;
                        hiddenInput.value = input.checked ? 'on' : '';
                        directForm.appendChild(hiddenInput);
                    } 
                    // Handle file inputs differently
                    else if (input.type === 'file') {
                        // Skip file inputs in the copy process
                        // They can't be copied programmatically for security reasons
                    }
                    // Regular inputs
                    else {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = input.name;
                        hiddenInput.value = input.value;
                        directForm.appendChild(hiddenInput);
                    }
                }
            });
            
            // Submit the form
            directForm.submit();
        });
        
        // Smooth scroll for section navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Add Value Button
        const addValueBtn = document.getElementById('add-value-btn');
        if (addValueBtn) {
            addValueBtn.addEventListener('click', function() {
                document.getElementById('new-value-form').classList.toggle('hidden');
            });
        }
        
        // Add Member Button
        const addMemberBtn = document.getElementById('add-member-btn');
        if (addMemberBtn) {
            addMemberBtn.addEventListener('click', function() {
                document.getElementById('new-member-form').classList.toggle('hidden');
                document.getElementById('no-team-members-message')?.classList.add('hidden');
            });
        }
        
        // Initialize Sortable for values
        const valuesContainer = document.getElementById('values-container');
        if (valuesContainer) {
            const valuesSortable = new Sortable(valuesContainer, {
                animation: 150,
                handle: '.grip-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    updateSortOrder('values', valuesSortable.toArray());
                }
            });
        }
        
        // Initialize Sortable for team members
        const membersContainer = document.getElementById('members-container');
        if (membersContainer) {
            const membersSortable = new Sortable(membersContainer, {
                animation: 150,
                handle: '.grip-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function() {
                    updateSortOrder('members', membersSortable.toArray());
                }
            });
        }
        
        // Function to update sort order via AJAX
        function updateSortOrder(type, items) {
            fetch('{{ route('admin.about.update-order') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: type,
                    items: items
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`${type} order updated successfully`);
                } else {
                    console.error('Error updating order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Icon preview functionality for certifications
        function setupIconPreview(selectId) {
            const select = document.getElementById(selectId);
            if (!select) return;
            
            // Create preview element if it doesn't exist
            let previewContainer = document.createElement('div');
            previewContainer.className = 'icon-preview mt-2 flex items-center';
            select.parentElement.appendChild(previewContainer);
            
            // Update preview on load
            updateIconPreview(select, previewContainer);
            
            // Update preview when selection changes
            select.addEventListener('change', function() {
                updateIconPreview(select, previewContainer);
            });
        }
        
        function updateIconPreview(select, previewContainer) {
            const selectedIcon = select.value;
            
            if (selectedIcon) {
                previewContainer.innerHTML = `
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-600 mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <span class="text-sm text-gray-600">Icon: ${selectedIcon}</span>
                `;
            } else {
                previewContainer.innerHTML = '<span class="text-sm text-gray-400">No icon selected</span>';
            }
        }
        
        // Setup icon previews for all certification icon selects
        setupIconPreview('certification_1_icon');
        setupIconPreview('certification_2_icon');
        setupIconPreview('certification_3_icon');
        setupIconPreview('certification_4_icon');
    });
</script>
@endpush 