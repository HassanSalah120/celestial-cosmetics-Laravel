@extends('layouts.admin')

@section('title', 'Email Log Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Email Log Details</h1>
        <a href="{{ route('admin.emails.logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Logs
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="border-b border-gray-200 px-4 py-3 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-700">Email Content</span>
                        </div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $log->success ? 'Sent Successfully' : 'Failed' }}
                        </span>
                    </div>
                </div>
                
                <div class="border-b border-gray-200 px-4 py-3">
                    <div class="flex flex-col space-y-2">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">From:</span>
                            <span class="text-sm text-gray-900">{{ $log->from_name ? $log->from_name . ' <' . $log->from_email . '>' : $log->from_email }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">To:</span>
                            <span class="text-sm text-gray-900">{{ $log->to_name ? $log->to_name . ' <' . $log->to_email . '>' : $log->to_email }}</span>
                        </div>
                        @if ($log->cc && count($log->cc) > 0)
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-500 w-20">CC:</span>
                                <span class="text-sm text-gray-900">
                                    @foreach ($log->cc as $cc)
                                        {{ $cc['email'] ?? $cc }}<br>
                                    @endforeach
                                </span>
                            </div>
                        @endif
                        @if ($log->bcc && count($log->bcc) > 0)
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-500 w-20">BCC:</span>
                                <span class="text-sm text-gray-900">
                                    @foreach ($log->bcc as $bcc)
                                        {{ $bcc['email'] ?? $bcc }}<br>
                                    @endforeach
                                </span>
                            </div>
                        @endif
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-500 w-20">Subject:</span>
                            <span class="text-sm text-gray-900 font-medium">{{ $log->subject }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-1 bg-gray-100">
                    <div class="bg-white border border-gray-200 min-h-screen">
                        @if ($log->body_html)
                            <iframe id="email-preview" class="w-full h-screen" srcdoc="{{ $log->body_html }}"></iframe>
                        @elseif ($log->body_text)
                            <div class="p-4">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ $log->body_text }}</pre>
                            </div>
                        @else
                            <div class="p-4 text-gray-500 text-center">
                                No email content available
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Log Information</h2>
                
                <div class="flex flex-col space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Date & Time</p>
                        <p class="text-base text-gray-900">{{ $log->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Template</p>
                        <p class="text-base text-gray-900">
                            @if ($log->emailTemplate)
                                <a href="{{ route('admin.emails.templates.show', $log->emailTemplate) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $log->emailTemplate->name }}
                                </a>
                            @else
                                <span class="text-gray-500">{{ $log->template_code ?: 'Unknown' }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $log->success ? 'Sent Successfully' : 'Failed' }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sent At</p>
                        <p class="text-base text-gray-900">{{ $log->sent_at ? $log->sent_at->format('M d, Y H:i:s') : 'Not sent' }}</p>
                    </div>
                    
                    @if (!$log->success && $log->error_message)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Error Message</p>
                            <p class="text-base text-red-600">{{ $log->error_message }}</p>
                        </div>
                    @endif
                    
                    @if ($log->related_model_type)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Related To</p>
                            <p class="text-base text-gray-900">
                                {{ class_basename($log->related_model_type) }} #{{ $log->related_model_id }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            
            @if ($log->variables && count($log->variables) > 0)
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Variables Used</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variable</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($log->variables as $key => $value)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="text-sm font-mono text-gray-900">{{$key}}</div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="text-sm text-gray-900">
                                                @if (is_array($value) || is_object($value))
                                                    <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ Str::limit($value, 100) }}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 