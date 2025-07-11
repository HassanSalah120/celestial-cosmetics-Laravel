<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update on Your Order - Celestial Cosmetics</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f8fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background: linear-gradient(to right, #2B5B6C, #1A3F4C);
            color: white;
            border-radius: 4px 4px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
            border-radius: 0 0 4px 4px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #718096;
        }
        h1 {
            color: white;
            font-size: 24px;
            margin: 0;
        }
        h2 {
            color: #2B5B6C;
            font-size: 20px;
            margin-top: 0;
        }
        p {
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #D4AF37;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #E5C65C;
        }
        .update-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f8fafc;
        }
        .status-label {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status-processing {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        .status-shipped {
            background-color: #E0E7FF;
            color: #3730A3;
        }
        .status-delivered {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-cancelled {
            background-color: #FEE2E2;
            color: #B91C1C;
        }
        .status-paid {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status-refunded {
            background-color: #E0E7FF;
            color: #3730A3;
        }
        .status-failed {
            background-color: #FEE2E2;
            color: #B91C1C;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Update</h1>
        </div>
        <div class="content">
            <h2>Hello {{ isset($order->shipping_address['first_name']) ? $order->shipping_address['first_name'] : 'Valued Customer' }},</h2>
            
            <p>We're writing to let you know that there has been an update to your order #{{ $order->id }}.</p>
            
            <div class="update-box">
                @php
                    $showTrackingInStatus = false;
                @endphp
                
                @if(in_array('status', $changedFields))
                    <p><strong>Order Status:</strong> Changed from 
                        <span class="status-label status-{{ $oldValues['status'] }}">{{ ucfirst($oldValues['status']) }}</span> 
                        to 
                        <span class="status-label status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </p>
                    
                    @if($order->status == 'shipped')
                        <p>Your order is on its way! 
                        @if($order->tracking_number)
                            You can track your package using the tracking number below.
                        @endif
                        </p>
                        
                        @if($order->tracking_number)
                            @php $showTrackingInStatus = true; @endphp
                            <div style="margin: 15px 0; padding: 15px; background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 6px; text-align: center;">
                                <p style="margin: 0 0 10px 0; font-weight: bold; color: #1E40AF;">TRACKING NUMBER</p>
                                <p style="font-size: 18px; background-color: #DBEAFE; padding: 8px; display: inline-block; margin: 0; border-radius: 4px; font-family: monospace; letter-spacing: 1px;">{{ $order->tracking_number }}</p>
                                <p style="margin: 10px 0 0 0; font-size: 12px; color: #4B5563;">Use this number to track your package on the carrier's website</p>
                            </div>
                            <p style="font-size: 14px; color: #4B5563; margin-top: 15px;">
                                <strong>Shipping Address:</strong><br>
                                {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}<br>
                                {{ $order->shipping_address['address_line1'] }}<br>
                                @if(isset($order->shipping_address['address_line2']) && !empty($order->shipping_address['address_line2']))
                                    {{ $order->shipping_address['address_line2'] }}<br>
                                @endif
                                {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['postal_code'] }}<br>
                                {{ $order->shipping_address['country'] }}
                            </p>
                        @endif
                    @elseif($order->status == 'delivered')
                        <p>Great news! Your order has been delivered. We hope you enjoy your products!</p>
                    @elseif($order->status == 'cancelled')
                        <p>Your order has been cancelled. If you have any questions, please contact our customer service.</p>
                    @endif
                @endif
                
                @if(in_array('payment_status', $changedFields))
                    <p><strong>Payment Status:</strong> Changed from 
                        <span class="status-label status-{{ $oldValues['payment_status'] }}">{{ ucfirst($oldValues['payment_status']) }}</span> 
                        to 
                        <span class="status-label status-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
                    </p>
                    
                    @if($order->payment_status == 'paid')
                        <p>Thank you for your payment! Your order is now being processed.</p>
                    @elseif($order->payment_status == 'refunded')
                        <p>Your payment has been refunded. The amount should appear in your account within a few business days.</p>
                    @endif
                @endif
                
                @if(in_array('tracking_number', $changedFields) && $order->tracking_number && !$showTrackingInStatus)
                    <p><strong>Tracking Information:</strong> A tracking number has been added to your order:</p>
                    <div style="margin: 15px 0; padding: 15px; background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 6px; text-align: center;">
                        <p style="margin: 0 0 10px 0; font-weight: bold; color: #1E40AF;">TRACKING NUMBER</p>
                        <p style="font-size: 18px; background-color: #DBEAFE; padding: 8px; display: inline-block; margin: 0; border-radius: 4px; font-family: monospace; letter-spacing: 1px;">{{ $order->tracking_number }}</p>
                        <p style="margin: 10px 0 0 0; font-size: 12px; color: #4B5563;">Use this number to track your package on the carrier's website</p>
                    </div>
                @endif
            </div>
            
            <h3>Order Summary</h3>
            <p><strong>Order ID:</strong> #{{ $order->order_number ?? $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y') }}</p>
            
            @php
                $codFee = $order->cod_fee ?? 0;
                $subtotal = $order->subtotal ?? $order->total_amount - $codFee ?? 0;
            @endphp
            
            @if($codFee > 0)
                <p><strong>Subtotal:</strong> {{ \App\Helpers\SettingsHelper::formatPrice($subtotal) }}</p>
                <p><strong>Cash on Delivery Fee:</strong> {{ \App\Helpers\SettingsHelper::formatPrice($codFee) }}</p>
            @endif
            <p><strong>Total Amount:</strong> {{ \App\Helpers\SettingsHelper::formatPrice($order->total ?? $order->total_amount ?? 0) }}</p>
            
            <p>
                <a href="{{ route('orders.show', $order->order_number ?? $order->id) }}" class="btn">View Order Details</a>
            </p>
            
            <p>Thank you for shopping with Celestial Cosmetics. If you have any questions about your order, please don't hesitate to contact us.</p>
            
            <p>Best regards,<br>The Celestial Cosmetics Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Celestial Cosmetics. All rights reserved.</p>
            <p>This email was sent to {{ isset($order->shipping_address['email']) ? $order->shipping_address['email'] : 'you' }}</p>
        </div>
    </div>
</body>
</html> 