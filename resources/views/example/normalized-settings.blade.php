@extends('layouts.admin')

@section('title', 'Normalized Settings Example')

@section('content')
<div class="container py-6">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800">Normalized Settings Example</h1>
            <p class="text-gray-600 mt-2">This page demonstrates how to work with the new normalized settings tables directly.</p>
        </div>
        
        @if(session('success'))
        <div class="m-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif
        
        <div class="p-6">
            <!-- General Settings Example -->
            <div class="mb-10">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">General Settings</h2>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Site Name</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $generalSettings->site_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Site Logo</dt>
                            <dd class="mt-1 text-base text-gray-900">
                                @if($generalSettings->site_logo)
                                <img src="{{ asset('storage/' . $generalSettings->site_logo) }}" alt="Site Logo" class="h-8">
                                @else
                                No logo set
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Language Switcher</dt>
                            <dd class="mt-1 text-base text-gray-900">
                                <span class="inline-flex items-center rounded-md bg-{{ $generalSettings->enable_language_switcher ? 'green' : 'red' }}-50 px-2 py-1 text-xs font-medium text-{{ $generalSettings->enable_language_switcher ? 'green' : 'red' }}-700 ring-1 ring-inset ring-{{ $generalSettings->enable_language_switcher ? 'green' : 'red' }}-700/10">
                                    {{ $generalSettings->enable_language_switcher ? 'Enabled' : 'Disabled' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Default Language</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $generalSettings->default_language }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div class="mt-4">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Update General Settings</h3>
                    <form action="{{ route('example.normalized-settings.update') }}" method="POST" class="space-y-4 bg-white p-4 rounded-lg border border-gray-200">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                            <input type="text" id="site_name" name="site_name" value="{{ $generalSettings->site_name }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        </div>
                        
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" id="enable_language_switcher" name="enable_language_switcher" value="1"
                                      {{ $generalSettings->enable_language_switcher ? 'checked' : '' }}
                                      class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                                <label for="enable_language_switcher" class="ml-2 block text-sm text-gray-700">Enable Language Switcher</label>
                            </div>
                        </div>
                        
                        <div>
                            <label for="default_language" class="block text-sm font-medium text-gray-700">Default Language</label>
                            <select id="default_language" name="default_language"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                <option value="en" {{ $generalSettings->default_language == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ $generalSettings->default_language == 'ar' ? 'selected' : '' }}>Arabic</option>
                            </select>
                        </div>
                        
                        <div>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Update Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- SEO Defaults Example -->
            <div class="mb-10">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">SEO Defaults</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Default Meta Title</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $seoDefaults->default_meta_title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Default Meta Description</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ Str::limit($seoDefaults->default_meta_description, 100) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Robots Content</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $seoDefaults->default_robots_content }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Structured Data</dt>
                            <dd class="mt-1 text-base text-gray-900">
                                <span class="inline-flex items-center rounded-md bg-{{ $seoDefaults->enable_structured_data ? 'green' : 'red' }}-50 px-2 py-1 text-xs font-medium text-{{ $seoDefaults->enable_structured_data ? 'green' : 'red' }}-700 ring-1 ring-inset ring-{{ $seoDefaults->enable_structured_data ? 'green' : 'red' }}-700/10">
                                    {{ $seoDefaults->enable_structured_data ? 'Enabled' : 'Disabled' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Navigation Example -->
            <div class="mb-10">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Navigation Structure</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <ul class="space-y-2">
                        @foreach($navItems as $item)
                            <li class="border-b border-gray-200 pb-2">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-800">{{ $item->name }}</span>
                                    <span class="ml-2 text-sm text-gray-500">({{ $item->url ?: 'No URL' }})</span>
                                </div>
                                
                                @if($item->children && $item->children->count() > 0)
                                    <ul class="pl-6 mt-2 space-y-1">
                                        @foreach($item->children as $child)
                                            <li>
                                                <div class="flex items-center">
                                                    <span class="text-gray-700">{{ $child->name }}</span>
                                                    <span class="ml-2 text-xs text-gray-500">({{ $child->url ?: 'No URL' }})</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <!-- Team Members Example -->
            <div class="mb-10">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Team Members</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($teamMembers as $member)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            @if($member->image)
                                <img src="{{ asset('storage/' . $member->image) }}" alt="{{ $member->name }}" 
                                     class="w-full h-40 object-cover rounded-md mb-3">
                            @else
                                <div class="w-full h-40 bg-gray-200 rounded-md mb-3 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                            <h3 class="text-lg font-medium text-gray-800">{{ $member->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $member->title }}</p>
                            @if($member->bio)
                                <p class="text-sm text-gray-500 mt-2">{{ Str::limit($member->bio, 100) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-700 mb-2">Add Team Member</h3>
                    <form action="{{ route('example.normalized-settings.add-team-member') }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-4 rounded-lg border border-gray-200">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" name="name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" id="title" name="title" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <textarea id="bio" name="bio" rows="3"
                                     class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm"></textarea>
                        </div>
                        
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                            <input type="file" id="image" name="image" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-dark">
                        </div>
                        
                        <div>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Add Team Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Corporate Values Example -->
            <div class="mb-10">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Corporate Values</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($values as $value)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 flex">
                            @if($value->icon)
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                                        <i class="{{ $value->icon }}"></i>
                                    </div>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-md font-medium text-gray-800">{{ $value->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $value->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 