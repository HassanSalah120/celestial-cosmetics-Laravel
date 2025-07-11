@extends('layouts.admin')

@section('content')
    <div class="pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">Currency Settings</h1>
            
            @include('admin.partials.alerts')
            
            <div class="bg-white shadow-sm rounded-lg">
                <form action="{{ route('admin.settings.currency.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Currency Code -->
                        <div>
                            <label for="currency_code" class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                            <input type="text" name="currency_code" id="currency_code" 
                                value="{{ $currencyConfig->currency_code ?? old('currency_code', 'EGP') }}" 
                                maxlength="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            <p class="mt-1 text-xs text-gray-500">Standard 3-letter code (e.g., USD, EUR, EGP)</p>
                        </div>
                        
                        <!-- Currency Symbol -->
                        <div>
                            <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                            <input type="text" name="currency_symbol" id="currency_symbol" 
                                value="{{ $currencyConfig->currency_symbol ?? old('currency_symbol', 'ج.م') }}" 
                                maxlength="5"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            <p class="mt-1 text-xs text-gray-500">Symbol to display (e.g., $, €, ج.م)</p>
                        </div>
                        
                        <!-- Currency Position -->
                        <div>
                            <label for="currency_position" class="block text-sm font-medium text-gray-700 mb-1">Currency Position</label>
                            <select name="currency_position" id="currency_position" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                <option value="before" {{ isset($currencyConfig->currency_position) && $currencyConfig->currency_position == 'before' ? 'selected' : '' }}>
                                    Before Amount ($100)
                                </option>
                                <option value="after" {{ isset($currencyConfig->currency_position) && $currencyConfig->currency_position == 'after' ? 'selected' : '' }}>
                                    After Amount (100$)
                                </option>
                            </select>
                        </div>
                        
                        <!-- Decimal Places -->
                        <div>
                            <label for="decimal_digits" class="block text-sm font-medium text-gray-700 mb-1">Decimal Places</label>
                            <select name="decimal_digits" id="decimal_digits" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                <option value="0" {{ isset($currencyConfig->decimal_digits) && $currencyConfig->decimal_digits == 0 ? 'selected' : '' }}>
                                    0 (100)
                                </option>
                                <option value="1" {{ isset($currencyConfig->decimal_digits) && $currencyConfig->decimal_digits == 1 ? 'selected' : '' }}>
                                    1 (100.5)
                                </option>
                                <option value="2" {{ isset($currencyConfig->decimal_digits) && $currencyConfig->decimal_digits == 2 ? 'selected' : '' }}>
                                    2 (100.50)
                                </option>
                                <option value="3" {{ isset($currencyConfig->decimal_digits) && $currencyConfig->decimal_digits == 3 ? 'selected' : '' }}>
                                    3 (100.500)
                                </option>
                            </select>
                        </div>
                        
                        <!-- Decimal Separator -->
                        <div>
                            <label for="decimal_separator" class="block text-sm font-medium text-gray-700 mb-1">Decimal Separator</label>
                            <select name="decimal_separator" id="decimal_separator" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                <option value="." {{ isset($currencyConfig->decimal_separator) && $currencyConfig->decimal_separator == '.' ? 'selected' : '' }}>
                                    Period (100.50)
                                </option>
                                <option value="," {{ isset($currencyConfig->decimal_separator) && $currencyConfig->decimal_separator == ',' ? 'selected' : '' }}>
                                    Comma (100,50)
                                </option>
                            </select>
                        </div>
                        
                        <!-- Thousands Separator -->
                        <div>
                            <label for="thousand_separator" class="block text-sm font-medium text-gray-700 mb-1">Thousands Separator</label>
                            <select name="thousand_separator" id="thousand_separator" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                                <option value="," {{ isset($currencyConfig->thousand_separator) && $currencyConfig->thousand_separator == ',' ? 'selected' : '' }}>
                                    Comma (1,000.50)
                                </option>
                                <option value="." {{ isset($currencyConfig->thousand_separator) && $currencyConfig->thousand_separator == '.' ? 'selected' : '' }}>
                                    Period (1.000,50)
                                </option>
                                <option value=" " {{ isset($currencyConfig->thousand_separator) && $currencyConfig->thousand_separator == ' ' ? 'selected' : '' }}>
                                    Space (1 000.50)
                                </option>
                                <option value="" {{ isset($currencyConfig->thousand_separator) && $currencyConfig->thousand_separator == '' ? 'selected' : '' }}>
                                    None (1000.50)
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                        <div class="p-6 bg-gray-50 rounded-md border border-gray-200">
                            <div class="flex flex-col space-y-4">
                                <div class="flex items-center">
                                    <span class="w-32 text-sm text-gray-500">Small Amount:</span>
                                    <span id="previewSmall" class="text-lg font-medium text-gray-800"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-32 text-sm text-gray-500">Medium Amount:</span>
                                    <span id="previewMedium" class="text-lg font-medium text-gray-800"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-32 text-sm text-gray-500">Large Amount:</span>
                                    <span id="previewLarge" class="text-lg font-medium text-gray-800"></span>
                                </div>
                            </div>
                            <p class="mt-4 text-sm text-gray-500">This is how your prices will be displayed on the website.</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currencySymbol = document.getElementById('currency_symbol');
            const currencyPosition = document.getElementById('currency_position');
            const decimalDigits = document.getElementById('decimal_digits');
            const decimalSeparator = document.getElementById('decimal_separator');
            const thousandSeparator = document.getElementById('thousand_separator');
            const previewSmall = document.getElementById('previewSmall');
            const previewMedium = document.getElementById('previewMedium');
            const previewLarge = document.getElementById('previewLarge');
            
            function formatCurrency(amount, symbol, position, decDigits, decSep, thouSep) {
                // Format the number part
                let parts = amount.toFixed(decDigits).split('.');
                let integerPart = parts[0];
                let decimalPart = parts.length > 1 ? parts[1] : '';
                
                // Add thousand separators
                if (thouSep) {
                    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thouSep);
                }
                
                // Combine integer and decimal parts
                let formattedNumber = integerPart;
                if (decDigits > 0) {
                    formattedNumber += decSep + decimalPart;
                }
                
                // Add currency symbol based on position
                if (position === 'before') {
                    return symbol + ' ' + formattedNumber;
                } else {
                    return formattedNumber + ' ' + symbol;
                }
            }
            
            function updatePreview() {
                const symbol = currencySymbol.value;
                const position = currencyPosition.value;
                const digits = parseInt(decimalDigits.value);
                const decSep = decimalSeparator.value;
                const thouSep = thousandSeparator.value;
                
                // Update previews with different amounts
                previewSmall.textContent = formatCurrency(99.99, symbol, position, digits, decSep, thouSep);
                previewMedium.textContent = formatCurrency(1234.56, symbol, position, digits, decSep, thouSep);
                previewLarge.textContent = formatCurrency(9876543.21, symbol, position, digits, decSep, thouSep);
                }
            
            // Update preview when any setting changes
            currencySymbol.addEventListener('input', updatePreview);
            currencyPosition.addEventListener('change', updatePreview);
            decimalDigits.addEventListener('change', updatePreview);
            decimalSeparator.addEventListener('change', updatePreview);
            thousandSeparator.addEventListener('change', updatePreview);
            
            // Initialize preview on page load
            updatePreview();
        });
    </script>
    @endpush
@endsection

