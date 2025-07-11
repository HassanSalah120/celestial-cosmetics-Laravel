<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Celestial Cosmetics</title>
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
        .order-details {
            margin-bottom: 20px;
        }
        .order-details p {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Order!</h1>
        </div>
        <div class="content">
            <p>Hello {{ isset($order->shipping_address['first_name']) ? $order->shipping_address['first_name'] : '' }} {{ isset($order->shipping_address['last_name']) ? $order->shipping_address['last_name'] : '' }},</p>
            
            <p>We're excited to confirm that we have received your order. Our team will be reaching out to you soon to coordinate delivery.</p>
            
            <div class="order-summary">
                <div class="order-summary-header">
                    Order #{{ $order->order_number ?? $order->id }} - {{ $order->created_at->format('F j, Y, g:i a') }}
                </div>
                <div class="order-summary-content">
                    <div class="order-details">
                        <h3>Payment Information:</h3>
                        <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                        
                        @if($order->payment_method == 'instapay' && $order->payment_status == 'pending')
                        <p><strong>Payment Instructions:</strong> Please complete your payment using the following details:</p>
                        <p>Account Name: Celestial Cosmetics<br>
                        Account Number: 1234-5678-9012-3456<br>
                        Reference: Order #{{ $order->order_number ?? $order->id }}</p>
                        @endif
                        
                        @if($order->payment_method == 'vodafone' && $order->payment_status == 'pending')
                        <p><strong>Payment Instructions:</strong> Please complete your payment using the following details:</p>
                        <p>Vodafone Cash Number: {{ \App\Helpers\SettingsHelper::get('vodafone_cash_number', 'Contact Customer Service') }}<br>
                        Reference: Order #{{ $order->order_number ?? $order->id }}</p>
                        @endif
                        
                        <h3>Shipping Address:</h3>
                        @if(is_array($order->shipping_address))
                        <p>{{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}<br>
                        {{ $order->shipping_address['address_line1'] ?? '' }}<br>
                        @if(isset($order->shipping_address['address_line2']) && !empty($order->shipping_address['address_line2']))
                            {{ $order->shipping_address['address_line2'] }}<br>
                        @endif
                        {{ $order->shipping_address['city'] ?? '' }}{{ isset($order->shipping_address['state']) && !empty($order->shipping_address['state']) ? ', ' . $order->shipping_address['state'] : '' }} {{ $order->shipping_address['postal_code'] ?? '' }}<br>
                        {{ $order->shipping_address['country'] ?? '' }}<br>
                        Phone: {{ $order->shipping_address['phone'] ?? '' }}</p>
                        @else
                        <p>{{ $order->shipping_first_name ?? '' }} {{ $order->shipping_last_name ?? '' }}<br>
                        {{ $order->shipping_address_line1 ?? '' }}<br>
                        @if($order->shipping_address_line2)
                            {{ $order->shipping_address_line2 }}<br>
                        @endif
                        {{ $order->shipping_city ?? '' }}{{ $order->shipping_state ? ', ' . $order->shipping_state : '' }} {{ $order->shipping_postal_code ?? '' }}<br>
                        {{ $order->shipping_country ?? '' }}<br>
                        Phone: {{ $order->shipping_phone ?? '' }}</p>
                        @endif
                    </div>
                    
                    <h3>Order Summary:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                @if($item->type === 'product')
                                <td>{{ $item->product ? $item->product->name : $item->name }}</td>
                                <td>Product</td>
                                @elseif($item->type === 'offer')
                                <td>{{ $item->offer ? $item->offer->title : $item->name }}</td>
                                <td>Offer</td>
                                @else
                                <td>{{ $item->name }}</td>
                                <td>{{ ucfirst($item->type) }}</td>
                                @endif
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($item->price) }}</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($item->subtotal) }}</td>
                            </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="4" style="text-align: right;">Subtotal:</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($order->subtotal ?? 0) }}</td>
                            </tr>
                            @php
                                $discount = $order->discount ?? $order->discount_amount ?? 0;
                            @endphp
                            @if($discount > 0)
                            <tr>
                                <td colspan="4" style="text-align: right;">Discount:</td>
                                <td>-{{ \App\Helpers\SettingsHelper::formatPrice($discount) }}</td>
                            </tr>
                            @endif
                            @php
                                $shippingCost = $order->shipping_cost ?? $order->shipping_fee ?? 0;
                            @endphp
                            <tr>
                                <td colspan="4" style="text-align: right;">Shipping:</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($shippingCost) }}</td>
                            </tr>
                            @if(($order->payment_fee ?? 0) > 0)
                            <tr>
                                <td colspan="4" style="text-align: right;">Payment Fee:</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($order->payment_fee) }}</td>
                            </tr>
                            @endif
                            @if(($order->cod_fee ?? 0) > 0)
                            <tr>
                                <td colspan="4" style="text-align: right;">Cash on Delivery Fee:</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($order->cod_fee) }}</td>
                            </tr>
                            @endif
                            <tr class="total-row">
                                <td colspan="4" style="text-align: right;">Total:</td>
                                <td>{{ \App\Helpers\SettingsHelper::formatPrice($order->total ?? $order->total_amount ?? 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p>You can view your order details and track its status by clicking the button below:</p>
                    <a href="{{ route('orders.show', $order->order_number ?? $order->id) }}" class="button">View Order Details</a>
                </div>
            </div>
            
            <p>If you have any questions or concerns about your order, please don't hesitate to contact our customer service team.</p>
            
            <p>Thank you for choosing Celestial Cosmetics!</p>
            
            <p>Best regards,<br>
            The Celestial Cosmetics Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Celestial Cosmetics. All rights reserved.</p>
            <p>This email was sent to {{ is_array($order->shipping_address) && isset($order->shipping_address['email']) ? $order->shipping_address['email'] : $order->shipping_email ?? 'you' }}</p>
        </div>
    </div>
</body>
</html> 