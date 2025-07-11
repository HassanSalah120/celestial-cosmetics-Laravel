<div class="text-center mt-12">
    <a href="{{ route('products.index') }}" 
        class="inline-flex items-center justify-center px-5 py-3 mt-6 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors duration-300">
        {{ $buttonText ?? __('View All Products') }}
        <svg class="{{ is_rtl() ? 'mr-2 -ml-1 transform rotate-180' : 'ml-2 -mr-1' }} w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
        </svg>
    </a>
</div> 