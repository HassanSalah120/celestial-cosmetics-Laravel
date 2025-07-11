<div class="bg-gradient-to-r from-primary to-secondary p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-semibold text-white mb-3">Subscribe to Our Newsletter</h3>
    <p class="text-white/80 mb-4">Get the latest updates on new products, special offers, and beauty tips.</p>
    
    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
        @csrf
        <div>
            <input type="email" name="email" placeholder="Your email address" 
                class="w-full px-4 py-2 rounded border-0 focus:ring-2 focus:ring-white/30" 
                required>
        </div>
        <div>
            <input type="text" name="name" placeholder="Your name (optional)" 
                class="w-full px-4 py-2 rounded border-0 focus:ring-2 focus:ring-white/30">
        </div>
        <div>
            <button type="submit" 
                class="w-full bg-white text-primary hover:bg-white/90 font-medium px-4 py-2 rounded transition-colors duration-200">
                Subscribe
            </button>
        </div>
    </form>
    
    @if(session('success') && request()->has('newsletter'))
    <div class="mt-4 p-3 bg-green-100 text-green-800 rounded-md">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('info') && request()->has('newsletter'))
    <div class="mt-4 p-3 bg-blue-100 text-blue-800 rounded-md">
        {{ session('info') }}
    </div>
    @endif
    
    @if(session('error') && request()->has('newsletter'))
    <div class="mt-4 p-3 bg-red-100 text-red-800 rounded-md">
        {{ session('error') }}
    </div>
    @endif
    
    <p class="text-white/70 text-xs mt-4">
        We respect your privacy. Unsubscribe at any time.
    </p>
</div> 