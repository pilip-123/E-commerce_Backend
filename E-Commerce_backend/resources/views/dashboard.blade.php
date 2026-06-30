<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Dashboard</h2>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-danger btn-sm">Sign Out</button>
            </form>
        </div>

        <div class="row g-4">
            @foreach ($recentProducts as $product)
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center p-3">
                            <div class="fw-semibold small">{{ $product->name }}</div>
                            <div class="text-success fw-bold mt-1">${{ number_format($product->price, 2) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($recentOrders->count())
            <h4 class="fw-bold mt-5 mb-3">Recent Orders</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td><span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }}">{{ $order->status }}</span></td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
