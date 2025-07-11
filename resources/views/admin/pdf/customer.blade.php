<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Customer: {{ $user->name }}</title>
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
            <p>Customer Profile</p>
            <p>{{ now()->format('F j, Y') }}</p>
        </div>
        
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Name:</div>
                        <div class="value">{{ $user->name }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Email:</div>
                        <div class="value">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Phone:</div>
                        <div class="value">{{ $user->phone ?? 'Not provided' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Customer Since:</div>
                        <div class="value">{{ $user->created_at->format('F j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Order Summary</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Total Orders:</div>
                        <div class="value">{{ $user->orders->count() }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Total Spent:</div>
                        <div class="value">{{ \App\Helpers\SettingsHelper::formatPrice($user->orders->sum('total_amount')) }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-cell">
                        <div class="label">Average Order Value:</div>
                        <div class="value">
                            @if($user->orders->count() > 0)
                                {{ \App\Helpers\SettingsHelper::formatPrice($user->orders->sum('total_amount') / $user->orders->count()) }}
                            @else
                                {{ \App\Helpers\SettingsHelper::formatPrice(0) }}
                            @endif
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="label">Last Order Date:</div>
                        <div class="value">
                            @if($user->orders->count() > 0)
                                {{ $user->orders->sortByDesc('created_at')->first()->created_at->format('F j, Y') }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($user->addresses && $user->addresses->count() > 0)
        <div class="section">
            <div class="section-title">Addresses</div>
            @foreach($user->addresses as $address)
                <div class="address">
                    <div class="label">{{ $address->is_default ? 'Default Address' : 'Address ' . $loop->iteration }}</div>
                    <div class="value">
                        {{ $address->first_name }} {{ $address->last_name }}<br>
                        {{ $address->address_line1 }}<br>
                        @if($address->address_line2)
                            {{ $address->address_line2 }}<br>
                        @endif
                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                        {{ $address->country }}<br>
                        @if($address->phone)
                            Phone: {{ $address->phone }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif
        
        @if($user->orders && $user->orders->count() > 0)
        <div class="section">
            <div class="section-title">Recent Orders</div>
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->orders->sortByDesc('created_at')->take(10) as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            <td>{{ $order->items->sum('quantity') }}</td>
                            <td>{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</td>
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