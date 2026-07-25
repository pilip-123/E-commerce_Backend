<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Receipt') }} #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #f1f5f9; padding: 24px; color: #1e293b; }
        .receipt-wrap { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; }
        .receipt-header { background: linear-gradient(135deg, #059669, #047857); padding: 32px 36px; display: flex; align-items: center; justify-content: space-between; }
        .receipt-header .shop-info { display: flex; align-items: center; gap: 14px; }
        .receipt-header .shop-info img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.3); }
        .receipt-header .shop-info h2 { color: #fff; font-size: 1.3rem; font-weight: 800; }
        .receipt-header .shop-info small { color: rgba(255,255,255,.7); font-size: .8rem; display: block; }
        .receipt-badge { background: rgba(255,255,255,.2); color: #fff; padding: 6px 16px; border-radius: 20px; font-size: .8rem; font-weight: 700; text-align: center; }
        .receipt-body { padding: 32px 36px; }
        .receipt-section { margin-bottom: 28px; }
        .receipt-section:last-child { margin-bottom: 0; }
        .receipt-section h6 { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #94a3b8; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .info-grid .label { font-size: .75rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
        .info-grid .value { font-size: .9rem; font-weight: 600; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        thead th { text-align: left; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; padding: 10px 0; border-bottom: 2px solid #f1f5f9; }
        thead th:last-child { text-align: right; }
        thead th:nth-child(3) { text-align: center; }
        tbody td { padding: 12px 0; border-bottom: 1px solid #f1f5f9; font-size: .85rem; }
        tbody td:last-child { text-align: right; font-weight: 700; }
        tbody td:nth-child(3) { text-align: center; }
        tbody td .product-img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; }
        tfoot td { padding: 10px 0; font-size: .85rem; }
        tfoot .total-row td { font-size: 1.05rem; font-weight: 800; color: #059669; padding-top: 14px; border-top: 2px solid #059669; }
        tfoot td:last-child { text-align: right; }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-wrap { box-shadow: none; border-radius: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt-wrap">
        <div class="receipt-header">
            <div class="shop-info">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <div>
                    <h2>E-Commerce</h2>
                    <small>{{ __('Your trusted online marketplace') }}</small>
                </div>
            </div>
            <div class="receipt-badge">
                <div style="font-size: .65rem; opacity: .8;">{{ __('Receipt') }}</div>
                <div>#{{ $order->id }}</div>
            </div>
        </div>

        <div class="receipt-body">
            <div class="receipt-section">
                <div class="info-grid">
                    <div>
                        <div class="label">{{ __('Customer') }}</div>
                        <div class="value">{{ $order->user->name ?? '—' }}</div>
                        <div style="font-size: .8rem; color: #64748b;">{{ $order->user->email ?? '' }}</div>
                    </div>
                    <div>
                        <div class="label">{{ __('Order Date') }}</div>
                        <div class="value">{{ $order->created_at->format('M d, Y') }}</div>
                        <div style="font-size: .8rem; color: #64748b;">{{ $order->created_at->format('h:i A') }}</div>
                    </div>
                    <div>
                        <div class="label">{{ __('Phone') }}</div>
                        <div class="value">{{ $order->phone ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="label">{{ __('Status') }}</div>
                        <div class="value" style="text-transform:capitalize;">{{ $order->status }}</div>
                    </div>
                </div>
            </div>

            @if ($order->shipping_address)
            <div class="receipt-section">
                <h6>{{ __('Shipping Address') }}</h6>
                <p style="font-size: .88rem; color: #475569; line-height: 1.6;">{{ $order->shipping_address }}</p>
            </div>
            @endif

            <div class="receipt-section">
                <h6>{{ __('Order Items') }} ({{ $order->items->count() }})</h6>
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Qty') }}</th>
                            <th>{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    @if ($item->product && $item->product->image)
                                        <img class="product-img" src="{{ asset('storage/' . $item->product->image) }}" alt="">
                                    @else
                                        <div style="width:36px;height:36px;border-radius:8px;background:#f1f5f9;"></div>
                                    @endif
                                    <span class="fw-semibold">{{ $item->product->name ?? __('Product') }}</span>
                                </div>
                            </td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $subtotal = $order->items->sum(fn($i) => $i->price * $i->quantity);
                            $itemCount = $order->items->sum('quantity');
                        @endphp
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:600;">{{ __('Subtotal') }} ({{ $itemCount }} {{ __('items') }})</td>
                            <td>${{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" style="text-align:right;">{{ __('Total') }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="receipt-section" style="border-top:1px solid #f1f5f9;padding-top:20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <p style="font-size:.75rem;color:#94a3b8;margin-bottom:4px;">{{ __('Contact') }}</p>
                        <p style="font-size:.85rem;color:#475569;">pilipyom985@gmail.com</p>
                        <p style="font-size:.85rem;color:#475569;">+(855) 66 509 793</p>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:.75rem;color:#94a3b8;margin-bottom:4px;">{{ __('Address') }}</p>
                        <p style="font-size:.82rem;color:#475569;max-width:240px;line-height:1.5;">Phum Tropeang Chhuk (Borey Sorla), Sangtak, Street 371, Phnom Penh</p>
                    </div>
                </div>
            </div>

            <div style="text-align:center;padding-top:16px;border-top:1px solid #f1f5f9;margin-top:20px;">
                <p style="font-size:.7rem;color:#94a3b8;">{{ __('Thank you for your purchase!') }}</p>
            </div>

            <div style="text-align:center;margin-top:16px;" class="no-print">
                <button onclick="window.print()" style="padding:10px 32px;border:0;border-radius:8px;background:#059669;color:#fff;font-size:.85rem;font-weight:600;cursor:pointer;">{{ __('Print Receipt') }}</button>
                <a href="{{ route('admin.orders.index') }}" style="display:inline-block;margin-left:8px;padding:10px 32px;border-radius:8px;background:#f1f5f9;color:#475569;font-size:.85rem;font-weight:600;text-decoration:none;">{{ __('Back') }}</a>
            </div>
        </div>
    </div>
</body>
</html>
