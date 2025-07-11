@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">User Details</h2>
            <p class="mt-1 text-sm text-gray-600">Viewing user information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.print.customer', $user->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Users
            </a>
        </div>
    </div>

    <!-- User Information -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Image Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="mx-auto w-32 h-32 rounded-full overflow-hidden mb-4 border-4 border-primary/10">
                @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7F9CF5&background=EBF4FF&size=128" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @endif
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-1">{{ $user->name }}</h3>
            <p class="text-sm text-gray-500 mb-3">{{ $user->email }}</p>
            <div class="flex justify-center">
                @if($user->role === 'admin')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary text-white">
                        Administrator
                    </span>
                @elseif($user->role === 'staff')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent text-white">
                        Staff
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Customer
                    </span>
                @endif
            </div>
        </div>

        <!-- Basic Information Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-2">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Basic Information</h3>
            <div class="space-y-4">
                <!-- User ID -->
                <div class="flex items-center">
                    <div class="w-1/3 text-sm font-medium text-gray-500">User ID</div>
                    <div class="w-2/3 text-sm text-gray-900">{{ $user->id }}</div>
                </div>
                
                <!-- Email Verification Status -->
                <div class="flex items-center">
                    <div class="w-1/3 text-sm font-medium text-gray-500">Email Status</div>
                    <div class="w-2/3">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Verified on {{ $user->email_verified_at->format('M d, Y') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Not Verified
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Role Information -->
                <div class="flex items-center">
                    <div class="w-1/3 text-sm font-medium text-gray-500">Role</div>
                    <div class="w-2/3">
                        @if($user->role === 'admin')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/20 text-primary">
                                Administrator
                            </span>
                            <p class="mt-1 text-xs text-gray-500">Has full access to all system features</p>
                        @elseif($user->role === 'staff')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent/20 text-accent">
                                Staff
                            </span>
                            <p class="mt-1 text-xs text-gray-500">Has limited access based on assigned permissions</p>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Customer
                            </span>
                            <p class="mt-1 text-xs text-gray-500">Regular customer account</p>
                        @endif
                    </div>
                </div>
                
                @if($user->role === 'staff' && $user->permissions)
                <!-- Staff Permissions -->
                <div class="flex items-start pt-2">
                    <div class="w-1/3 text-sm font-medium text-gray-500 pt-1">Permissions</div>
                    <div class="w-2/3">
                        <div class="flex flex-wrap gap-2">
                            @php
                                $permissions = is_string($user->permissions) ? json_decode($user->permissions, true) : $user->permissions;
                                $permissions = is_array($permissions) ? $permissions : [];
                            @endphp
                            
                            @foreach($permissions as $permission)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $permission }}
                                </span>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.roles.edit', $user) }}" class="text-primary hover:text-primary-dark text-sm font-medium underline">
                                Manage permissions
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Registration Date -->
                <div class="flex items-center">
                    <div class="w-1/3 text-sm font-medium text-gray-500">Registered</div>
                    <div class="w-2/3 text-sm text-gray-900">{{ $user->created_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
                
                <!-- Last Updated -->
                <div class="flex items-center">
                    <div class="w-1/3 text-sm font-medium text-gray-500">Last Updated</div>
                    <div class="w-2/3 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-3">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit User
                </a>
                
                @if($user->role !== 'user')
                <a href="{{ route('admin.roles.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Manage Role & Permissions
                </a>
                @endif
                
                @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete User
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
