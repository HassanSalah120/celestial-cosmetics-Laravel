<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inventory Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table td {
            padding: 6px;
            border-bottom: 1px solid #eee;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .stock-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .stock-in { background-color: #D4EDDA; color: #155724; }
        .stock-low { background-color: #FFF3CD; color: #856404; }
        .stock-out { background-color: #F8D7DA; color: #721C24; }
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Celestial Cosmetics</h1>
            <p>Inventory Report</p>
            <p>Generated on: {{ $data['generatedAt']->format('F j, Y, g:i A') }}</p>
        </div>
        
        <div class="section">
            <div class="summary-box">
                <div class="summary-item">
                    <span class="summary-label">Total Products:</span>
                    <span>{{ $data['products']->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Low Stock Products:</span>
                    <span>{{ $data['lowStockProducts']->count() }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Out of Stock Products:</span>
                    <span>{{ $data['outOfStockProducts']->count() }}</span>
                </div>
            </div>
        </div>
        
        <!-- Low Stock Products -->
        <div class="section">
            <div class="section-title">Low Stock Products</div>
            @if($data['lowStockProducts']->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['lowStockProducts'] as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category ? $product->category->name : 'Uncategorized' }}</td>
                                <td>
                                    {{ $product->stock }}
                                    <span class="stock-status stock-low">Low</span>
                                </td>
                                <td>{{ $product->low_stock_threshold }}</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No products are currently low in stock.</p>
            @endif
        </div>
        
        <!-- Out of Stock Products -->
        <div class="section">
            <div class="section-title">Out of Stock Products</div>
            @if($data['outOfStockProducts']->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Threshold</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['outOfStockProducts'] as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category ? $product->category->name : 'Uncategorized' }}</td>
                                <td>{{ $product->low_stock_threshold }}</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No products are currently out of stock.</p>
            @endif
        </div>
        
        <div class="page-break"></div>
        
        <!-- All Products -->
        <div class="section">
            <div class="section-title">All Products Inventory</div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Threshold</th>
                        <th>Status</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['products'] as $product)
                        @php
                            $stockStatus = '';
                            $stockClass = '';
                            
                            if($product->stock <= 0) {
                                $stockStatus = 'Out of Stock';
                                $stockClass = 'stock-out';
                            } elseif($product->stock <= $product->low_stock_threshold) {
                                $stockStatus = 'Low Stock';
                                $stockClass = 'stock-low';
                            } else {
                                $stockStatus = 'In Stock';
                                $stockClass = 'stock-in';
                            }
                        @endphp
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category ? $product->category->name : 'Uncategorized' }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ $product->low_stock_threshold }}</td>
                            <td><span class="stock-status {{ $stockClass }}">{{ $stockStatus }}</span></td>
                            <td>{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <p>This report was automatically generated on {{ $data['generatedAt']->format('F j, Y, g:i A') }}.</p>
            <p>Celestial Cosmetics &copy; {{ date('Y') }} - All rights reserved.</p>
        </div>
    </div>
</body>
</html> 