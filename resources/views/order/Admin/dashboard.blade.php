@extends('layouts.app')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f3f3fd; }
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px 10px 40px 10px;
    min-width: 1200px;
}
.dashboard-header {
    background: linear-gradient(135deg, #7b2ff2 0%, #f357a8 100%);
    color: white;
    padding: 38px 28px 32px 28px;
    border-radius: 18px 18px 0 0;
    margin-bottom: 38px;
    text-align: center;
    box-shadow: 0 4px 32px 0 #7b2ff215;
}
.dashboard-title {
    font-size: 2.6rem;
    font-weight: 800;
    margin-bottom: 10px;
    letter-spacing: 1.3px;
    background: linear-gradient(90deg, #fff 70%, #ece5ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.dashboard-header .fa-tachometer-alt {
    font-size: 2.5rem;
    color: #fff;
    margin-right: 10px;
}
.dashboard-subtitle {
    color: #d9d9d9;
    opacity: 0.96;
    font-size: 1.11rem;
    font-weight: 500;
    letter-spacing: 0.2px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 22px;
    margin-bottom: 38px;
}
.stat-card {
    background: #fff;
    padding: 27px 0 22px 0;
    border-radius: 15px;
    box-shadow: 0 2px 12px rgba(123,47,242,0.07);
    text-align: center;
    transition: transform 0.28s, box-shadow 0.28s;
    cursor: pointer;
    border: 2.5px solid transparent;
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 6px 24px rgba(123,47,242,0.13);
    border-color: #ede7ff;
}
.stat-icon {
    font-size: 2.7rem;
    margin-bottom: 16px;
}
.stat-number {
    font-size: 2.3rem;
    font-weight: 700;
    margin-bottom: 8px;
    transition: all 0.3s;
}
.stat-label {
    color: #666;
    font-size: 14px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.6px;
}
.stat-total { color: #7b2ff2; }
.stat-total .stat-icon { color: #7b2ff2; }
.stat-pending { color: #ff9800; }
.stat-pending .stat-icon { color: #ff9800; }
.stat-confirmed { color: #28a745; }
.stat-confirmed .stat-icon { color: #28a745; }
.stat-shipped { color: #17a2b8; }
.stat-shipped .stat-icon { color: #17a2b8; }
.stat-delivered { color: #6f42c1; }
.stat-delivered .stat-icon { color: #6f42c1; }

.quick-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 18px;
    margin: 30px 0 44px 0;
}
.btn {
    padding: 12px 28px;
    border: none;
    border-radius: 22px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-size: 1.10rem;
    font-weight: 700;
    box-shadow: 0 2px 12px 0 #7b2ff21a;
    transition: all 0.26s;
    gap: 10px;
}
.btn-primary {
    background: linear-gradient(90deg, #7b2ff2 60%, #4361ee 100%);
    color: #fff;
    border: none;
}
.btn-primary:hover {
    background: linear-gradient(90deg, #4361ee 60%, #7b2ff2 100%);
    color: #fff;
}
.btn-outline-primary {
    border: 2px solid #7b2ff2;
    color: #7b2ff2;
    background: #fff;
}
.btn-outline-primary:hover {
    background: #ede7ff;
    color: #7b2ff2;
}
.orders-table-container {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(123,47,242,0.07);
    overflow-x: auto;
}
.section-header {
    background: #f7f7fb;
    padding: 22px 26px;
    border-bottom: 1px solid #eee;
}
.section-title {
    font-size: 1.33rem;
    font-weight: 700;
    margin: 0;
    color: #232b3b;
    letter-spacing: 0.6px;
}
.orders-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}
.orders-table th, .orders-table td {
    padding: 18px 12px;
    text-align: left;
    background: #fff;
}
.orders-table th {
    font-size: 1.13rem;
    font-weight: 700;
    color: #232b3b;
    border-bottom: 2px solid #e2e7f2;
    background: #f7f7fb;
    letter-spacing: 0.5px;
}
.orders-table td {
    font-size: 1.06rem;
    border-bottom: 1px solid #f2f2f2;
    vertical-align: middle;
}
.orders-table tr:hover {
    background-color: #f7f7fa;
}
.order-id {
    font-family: monospace;
    font-weight: bold;
    color: #7b2ff2;
    letter-spacing: 0.5px;
    font-size: 1.01rem;
}
.customer-info { margin-bottom: 2px; }
.customer-name {
    font-weight: 600;
    color: #232b3b;
    font-size: 1.04rem;
}
.customer-email {
    color: #888;
    font-size: 12px;
}
.status-badge {
    display: inline-block;
    padding: 5px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.9px;
}
.status-pending {
    background: #fff2cc;
    color: #e17055;
}
.status-confirmed {
    background: #d0f8ef;
    color: #00b894;
}
.status-processing {
    background: #c7c3f5;
    color: #6c5ce7;
}
.status-shipped {
    background: #fdc5e9;
    color: #e84393;
}
.status-delivered {
    background: #b7e0fa;
    color: #0984e3;
}
.status-cancelled {
    background: #ffe0d2;
    color: #e17055;
}
.action-buttons {
    display: flex;
    gap: 7px;
}
.btn-action {
    padding: 7px 13px;
    border-radius: 9px;
    font-size: 1.07rem;
    font-weight: 600;
    background: #fff;
    border: 2px solid #7b2ff2;
    color: #7b2ff2;
    box-shadow: 0 1px 4px #7b2ff212;
    cursor: pointer;
    transition: background 0.16s, color 0.16s;
}
.btn-action:hover {
    background: #7b2ff2;
    color: #fff;
}
.empty-state {
    text-align: center;
    padding: 70px 20px 60px 20px;
    color: #666;
}
.empty-state i {
    font-size: 4.3rem;
    margin-bottom: 22px;
    color: #eaeaea;
}
@media (max-width: 900px) {
    .dashboard-container { padding: 7px; }
    .dashboard-header { padding: 25px 3vw 18px 3vw; }
    .orders-table th, .orders-table td { padding: 8px 3px; font-size: 13px; }
    .section-header { padding: 12px 5vw; }
    .quick-actions { gap: 8px; }
    .stat-card { padding: 18px 0 12px 0;}
}
@media (max-width: 600px) {
    .dashboard-title { font-size: 1.5rem; }
    .stats-grid { grid-template-columns: 1fr; gap: 14px;}
}
</style>

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="fas fa-tachometer-alt"></i> Orders Dashboard
        </h1>
        <p class="dashboard-subtitle">Monitor and manage your e-commerce orders</p>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card stat-total status-stat-filter" data-status="all" title="Total Orders" style="cursor:pointer;">
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-number">{{ $total_orders }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card stat-pending status-stat-filter" data-status="pending" title="Pending Orders" style="cursor:pointer;">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $pending_orders }}</div>
            <div class="stat-label">Pending Orders</div>
        </div>
        <div class="stat-card stat-confirmed status-stat-filter" data-status="confirmed" title="Confirmed Orders" style="cursor:pointer;">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $confirmed_orders }}</div>
            <div class="stat-label">Confirmed</div>
        </div>
        <div class="stat-card stat-shipped status-stat-filter" data-status="shipped" title="Shipped Orders" style="cursor:pointer;">
            <div class="stat-icon">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <div class="stat-number">{{ $shipped_orders }}</div>
            <div class="stat-label">Shipped</div>
        </div>
        <div class="stat-card stat-delivered status-stat-filter" data-status="delivered" title="Delivered Orders" style="cursor:pointer;">
            <div class="stat-icon">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="stat-number">{{ $delivered_orders }}</div>
            <div class="stat-label">Delivered</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="#" class="btn btn-primary">
            <i class="fas fa-list"></i> View All Orders
        </a>
        <a href="#" class="btn btn-outline-primary">
            <i class="fas fa-clock"></i> Pending Orders
        </a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-box"></i> Manage Products
        </a>
    </div>

    <!-- Recent Orders -->
    <div class="orders-table-container">
        <div class="section-header">
            <h2 class="section-title">Recent Orders</h2>
                <!-- Status Filter Form -->
                <form method="GET" action="{{ route('admin.manage.orders') }}" style="margin-top: 18px; display: flex; align-items: center; gap: 12px;">
                    <label for="status" style="font-weight: 600;">Filter by Status:</label>
                    <select name="status" id="status" class="form-control" style="min-width: 140px;">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary" style="padding: 7px 18px; border-radius: 8px; font-size: 1rem;">Apply</button>
                </form>
        </div>
        @if($recent_orders->isNotEmpty())
        <table class="orders-table" id="admin-orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_orders as $order)
                <tr data-status="{{ $order->status }}">
                    <td>
                        <div class="order-id">#{{ Str::limit($order->order_id, 12) }}</div>
                    </td>
                    <td>
                        <div class="customer-info">
                            <div class="customer-name">{{ $order->first_name }} {{ $order->last_name }}</div>
                            <div class="customer-email">{{ $order->email }}</div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $order->created_at->format('g:i A') }}</small>
                    </td>
                    <td>
                        {{ $order->total_items }} {{ Str::plural('item', $order->total_items) }}
                    </td>
                    <td>
                        <strong>${{ number_format($order->grand_total, 2) }}</strong>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('orders.admin_order_detail', $order->order_id) }}" 
                               class="btn-action" title="View Order">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Stat card click filter
            document.querySelectorAll('.status-stat-filter').forEach(card => {
                card.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');
                    document.querySelectorAll('#admin-orders-table tbody tr').forEach(row => {
                        if (status === 'all') {
                            row.style.display = '';
                        } else {
                            row.style.display = row.getAttribute('data-status') === status ? '' : 'none';
                        }
                    });
                    document.querySelectorAll('.status-stat-filter').forEach(c => c.classList.remove('active-admin-status'));
                    this.classList.add('active-admin-status');
                });
            });
        });
        </script>
        <style>
        .status-stat-filter.active-admin-status {
            box-shadow: 0 0 0 2px #6366f1;
            border-color: #6366f1;
            background: #eef2ff;
        }
        </style>
        @else
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>No Orders Yet</h3>
            <p>When customers place orders, they will appear here.</p>
        </div>
        @endif
    </div>
</div>
@endsection


<script>
// Auto-refresh dashboard every 1 minute
setTimeout(function() {
    location.reload();
}, 60000);

// Live updates for pending orders count
function updatePendingCount() {
    fetch('/orders/pending-count/')
        .then(response => response.json())
        .then(data => {
            const pendingElement = document.querySelector('.stat-pending .stat-number');
            if (pendingElement && data.count !== undefined) {
                const currentCount = parseInt(pendingElement.textContent);
                if (currentCount !== data.count) {
                    pendingElement.textContent = data.count;
                    pendingElement.style.transform = 'scale(1.18)';
                    setTimeout(() => {
                        pendingElement.style.transform = 'scale(1)';
                    }, 340);
                }
            }
        })
        .catch(error => {
            console.log('Error updating pending count:', error);
        });
}
setInterval(updatePendingCount, 30000);
</script>
