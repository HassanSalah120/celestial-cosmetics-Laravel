@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
@endphp

<!-- Navigation Links -->
<nav class="mt-5 flex-1 px-2">
    <!-- Dashboard Section -->
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Dashboard
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('view_dashboard'))
            <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('view_reports'))
            <a href="{{ route('admin.reports.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.reports.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Reports</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Product Management Section -->
    @if(Auth::user()->hasAnyPermission(['manage_products', 'manage_categories', 'manage_inventory']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Product Management
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('manage_products'))
            <a href="{{ route('admin.products.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.products.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>Products</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_categories'))
            <a href="{{ route('admin.categories.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.categories.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Categories</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Order Management Section -->
    @if(Auth::user()->hasAnyPermission(['manage_orders']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Order Management
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('manage_orders'))
            <a href="{{ route('admin.orders.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.orders.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.orders.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span>Orders</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- User Management Section -->
    @if(Auth::user()->hasAnyPermission(['manage_users', 'manage_roles']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                User Management
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('manage_users'))
            <a href="{{ route('admin.users.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.users.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Users</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_roles'))
            <a href="{{ route('admin.roles.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.roles.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Roles</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Marketing Section -->
    @if(Auth::user()->hasAnyPermission(['manage_coupons']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Marketing
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('manage_coupons'))
            <a href="{{ route('admin.coupons.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.coupons.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.coupons.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Coupons</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_products'))
            <a href="{{ route('admin.offers.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.offers.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.offers.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                </svg>
                <span>Offers</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Communication Section -->
    @if(Auth::user()->hasAnyPermission(['manage_contact_messages', 'manage_settings']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Communication
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('manage_contact_messages'))
            <a href="{{ route('admin.contact-messages.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.contact-messages.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.contact-messages.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Contact Messages</span>
                @php
                    $newMessagesCount = \App\Models\ContactMessage::where('status', 'new')->count();
                @endphp
                @if($newMessagesCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs leading-none text-white bg-primary rounded-full">
                        {{ $newMessagesCount }}
                    </span>
                @endif
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.emails.templates.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.emails.templates.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.emails.templates.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                </svg>
                <span>Email Templates</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.emails.logs.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.emails.logs.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.emails.logs.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Email Logs</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Settings Section -->
    @if(Auth::user()->hasAnyPermission(['manage_settings']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Settings
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.settings.general') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.general') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.settings.general') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>General Settings</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.shipping.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.shipping.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.shipping.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                <span>Shipping Settings</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.seo.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.seo.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.seo.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                </svg>
                <span>SEO Settings</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.homepage-content') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.homepage-content') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.homepage-content') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Homepage Content</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('view_activity_logs'))
            <a href="{{ route('admin.activities.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.activities.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.activities.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Activity Logs</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.settings.currency') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.currency') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.settings.currency') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Currency Settings</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.settings.language') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.language') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.settings.language') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                </svg>
                <span>Language Settings</span>
            </a>
            @endif
        </div>
    </div>
    @endif
</nav>

<!-- User Profile -->
<div class="flex-shrink-0 border-t border-primary-dark/50 p-4 mt-auto">
    <div class="flex items-center">
        <div class="flex-shrink-0 relative">
            @if(Auth::user()->profile_image && file_exists(public_path('storage/' . Auth::user()->profile_image)))
                <img class="h-10 w-10 rounded-full object-cover border-2 border-accent/30" 
                     src="{{ asset('storage/' . Auth::user()->profile_image) }}" 
                     alt="{{ Auth::user()->name }}">
            @else
                <div class="h-10 w-10 rounded-full bg-accent flex items-center justify-center text-white border-2 border-accent/30 overflow-hidden">
                    <img class="h-full w-full object-cover" 
                         src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=D4AF37&size=100" 
                         alt="{{ Auth::user()->name }}">
                </div>
            @endif
            <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-400 border border-primary"></div>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-300 truncate">
                @if(Auth::user()->isAdmin())
                    Administrator
                @elseif(Auth::user()->hasRole('manager'))
                    Manager
                @elseif(Auth::user()->hasRole('staff'))
                    Staff Member
                @else
                    User
                @endif
            </p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
            @csrf
            <button type="submit" class="rounded-full p-1 text-white/60 hover:text-white hover:bg-primary-dark/30 transition-colors duration-200 flex items-center">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </form>
    </div>
</div>

<div class="sm:col-span-6">
    <label class="block text-sm font-medium text-gray-700">
        Hero Image
    </label>
    
    <div class="mt-2 flex items-center">
        @if(isset($settings['homepage_hero_image']))
            <div class="mr-4 flex-shrink-0 w-32 h-32 bg-gray-100 rounded-md overflow-hidden">
                <img src="{{ asset($settings['homepage_hero_image']->value) }}" alt="Hero Image" class="w-full h-full object-cover">
            </div>
        @endif
        
        <input type="file" name="hero_image" accept="image/*"
               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary-light/80">
    </div>
</div>

<script>
    // Initialize collapsible menus
    document.addEventListener('DOMContentLoaded', function() {
        const collapsibleButtons = document.querySelectorAll('.collapsible-menu-btn');
        
        collapsibleButtons.forEach(button => {
            button.style.cursor = 'pointer';
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Find the submenu that follows this button
                const submenu = this.nextElementSibling;
                const icon = this.querySelector('.submenu-icon');
                
                // Toggle the submenu visibility
                if (submenu.classList.contains('hidden')) {
                    submenu.classList.remove('hidden');
                    submenu.classList.add('block');
                    icon.classList.add('rotate-180');
                } else {
                    submenu.classList.remove('block');
                    submenu.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            });
            
            // Auto-expand menu if any child is active
            const submenu = button.nextElementSibling;
            if (submenu) {
                const activeChild = submenu.querySelector('.bg-primary-dark\\/60');
                if (activeChild) {
                    submenu.classList.remove('hidden');
                    submenu.classList.add('block');
                    const icon = button.querySelector('.submenu-icon');
                    if (icon) {
                        icon.classList.add('rotate-180');
                    }
                }
            }
        });
    });
</script> 