@php
    $session_locale = session('locale', app()->getLocale());
@endphp

<!-- Desktop Language Switcher -->
<div class="relative" x-data="{ open: false }">
    <!-- Language Menu Button -->
    <button id="language-menu-button" @click="open = !open" class="flex items-center space-x-1 focus:outline-none {{ Settings::get('header_text_color', 'text-white/80') }} hover:{{ Settings::get('header_hover_color', 'text-accent') }} transition-colors duration-200" type="button">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
        </svg>
        @if($session_locale == 'ar')
        <span>{{ __('messages.arabic') }}</span>
        @else
        <span>{{ __('messages.english') }}</span>
        @endif
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Language Menu Dropdown -->
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white dark:bg-black/90 rounded-md shadow-lg z-50" role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1">
        <div class="py-1 flex flex-col" role="none">
            @foreach(config('app.available_locales') as $locale)
                <a href="/language/{{ $locale }}/flush?redirect={{ urlencode('/') }}&_={{ time() . rand(1000, 9999) }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm {{ $session_locale == $locale ? 'bg-gray-100 dark:bg-gray-800 text-primary dark:text-accent' : 'text-gray-700 dark:text-white/80 hover:bg-gray-100 hover:dark:bg-gray-800' }}"
                   role="menuitem" 
                   tabindex="-1" 
                   id="language-menu-item-{{ $loop->index }}"
                   onclick="forceHardReload('{{ $locale }}');">
                    <div class="flex items-center">
                        @if($locale == 'ar')
                            <span class="mr-2">{{ __('messages.arabic') }}</span>
                            <span class="text-xs text-gray-400">(RTL)</span>
                        @else
                            <span class="mr-2">{{ __('messages.english') }}</span>
                            <span class="text-xs text-gray-400">(LTR)</span>
                        @endif
                    </div>
                    @if($session_locale == $locale)
                        <svg class="w-5 h-5 text-primary dark:text-accent" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading indicator CSS
    const style = document.createElement('style');
    style.innerHTML = `
        .page-loading {
            cursor: wait;
            pointer-events: none;
            position: relative;
        }
        .page-loading:after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.5);
            z-index: 9999;
        }
        
        @keyframes spinner {
            to {transform: rotate(360deg);}
        }
        
        .loading-spinner:before {
            content: '';
            box-sizing: border-box;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 30px;
            height: 30px;
            margin-top: -15px;
            margin-left: -15px;
            border-radius: 50%;
            border: 3px solid #ccc;
            border-top-color: #07d;
            animation: spinner .6s linear infinite;
            z-index: 10000;
        }
    `;
    document.head.appendChild(style);
});

// Function to force a hard reload with language change
function forceHardReload(locale) {
    // Show loading overlay
    document.documentElement.classList.add('page-loading');
    document.documentElement.classList.add('loading-spinner');
    
    // Force reload to homepage with language parameter
    const timestamp = Date.now();
    const random = Math.floor(Math.random() * 1000000);
    
    // Create the URL with cache-busting parameters
    const url = `/language/${locale}/flush?redirect=${encodeURIComponent('/')}&_=${timestamp}&r=${random}`;
    
    // Navigate to the URL to change language and reload the page
    window.location.href = url;
    
    // Fallback in case navigation doesn't happen
    setTimeout(() => {
        window.location.reload(true);
    }, 3000);
    
    return false; // Prevent default link behavior
}
</script> 