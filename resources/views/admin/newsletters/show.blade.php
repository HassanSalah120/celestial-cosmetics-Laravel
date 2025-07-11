@extends('layouts.admin')

@section('title', 'Subscriber Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Subscriber Details</h1>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('admin.newsletters.index') }}" class="btn border-slate-200 hover:border-slate-300 text-slate-600">
                <svg class="w-4 h-4 fill-current text-slate-500 shrink-0" viewBox="0 0 16 16">
                    <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z"></path>
                </svg>
                <span class="ml-2">Back to List</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-md">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-sm border border-slate-200 mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">
            <!-- Subscriber details -->
            <div class="grow p-6">
                <header class="flex items-center mb-6">
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 fill-current text-slate-500" viewBox="0 0 16 16">
                            <path d="M8 8c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl leading-snug text-slate-800 font-bold">{{ $subscriber->name ?? 'No Name' }}</h2>
                </header>
                
                <!-- Subscriber information -->
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <section>
                        <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Basic Information</h3>
                        <div class="text-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <div>
                                        <span class="font-semibold text-slate-800">Email Address:</span>
                                        <span class="ml-2">{{ $subscriber->email }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-slate-800">Status:</span>
                                        <span class="ml-2">
                                            @if($subscriber->status === 'active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                    Unsubscribed
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <span class="font-semibold text-slate-800">Subscribed On:</span>
                                        <span class="ml-2">{{ $subscriber->subscribed_at->format('F j, Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-slate-800">Last Updated:</span>
                                        <span class="ml-2">{{ $subscriber->updated_at->format('F j, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    <!-- Linked Account -->
                    @if($subscriber->user)
                    <section>
                        <h3 class="text-xl leading-snug text-slate-800 font-bold mb-1">Linked Account</h3>
                        <div class="text-sm">
                            <div class="space-y-2">
                                <div>
                                    <span class="font-semibold text-slate-800">User Account:</span>
                                    <a href="{{ route('admin.users.edit', $subscriber->user) }}" class="ml-2 text-blue-600 hover:text-blue-800">
                                        {{ $subscriber->user->name }}
                                    </a>
                                </div>
                                <div>
                                    <span class="font-semibold text-slate-800">Registered On:</span>
                                    <span class="ml-2">{{ $subscriber->user->created_at->format('F j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endif
                    
                    <!-- Actions -->
                    <section>
                        <h3 class="text-xl leading-snug text-slate-800 font-bold mb-4">Actions</h3>
                        <div class="flex flex-wrap gap-3">
                            <form action="{{ route('admin.newsletters.toggle-status', $subscriber) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                @if($subscriber->status === 'active')
                                    <button type="submit" class="btn border-slate-200 hover:border-slate-300 text-slate-600">
                                        <svg class="w-4 h-4 fill-current text-rose-500 shrink-0" viewBox="0 0 16 16">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z"></path>
                                        </svg>
                                        <span class="ml-2">Unsubscribe</span>
                                    </button>
                                @else
                                    <button type="submit" class="btn border-slate-200 hover:border-slate-300 text-slate-600">
                                        <svg class="w-4 h-4 fill-current text-emerald-500 shrink-0" viewBox="0 0 16 16">
                                            <path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"></path>
                                        </svg>
                                        <span class="ml-2">Reactivate</span>
                                    </button>
                                @endif
                            </form>
                            
                            <form action="{{ route('admin.newsletters.destroy', $subscriber) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this subscriber?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn border-slate-200 hover:border-slate-300 text-rose-500">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                                        <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"></path>
                                    </svg>
                                    <span class="ml-2">Delete Permanently</span>
                                </button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 