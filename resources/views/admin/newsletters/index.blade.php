@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Newsletter Subscribers</h1>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <!-- Add subscriber button -->
            <a href="{{ route('admin.newsletters.create') }}" class="btn bg-primary hover:bg-primary-dark text-white">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span class="hidden xs:block ml-2">Send Newsletter</span>
            </a>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-12 gap-6 mb-8">
        <!-- Total Subscribers -->
        <div class="col-span-12 sm:col-span-4">
            <div class="flex flex-col bg-white shadow-lg rounded-sm border border-slate-200 p-4">
                <div class="grow flex flex-col justify-center">
                    <div class="flex items-center mb-1">
                        <h3 class="text-lg font-semibold text-slate-800">Total Subscribers</h3>
                    </div>
                    <div class="text-3xl font-bold text-slate-800">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        
        <!-- Active Subscribers -->
        <div class="col-span-12 sm:col-span-4">
            <div class="flex flex-col bg-white shadow-lg rounded-sm border border-slate-200 p-4">
                <div class="grow flex flex-col justify-center">
                    <div class="flex items-center mb-1">
                        <h3 class="text-lg font-semibold text-slate-800">Active Subscribers</h3>
                    </div>
                    <div class="text-3xl font-bold text-emerald-500">{{ $stats['active'] }}</div>
                </div>
            </div>
        </div>
        
        <!-- Unsubscribed -->
        <div class="col-span-12 sm:col-span-4">
            <div class="flex flex-col bg-white shadow-lg rounded-sm border border-slate-200 p-4">
                <div class="grow flex flex-col justify-center">
                    <div class="flex items-center mb-1">
                        <h3 class="text-lg font-semibold text-slate-800">Unsubscribed</h3>
                    </div>
                    <div class="text-3xl font-bold text-rose-500">{{ $stats['unsubscribed'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="mb-5">
        <form action="{{ route('admin.newsletters.index') }}" method="GET" class="flex flex-wrap -mx-2">
            <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                <label for="status" class="block text-sm font-medium mb-1">Status</label>
                <select id="status" name="status" class="form-select w-full">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="unsubscribed" {{ request('status') === 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                </select>
            </div>
            
            <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                <label for="search" class="block text-sm font-medium mb-1">Search</label>
                <input id="search" name="search" type="text" value="{{ request('search') }}" class="form-input w-full" placeholder="Search by email or name">
            </div>
            
            <div class="w-full md:w-1/3 px-2 flex items-end">
                <button type="submit" class="btn bg-primary hover:bg-primary-dark text-white">Filter</button>
                @if(request()->anyFilled(['status', 'search']))
                    <a href="{{ route('admin.newsletters.index') }}" class="btn border-slate-200 hover:border-slate-300 text-slate-600 ml-2">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Subscribers table -->
    <div class="bg-white shadow-lg rounded-sm border border-slate-200 mb-8">
        <div class="p-3">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <!-- Table header -->
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50 border-t border-b border-slate-200">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Email</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Name</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Status</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Subscribed At</div>
                            </th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-right">Actions</div>
                            </th>
                        </tr>
                    </thead>
                    <!-- Table body -->
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="font-medium text-slate-800">{{ $subscriber->email }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">{{ $subscriber->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">
                                        @if($subscriber->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                                Unsubscribed
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-left">{{ $subscriber->subscribed_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-1">
                                        <a href="{{ route('admin.newsletters.show', $subscriber) }}" class="text-slate-400 hover:text-slate-500" title="View">
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 16 16">
                                                <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" />
                                                <circle cx="8" cy="8" r="2" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.newsletters.destroy', $subscriber) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscriber?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-600" title="Delete">
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 16 16">
                                                    <path d="M2 6v8c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V6H2zm3 7c0 .6-.4 1-1 1s-1-.4-1-1V8c0-.6.4-1 1-1s1 .4 1 1v5zm4 0c0 .6-.4 1-1 1s-1-.4-1-1V8c0-.6.4-1 1-1s1 .4 1 1v5zm4 0c0 .6-.4 1-1 1s-1-.4-1-1V8c0-.6.4-1 1-1s1 .4 1 1v5zm-9-7h12V3c0-.6-.4-1-1-1h-2V1c0-.6-.4-1-1-1H8c-.6 0-1 .4-1 1v1H5c-.6 0-1 .4-1 1v2z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-2 first:pl-5 last:pr-5 py-8 text-center">
                                    <div class="text-slate-500">No subscribers found</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="mt-8">
        {{ $subscribers->links() }}
    </div>
</div>
@endsection 