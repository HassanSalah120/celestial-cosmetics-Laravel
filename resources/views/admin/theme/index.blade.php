@extends('layouts.admin')

@section('title', 'Theme Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Theme Settings</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.theme.showcase') }}" class="bg-secondary text-white py-2 px-4 rounded-md hover:bg-secondary-dark transition-colors flex items-center">
                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Theme Showcase
            </a>
            <a href="{{ route('admin.theme.create') }}" class="bg-primary text-white py-2 px-4 rounded-md hover:bg-primary-dark transition-colors flex items-center">
                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create New Theme
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <svg class="h-6 w-6 text-green-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p>{{ session('success') }}</p>
            </div>
            <button onclick="window.location.reload()" class="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 transition-colors text-sm flex items-center">
                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm flex items-center" role="alert">
            <svg class="h-6 w-6 text-red-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold">Select a Theme</h2>
        </div>
        
        <div class="p-6">
            <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <svg class="h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">Currently Active Theme</h3>
                    <p class="text-gray-600">
                        <span class="font-semibold text-primary">{{ $activeTheme ? $activeTheme->name : 'None' }}</span>
                        @if($activeTheme && $activeTheme->group)
                        <span class="text-gray-400 text-sm ml-2">({{ $activeTheme->group }})</span>
                        @endif
                    </p>
                </div>
                <div class="ml-auto">
                    <button type="button" onclick="window.location.reload()" class="bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md hover:bg-gray-300 transition-colors flex items-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh Page
                    </button>
                </div>
            </div>

            <!-- Group navigation tabs -->
            @if(isset($groupedThemes) && $groupedThemes->count() > 0)
                <div class="mb-6 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="theme-tabs" role="tablist">
                        @foreach($groupedThemes as $group => $themesInGroup)
                            <li class="mr-2" role="presentation">
                                <button class="group-tab inline-block p-4 border-b-2 rounded-t-lg hover:text-primary hover:border-primary {{ $loop->first ? 'active text-primary border-primary' : 'border-transparent' }}" 
                                        id="tab-{{ $loop->index }}" 
                                        data-tabs-target="#group-{{ $loop->index }}" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="group-{{ $loop->index }}" 
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ $group ? $group : 'Ungrouped' }}
                                    <span class="ml-1 bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $themesInGroup->count() }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Group content panels -->
                <div id="theme-content">
                    @foreach($groupedThemes as $group => $themesInGroup)
                        <div class="group-content {{ $loop->first ? 'block' : 'hidden' }}" id="group-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-{{ $loop->index }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($themesInGroup as $theme)
                                    <div class="relative theme-card" data-theme-id="{{ $theme->id }}">
                                        <div class="border-2 theme-border rounded-lg p-5 transition-all hover:border-primary h-full {{ $activeTheme && $activeTheme->id == $theme->id ? 'border-primary' : 'border-gray-200' }}">
                                            <div class="flex items-center mb-3">
                                                <div class="h-5 w-5 rounded-full border-2 theme-indicator flex-shrink-0 mr-3 {{ $activeTheme && $activeTheme->id == $theme->id ? 'bg-primary border-primary' : 'border-gray-300' }}"></div>
                                                <span class="text-lg font-medium">{{ $theme->name }}</span>
                                            </div>
                                            
                                            <!-- Color Preview -->
                                            <div class="p-3 bg-gray-50 rounded-lg">
                                                <div class="grid grid-cols-3 gap-2">
                                                    @foreach($theme->colors as $name => $color)
                                                        <div class="flex flex-col items-center">
                                                            <div class="w-10 h-10 rounded-full border shadow-sm" style="background-color: {{ $color }}" title="{{ $name }}"></div>
                                                            <span class="text-xs text-gray-500 mt-1">{{ $name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                            <!-- UI Preview -->
                                            <div class="mt-4 border rounded-md overflow-hidden shadow-sm">
                                                <div class="h-8" style="background-color: {{ $theme->colors['primary'] ?? '#1f5964' }}"></div>
                                                <div class="p-3 flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0"></div>
                                                    <div class="ml-2">
                                                        <div class="h-2 w-20 rounded-full" style="background-color: {{ $theme->colors['secondary'] ?? '#312e43' }}"></div>
                                                        <div class="h-2 w-16 mt-1 rounded-full bg-gray-200"></div>
                                                    </div>
                                                    <div class="ml-auto">
                                                        <div class="h-6 w-12 rounded-md" style="background-color: {{ $theme->colors['accent'] ?? '#d4af37' }}"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Theme Actions -->
                                            <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                                                @if(!$theme->is_active)
                                                    <form action="{{ route('admin.theme.destroy', $theme) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this theme?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @else
                                                    <div></div>
                                                @endif
                                                
                                                <div class="flex space-x-3">
                                                    @if(!$theme->is_active)
                                                    <form action="{{ route('admin.theme.update') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="theme_id" value="{{ $theme->id }}">
                                                        <input type="hidden" name="direct_apply" value="1">
                                                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm flex items-center">
                                                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                            </svg>
                                                            Apply Now
                                                        </button>
                                                    </form>
                                                    @else
                                                    <span class="text-green-600 text-sm flex items-center">
                                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Current Theme
                                                    </span>
                                                    @endif
                                                    
                                                    <a href="{{ route('admin.theme.edit', $theme) }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    
                                                    <form action="{{ route('admin.theme.duplicate', $theme) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-primary hover:text-primary-dark text-sm flex items-center">
                                                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                            </svg>
                                                            Duplicate
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($activeTheme && $activeTheme->id == $theme->id)
                                            <div class="absolute top-3 right-3">
                                                <span class="bg-primary text-white text-xs font-semibold px-2.5 py-1 rounded-full">Active</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Fallback to display all themes without grouping (original view) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach($themes as $theme)
                        <div class="relative theme-card" data-theme-id="{{ $theme->id }}">
                            <div class="border-2 theme-border rounded-lg p-5 transition-all hover:border-primary h-full {{ $activeTheme && $activeTheme->id == $theme->id ? 'border-primary' : 'border-gray-200' }}">
                                <div class="flex items-center mb-3">
                                    <div class="h-5 w-5 rounded-full border-2 theme-indicator flex-shrink-0 mr-3 {{ $activeTheme && $activeTheme->id == $theme->id ? 'bg-primary border-primary' : 'border-gray-300' }}"></div>
                                    <span class="text-lg font-medium">{{ $theme->name }}</span>
                                </div>
                                
                                <!-- Color Preview -->
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach($theme->colors as $name => $color)
                                            <div class="flex flex-col items-center">
                                                <div class="w-10 h-10 rounded-full border shadow-sm" style="background-color: {{ $color }}" title="{{ $name }}"></div>
                                                <span class="text-xs text-gray-500 mt-1">{{ $name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- UI Preview -->
                                <div class="mt-4 border rounded-md overflow-hidden shadow-sm">
                                    <div class="h-8" style="background-color: {{ $theme->colors['primary'] ?? '#1f5964' }}"></div>
                                    <div class="p-3 flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex-shrink-0"></div>
                                        <div class="ml-2">
                                            <div class="h-2 w-20 rounded-full" style="background-color: {{ $theme->colors['secondary'] ?? '#312e43' }}"></div>
                                            <div class="h-2 w-16 mt-1 rounded-full bg-gray-200"></div>
                                        </div>
                                        <div class="ml-auto">
                                            <div class="h-6 w-12 rounded-md" style="background-color: {{ $theme->colors['accent'] ?? '#d4af37' }}"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Theme Actions -->
                                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                                    @if(!$theme->is_active)
                                        <form action="{{ route('admin.theme.destroy', $theme) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this theme?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <div></div>
                                    @endif
                                    
                                    <div class="flex space-x-3">
                                        @if(!$theme->is_active)
                                        <form action="{{ route('admin.theme.update') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="theme_id" value="{{ $theme->id }}">
                                            <input type="hidden" name="direct_apply" value="1">
                                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm flex items-center">
                                                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                Apply Now
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-green-600 text-sm flex items-center">
                                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Current Theme
                                        </span>
                                        @endif
                                        
                                        <a href="{{ route('admin.theme.edit', $theme) }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        
                                        <form action="{{ route('admin.theme.duplicate', $theme) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-primary hover:text-primary-dark text-sm flex items-center">
                                                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                Duplicate
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            @if($activeTheme && $activeTheme->id == $theme->id)
                                <div class="absolute top-3 right-3">
                                    <span class="bg-primary text-white text-xs font-semibold px-2.5 py-1 rounded-full">Active</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    <!-- Theme Preview Section -->
    @if($activeTheme)
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold">Theme Preview</h2>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Color Palette -->
                <div>
                    <h3 class="text-lg font-medium mb-4">Color Palette</h3>
                    <div class="space-y-4">
                        @foreach($activeTheme->colors as $name => $color)
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg shadow-sm" style="background-color: {{ $color }}"></div>
                                <div class="ml-4">
                                    <div class="font-medium">{{ ucfirst(str_replace('-', ' ', $name)) }}</div>
                                    <div class="text-sm text-gray-500 font-mono">{{ $color }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- UI Elements Preview -->
                <div>
                    <h3 class="text-lg font-medium mb-4">UI Elements</h3>
                    <div class="space-y-6">
                        <!-- Buttons -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Buttons</h4>
                            <div class="flex flex-wrap gap-3">
                                <button class="bg-primary text-white px-4 py-2 rounded-md">Primary Button</button>
                                <button class="bg-secondary text-white px-4 py-2 rounded-md">Secondary Button</button>
                                <button class="bg-accent text-white px-4 py-2 rounded-md">Accent Button</button>
                            </div>
                        </div>
                        
                        <!-- Cards -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Cards</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                    <div class="h-6" style="background-color: {{ $activeTheme->colors['primary'] ?? '#1f5964' }}"></div>
                                    <div class="p-3">
                                        <div class="h-2 w-16 rounded-full" style="background-color: {{ $activeTheme->colors['secondary'] ?? '#312e43' }}"></div>
                                        <div class="h-2 w-12 mt-1 rounded-full bg-gray-200"></div>
                                    </div>
                                </div>
                                <div class="border rounded-lg overflow-hidden shadow-sm">
                                    <div class="h-6" style="background-color: {{ $activeTheme->colors['secondary'] ?? '#312e43' }}"></div>
                                    <div class="p-3">
                                        <div class="h-2 w-16 rounded-full" style="background-color: {{ $activeTheme->colors['primary'] ?? '#1f5964' }}"></div>
                                        <div class="h-2 w-12 mt-1 rounded-full bg-gray-200"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Text Colors -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Text Colors</h4>
                            <div class="space-y-1">
                                <div class="font-medium" style="color: {{ $activeTheme->colors['primary'] ?? '#1f5964' }}">Primary Text</div>
                                <div class="font-medium" style="color: {{ $activeTheme->colors['secondary'] ?? '#312e43' }}">Secondary Text</div>
                                <div class="font-medium" style="color: {{ $activeTheme->colors['accent'] ?? '#d4af37' }}">Accent Text</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // Tab functionality for theme groups
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.group-tab');
        const contents = document.querySelectorAll('.group-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Deactivate all tabs
                tabs.forEach(t => {
                    t.classList.remove('active', 'text-primary', 'border-primary');
                    t.classList.add('border-transparent');
                    t.setAttribute('aria-selected', 'false');
                });
                
                // Hide all content panels
                contents.forEach(c => {
                    c.classList.add('hidden');
                    c.classList.remove('block');
                });
                
                // Activate clicked tab
                tab.classList.add('active', 'text-primary', 'border-primary');
                tab.classList.remove('border-transparent');
                tab.setAttribute('aria-selected', 'true');
                
                // Show corresponding content
                const targetId = tab.getAttribute('data-tabs-target');
                const targetContent = document.querySelector(targetId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                    targetContent.classList.add('block');
                }
            });
        });
    });
</script>
@endsection 