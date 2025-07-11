@extends('layouts.admin')

@section('title', 'Test Email Configuration')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Test Email Configuration</h1>
    </div>

    <div class="border-t border-slate-200">
        <!-- Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden my-6">
            <div class="p-6">
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-slate-800">Send Test Email</h2>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-md">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-amber-50 text-amber-800 p-4 rounded mb-6">
                    <p>This tool allows you to test your email configuration. The system will attempt to send a test email to the address you provide.</p>
                    <p class="mt-2">Current mail configuration: <span class="font-semibold">{{ config('mail.default') }}</span> driver</p>
                </div>

                <form action="{{ route('admin.emails.test.send') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Enter email address" required value="{{ old('email') }}">
                        
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Send Test Email
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden my-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-4">Email Configuration Information</h2>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Mail Driver</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ config('mail.default') }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Mail Host</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ config('mail.mailers.smtp.host') }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Mail Port</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ config('mail.mailers.smtp.port') }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">Mail Encryption</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ config('mail.mailers.smtp.encryption') ?? 'None' }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">From Address</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ config('mail.from.address') }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">From Name</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700">{{ config('mail.from.name') }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <p class="text-sm text-gray-500">To change these settings, update your <code class="bg-gray-100 px-1 py-0.5 rounded">.env</code> file and restart the application.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 