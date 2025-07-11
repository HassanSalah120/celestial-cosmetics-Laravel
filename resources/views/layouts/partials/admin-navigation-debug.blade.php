{{-- Simple debug version to identify syntax errors --}}
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

<!-- Navigation Links -->
<nav class="mt-5 flex-1 px-2">
    <!-- Dashboard Section -->
    <div class="mb-4">
        <h3>Dashboard</h3>
    </div>

    <!-- User Profile -->
    <div class="flex-shrink-0 border-t border-primary-dark/50 p-4 mt-auto">
        <div class="flex items-center">
            <div>User: {{ Auth::user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</nav> 