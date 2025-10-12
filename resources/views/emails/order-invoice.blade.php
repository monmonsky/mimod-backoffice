<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 12px;
        }
        .status-paid {
            background-color: #10b981;
            color: #ffffff;
        }
        .status-unpaid {
            background-color: #f59e0b;
            color: #ffffff;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .info-card {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
            font-size: 14px;
        }
        .info-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead {
            background-color: #f3f4f6;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .item-name {
            font-weight: 600;
            color: #1f2937;
        }
        .item-details {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        .text-right {
            text-align: right;
        }
        .summary-card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        .summary-row.total {
            padding-top: 15px;
            margin-top: 15px;
            border-top: 2px solid #667eea;
            font-size: 18px;
            font-weight: 700;
        }
        .summary-label {
            color: #4b5563;
        }
        .summary-value {
            color: #1f2937;
            font-weight: 600;
        }
        .summary-row.total .summary-label,
        .summary-row.total .summary-value {
            color: #667eea;
        }
        .address-section {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .address-box {
            flex: 1;
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
        }
        .address-box h3 {
            font-size: 14px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .address-box p {
            font-size: 14px;
            color: #4b5563;
            margin: 4px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #1f2937;
            color: #9ca3af;
            padding: 30px;
            text-align: center;
            font-size: 13px;
        }
        .footer p {
            margin: 8px 0;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 25px 0;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header {
                padding: 30px 20px;
            }
            .content {
                padding: 20px;
            }
            .address-section {
                flex-direction: column;
            }
            .items-table th,
            .items-table td {
                padding: 10px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $storeInfo['name'] }}</h1>
            <p>Terima kasih atas pesanan Anda!</p>
            <span class="status-badge status-{{ $order->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
                {{ $order->payment_status === 'paid' ? '✓ Lunas' : 'Belum Dibayar' }}
            </span>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Hi {{ $customer->name }},</p>
            <p>Pesanan Anda telah kami terima dan sedang diproses. Berikut detail pesanan Anda:</p>

            <!-- Order Info -->
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Nomor Invoice</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Pesanan</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Pesanan</span>
                    <span class="info-value">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Pembayaran</span>
                    <span class="info-value">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</span>
                </div>
            </div>

            <!-- Items -->
            <h2 class="section-title">Detail Pesanan</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderItems as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if($item->sku || $item->size || $item->color)
                            <div class="item-details">
                                @if($item->sku) SKU: {{ $item->sku }} @endif
                                @if($item->size) • {{ $item->size }} @endif
                                @if($item->color) • {{ $item->color }} @endif
                            </div>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <div class="summary-card">
                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->shipping_cost > 0)
                <div class="summary-row">
                    <span class="summary-label">Ongkir ({{ $order->courier ?? 'Kurir' }})</span>
                    <span class="summary-value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div class="summary-row">
                    <span class="summary-label">Pajak</span>
                    <span class="summary-value">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="summary-row">
                    <span class="summary-label">Diskon</span>
                    <span class="summary-value">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="summary-row total">
                    <span class="summary-label">Total</span>
                    <span class="summary-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Addresses -->
            <h2 class="section-title">Informasi Pengiriman</h2>
            <div class="address-section">
                <div class="address-box">
                    <h3>Alamat Penerima</h3>
                    <p><strong>{{ $customer->name }}</strong></p>
                    <p>{{ $order->shipping_address }}</p>
                    @if($order->shipping_city)
                    <p>{{ $order->shipping_city }}@if($order->shipping_province), {{ $order->shipping_province }}@endif</p>
                    @endif
                    @if($order->shipping_postal_code)
                    <p>{{ $order->shipping_postal_code }}</p>
                    @endif
                    @if($order->shipping_phone)
                    <p>Telp: {{ $order->shipping_phone }}</p>
                    @endif
                </div>

                <div class="address-box">
                    <h3>Dari</h3>
                    <p><strong>{{ $storeInfo['name'] }}</strong></p>
                    <p>{{ $storeInfo['address'] }}</p>
                    <p>Email: {{ $storeInfo['email'] }}</p>
                    <p>Phone: {{ $storeInfo['phone'] }}</p>
                </div>
            </div>

            @if($order->tracking_number)
            <div class="divider"></div>
            <h2 class="section-title">Tracking Pengiriman</h2>
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Kurir</span>
                    <span class="info-value">{{ $order->courier }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Resi</span>
                    <span class="info-value">{{ $order->tracking_number }}</span>
                </div>
            </div>
            @endif

            <!-- CTA Button -->
            <center>
                <a href="{{ config('app.url') }}/track-order/{{ $order->order_number }}" class="button">
                    Lacak Pesanan
                </a>
            </center>

            <div class="divider"></div>

            <p style="font-size: 13px; color: #6b7280; text-align: center;">
                Jika Anda memiliki pertanyaan, silakan hubungi kami di
                <a href="mailto:{{ $storeInfo['email'] }}" style="color: #667eea;">{{ $storeInfo['email'] }}</a>
                atau WhatsApp <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storeInfo['phone']) }}" style="color: #667eea;">{{ $storeInfo['phone'] }}</a>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $storeInfo['name'] }}</strong></p>
            <p>{{ $storeInfo['address'] }}</p>
            <p>Email: <a href="mailto:{{ $storeInfo['email'] }}">{{ $storeInfo['email'] }}</a> | Phone: {{ $storeInfo['phone'] }}</p>
            <p style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
                © {{ date('Y') }} {{ $storeInfo['name'] }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
