@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="orders-container">
    <div class="page-header">
        <h1 class="page-title">My Orders</h1>
        <p class="page-subtitle">Track and manage your orders</p>
    </div>
    @if($orders->count())
        <!-- Order Summary Stats -->
        <div class="order-summary">
            <div class="summary-item status-summary-filter" data-status="all" style="cursor:pointer;">
                <i class="fas fa-shopping-cart summary-icon" style="color: #1e1f8a; font-size:2.5rem; padding-bottom: 0.5rem;"></i>
                <div class="summary-number">{{ $orders->count() }}</div>
                <div class="summary-label">Total Orders</div>
            </div>
            <div class="summary-item status-summary-filter" data-status="pending" style="cursor:pointer;">
                <i class="fas fa-clock summary-icon" style="color:#f59e0b;font-size:2.5rem; padding-bottom: 0.5rem;"></i>
                <div class="summary-number">{{ $orders->where('status', 'pending')->count() }}</div>
                <div class="summary-label">Pending</div>
            </div>
            <div class="summary-item status-summary-filter" data-status="shipped" style="cursor:pointer;">
                <i class="fas fa-shipping-fast summary-icon" style="color:#6366f1;font-size:2.5rem; padding-bottom: 0.5rem;"></i>
                <div class="summary-number">{{ $orders->where('status', 'shipped')->count() }}</div>
                <div class="summary-label">Shipped</div>
            </div>
            <div class="summary-item status-summary-filter" data-status="delivered" style="cursor:pointer;">
                <i class="fas fa-box-open summary-icon" style="color:#10b981;font-size:2.5rem; padding-bottom: 0.5rem;"></i>
                <div class="summary-number">{{ $orders->where('status', 'delivered')->count() }}</div>
                <div class="summary-label">Delivered</div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ...existing code...
                // Status summary filter click
                document.querySelectorAll('.status-summary-filter').forEach(box => {
                    box.addEventListener('click', function() {
                        const status = this.getAttribute('data-status');
                        document.querySelectorAll('.order-card').forEach(card => {
                            if (status === 'all') {
                                card.style.display = '';
                            } else {
                                card.style.display = card.getAttribute('data-status') === status ? '' : 'none';
                            }
                        });
                        document.querySelectorAll('.status-summary-filter').forEach(b => b.classList.remove('active-status-summary'));
                        this.classList.add('active-status-summary');
                    });
                });
            });
            </script>
            <style>
            .status-summary-filter.active-status-summary {
                box-shadow: 0 0 0 2px #6366f1;
                border-color: #6366f1;
                background: #eef2ff;
            }
            </style>
        </div>
        <!-- Orders List -->
        <div id="orders-list">
        @foreach($orders as $order)
        <div class="order-card card-accent-{{ $order->status }}" data-status="{{ $order->status }}">
            <div class="order-card-top">
                <div class="order-card-summary">
                    <div class="order-id">#{{ $order->order_id }}</div>
                    <div class="order-date"><i class="fas fa-calendar"></i> {{ $order->created_at->format('M d, Y') }}</div>
                    <div class="order-status-badge status-badge status-{{ $order->status }}">
                        @if($order->status == 'pending')<i class="fas fa-clock"></i>@endif
                        @if($order->status == 'confirmed')<i class="fas fa-check-circle"></i>@endif
                        @if($order->status == 'processing')<i class="fas fa-cog fa-spin"></i>@endif
                        @if($order->status == 'shipped')<i class="fas fa-shipping-fast"></i>@endif
                        @if($order->status == 'delivered')<i class="fas fa-box-open"></i>@endif
                        @if($order->status == 'cancelled')<i class="fas fa-times-circle"></i>@endif
                        @if($order->status == 'refunded')<i class="fas fa-undo"></i>@endif
                        @if($order->status == 'completed')<i class="fas fa-check"></i>@endif
                        {{ ucfirst($order->status) }}
                    </div>
                    <div class="order-total">${{ number_format($order->grand_total, 2) }}</div>
                </div>
                
            </div>
            <div class="order-body">
                <div class="order-items">
                    @foreach($order->order_items ?? [] as $item)
                    <div class="order-item">
                        <div class="item-image-bg">
                        @if($item->product && $item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="item-image">
                        @else
                            <div class="item-image">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                        </div>
                        <div class="item-details">
                            <div class="item-name">{{ $item->product->name ?? 'Product Deleted' }}</div>
                            <div class="item-info">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</div>
                        </div>
                        <div class="item-total">${{ number_format($item->quantity * $item->price, 2) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="order-card-bottom">
                <div class="order-meta">
                    <span><i class="fas fa-clock"></i> {{ $order->created_at->format('g:i A') }}</span>
                    <span><i class="fas fa-box"></i> {{ $order->total_items }} item{{ $order->total_items > 1 ? 's' : '' }}</span>
                    @if($order->payment)
                        <span class="payment-info"><i class="fas fa-credit-card"></i> Payment: {{ ucfirst($order->payment->status) }}</span>
                    @endif
                </div>
                <div class="order-actions">
                    <a href="{{ route('orders.order_detail', ['order_id' => $order->order_id]) }}" class="btn btn-primary btn-sm" title="View order details">
                        <i class="fas fa-eye"></i>
                        <span class="btn-text">View Details</span>
                    </a>
                    @if($order->status == 'shipped')
                        <form method="POST" action="{{ route('orders.markDelivered', $order->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm"
                                    title="Mark as received"
                                    onclick="return confirm('Mark this order as delivered? This confirms you have received your order.')">
                                <i class="fas fa-check"></i>
                                <span class="btn-text">Received</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <style>
        .order-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 #7b2ff215;
            margin-bottom: 32px;
            padding: 0;
            position: relative;
            border-left: 8px solid var(--primary-color);
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .order-card:hover {
            box-shadow: 0 8px 32px rgba(123,47,242,0.18);
            border-left-color: var(--primary-dark);
        }
        .card-accent-pending { border-left-color: #f59e0b; }
        .card-accent-confirmed { border-left-color: #059669; }
        .card-accent-processing { border-left-color: #7c3aed; }
        .card-accent-shipped { border-left-color: #2563eb; }
        .card-accent-delivered { border-left-color: #16a34a; }
        .card-accent-cancelled { border-left-color: #dc2626; }
        .card-accent-refunded { border-left-color: #db2777; }
        .card-accent-completed { border-left-color: #16a34a; }
        .order-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1.5rem 1.5rem 0.5rem 1.5rem;
            position: relative;
        }
        .order-card-summary {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }
        .order-id {
            font-family: monospace;
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.1rem;
            background: var(--gray-100);
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
        }
        .order-date {
            color: var(--gray-500);
            font-size: 0.95rem;
            background: var(--gray-50);
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
        }
        .order-status-badge {
            position: absolute;
            top: 1.2rem;
            right: 1.5rem;
            z-index: 2;
            box-shadow: 0 2px 8px #7b2ff212;
        }
        .order-total {
            font-size: 1rem;
            font-weight: 800;
            color: var(--success-color);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background: var(--gray-50);
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
        }
        .order-card-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem 1.5rem 1.5rem;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
            flex-wrap: wrap;
            gap: 1rem;
        }
        .item-image-bg {
            background: linear-gradient(135deg, var(--gray-100), var(--gray-50));
            border-radius: 12px;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 60px;
            min-height: 60px;
            margin-right: 1rem;
        }
        .order-actions {
            gap: 1rem;
        }
        .order-actions .btn {
            min-width: 120px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            line-height: 1.2;
            padding-top: 0;
            padding-bottom: 0;
        }
        @media (max-width: 640px) {
            .order-card-top, .order-card-bottom {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
                padding: 1rem;
            }
            .order-status-badge {
                position: static;
                margin-bottom: 0.5rem;
            }
            .order-card-summary {
                flex-direction: column;
                gap: 0.5rem;
            }
            .order-total {
                margin-top: 0.5rem;
            }
        }
        </style>
        @endforeach
        </div>
        <!-- Pagination can be added here if needed -->
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Start Shopping
            </a>
        </div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const icons = document.querySelectorAll('.status-filter-icon');
        const ordersList = document.getElementById('orders-list');
        icons.forEach(icon => {
            icon.addEventListener('click', function() {
                const status = this.getAttribute('data-status');
                document.querySelectorAll('.order-card').forEach(card => {
                    if (status === 'all') {
                        card.style.display = '';
                    } else {
                        card.style.display = card.getAttribute('data-status') === status ? '' : 'none';
                    }
                });
                icons.forEach(i => i.classList.remove('active-status'));
                this.classList.add('active-status');
            });
        });
    });
    </script>
    <style>
    .order-status-filter .status-filter-icon {
        font-size: 2rem;
        opacity: 0.7;
        transition: opacity 0.2s, transform 0.2s;
    }
    .order-status-filter .status-filter-icon.active-status {
        opacity: 1;
        transform: scale(1.2);
        border-bottom: 2px solid #6366f1;
    }
    </style>
</div>
@endsection
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --primary-color: #4f46e5;
    --primary-dark: #3730a3;
    --primary-light: #8b87f7;
    --secondary-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --success-color: #10b981;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --white: #ffffff;
    --border-radius: 12px;
    --border-radius-sm: 8px;
    --border-radius-lg: 16px;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}
.order-total {
    font-size: clamp(1.25rem, 4vw, 1.5rem);
    font-weight: 800;
    color: var(--success-color);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.order-meta {
    display: flex;
    gap: 1.5rem;
    font-size: 0.875rem;
    opacity: 0.9;
    flex-wrap: wrap;
    align-items: center;
}

@media (max-width: 640px) {
    .order-meta {
        justify-content: center;
        gap: 1rem;
    }
}

.order-meta > span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.25rem 0.75rem;
    border-radius: var(--border-radius-sm);
    backdrop-filter: blur(10px);
}

.order-body {
    padding: 1.5rem;
    background: var(--gray-50);
}
.order-items {
    margin-bottom: 1rem;
    background: var(--white);
    border-radius: var(--border-radius-sm);
    overflow: hidden;
    border: 1px solid var(--gray-200);
}

.order-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--gray-100);
    transition: all 0.3s ease;
    gap: 1rem;
    position: relative;
}

.order-item:last-child {
    border-bottom: none;
}

.order-item:hover {
    background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
    transform: translateX(4px);
}

.order-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary-color);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.order-item:hover::before {
    transform: scaleY(1);
}

.item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: var(--border-radius-sm);
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--gray-200);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.item-image:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

@media (max-width: 640px) {
    .item-image {
        width: 50px;
        height: 50px;
    }
}

.item-image .fa-image {
    font-size: 1.5rem;
    color: var(--gray-400);
}

.item-details {
    flex: 1;
    min-width: 0;
}

.item-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--gray-800);
    font-size: 0.95rem;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 640px) {
    .item-name {
        white-space: normal;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
}

.item-info {
    color: var(--gray-600);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.item-total {
    font-weight: 700;
    color: var(--success-color);
    font-size: 1rem;
    text-align: right;
    min-width: fit-content;
}

@media (max-width: 480px) {
    .order-item {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .item-details {
        text-align: center;
    }
    
    .item-name {
        white-space: normal;
    }
    
    .item-total {
        text-align: center;
        font-size: 1.1rem;
    }
}
.order-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: var(--white);
    border-top: 1px solid var(--gray-200);
    flex-wrap: wrap;
    gap: 1rem;
}

@media (max-width: 640px) {
    .order-status {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
}

.status-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

@media (max-width: 640px) {
    .status-info {
        justify-content: center;
        flex-direction: column;
        gap: 0.5rem;
    }
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}

.status-badge:hover::before {
    transform: translateX(100%);
}

.status-pending { 
    background: linear-gradient(135deg, #fef3c7, #fbbf24); 
    color: #92400e; 
    border: 2px solid #f59e0b;
}

.status-confirmed { 
    background: linear-gradient(135deg, #d1fae5, #10b981); 
    color: #065f46; 
    border: 2px solid #059669;
}

.status-processing { 
    background: linear-gradient(135deg, #e0e7ff, #8b5cf6); 
    color: #5b21b6; 
    border: 2px solid #7c3aed;
}

.status-shipped { 
    background: linear-gradient(135deg, #dbeafe, #3b82f6); 
    color: #1e40af; 
    border: 2px solid #2563eb;
}

.status-delivered { 
    background: linear-gradient(135deg, #dcfce7, #22c55e); 
    color: #15803d; 
    border: 2px solid #16a34a;
}

.status-cancelled { 
    background: linear-gradient(135deg, #fee2e2, #ef4444); 
    color: #991b1b; 
    border: 2px solid #dc2626;
}

.status-refunded { 
    background: linear-gradient(135deg, #fce7f3, #ec4899); 
    color: #be185d; 
    border: 2px solid #db2777;
}

.status-completed { 
    background: linear-gradient(135deg, #dcfce7, #22c55e); 
    color: #15803d; 
    border: 2px solid #16a34a;
}

.payment-info {
    font-size: 0.8rem;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.order-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

@media (max-width: 640px) {
    .order-actions {
        width: 100%;
        justify-content: center;
    }
}
.order-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 640px) {
    .order-summary {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .order-summary {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}

.summary-item {
    background: var(--white);
    padding: 2rem 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--gray-200);
    position: relative;
    overflow: hidden;
}

.summary-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.summary-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-light);
}

.summary-item:hover::before {
    transform: scaleX(1);
}

@media (max-width: 640px) {
    .summary-item {
        padding: 1.5rem 1rem;
    }
}

.summary-number {
    font-size: clamp(1.75rem, 5vw, 2.5rem);
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    line-height: 1;
    text-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);
}

.summary-label {
    color: var(--gray-600);
    font-size: 0.875rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.05em;
}
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    text-decoration: none;
    border-radius: var(--border-radius-sm);
    border: none;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    letter-spacing: 0.025em;
    text-transform: uppercase;
    user-select: none;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}

.btn:hover::before {
    transform: translateX(100%);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

.btn-primary { 
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); 
    color: var(--white); 
    border: 2px solid var(--primary-color);
}

.btn-primary:hover { 
    background: linear-gradient(135deg, var(--primary-dark), #312e81); 
    color: var(--white); 
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-outline-primary {
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
    background: transparent;
}

.btn-outline-primary:hover { 
    background: var(--primary-color); 
    color: var(--white); 
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-outline-danger {
    border: 2px solid var(--danger-color);
    color: var(--danger-color);
    background: transparent;
}

.btn-outline-danger:hover { 
    background: var(--danger-color); 
    color: var(--white); 
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-success { 
    background: linear-gradient(135deg, var(--success-color), #059669); 
    color: var(--white);
    border: 2px solid var(--success-color);
}

.btn-success:hover { 
    background: linear-gradient(135deg, #059669, #047857); 
    color: var(--white); 
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn:active {
    transform: translateY(0);
}

.btn:focus {
    outline: 2px solid var(--primary-light);
    outline-offset: 2px;
}

@media (max-width: 640px) {
    .btn {
        padding: 0.875rem 1.25rem;
        font-size: 0.875rem;
        width: 100%;
        justify-content: center;
    }
    
    .btn-sm {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
}
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-600);
    background: var(--white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow);
    margin: 2rem 0;
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
    opacity: 0.5;
}

.empty-state > * {
    position: relative;
    z-index: 1;
}

.empty-state i {
    font-size: clamp(3rem, 8vw, 5rem);
    margin-bottom: 1.5rem;
    color: var(--gray-300);
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.empty-state h3 {
    margin-bottom: 1rem;
    color: var(--gray-800);
    font-size: clamp(1.25rem, 4vw, 1.75rem);
    font-weight: 700;
}

.empty-state p {
    margin-bottom: 2rem;
    font-size: clamp(0.95rem, 2vw, 1.1rem);
    line-height: 1.6;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 640px) {
    .empty-state {
        padding: 3rem 1.5rem;
        margin: 1rem 0;
    }
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .orders-container { 
        padding: 1rem; 
    }
    
    .page-title { 
        font-size: clamp(1.5rem, 6vw, 2rem); 
    }
    
    .order-main-info { 
        flex-direction: column; 
        text-align: center; 
        gap: 0.75rem; 
    }
    
    .order-meta { 
        justify-content: center; 
        flex-wrap: wrap; 
        text-align: center; 
        gap: 0.75rem; 
    }
    
    .item-image { 
        margin: 0; 
    }
    
    .order-actions { 
        flex-direction: column; 
        width: 100%;
    }
    
    .order-summary { 
        grid-template-columns: repeat(2, 1fr); 
    }
}

@media (max-width: 480px) {
    .orders-container {
        padding: 0.75rem;
    }
    
    .page-header {
        margin-bottom: 2rem;
        padding: 1.5rem 1rem;
    }
    
    .order-summary {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .order-header {
        padding: 1rem;
    }
    
    .order-body {
        padding: 1rem;
    }
    
    .order-status {
        padding: 1rem;
    }
}

/* Enhanced Animation States */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus and accessibility improvements */
.btn:focus-visible,
.order-card:focus-within {
    outline: 3px solid var(--primary-light);
    outline-offset: 2px;
}

/* Loading state styles */
.loading {
    opacity: 0.7;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--primary-color);
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@section('content')
<div class="orders-container">
    <div class="page-header">
        <h1 class="page-title">My Orders</h1>
        <p class="page-subtitle">Track and manage your orders</p>
    </div>
    @if($orders->count())
        <!-- Order Summary Stats -->
        <div class="order-summary">
            <div class="summary-item">
                <div class="summary-number">{{ $orders->count() }}</div>
                <div class="summary-label">Total Orders</div>
            </div>
            <div class="summary-item">
                    <div class="summary-number">{{ $orders->where('status', 'pending')->count() }}</div>
                <div class="summary-label">Pending</div>
            </div>
            <div class="summary-item">
                    <div class="summary-number">{{ $orders->where('status', 'shipped')->count() }}</div>
                <div class="summary-label">Shipped</div>
            </div>
            <div class="summary-item">
                    <div class="summary-number">{{ $orders->where('status', 'delivered')->count() }}</div>
                <div class="summary-label">Delivered</div>
            </div>
        </div>
        <!-- Orders List -->
        @foreach($orders as $order)
        <div class="order-card">
            <div class="order-header">
                <div class="order-main-info">
                    <div class="order-id">Order #{{ $order->order_id }}</div>
                    <div class="order-total">${{ $order->grand_total }}</div>
                </div>
                <div class="order-meta">
                    <span><i class="fas fa-calendar"></i> {{ $order->created_at->format('M d, Y') }}</span>
                    <span><i class="fas fa-clock"></i> {{ $order->created_at->format('g:i A') }}</span>
                    <span><i class="fas fa-box"></i> {{ $order->total_items }} item{{ $order->total_items > 1 ? 's' : '' }}</span>
                </div>
            </div>
            <div class="order-body">
                <div class="order-items">
                    @foreach ($order->order_items as $item)
                    <div class="order-item">
                        @if ($item->product->image_url)
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="item-image">
                        @else
                            <div class="item-image">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                        <div class="item-details">
                            <div class="item-name">{{ $item->product->name }}</div>
                            <div class="item-info">Qty: {{ $item->quantity }} × ${{ $item->price }}</div>
                        </div>
                        <div class="item-total">${{ $item->total_price }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="order-status">
                <div class="status-info">
                    <span class="status-badge status-{{ $order->status }}">
                        @if ($order->status == 'pending')
                            <i class="fas fa-clock"></i>
                        @elseif ($order->status == 'confirmed')
                            <i class="fas fa-check-circle"></i>
                        @elseif ($order->status == 'processing')
                            <i class="fas fa-cog fa-spin"></i>
                        @elseif ($order->status == 'shipped')
                            <i class="fas fa-shipping-fast"></i>
                        @elseif ($order->status == 'delivered')
                            <i class="fas fa-box-open"></i>
                        @elseif ($order->status == 'cancelled')
                            <i class="fas fa-times-circle"></i>
                        @elseif ($order->status == 'refunded')
                            <i class="fas fa-undo"></i>
                        @endif
                        {{ $order->status }}
                    </span>
                    @if ($order->payment)
                        <div class="payment-info">
                            <i class="fas fa-credit-card"></i>
                            Payment: {{ $order->payment->status }}
                        </div>
                    @endif
                </div>
                <div class="order-actions">
                    <a href="{{ route('orders.order_detail', $order->order_id) }}" 
                       class="btn btn-primary btn-sm"
                       title="View order details">
                        <i class="fas fa-eye"></i> 
                        <span class="btn-text">View Details</span>
                    </a>
                    @if ($order->status == 'shipped')
                        <form method="POST" action="{{ route('orders.markDelivered', $order->order_id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm"
                                    title="Mark as received"
                                    onclick="return confirm('Mark this order as delivered? This confirms you have received your order.')">
                                <i class="fas fa-check"></i> 
                                <span class="btn-text">Received</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    @endif
        <!-- Pagination can be added here if needed -->
</div>
@endsection

@section('extra_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh functionality
    setTimeout(function() { location.reload(); }, 300000);
    
    // Enhanced mobile interactions
    const orderCards = document.querySelectorAll('.order-card');
    const isMobile = window.innerWidth <= 768;
    
    // Touch gestures for mobile
    if (isMobile) {
        orderCards.forEach(card => {
            let startY = 0;
            let currentY = 0;
            let isDragging = false;
            
            card.addEventListener('touchstart', (e) => {
                startY = e.touches[0].clientY;
                isDragging = true;
                card.style.transition = 'none';
            });
            
            card.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                currentY = e.touches[0].clientY;
                const deltaY = currentY - startY;
                
                if (Math.abs(deltaY) > 10) {
                    card.style.transform = `translateY(${deltaY * 0.1}px)`;
                }
            });
            
            card.addEventListener('touchend', () => {
                if (isDragging) {
                    card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.transform = '';
                    isDragging = false;
                }
            });
        });
    }
    
    // Status badge animations
    const statusBadges = document.querySelectorAll('.status-badge');
    statusBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    // Image lazy loading and error handling
    const images = document.querySelectorAll('.item-image img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const placeholder = this.parentNode.querySelector('.fa-image');
            if (placeholder) {
                placeholder.style.display = 'flex';
            }
        });
        
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.3s ease';
    });
    
    // Button ripple effect
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.6);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add ripple animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        .btn {
            position: relative;
            overflow: hidden;
        }
        
        @media (max-width: 640px) {
            .btn-text {
                display: none;
            }
        }
        
        @media (min-width: 641px) {
            .btn i {
                margin-right: 0.5rem;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe order cards for scroll animations
    orderCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
    
    // Performance optimization - debounced scroll
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(() => {
            // Add scroll-based effects here if needed
        }, 100);
    });
    
    // Keyboard navigation support
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            const focusedElement = document.activeElement;
            if (focusedElement.classList.contains('order-card')) {
                const detailLink = focusedElement.querySelector('.btn-primary');
                if (detailLink) {
                    detailLink.click();
                }
            }
        }
    });
    
    // Make order cards focusable for accessibility
    orderCards.forEach(card => {
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', `Order ${card.querySelector('.order-id').textContent}`);
    });
});

// Service Worker registration for offline support (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/static/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}
</script>
@endsection