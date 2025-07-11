<div class="store-status-indicator relative group" aria-live="polite" aria-atomic="true">
    <!-- Enhanced status button with text label -->
    <button type="button" class="flex items-center space-x-2 px-2 py-1.5 rounded-full bg-white/10 backdrop-blur-sm hover:bg-white/15 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-accent/50" aria-label="{{ $isOpen ? 'Store is open' : 'Store is closed' }}">
        <!-- Status indicator with animation -->
        <div class="relative flex items-center justify-center w-6 h-6 rounded-full overflow-hidden shadow-sm">
            <!-- Background with animation -->
            <div class="{{ $isOpen ? 'bg-green-500' : 'bg-accent' }} absolute inset-0 {{ $isOpen ? 'animate-status-pulse' : '' }}"></div>
            
            <!-- Icon centered on background -->
            <div class="relative z-10 flex items-center justify-center w-full h-full">
                @if($isOpen)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM5.172 9.172a4 4 0 015.656 0M10 6.5v1M13.172 9.172a4 4 0 010 5.656" clip-rule="evenodd"/>
                </svg>
                @endif
            </div>
        </div>

        <!-- Status text label -->
        <span class="text-white text-xs font-medium truncate hidden sm:inline">
            {{ $isOpen ? (is_rtl() ? 'مفتوح الآن' : 'Open Now') : (is_rtl() ? 'مغلق الآن' : 'Closed') }}
        </span>
    </button>
    
    <!-- Enhanced tooltip with better styling and animation -->
    <div class="hidden group-hover:block absolute z-50 top-full mt-2 {{ is_rtl() ? 'right-0' : 'left-0' }} w-64 transform transition-all duration-200 ease-out opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0" role="tooltip">
        <!-- Tooltip arrow -->
        <div class="absolute {{ is_rtl() ? 'right-4' : 'left-4' }} -top-2 w-4 h-4 transform rotate-45 bg-white border-t border-l border-gray-100"></div>
        
        <!-- Tooltip content with enhanced styling -->
        <div class="relative bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden">
            <!-- Top status bar -->
            <div class="py-2 px-4 {{ $isOpen ? 'bg-green-500' : 'bg-accent' }} text-white flex items-center justify-between">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <div class="{{ $isOpen ? 'bg-white/20' : 'bg-white/20' }} p-1 rounded-full">
                        @if($isOpen)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        @endif
                    </div>
                    <div class="font-medium">
                        {{ $isOpen ? (is_rtl() ? 'مفتوح الآن' : 'Open Now') : (is_rtl() ? 'مغلق الآن' : 'Closed Now') }}
                    </div>
                </div>
                <div class="text-xs opacity-80">
                    @if(is_rtl())
                        {{ $currentDayName }}
                    @else
                        {{ $currentDayName }}
                    @endif
                </div>
            </div>
            
            <!-- Schedule information -->
            <div class="p-4 space-y-3">
                <!-- Today's hours -->
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600 font-medium">{{ is_rtl() ? 'اليوم' : "Today's Hours" }}</div>
                    <div class="text-sm font-bold {{ $isOpen ? 'text-green-600' : 'text-accent' }}">
                        @if(is_rtl())
                            @php
                                // Convert AM/PM for Arabic display
                                $arabicHours = str_replace(' AM', ' ص', str_replace(' PM', ' م', $todayHours));
                            @endphp
                            {{ $arabicHours }}
                        @else
                            {{ $todayHours }}
                        @endif
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="border-t border-gray-100"></div>
                
                <!-- Timezone info -->
                <div class="flex items-center {{ is_rtl() ? 'justify-end' : 'justify-start' }} text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ is_rtl() ? 'ml-1' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ is_rtl() ? 'توقيت القاهرة' : 'Cairo Time' }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Adding custom animation styles -->
    <style>
        @keyframes statusPulse {
            0% { opacity: 0.9; }
            50% { opacity: 1; }
            100% { opacity: 0.9; }
        }
        .animate-status-pulse {
            animation: statusPulse 2s ease-in-out infinite;
        }
    </style>
</div> 