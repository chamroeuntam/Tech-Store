
@extends('layouts.app')

@section('title', 'Admin Order Details')

@section('content')

<style>
.order-detail-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
    padding: 32px 18px 60px 18px;
    border-radius: 18px;
    box-shadow: 0 4px 32px 0 #7b2ff215;
    max-width: 1100px;
    margin: 0 auto;
    position: relative;
}
.page-header {
    text-align: left;
    margin-bottom: 24px;
}
.page-title {
    font-size: 2.1rem;
    font-weight: 800;
    color: #6366f1;
    margin-bottom: 6px;
}
.order-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 22px;
    margin-bottom: 18px;
}
.order-summary > div {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(123,47,242,0.07);
    text-align: left;
    padding: 18px 24px;
    font-size: 1.05rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.order-summary strong {
    color: #6366f1;
    font-weight: 700;
    margin-right: 8px;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 18px;
    border-radius: 20px;
    font-size: 0.98rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    background: #e0e7ff;
    color: #6366f1;
}
.status-pending { background: #fff2cc; color: #e17055; }
.status-confirmed { background: #d0f8ef; color: #00b894; }
.status-processing { background: #c7c3f5; color: #6c5ce7; }
.status-shipped { background: #fdc5e9; color: #e84393; }
.status-delivered { background: #b7e0fa; color: #0984e3; }
.status-completed { background: #d1fae5; color: #16a34a; }
.status-cancelled { background: #ffe0d2; color: #e17055; }
.status-refunded { background: #e5e7eb; color: #374151; }
.order-items-list {
    margin-top: 18px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(123,47,242,0.07);
    padding: 12px 0;
}
.order-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 24px;
    border-bottom: 1px solid #e5e7eb;
    transition: background 0.2s;
    gap: 1.2rem;
}
.order-item-row:last-child {
    border-bottom: none;
}
.order-item-row:hover {
    background: #f3f4f6;
}
.item-image {
    min-width: 60px;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e0e7ff;
    border-radius: 10px;
    overflow: hidden;
}
.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
.item-details {
    flex: 1;
    min-width: 0;
    padding: 0 18px;
}
.item-name {
    font-weight: 600;
    color: #232b3b;
    font-size: 1.05rem;
    margin-bottom: 2px;
    margin-bottom: 18px;
}
.shipping-info div {
    margin-bottom: 8px;
    font-size: 1.05rem;
}
.order-notes {
    background: #f3f4f6;
    border-radius: 10px;
    padding: 12px 18px;
    font-size: 1rem;
    color: #374151;
    margin-bottom: 18px;
}
.sticky-action-bar {
    position: sticky;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #fff;
    box-shadow: 0 -2px 12px rgba(123,47,242,0.07);
    padding: 18px 0 0 0;
    z-index: 10;
        display: flex;
        gap: 1.2rem;
        justify-content: flex-end;
        border-radius: 0 0 18px 18px;
        flex-wrap: wrap;
        align-items: center;
        min-height: 60px;
}
.sticky-action-bar .btn {
    min-width: 140px;
    height: 44px;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 10px;
    box-shadow: 0 2px 8px #7b2ff212;
    margin-bottom: 0;
    margin-right: 0;
}
.form-select {
    font-size: 1rem;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #232b3b;
    font-weight: 500;
    margin-right: 0.8rem;
}
.btn-primary, .btn-success, .btn-secondary {
    font-size: 1rem;
    font-weight: 700;
    border-radius: 10px;
    padding: 10px 24px;
    min-width: 140px;
    height: 44px;
    box-shadow: 0 2px 8px #7b2ff212;
    display: inline-flex;
    align-items: center;
    gap: 0.7rem;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
    overflow: hidden;
}
.btn-primary i, .btn-success i, .btn-secondary i {
    transition: transform 0.3s cubic-bezier(.4,0,.2,1);
}
.btn-primary:hover i, .btn-success:hover i, .btn-secondary:hover i {
    transform: scale(1.2) rotate(-10deg);
}
.btn-primary:hover, .btn-success:hover, .btn-secondary:hover {
    box-shadow: 0 4px 16px #7b2ff230;
    transform: translateY(-2px) scale(1.03);

    cursor: pointer;
    transition: box-shadow 0.2s, transform 0.2s;
}
.order-status-badge:hover, .status-badge:hover {
    box-shadow: 0 4px 16px #6366f1a0;
    transform: scale(1.08);

    transition: box-shadow 0.2s, transform 0.2s;
    cursor: pointer;
}
.item-image:hover {
    box-shadow: 0 4px 16px #6366f1a0;
    transform: scale(1.08) rotate(-2deg);
}

.btn-primary { background: linear-gradient(135deg, #6366f1, #3730a3); color: #fff; border: none; }
.btn-success { background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; }
.btn-secondary { background: linear-gradient(135deg, #e0e7ff, #6366f1); color: #232b3b; border: none; }
.btn-primary:hover, .btn-success:hover, .btn-secondary:hover { opacity: 0.9; }
@media (max-width: 640px) {
    .order-detail-container { padding: 12px 2px 32px 2px; }
    .order-summary > div, .shipping-info { padding: 12px 8px; font-size: 0.98rem; }
    .order-items-list { padding: 8px 0; }
    .order-item-row { padding: 10px 8px; }
    .sticky-action-bar { padding: 12px 0 0 0; }
}
</style>

<div class="order-detail-container">

    <!-- Header -->
    <div class="page-header mb-3">
        <h1 class="page-title mb-1">Order #{{ $order->order_id }}</h1>
        <p class="text-muted">Placed on {{ $order->created_at->format('M d, Y g:i A') }} | Email: {{ $order->user?->email ?? '-' }}</p>
        <p style="border-top: 3px dotted #dddddd"></P>
    </div>

    <!-- Summary -->
    <div class="order-summary">
        <div><strong>Status:</strong> <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></div>
        <div><strong>Total:</strong> ${{ $order->grand_total }}</div>
        <div><strong>Shipping:</strong> {{ $order->shippingMethod?->name ?? 'No shipping method' }}</div>
        <div><strong>Payment:</strong> {{ $order->payment?->name ?? ($order->payment?->payment_method ?? '-') }}</div>
        <div><strong>Payment:</strong> {{ $order->payment?->status ?? '-' }}</div>
        <div><strong>User:</strong> {{ $order->user?->username ?? '-' }}</div>
        
    </div>

    <hr>

    <!-- Order Items -->
    <h3 class="mt-3">Order Items</h3>
    <div class="order-items-list">
        @foreach ($order->order_items as $item)
            <div class="order-item-row">
                <div class="item-image">
                    @if ($item->product?->image_url)
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product?->name ?? '-' }}" 
                             style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                    @else
                        <span class="no-image text-muted"><i class="fas fa-image fa-lg"></i></span>
                    @endif
                </div>

                <div class="item-details">
                    <div class="item-name fw-semibold">{{ $item->product?->name ?? '-' }}</div>
                    <div class="item-info text-muted">Qty: {{ $item->quantity }} × ${{ $item->price }}</div>
                </div>

                <div class="item-total fw-semibold">${{ $item->total_price }}</div>
            </div>
        @endforeach
    </div>

    <hr>

    <!-- Shipping -->
    <h3>Shipping Information</h3>
    <div class="shipping-info">
        <div><strong>Name:</strong> {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</div>
        <div><strong>Phone:</strong> {{ $order->shipping_phone }}</div>
        <div><strong>Shipping Method:</strong> {{ $order->shippingMethod && $order->shippingMethod->name ? $order->shippingMethod->name : ($order->shippingMethod->id ?? '-') }}</div>
        <div><strong>Payment Method:</strong> {{ $order->payment?->name ?? ($order->payment?->payment_method ?? '-') }}</div>
        <div><strong>Address:</strong> {{ $order->shipping_address }}</div>
    </div>

    <hr>

    <!-- Notes -->
    <h3>Order Notes</h3>
    <div class="order-notes border p-2 rounded bg-light">
        {{ $order->order_notes ?? '-' }}
    </div>

    <hr>

    <!-- Sticky Action Bar for Admin Actions -->
    <div class="sticky-action-bar">
            <div style="display: flex; gap: 1.2rem; align-items: center; width: 100%; justify-content: flex-end; padding: 18px 18px 18px 0; flex-wrap: nowrap;">
                <form method="POST" action="{{ route('orders.admin_mark_delivered', $order->order_id) }}" style="display: flex; align-items: center; margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Mark this order as delivered?')">
                        <i class="fas fa-check"></i> <span class="btn-text">Mark as Delivered</span>
                    </button>
                </form>
                <form method="POST" action="{{ route('orders.admin_update_status', $order->order_id) }}" style="display: flex; align-items: center; margin: 0;">
                    @csrf
                    <select name="status" class="form-select" style="max-width:200px; margin-right:8px;">
                        @foreach (['pending','confirmed','processing','shipped','delivered','completed','cancelled','refunded'] as $status)
                            <option value="{{ $status }}" @if($order->status == $status) selected @endif>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> <span class="btn-text">Update</span>
                    </button>
                </form>
                <a href="{{ route('admin.manage.orders') }}" class="btn btn-secondary" style="display: flex; align-items: center; margin: 0;">
                    <i class="fas fa-arrow-left"></i> <span class="btn-text">Back to Dashboard</span>
                </a>
            </div>
</div>

@endsection
