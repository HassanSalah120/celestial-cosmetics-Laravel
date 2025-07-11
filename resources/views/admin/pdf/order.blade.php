<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Order #{{ $order->order_number }}</title>
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
        .address {
            margin-bottom: 15px;
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
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .total-row {
            margin: 5px 0;
        }
        .total-label {
            display: inline-block;
            width: 150px;
            text-align: right;
            margin-right: 10px;
        }
        .total-value {
            display: inline-block;
            width: 100px;
            text-align: right;
            font-weight: bold;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #2B5B6C;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 2px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Celestial Cosmetics</h1>
            <p>Order #{{ $order->order_number }}</p>
            <p>{{ $order->created_at->format('F j, Y, g:i A') }}</p>
        </div>
        
        <div class="section">
            <div class="section-title">Order Summary</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Order Status:</div>
                        <div class="value">{{ ucfirst($order->status) }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Payment Status:</div>
                        <div class="value">{{ ucfirst($order->payment_status) }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Payment Method:</div>
                        <div class="value">{{ ucfirst($order->payment_method) }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Shipping Method:</div>
                        <div class="value">{{ ucfirst($order->shipping_method) }}</div>
                    </div>
                </div>
                @if($order->tracking_number)
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Tracking Number:</div>
                        <div class="value">{{ $order->tracking_number }}</div>
                    </div>
                    <div class="info-cell"></div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Customer:</div>
                        <div class="value">
                            @if($order->user)
                                {{ $order->user->name }} ({{ $order->user->email }})
                            @else
                                Guest Customer
                            @endif
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Phone:</div>
                        <div class="value">
                            @if(is_array($order->shipping_address) && isset($order->shipping_address['phone']))
                                {{ $order->shipping_address['phone'] }}
                            @else
                                {{ $order->shipping_phone ?? 'N/A' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Shipping & Billing Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Shipping Address:</div>
                        <div class="value address">
                            @if(is_array($order->shipping_address))
                                {{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}<br>
                                {{ $order->shipping_address['address_line1'] ?? '' }}<br>
                                @if(isset($order->shipping_address['address_line2']) && $order->shipping_address['address_line2'])
                                    {{ $order->shipping_address['address_line2'] }}<br>
                                @endif
                                {{ $order->shipping_address['city'] ?? '' }}{{ isset($order->shipping_address['state']) && $order->shipping_address['state'] ? ', ' . $order->shipping_address['state'] : '' }} {{ $order->shipping_address['postal_code'] ?? '' }}<br>
                                {{ $order->shipping_address['country'] ?? '' }}
                            @else
                                {{ $order->shipping_first_name ?? '' }} {{ $order->shipping_last_name ?? '' }}<br>
                                {{ $order->shipping_address_line1 ?? '' }}<br>
                                @if($order->shipping_address_line2)
                                    {{ $order->shipping_address_line2 }}<br>
                                @endif
                                {{ $order->shipping_city ?? '' }}{{ $order->shipping_state ? ', ' . $order->shipping_state : '' }} {{ $order->shipping_postal_code ?? '' }}<br>
                                {{ $order->shipping_country ?? '' }}
                            @endif
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Billing Address:</div>
                        <div class="value address">
                            @if(is_array($order->billing_address))
                                {{ $order->billing_address['first_name'] ?? '' }} {{ $order->billing_address['last_name'] ?? '' }}<br>
                                {{ $order->billing_address['address_line1'] ?? '' }}<br>
                                @if(isset($order->billing_address['address_line2']) && $order->billing_address['address_line2'])
                                    {{ $order->billing_address['address_line2'] }}<br>
                                @endif
                                {{ $order->billing_address['city'] ?? '' }}{{ isset($order->billing_address['state']) && $order->billing_address['state'] ? ', ' . $order->billing_address['state'] : '' }} {{ $order->billing_address['postal_code'] ?? '' }}<br>
                                {{ $order->billing_address['country'] ?? '' }}
                            @else
                                Same as shipping address
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Order Items</div>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ ucfirst($item->type) }}</td>
                        <td>{{ \App\Helpers\SettingsHelper::formatPrice($item->price) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ \App\Helpers\SettingsHelper::formatPrice($item->subtotal) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="totals">
                <div class="total-row">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-value">{{ \App\Helpers\SettingsHelper::formatPrice($order->subtotal) }}</span>
                </div>
                
                @if(($order->discount_amount ?? 0) > 0)
                <div class="total-row">
                    <span class="total-label">Discount:</span>
                    <span class="total-value">-{{ \App\Helpers\SettingsHelper::formatPrice($order->discount_amount) }}</span>
                </div>
                @endif
                
                <div class="total-row">
                    <span class="total-label">Shipping:</span>
                    <span class="total-value">{{ \App\Helpers\SettingsHelper::formatPrice($order->shipping_fee) }}</span>
                </div>
                
                @if(($order->payment_fee ?? 0) > 0)
                <div class="total-row">
                    <span class="total-label">Payment Fee:</span>
                    <span class="total-value">{{ \App\Helpers\SettingsHelper::formatPrice($order->payment_fee) }}</span>
                </div>
                @endif
                
                @if(($order->cod_fee ?? 0) > 0)
                <div class="total-row">
                    <span class="total-label">COD Fee:</span>
                    <span class="total-value">{{ \App\Helpers\SettingsHelper::formatPrice($order->cod_fee) }}</span>
                </div>
                @endif
                
                <div class="total-row grand-total">
                    <span class="total-label">Total:</span>
                    <span class="total-value">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</span>
                </div>
            </div>
        </div>
        
        @if($order->notes)
        <div class="section">
            <div class="section-title">Order Notes</div>
            <p>{{ $order->notes }}</p>
        </div>
        @endif
        
        <div class="footer">
            <p>This document was automatically generated on {{ now()->format('F j, Y, g:i A') }}.</p>
            <p>Celestial Cosmetics &copy; {{ date('Y') }} - All rights reserved.</p>
        </div>
    </div>
</body>
</html> 