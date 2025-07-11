@extends('layouts.admin')

@section('title', 'Edit Store Hours')

@section('content')
    <div class="pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Edit Store Hours</h1>
                <a href="{{ route('admin.store-hours.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
            
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <form action="{{ route('admin.store-hours.update') }}" method="POST" class="px-4 py-5 sm:p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($storeHours as $index => $storeHour)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $storeHour->day }}
                                            <input type="hidden" name="days[{{ $index }}][id]" value="{{ $storeHour->id }}">
                                            <input type="hidden" id="hours_input_{{ $index }}" name="days[{{ $index }}][hours]" value="{{ $storeHour->hours }}">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-3">
                                                <div class="flex items-center">
                                                    <input type="checkbox" id="closed_{{ $index }}" class="closed-checkbox mr-2 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                                                        {{ strtolower($storeHour->hours) === 'closed' ? 'checked' : '' }}>
                                                    <label for="closed_{{ $index }}" class="text-sm font-medium text-gray-700">Closed</label>
                                                </div>
                                                
                                                <div class="time-selector {{ strtolower($storeHour->hours) === 'closed' ? 'hidden' : '' }} flex flex-wrap items-center space-x-2">
                                                    @php
                                                        $openTime = '';
                                                        $closeTime = '';
                                                        $openAmPm = 'AM';
                                                        $closeAmPm = 'PM';
                                                        
                                                        if (strtolower($storeHour->hours) !== 'closed') {
                                                            $parts = explode(' - ', $storeHour->hours);
                                                            if (count($parts) == 2) {
                                                                // Parse opening time
                                                                $openTimeFull = $parts[0];
                                                                $openAmPm = strpos($openTimeFull, 'PM') !== false ? 'PM' : 'AM';
                                                                $openTime = str_replace([' AM', ' PM'], '', $openTimeFull);
                                                                
                                                                // Parse closing time
                                                                $closeTimeFull = $parts[1];
                                                                $closeAmPm = strpos($closeTimeFull, 'PM') !== false ? 'PM' : 'AM';
                                                                $closeTime = str_replace([' AM', ' PM'], '', $closeTimeFull);
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    <div class="flex items-center">
                                                        <span class="text-sm text-gray-600 mr-2">Opens:</span>
                                                        <input type="text" 
                                                            class="open-time time-input block rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm w-24" 
                                                            placeholder="9:00" 
                                                            value="{{ $openTime }}">
                                                        
                                                        <select class="open-ampm ml-2 block rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                                            <option value="AM" {{ $openAmPm === 'AM' ? 'selected' : '' }}>AM</option>
                                                            <option value="PM" {{ $openAmPm === 'PM' ? 'selected' : '' }}>PM</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="flex items-center mt-2 sm:mt-0 sm:ml-4">
                                                        <span class="text-sm text-gray-600 mr-2">Closes:</span>
                                                        <input type="text" 
                                                            class="close-time time-input block rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm w-24" 
                                                            placeholder="5:00" 
                                                            value="{{ $closeTime }}">
                                                        
                                                        <select class="close-ampm ml-2 block rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                                            <option value="AM" {{ $closeAmPm === 'AM' ? 'selected' : '' }}>AM</option>
                                                            <option value="PM" {{ $closeAmPm === 'PM' ? 'selected' : '' }}>PM</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <p class="text-xs text-gray-500">Set opening and closing times or mark as closed</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update the hidden input with formatted time
        function updateHiddenInput(row) {
            const index = row.querySelector('.closed-checkbox').id.split('_')[1];
            const hiddenInput = document.getElementById('hours_input_' + index);
            const isClosed = row.querySelector('.closed-checkbox').checked;
            
            if (isClosed) {
                hiddenInput.value = 'Closed';
            } else {
                const openTime = row.querySelector('.open-time').value.trim();
                const openAmPm = row.querySelector('.open-ampm').value;
                const closeTime = row.querySelector('.close-time').value.trim();
                const closeAmPm = row.querySelector('.close-ampm').value;
                
                if (openTime && closeTime) {
                    hiddenInput.value = `${openTime} ${openAmPm} - ${closeTime} ${closeAmPm}`;
                }
            }
        }
        
        // Handle closed checkbox changes
        document.querySelectorAll('.closed-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                const timeSelector = row.querySelector('.time-selector');
                
                if (this.checked) {
                    timeSelector.classList.add('hidden');
                } else {
                    timeSelector.classList.remove('hidden');
                }
                
                updateHiddenInput(row);
            });
        });
        
        // Handle time input changes
        document.querySelectorAll('.time-input, .open-ampm, .close-ampm').forEach(input => {
            input.addEventListener('change', function() {
                const row = this.closest('tr');
                updateHiddenInput(row);
            });
            
            // Also update on keyup for text inputs
            if (input.tagName === 'INPUT') {
                input.addEventListener('keyup', function() {
                    const row = this.closest('tr');
                    updateHiddenInput(row);
                });
            }
        });
        
        // Initialize all rows
        document.querySelectorAll('tr').forEach(row => {
            if (row.querySelector('.closed-checkbox')) {
                updateHiddenInput(row);
            }
        });
    });
</script>
@endpush 