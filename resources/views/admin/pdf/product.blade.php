<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Product: {{ $product->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            color: #2B5B6C;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2B5B6C;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 5px 0;
            width: 50%;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        .stock-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .stock-in { background-color: #D4EDDA; color: #155724; }
        .stock-low { background-color: #FFF3CD; color: #856404; }
        .stock-out { background-color: #F8D7DA; color: #721C24; }
        .description {
            margin-top: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Celestial Cosmetics</h1>
            <p>Product Details</p>
            <p>{{ now()->format('F j, Y') }}</p>
        </div>
        
        <div class="section">
            <div class="section-title">Product Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Product Name:</div>
                        <div class="value">{{ $product->name }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">SKU:</div>
                        <div class="value">{{ $product->sku }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Category:</div>
                        <div class="value">{{ $product->category ? $product->category->name : 'Uncategorized' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Status:</div>
                        <div class="value">{{ $product->active ? 'Active' : 'Inactive' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Pricing & Inventory</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Regular Price:</div>
                        <div class="value">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Sale Price:</div>
                        <div class="value">{{ $product->sale_price ? \App\Helpers\SettingsHelper::formatPrice($product->sale_price) : 'N/A' }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Current Stock:</div>
                        <div class="value">
                            {{ $product->stock }} units
                            @if($product->stock > $product->low_stock_threshold)
                                <span class="stock-status stock-in">In Stock</span>
                            @elseif($product->stock > 0)
                                <span class="stock-status stock-low">Low Stock</span>
                            @else
                                <span class="stock-status stock-out">Out of Stock</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Low Stock Threshold:</div>
                        <div class="value">{{ $product->low_stock_threshold }} units</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Weight:</div>
                        <div class="value">{{ $product->weight ? $product->weight . ' ' . ($product->weight_unit ?? 'kg') : 'N/A' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Dimensions:</div>
                        <div class="value">
                            @if($product->length && $product->width && $product->height)
                                {{ $product->length }} x {{ $product->width }} x {{ $product->height }} {{ $product->dimension_unit ?? 'cm' }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Product Description</div>
            <div class="description">
                {!! $product->description !!}
            </div>
        </div>
        
        @if($product->short_description)
        <div class="section">
            <div class="section-title">Short Description</div>
            <div class="description">
                {!! $product->short_description !!}
            </div>
        </div>
        @endif
        
        <div class="footer">
            <p>This document was automatically generated on {{ now()->format('F j, Y, g:i A') }}.</p>
            <p>Celestial Cosmetics &copy; {{ date('Y') }} - All rights reserved.</p>
        </div>
    </div>
</body>
</html> 