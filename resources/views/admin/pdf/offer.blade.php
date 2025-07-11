<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Offer: {{ $offer->title }}</title>
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
            <p>Offer Details</p>
            <p>{{ now()->format('F j, Y') }}</p>
        </div>
        
        <div class="section">
            <div class="section-title">Offer Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Offer Title:</div>
                        <div class="value">{{ $offer->title }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Offer Code:</div>
                        <div class="value">{{ $offer->code ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Start Date:</div>
                        <div class="value">{{ $offer->start_date ? $offer->start_date->format('F j, Y') : 'Always Active' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">End Date:</div>
                        <div class="value">{{ $offer->end_date ? $offer->end_date->format('F j, Y') : 'No End Date' }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Status:</div>
                        <div class="value">{{ $offer->active ? 'Active' : 'Inactive' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Featured:</div>
                        <div class="value">{{ $offer->is_featured ? 'Yes' : 'No' }}</div>
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
                        <div class="value">{{ \App\Helpers\SettingsHelper::formatPrice($offer->price) }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Discount:</div>
                        <div class="value">{{ $offer->discount_percent ? $offer->discount_percent . '%' : 'N/A' }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Current Stock:</div>
                        <div class="value">
                            {{ $offer->stock }} units
                            @if($offer->stock > $offer->low_stock_threshold)
                                <span class="stock-status stock-in">In Stock</span>
                            @elseif($offer->stock > 0)
                                <span class="stock-status stock-low">Low Stock</span>
                            @else
                                <span class="stock-status stock-out">Out of Stock</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Low Stock Threshold:</div>
                        <div class="value">{{ $offer->low_stock_threshold }} units</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Offer Description</div>
            <div class="description">
                {!! $offer->description !!}
            </div>
        </div>
        
        @if($offer->products && $offer->products->count() > 0)
        <div class="section">
            <div class="section-title">Products in Offer</div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Regular Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offer->products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category ? $product->category->name : 'Uncategorized' }}</td>
                            <td>{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</td>
                            <td>{{ $product->stock }} units</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <div class="footer">
            <p>This document was automatically generated on {{ now()->format('F j, Y, g:i A') }}.</p>
            <p>Celestial Cosmetics &copy; {{ date('Y') }} - All rights reserved.</p>
        </div>
    </div>
</body>
</html> 