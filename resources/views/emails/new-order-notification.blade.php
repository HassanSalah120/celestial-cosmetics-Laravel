<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Notification - Celestial Cosmetics</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            background-color: #f5f8fa;
            margin: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #2B5B6C;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f2f2f2;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .order-summary {
            margin: 20px 0;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }
        .order-summary-header {
            background-color: #f5f5f5;
            padding: 10px 15px;
            font-weight: bold;
            border-bottom: 1px solid #e0e0e0;
        }
        .order-summary-content {
            padding: 15px;
        }
        .customer-details, .order-details {
            margin-bottom: 20px;
        }
        .customer-details p, .order-details p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background-color: #D4AF37;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border-bottom: 1px solid #e0e0e0;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .total-row {
            font-weight: bold;
        }
        .alert-message {
            background-color: #fdedea;
            border-left: 4px solid #d73a49;
            padding: 10px 15px;
            margin: 20px 0;
            color: #24292e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Order Received</h1>
        </div>
        <div class="content">
            <p>A new order has been placed on Celestial Cosmetics website.</p>
            
            <div class="order-summary">
                <div class="order-summary-header">
                    Order #{{ $order->id }} - {{ $order->created_at->format('F j, Y, g:i a') }}
                </div>
                <div class="order-summary-content">
                    <div class="customer-details">
                        <h3>Customer Information:</h3>
                        <p><strong>Name:</strong> {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}</p>
                        <p><strong>Email:</strong> {{ $order->shipping_address['email'] }}</p>
                        <p><strong>Phone:</strong> {{ $order->shipping_address['phone'] }}</p>
                    </div>
                    
                    <div class="order-details">
                        <h3>Payment Information:</h3>
                        <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                        <p><strong>Total Amount:</strong> {{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ number_format($order->total_amount, 2) }}</p>
                        
                        <h3>Shipping Address:</h3>
                        <p>{{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}<br>
                        {{ $order->shipping_address['address_line1'] }}<br>
                        @if(isset($order->shipping_address['address_line2']) && !empty($order->shipping_address['address_line2']))
                            {{ $order->shipping_address['address_line2'] }}<br>
                        @endif
                        {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['postal_code'] }}<br>
                        {{ $order->shipping_address['country'] }}<br>
                        Phone: {{ $order->shipping_address['phone'] }}</p>
                        
                        <h3>Billing Address:</h3>
                        <p>{{ $order->billing_address['first_name'] }} {{ $order->billing_address['last_name'] }}<br>
                        {{ $order->billing_address['address_line1'] }}<br>
                        @if(isset($order->billing_address['address_line2']) && !empty($order->billing_address['address_line2']))
                            {{ $order->billing_address['address_line2'] }}<br>
                        @endif
                        {{ $order->billing_address['city'] }}, {{ $order->billing_address['state'] }} {{ $order->billing_address['postal_code'] }}<br>
                        {{ $order->billing_address['country'] }}<br>
                        Phone: {{ $order->billing_address['phone'] }}</p>
                    </div>
                    
                    <h3>Order Items:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product ? $item->product->name : 'Product Not Available' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ number_format($item->price, 2) }}</td>
                                <td>{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                            @if($order->cod_fee > 0)
                            <tr>
                                <td colspan="3" style="text-align: right;">Subtotal:</td>
                                <td>{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ number_format($order->total_amount - $order->cod_fee, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align: right;">Cash on Delivery Fee:</td>
                                <td>{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ number_format($order->cod_fee, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;">Total:</td>
                                <td>{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    @if($order->payment_method == 'cod')
                    <div class="alert-message">
                        <strong>Action Required:</strong> This order is using Cash on Delivery payment method. Please prepare for collection upon delivery.
                        @if($order->cod_fee > 0)
                        <br>A Cash on Delivery fee of {{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ number_format($order->cod_fee, 2) }} has been added to the order total.
                        @endif
                    </div>
                    @elseif($order->payment_method == 'instapay' && $order->payment_status == 'pending')
                    <div class="alert-message">
                        <strong>Action Required:</strong> This order is using Instapay and payment is pending. Please verify payment receipt before processing.
                    </div>
                    @endif
                    
                    <p>Please process this order from the admin dashboard:</p>
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="button">Manage Order</a>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Celestial Cosmetics. All rights reserved.</p>
            <p>This is an automated notification from the Celestial Cosmetics system.</p>
        </div>
    </div>
</body>
</html> 