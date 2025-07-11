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
                Verify Your Email Address
            </h2>
            <p class="mt-2 text-sm text-white/80">
                Before proceeding, please check your email for a verification link
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-xl border border-white/10">
            @if (session('status'))
                <div class="bg-accent/20 border border-accent/50 text-white p-4 rounded-lg mb-6">
                    {{ session('status') }}
                </div>
            @endif

            <div class="text-white text-center mb-6">
                <p class="mb-4">
                    A verification link has been sent to your email address: <strong>{{ auth()->user()->email }}</strong>
                </p>
                <p>
                    If you didn't receive the email, click below to request another.
                </p>
            </div>

            <form class="mt-8" action="{{ route('verification.send') }}" method="POST">
                @csrf
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors duration-200">
                        Resend Verification Email
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-white/80 hover:text-accent transition-colors">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection 