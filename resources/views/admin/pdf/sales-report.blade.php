<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sales Report</title>
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
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 5px 10px;
            width: 33%;
            vertical-align: top;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
            font-size: 9px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #2B5B6C;
            margin-top: 5px;
        }
        .summary-subtext {
            font-size: 8px;
            color: #999;
            margin-top: 2px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .status-completed { background-color: #D4EDDA; color: #155724; }
        .status-shipped { background-color: #D1ECF1; color: #0C5460; }
        .status-processing { background-color: #FFF3CD; color: #856404; }
        .status-cancelled { background-color: #F8D7DA; color: #721C24; }
        .status-pending { background-color: #E2E3E5; color: #383D41; }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Celestial Cosmetics</h1>
            <p>Sales Report</p>
            <p>Period: {{ $data['startDate']->format('F j, Y') }} - {{ $data['endDate']->format('F j, Y') }}</p>
            <p>Generated on: {{ $data['generatedAt']->format('F j, Y, g:i A') }}</p>
        </div>
        
        <div class="section">
            <div class="summary-box">
                <div class="summary-grid">
                    <div class="summary-row">
                        <div class="summary-cell">
                            <div class="summary-label">Total Revenue</div>
                            <div class="summary-value">{{ \App\Helpers\SettingsHelper::formatPrice($data['totalRevenue']) }}</div>
                            <div class="summary-subtext">{{ $data['totalOrders'] }} orders · Avg. {{ \App\Helpers\SettingsHelper::formatPrice($data['averageOrderValue']) }}</div>
                        </div>
                        <div class="summary-cell">
                            <div class="summary-label">Items Sold</div>
                            <div class="summary-value">{{ $data['orders']->sum(function($order) { return $order->items->sum('quantity'); }) }}</div>
                            <div class="summary-subtext">Across {{ $data['orders']->count() }} orders</div>
                        </div>
                        <div class="summary-cell">
                            <div class="summary-label">Order Status</div>
                            <div class="summary-value">
                                @php
                                    $statusCounts = $data['orders']->groupBy('status')->map->count();
                                    $topStatus = $statusCounts->sortDesc()->keys()->first() ?? 'N/A';
                                @endphp
                                {{ ucfirst($topStatus) }}
                            </div>
                            <div class="summary-subtext">Most common status</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="section">
            <div class="section-title">Orders in Period</div>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['orders'] as $order)
                        <tr>
                            <td>#{{ $order->order_number ?? $order->id }}</td>
                            <td>{{ $order->created_at->format('M d, Y g:i A') }}</td>
                            <td>{{ $order->user ? $order->user->name : 'Guest' }}</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->items->sum('quantity') }}</td>
                            <td>{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount ?? 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="page-break"></div>
        
        <!-- Top Products -->
        <div class="section">
            <div class="section-title">Top Selling Products</div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $productSales = collect();
                        foreach($data['orders'] as $order) {
                            foreach($order->items as $item) {
                                if($item->type == 'product' && $item->product) {
                                    $productId = $item->product->id;
                                    if(!$productSales->has($productId)) {
                                        $productSales = $productSales->put($productId, [
                                            'name' => $item->product->name,
                                            'category' => $item->product->category ? $item->product->category->name : 'Uncategorized',
                                            'quantity' => 0,
                                            'revenue' => 0
                                        ]);
                                    }
                                    $currentProduct = $productSales->get($productId);
                                    $currentProduct['quantity'] += $item->quantity;
                                    $currentProduct['revenue'] += $item->subtotal;
                                    $productSales = $productSales->put($productId, $currentProduct);
                                }
                            }
                        }
                        $topProducts = $productSales->sortByDesc('revenue')->take(10);
                    @endphp
                    
                    @forelse($topProducts as $product)
                        <tr>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['category'] }}</td>
                            <td>{{ $product['quantity'] }}</td>
                            <td>{{ \App\Helpers\SettingsHelper::formatPrice($product['revenue']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No product sales in this period</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Top Offers -->
        <div class="section">
            <div class="section-title">Top Selling Offers</div>
            <table>
                <thead>
                    <tr>
                        <th>Offer</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $offerSales = collect();
                        foreach($data['orders'] as $order) {
                            foreach($order->items as $item) {
                                if($item->type == 'offer' && $item->offer) {
                                    $offerId = $item->offer->id;
                                    if(!$offerSales->has($offerId)) {
                                        $offerSales = $offerSales->put($offerId, [
                                            'title' => $item->offer->title,
                                            'quantity' => 0,
                                            'revenue' => 0
                                        ]);
                                    }
                                    $currentOffer = $offerSales->get($offerId);
                                    $currentOffer['quantity'] += $item->quantity;
                                    $currentOffer['revenue'] += $item->subtotal;
                                    $offerSales = $offerSales->put($offerId, $currentOffer);
                                }
                            }
                        }
                        $topOffers = $offerSales->sortByDesc('revenue')->take(10);
                    @endphp
                    
                    @forelse($topOffers as $offer)
                        <tr>
                            <td>{{ $offer['title'] }}</td>
                            <td>{{ $offer['quantity'] }}</td>
                            <td>{{ \App\Helpers\SettingsHelper::formatPrice($offer['revenue']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No offer sales in this period</td>
                        </tr>
                    @endforelse
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