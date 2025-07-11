<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label - Order #{{ $order->id }}</title>
    <style>
        @media print {
            @page {
                size: 4in 6in;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        
        .container {
            width: 4in;
            height: 6in;
            padding: 0.2in;
            box-sizing: border-box;
            border: 1px dashed #ccc;
            page-break-after: always;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 0.1in;
            margin-bottom: 0.1in;
        }
        
        .logo {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 0.05in;
        }
        
        .order-info {
            font-size: 9pt;
            margin-bottom: 0.1in;
        }
        
        .section {
            margin-bottom: 0.1in;
        }
        
        .tracking {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 0.1in 0;
        }
        
        .tracking-number {
            font-family: monospace;
            letter-spacing: 1px;
            background-color: #f0f0f0;
            padding: 0.05in;
            border-radius: 0.05in;
            display: inline-block;
        }
        
        .address-box {
            border: 1px solid #000;
            padding: 0.1in;
            margin-bottom: 0.1in;
        }
        
        .address-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            margin-bottom: 0.05in;
        }
        
        .address {
            margin-bottom: 0.05in;
        }
        
        .qr-code {
            text-align: center;
            margin: 0.1in 0;
        }
        
        .label-info {
            font-size: 7pt;
            text-align: center;
            margin-top: 0.1in;
        }
        
        .print-btn {
            background-color: #2B5B6C;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 20px 0;
        }
        
        .footer {
            font-size: 7pt;
            text-align: center;
            margin-top: 0.1in;
            border-top: 1px solid #ccc;
            padding-top: 0.05in;
        }
        
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 20px;">
        <h1>Shipping Label - Order #{{ $order->id }}</h1>
        <button class="print-btn" onclick="window.print()">Print Shipping Label</button>
        <p><a href="{{ route('admin.orders.show', $order) }}">← Back to Order Details</a></p>
    </div>

    <div class="container">
        <div class="header">
            <div class="logo">CELESTIAL COSMETICS</div>
            <div class="order-info">
                <span class="bold">Order:</span> #{{ $order->id }} | 
                <span class="bold">Date:</span> {{ $order->created_at->format('m/d/Y') }} | 
                <span class="bold">Payment:</span> {{ ucfirst($order->payment_status) }}
            </div>
        </div>
        
        @if($order->tracking_number)
        <div class="tracking">
            <div>TRACKING NUMBER</div>
            <div class="tracking-number">{{ $order->tracking_number }}</div>
        </div>
        @endif
        
        <div class="section">
            <div class="address-box">
                <div class="address-title">Ship To:</div>
                <div class="address">
                    <div class="bold">{{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}</div>
                    <div>{{ $order->shipping_address['address_line1'] }}</div>
                    @if(isset($order->shipping_address['address_line2']) && !empty($order->shipping_address['address_line2']))
                        <div>{{ $order->shipping_address['address_line2'] }}</div>
                    @endif
                    <div>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['postal_code'] }}</div>
                    <div>{{ $order->shipping_address['country'] }}</div>
                    <div>Phone: {{ $order->shipping_address['phone'] }}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="address-box">
                <div class="address-title">From:</div>
                <div class="address">
                    <div class="bold">Celestial Cosmetics</div>
                    <div>123 Starlight Avenue</div>
                    <div>Stellar Heights, CA 90210</div>
                    <div>United States</div>
                    <div>support@celestialcosmetics.com</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="address-box">
                <div class="address-title">Package Information:</div>
                <div class="address">
                    <div><span class="bold">Items:</span> {{ $order->items->sum('quantity') }}</div>
                    <div><span class="bold">Total:</span> {{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</div>
                    <div><span class="bold">Shipping:</span> Standard</div>
                    <div><span class="bold">Weight:</span> {{ $order->items->sum('quantity') * 0.25 }} lbs (est.)</div>
                </div>
            </div>
        </div>
        
        @if($order->tracking_number)
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($order->tracking_number) }}" width="100" height="100" alt="QR Code">
        </div>
        @endif
        
        <div class="footer">
            Order #{{ $order->id }} | {{ $order->created_at->format('m/d/Y') }} | Celestial Cosmetics
        </div>
    </div>
</body>
</html> 