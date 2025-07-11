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
                Overview
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
            @if(Auth::user()->hasPermission('view_activity_logs'))
            <a href="{{ route('admin.activities.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.activities.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.activities.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span>Activity Logs</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Products & Catalog Section -->
    @if(Auth::user()->hasAnyPermission(['manage_products', 'manage_categories', 'manage_coupons']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Products & Catalog
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 00-2 2h-2m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Offers</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Orders & Sales Section -->
    @if(Auth::user()->hasAnyPermission(['manage_orders', 'view_reports']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Orders & Sales
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
    @endif

    <!-- Content Management Section -->
    @if(Auth::user()->hasAnyPermission(['manage_settings', 'manage_seo']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Content Management
            </h3>
        </div>
        <div class="space-y-1">
            @if(Auth::user()->hasPermission('manage_settings'))
            <a href="{{ route('admin.homepage-content') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.homepage-content') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.homepage-content') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Homepage Content</span>
            </a>
            
            <div class="menu-item">
                <button type="button" class="collapsible-menu-btn group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.about.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.about.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="flex-1">About Page</span>
                    <svg class="submenu-icon transform transition-transform duration-200 h-5 w-5 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="submenu ml-8 mt-1 space-y-1 {{ request()->routeIs('admin.about.*') ? 'block' : 'hidden' }}">
                    <a href="{{ route('admin.about.edit') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.about.edit') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>Full Editor</span>
                    </a>
                    <a href="{{ route('admin.about.simple') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.about.simple') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>Simple Editor</span>
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.testimonials.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.testimonials.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.testimonials.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span>Testimonials</span>
            </a>
            <a href="{{ route('admin.header-navigation.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.header-navigation.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.header-navigation.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Header Navigation</span>
            </a>
            <a href="{{ route('admin.footer.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.footer.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.footer.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                </svg>
                <span>Footer Content</span>
            </a>
            <a href="{{ route('admin.store-hours.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.store-hours.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.store-hours.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Store Hours</span>
            </a>
            @endif

            @if(Auth::user()->hasPermission('manage_seo'))
            <div class="menu-item">
                <button type="button" class="collapsible-menu-btn group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.seo.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.seo.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                    </svg>
                    <span class="flex-1">SEO Tools</span>
                    <svg class="submenu-icon transform transition-transform duration-200 h-5 w-5 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="submenu ml-8 mt-1 space-y-1 {{ request()->routeIs('admin.seo.*') ? 'block' : 'hidden' }}">
                    <a href="{{ route('admin.seo.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.seo.index') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>SEO Overview</span>
                    </a>
                    <a href="{{ route('admin.seo.products') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.seo.products') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>Product SEO</span>
                    </a>
                    <a href="{{ route('admin.seo.categories') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.seo.categories') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>Category SEO</span>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Customers & Users Section -->
    @if(Auth::user()->hasAnyPermission(['manage_users', 'manage_roles', 'manage_contact_messages', 'manage_settings']))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Customers & Users
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
            <a href="{{ route('admin.newsletters.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.newsletters.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.newsletters.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <span>Newsletter</span>
                @php
                    $subscribersCount = \App\Models\NewsletterSubscription::where('status', 'active')->count();
                @endphp
                @if($subscribersCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs leading-none text-white bg-emerald-500 rounded-full">
                        {{ $subscribersCount }}
                    </span>
                @endif
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Store Operations Section -->
    @if(Auth::user()->hasPermission('manage_settings'))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                Store Operations
            </h3>
        </div>
        <div class="space-y-1">
            <a href="{{ route('admin.shipping.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.shipping.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.shipping.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <span>Shipping Methods</span>
            </a>
            <a href="{{ route('admin.settings.payment') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.payment') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.settings.payment') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Payment Methods</span>
            </a>
        </div>
    </div>
    @endif

    <!-- System Settings Section -->
    @if(Auth::user()->hasPermission('manage_settings'))
    <div class="mb-4">
        <div class="px-3 mb-2">
            <h3 class="text-xs font-semibold text-white/70 uppercase tracking-wider">
                System Settings
            </h3>
        </div>
        <div class="space-y-1">
            <div class="menu-item">
                <button type="button" class="collapsible-menu-btn group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.theme.*') ? 'bg-primary-dark text-white shadow-sm' : 'text-white/90 hover:bg-primary-dark/70 hover:text-white' }}">
                    <svg class="mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('admin.settings.*') ? 'text-accent' : 'text-white/80 group-hover:text-accent' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="flex-1">Core Settings</span>
                    <svg class="submenu-icon transform transition-transform duration-200 h-5 w-5 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="submenu ml-8 mt-1 space-y-1 {{ request()->routeIs('admin.settings.general') || request()->routeIs('admin.settings.currency') || request()->routeIs('admin.settings.language') || request()->routeIs('admin.theme.*') ? 'block' : 'hidden' }}">
                    <a href="{{ route('admin.settings.general') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.general') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>General</span>
                    </a>
                    <a href="{{ route('admin.theme.index') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.theme.index') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>Theme</span>
                    </a>
                    <a href="{{ route('admin.settings.currency') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.currency') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>Currency</span>
                    </a>
                    <a href="{{ route('admin.settings.language') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings.language') ? 'bg-primary-dark/60 text-white shadow-sm' : 'text-white/80 hover:bg-primary-dark/40 hover:text-white' }}">
                        <span>Language</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</nav> 