@extends('layouts.app')
@section('title', 'Dashboard - Product Management')
@section('content')
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
    body {
        font-family: 'Kantumruy Pro', sans-serif;
        background: #f8f9fa;
        margin: 0;
        padding: 0;
        color: #23272f;
    }
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }
    .dashboard-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding: 2rem 1rem;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border-radius: 16px;
        border: 1px solid rgba(99, 102, 241, 0.1);
    }
    .dashboard-title {
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        color: #6133ea;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    .dashboard-subtitle {
        color: #888;
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.8;
    }
    .dashboard-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        text-align: center;
        min-width: 140px;
        justify-content: center;
        box-shadow: 0 2px 16px rgba(80, 68, 196, 0.08);
        min-height: 44px;
        color: #fff;
    }
    .btn-primary {
        background: linear-gradient(90deg, #6133ea 0%, #10b981 100%);
        color: #fff;
        border: none;
        box-shadow: 0 2px 16px rgba(99, 102, 241, 0.12);
        font-weight: 700;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #4b25be 0%, #059669 100%);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(97, 51, 234, 0.4);
    }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; transform: translateY(-2px); box-shadow: 0 10px 15px -3 rgba(239,68,68,0.3); }
    .btn-secondary { background: #f5f5f5; color: #23272f; border: 1px solid #e5e7eb; }
    .btn-secondary:hover { background: #e5e7eb; transform: translateY(-2px); }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .stat-card {
        background: #fff;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(80,68,196,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.2s ease;
        text-align: center;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(80,68,196,0.15); }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }
    .stat-icon.primary { background: rgba(99,102,241,0.1); color: #6133ea; }
    .stat-icon.success { background: rgba(16,185,129,0.1); color: #10b981; }
    .stat-icon.warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .stat-icon.danger { background: rgba(239,68,68,0.1); color: #dc3545; }
    .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.9rem; color: #888; font-weight: 500; }

    .products-section { margin-top: 2rem; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .section-title { font-size: 1.5rem; font-weight: 700; margin:0; }

    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px,1fr)); gap: 1.5rem; margin-top:1.5rem; padding:0; }
    .product-card { background: #fff; border-radius:16px; box-shadow:0 2px 16px rgba(80,68,196,0.08); border:1px solid rgba(0,0,0,0.05); overflow:hidden; transition:all 0.2s ease; height: fit-content; }
    .product-card:hover { transform: translateY(-4px); box-shadow:0 8px 25px rgba(80,68,196,0.15); }
    .product-image { width:100%; height:200px; object-fit:cover; border-bottom:1px solid rgba(0,0,0,0.05); }
    .product-image-placeholder { width:100%; height:200px; background:#f8f9fa; display:flex; align-items:center; justify-content:center; color:#aaa; font-weight:500; font-size:1rem; border-bottom:1px solid rgba(0,0,0,0.05); flex-direction: column; gap:0.25rem; }
    .product-info { padding:1.5rem; }
    .product-name { min-height:67.2px; ze:1.1rem; font-weight:600; margin-bottom:0.75rem; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .product-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:0.5rem; }
    .product-price { font-size:1.25rem; font-weight:700; color:#6133ea; }
    .product-stock { display:flex; align-items:center; gap:0.5rem; font-size:0.9rem; font-weight:500; }
    .stock-badge { padding:0.25rem 0.75rem; border-radius:20px; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; }
    .stock-out { background: rgba(239,68,68,0.1); color:#dc3545; }
    .stock-low { background: rgba(245,158,11,0.1); color:#f59e0b; }
    .stock-in { background: rgba(16,185,129,0.1); color:#10b981; }
    .product-description {min-height: 64.8px; font-size:0.9rem; color:#888; line-height:1.5; margin-bottom:1.25rem; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
    .product-actions { display:flex; gap:0.75rem; }
    .product-actions .btn { flex:1; min-width:auto; padding:0.5rem 1rem; font-size:0.875rem; min-height:40px; }
    .empty-state { text-align:center; padding:4rem 2rem; background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(80,68,196,0.08); border:1px solid rgba(0,0,0,0.05); }
    .empty-state-icon { width:80px; height:80px; background: rgba(99,102,241,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; font-size:2rem; color:#6133ea; }
    .empty-state h3 { font-size:1.5rem; margin-bottom:1rem; color:#23272f; font-weight:600; }
    .empty-state p { font-size:1rem; margin-bottom:2rem; color:#888; max-width:400px; margin-left:auto; margin-right:auto; }

    @media(max-width:768px) {
        .dashboard-actions { flex-direction: column; }
        .stats-grid { grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap:1rem; }
        .product-grid { grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:1rem; }
        .product-image { height:180px; }
    }
    @media(max-width:480px) {
        .stats-grid { grid-template-columns:1fr; }
        .product-grid { grid-template-columns:1fr; gap:1rem; }
        .product-image { height:160px; }
    }

    .dashboard-actions .btn-products {
        background: #6133ea;
        color: #fff;
    }
    .dashboard-actions .btn-categories {
        background: #10b981;
        color: #fff;
    }
    .dashboard-actions .btn-shipping {
        background: #2563eb;
        color: #fff;
    }
    .dashboard-actions .btn-payment {
        background: #f59e0b;
        color: #fff;
    }
    .dashboard-actions .btn-products:hover {
        background: #4b25be;
    }
    .dashboard-actions .btn-categories:hover {
        background: #059669;
    }
    .dashboard-actions .btn-shipping:hover {
        background: #1e40af;
    }
    .dashboard-actions .btn-payment:hover {
        background: #d97706;
    }
</style>
</head>
<body>
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Product Management</h1>
        <p class="dashboard-subtitle">Manage your stock and products</p>
        <div class="dashboard-actions">
            <a href="#" class="btn btn-products"><i class="fa fa-cog"></i> Manage Products</a>
            <a href="#" class="btn btn-categories"><i class="fa fa-cog"></i> Manage Categories</a>
            <a href="{{ route('dashboard.shipping.index') }}" class="btn btn-shipping"><i class="fa fa-truck"></i> Manage Shipping Methods</a>
            <a href="{{ route('dashboard.payment-method.index') }}" class="btn btn-payment"><i class="fa fa-credit-card"></i> Manage Payment Methods</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon primary"><i class="fa fa-box"></i></div><div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div><div class="stat-label">Total Products</div></div>
        <div class="stat-card"><div class="stat-icon success"><i class="fa fa-check-circle"></i></div><div class="stat-value">{{ number_format($stats['in_stock'] ?? 0) }}</div><div class="stat-label">In Stock</div></div>
        <div class="stat-card"><div class="stat-icon warning"><i class="fa fa-exclamation-triangle"></i></div><div class="stat-value">{{ number_format($stats['low_stock'] ?? 0) }}</div><div class="stat-label">Low Stock</div></div>
        <div class="stat-card"><div class="stat-icon danger"><i class="fa fa-times-circle"></i></div><div class="stat-value">{{ number_format($stats['out_of_stock'] ?? 0) }}</div><div class="stat-label">Out of Stock</div></div>
    </div>

    <!-- Products Section -->
    <div class="products-section">
        <div class="section-header">
            <h2 class="section-title">Your Products</h2>
            <div class="section-actions">
                <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add New</a>
            </div>
        </div>

        <div class="product-grid">
            @if(count($products) === 0)
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fa fa-box-open"></i></div>
                    <h3>No Products</h3>
                    <p>You haven't added any products yet. Click the button below to start adding products to your store.</p>
                    <a href="{{ route('dashboard.products.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Your First Product</a>
                </div>
            @endif
            @foreach($products as $product)
            @php
                // Normalize image URL: allow stored value to include 'storage/' or be a path
                $imageUrl = null;
                if (!empty($product->image)) {
                    if (str_starts_with($product->image, 'http') ) {
                        $imageUrl = $product->image;
                    } elseif (str_starts_with($product->image, 'storage/')) {
                        $imageUrl = asset($product->image);
                    } else {
                        $imageUrl = asset('storage/' . ltrim($product->image, '/'));
                    }
                }

                $qty = $product->quantity ?? ($product->stock ?? 0);
                if ($qty > 5) {
                    $stockClass = 'stock-in';
                    $stockText = 'In Stock';
                } elseif ($qty > 0) {
                    $stockClass = 'stock-low';
                    $stockText = 'Low Stock';
                } else {
                    $stockClass = 'stock-out';
                    $stockText = 'Out of Stock';
                }
            @endphp

            <div class="product-card">
                <a href="{{ route('products.index', $product->id) }}" class="product-link">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="product-image">
                    @else
                        <div class="product-image-placeholder"><i class="fa fa-image"></i> No Image</div>
                    @endif
                </a>
                <div class="product-info">
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <div class="product-meta">
                        <span class="product-price">${{ number_format($product->price ?? 0, 2) }}</span>
                        <div class="product-stock">
                            <span>{{ $qty }} units</span>
                            <span class="stock-badge {{ $stockClass }}">{{ $stockText }}</span>
                        </div>
                    </div>
                    <p class="product-description">{{ $product->description }}</p>
                    <div class="product-actions">
                        <a href="{{ route('dashboard.products.edit', $product->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                        <form action="{{ route('dashboard.products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</body>
@endsection
