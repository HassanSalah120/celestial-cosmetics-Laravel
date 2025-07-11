@extends('layouts.app')

@section('content')
<section class="min-h-screen flex items-center justify-center bg-gradient-to-b from-primary to-secondary py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-20 left-10 w-64 h-64 bg-accent/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('{{ asset('storage/1 (16).jpg') }}')] bg-cover bg-center opacity-10"></div>
        
        <!-- Celestial Elements -->
        <div class="star-icon top-1/4 left-1/4 w-4 h-4">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"/></svg>
        </div>
        <div class="star-icon delay-2 top-1/3 right-1/4 w-6 h-6">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"/></svg>
        </div>
        <div class="moon-icon delay-3 top-1/4 right-1/3 w-8 h-8">
            <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"/></svg>
        </div>
    </div>

    <div class="max-w-md w-full relative z-10 space-y-8">
        <div class="text-center">
            @if(Settings::get('site_logo'))
                <img src="{{ asset('storage/' . Settings::get('site_logo')) }}" alt="Celestial Cosmetics" class="h-24 mx-auto rounded-lg shadow-lg mb-6">
            @else
                <img src="{{ asset('storage/logo.jpg') }}" alt="Celestial Cosmetics" class="h-24 mx-auto rounded-lg shadow-lg mb-6">
            @endif
            <h2 class="mt-6 text-3xl font-display font-bold text-white">
                Create your account (Test Mode)
            </h2>
            <p class="mt-2 text-sm text-white/80">
                This registration form will automatically verify your account without email
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-xl border border-white/10">
            @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500/50 text-white p-4 rounded-lg mb-6">
                <div class="font-medium">{{ __('Oops! Something went wrong.') }}</div>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
                @csrf
                <input type="hidden" name="auto_verify" value="1">
                <div class="rounded-md space-y-4">
                    <div>
                        <label for="name" class="sr-only">Full name</label>
                        <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-white/20 bg-white/10 text-white placeholder-white/60 focus:outline-none focus:ring-accent focus:border-accent focus:z-10 sm:text-sm" placeholder="Full name">
                    </div>
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-white/20 bg-white/10 text-white placeholder-white/60 focus:outline-none focus:ring-accent focus:border-accent focus:z-10 sm:text-sm" placeholder="Email address">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-white/20 bg-white/10 text-white placeholder-white/60 focus:outline-none focus:ring-accent focus:border-accent focus:z-10 sm:text-sm" placeholder="Password">
                    </div>
                    <div>
                        <label for="password_confirmation" class="sr-only">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-white/20 bg-white/10 text-white placeholder-white/60 focus:outline-none focus:ring-accent focus:border-accent focus:z-10 sm:text-sm" placeholder="Confirm password">
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors duration-200">
                        Create account (Auto-Verify)
                    </button>
                </div>
                <div class="text-center text-xs text-white/60">
                    <p class="mb-2">This form is for testing purposes. It will skip email verification.</p>
                    <p>By creating an account, you agree to our <a href="#" class="text-accent hover:text-accent-dark">Terms of Service</a> and <a href="#" class="text-accent hover:text-accent-dark">Privacy Policy</a>.</p>
                </div>
            </form>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/20"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 text-white/80 bg-primary/50">Or continue with</span>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="{{ route('auth.google') }}" class="group relative w-full flex justify-center py-3 px-4 border border-white/20 bg-white/5 hover:bg-white/10 text-sm font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z"/>
                            <path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2970142 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z"/>
                            <path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z"/>
                            <path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z"/>
                        </svg>
                        Sign up with Google
                    </a>
                </div>
                <div class="mt-3 text-center text-xs text-white/60">
                    <p>Sign up with Google also auto-verifies your account</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 