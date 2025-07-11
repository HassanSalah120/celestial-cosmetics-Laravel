@extends('layouts.admin')

@section('title', 'Theme Showcase')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Theme Showcase</h1>
        <a href="{{ route('admin.theme.index') }}" class="bg-primary text-white py-2 px-4 rounded-md hover:bg-primary-dark transition-colors flex items-center">
            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Themes
        </a>
    </div>

    <p class="mb-8 text-gray-600">Preview all available themes in one place. Click on any theme to see a full preview of how it would look on your site.</p>

    <!-- Group navigation tabs -->
    @if(isset($groupedThemes) && $groupedThemes->count() > 0)
        <div class="mb-6 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-xl font-semibold">Theme Groups</h2>
            </div>
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-wrap gap-2">
                    <button class="group-filter-btn px-4 py-2 rounded-md bg-primary text-white" data-group="all">
                        All Themes
                        <span class="ml-1 bg-white text-primary text-xs font-semibold px-2 py-0.5 rounded-full">{{ $themes->count() }}</span>
                    </button>
                    @foreach($groupedThemes as $group => $themesInGroup)
                        <button class="group-filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300" data-group="{{ $group ? $group : 'ungrouped' }}">
                            {{ $group ? $group : 'Ungrouped' }}
                            <span class="ml-1 bg-white text-gray-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $themesInGroup->count() }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8">
        @foreach($themes as $theme)
            <div class="bg-white shadow-md rounded-lg overflow-hidden theme-showcase-card" 
                 data-theme-id="{{ $theme->id }}" 
                 data-theme-group="{{ $theme->group ? $theme->group : 'ungrouped' }}">
                <div class="border-b border-gray-200 px-6 py-4" style="background-color: {{ $theme->colors['primary'] ?? '#1f5964' }}">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-white">{{ $theme->name }}</h2>
                        @if($theme->group)
                            <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full">{{ $theme->group }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Theme actions -->
                    <div class="mb-6 flex justify-between">
                        <div>
                            @if($activeTheme && $activeTheme->id == $theme->id)
                                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full flex items-center">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Currently Active
                                </span>
                            @else
                                <form action="{{ route('admin.theme.update') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="theme_id" value="{{ $theme->id }}">
                                    <input type="hidden" name="from_showcase" value="1">
                                    <button type="submit" class="bg-green-500 text-white text-sm font-medium px-3 py-1 rounded-md hover:bg-green-600 transition-colors flex items-center">
                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Apply Theme
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.theme.edit', $theme) }}" class="bg-blue-500 text-white text-sm font-medium px-3 py-1 rounded-md hover:bg-blue-600 transition-colors flex items-center">
                                <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('admin.theme.duplicate', $theme) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-purple-500 text-white text-sm font-medium px-3 py-1 rounded-md hover:bg-purple-600 transition-colors flex items-center">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Duplicate
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Color Palette -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Color Palette</h3>
                        <div class="grid grid-cols-3 md:grid-cols-9 gap-3">
                            @foreach($theme->colors as $name => $color)
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-lg shadow-sm border" style="background-color: {{ $color }}"></div>
                                    <span class="text-xs text-gray-500 mt-1">{{ $name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Theme Preview -->
                    <div class="border rounded-lg overflow-hidden shadow-sm">
                        <!-- Header -->
                        <div class="p-4" style="background-color: {{ $theme->colors['primary'] ?? '#1f5964' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-white"></div>
                                    <div class="ml-3 text-white font-medium">Celestial Cosmetics</div>
                                </div>
                                <div class="flex space-x-4">
                                    <div class="w-16 h-6 rounded bg-white/20"></div>
                                    <div class="w-16 h-6 rounded bg-white/20"></div>
                                    <div class="w-16 h-6 rounded bg-white/20"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hero -->
                        <div class="h-48 bg-gray-100 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="h-6 w-48 rounded-full mx-auto" style="background-color: {{ $theme->colors['secondary'] ?? '#312e43' }}"></div>
                                    <div class="h-4 w-64 rounded-full mx-auto mt-2 bg-gray-300"></div>
                                    <div class="mt-4">
                                        <div class="h-10 w-32 rounded-md mx-auto" style="background-color: {{ $theme->colors['accent'] ?? '#d4af37' }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Sidebar -->
                                <div class="col-span-1">
                                    <div class="p-4 rounded-lg" style="background-color: {{ $theme->colors['primary-light'] ?? '#2d6e7e' }}">
                                        <div class="h-5 w-24 rounded bg-white/30 mb-4"></div>
                                        <div class="space-y-2">
                                            <div class="h-4 w-full rounded bg-white/20"></div>
                                            <div class="h-4 w-full rounded bg-white/20"></div>
                                            <div class="h-4 w-full rounded bg-white/20"></div>
                                        </div>
                                        <div class="mt-4 p-2 rounded-md text-center text-white text-sm" style="background-color: {{ $theme->colors['accent'] ?? '#d4af37' }}">
                                            Action Button
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Main Content -->
                                <div class="col-span-2">
                                    <div class="mb-4">
                                        <div class="h-6 w-48 rounded" style="background-color: {{ $theme->colors['secondary'] ?? '#312e43' }}"></div>
                                        <div class="h-4 w-full rounded bg-gray-200 mt-2"></div>
                                        <div class="h-4 w-5/6 rounded bg-gray-200 mt-1"></div>
                                        <div class="h-4 w-4/6 rounded bg-gray-200 mt-1"></div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mt-6">
                                        <div class="border rounded-lg overflow-hidden">
                                            <div class="h-32 bg-gray-200"></div>
                                            <div class="p-3">
                                                <div class="h-5 w-full rounded" style="background-color: {{ $theme->colors['primary-dark'] ?? '#174853' }}"></div>
                                                <div class="h-4 w-5/6 rounded bg-gray-200 mt-2"></div>
                                                <div class="mt-2 flex justify-between items-center">
                                                    <div class="h-6 w-16 rounded" style="background-color: {{ $theme->colors['accent-light'] ?? '#dbba5d' }}"></div>
                                                    <div class="h-6 w-6 rounded-full" style="background-color: {{ $theme->colors['secondary-light'] ?? '#423f5a' }}"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="border rounded-lg overflow-hidden">
                                            <div class="h-32 bg-gray-200"></div>
                                            <div class="p-3">
                                                <div class="h-5 w-full rounded" style="background-color: {{ $theme->colors['primary-dark'] ?? '#174853' }}"></div>
                                                <div class="h-4 w-5/6 rounded bg-gray-200 mt-2"></div>
                                                <div class="mt-2 flex justify-between items-center">
                                                    <div class="h-6 w-16 rounded" style="background-color: {{ $theme->colors['accent-light'] ?? '#dbba5d' }}"></div>
                                                    <div class="h-6 w-6 rounded-full" style="background-color: {{ $theme->colors['secondary-light'] ?? '#423f5a' }}"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="p-4" style="background-color: {{ $theme->colors['primary-dark'] ?? '#174853' }}">
                            <div class="flex flex-col md:flex-row justify-between">
                                <div class="mb-4 md:mb-0">
                                    <div class="h-5 w-32 rounded bg-white/30 mb-2"></div>
                                    <div class="h-4 w-48 rounded bg-white/20"></div>
                                </div>
                                <div class="grid grid-cols-3 gap-6">
                                    <div>
                                        <div class="h-5 w-20 rounded bg-white/30 mb-2"></div>
                                        <div class="space-y-1">
                                            <div class="h-4 w-16 rounded bg-white/20"></div>
                                            <div class="h-4 w-16 rounded bg-white/20"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="h-5 w-20 rounded bg-white/30 mb-2"></div>
                                        <div class="space-y-1">
                                            <div class="h-4 w-16 rounded bg-white/20"></div>
                                            <div class="h-4 w-16 rounded bg-white/20"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="h-5 w-20 rounded bg-white/30 mb-2"></div>
                                        <div class="space-y-1">
                                            <div class="h-4 w-16 rounded bg-white/20"></div>
                                            <div class="h-4 w-16 rounded bg-white/20"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- UI Elements -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium mb-4">UI Elements</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Buttons -->
                            <div class="p-4 border rounded-lg">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Buttons</h4>
                                <div class="flex flex-wrap gap-3">
                                    <button class="px-4 py-2 rounded-md text-white" style="background-color: {{ $theme->colors['primary'] ?? '#1f5964' }}">Primary</button>
                                    <button class="px-4 py-2 rounded-md text-white" style="background-color: {{ $theme->colors['secondary'] ?? '#312e43' }}">Secondary</button>
                                    <button class="px-4 py-2 rounded-md text-white" style="background-color: {{ $theme->colors['accent'] ?? '#d4af37' }}">Accent</button>
                                    <button class="px-4 py-2 rounded-md border" style="border-color: {{ $theme->colors['primary'] ?? '#1f5964' }}; color: {{ $theme->colors['primary'] ?? '#1f5964' }}">Outline</button>
                                </div>
                            </div>
                            
                            <!-- Text Colors -->
                            <div class="p-4 border rounded-lg">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Typography</h4>
                                <div class="space-y-2">
                                    <div class="text-lg font-bold" style="color: {{ $theme->colors['primary'] ?? '#1f5964' }}">Primary Heading</div>
                                    <div class="text-base font-medium" style="color: {{ $theme->colors['secondary'] ?? '#312e43' }}">Secondary Text</div>
                                    <div class="text-sm" style="color: {{ $theme->colors['accent'] ?? '#d4af37' }}">Accent Text</div>
                                    <div class="text-sm text-gray-500">Regular Text</div>
                                </div>
                            </div>
                            
                            <!-- Form Elements -->
                            <div class="p-4 border rounded-lg">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Form Elements</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1" style="color: {{ $theme->colors['secondary'] ?? '#312e43' }}">Input Field</label>
                                        <input type="text" class="w-full border rounded-md p-2 focus:outline-none" style="border-color: {{ $theme->colors['primary-light'] ?? '#2d6e7e' }}; focus:border-color: {{ $theme->colors['primary'] ?? '#1f5964' }}" placeholder="Enter text...">
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" class="h-4 w-4 rounded" style="accent-color: {{ $theme->colors['primary'] ?? '#1f5964' }}">
                                        <label class="ml-2 text-sm" style="color: {{ $theme->colors['secondary'] ?? '#312e43' }}">Checkbox</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Cards -->
                            <div class="p-4 border rounded-lg">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Cards</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="border rounded-lg overflow-hidden shadow-sm">
                                        <div class="h-6" style="background-color: {{ $theme->colors['primary'] ?? '#1f5964' }}"></div>
                                        <div class="p-3">
                                            <div class="h-2 w-16 rounded-full" style="background-color: {{ $theme->colors['secondary'] ?? '#312e43' }}"></div>
                                            <div class="h-2 w-12 mt-1 rounded-full bg-gray-200"></div>
                                        </div>
                                    </div>
                                    <div class="border rounded-lg overflow-hidden shadow-sm">
                                        <div class="h-6" style="background-color: {{ $theme->colors['accent'] ?? '#d4af37' }}"></div>
                                        <div class="p-3">
                                            <div class="h-2 w-16 rounded-full" style="background-color: {{ $theme->colors['primary'] ?? '#1f5964' }}"></div>
                                            <div class="h-2 w-12 mt-1 rounded-full bg-gray-200"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
</div>

<script>
    // Check if we need to refresh the page (when coming back from a theme update)
    if (window.location.search.includes('refresh=')) {
        window.location.href = window.location.pathname; // Remove the query parameter and refresh
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.group-filter-btn');
        const themeCards = document.querySelectorAll('.theme-showcase-card');
        
        // Set initial active state
        filterButtons[0].classList.add('active');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Update active button styling
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-primary', 'text-white');
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                });
                button.classList.add('active', 'bg-primary', 'text-white');
                button.classList.remove('bg-gray-200', 'text-gray-700');
                
                const selectedGroup = button.getAttribute('data-group');
                
                // Filter theme cards
                themeCards.forEach(card => {
                    if (selectedGroup === 'all' || card.getAttribute('data-theme-group') === selectedGroup) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection 