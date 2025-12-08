@extends('layouts.app')
@section('title', 'Product Wishlist')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&display=swap" rel="stylesheet">
@vite('resources/css/wishlist.css')

<div class="wishlist-container">
    <div class="wishlist-header mb-4 d-flex align-items-center justify-content-between position-relative">
        <i class="fas fa-heart header-icon text-white"></i>
        <h2 class="header-title text-center flex-grow-1 m-0">Wishlist i want to buys</h2>
        <div class="wishlist-count">{{ $wishlist_count ?? 0 }} items</div>
    </div>


    @if ($items->isEmpty())
        <div class="empty-wishlist">
            <i class="far fa-heart"></i>
            <h2>No items in your wishlist</h2>
            <p>It looks like you haven't added anything to your wishlist yet.</p>
        </div>
    @else
        <div class="wishlist-table-container">
            <table class="wishlist-table" id="wishlist-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr data-id="{{ $item['id'] }}">
                        <td>
                            @if ($item['image_url'])
                                <img src="{{ $item['image_url'] }}" class="product-image" alt="{{ $item['name'] }}">
                            @else
                                <div class="image-placeholder"><i class="fas fa-camera"></i></div>
                            @endif
                        </td>
                        <td>{{ $item['name'] }}</td>
                        <td>${{ number_format($item['price'], 2) }}</td>
                        <td>
                            <span class="stock-status {{ $item['in_stock'] ? 'stock-in' : 'stock-out' }}">
                                {{ $item['in_stock'] ? ($item['stock'] . ' in stock') : 'Out of Stock' }}
                            </span>
                        </td>
                        <td class="btn-action">
                            @if ($item['in_stock'])
                                <form method="POST" action="{{ route('cart.add', $item['product_id']) }}" style="display:inline-block;">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] ?? 1 }}">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Add to cart</button>
                                </form>
                            @else
                                <button class="btn btn-disabled" disabled><i class="fas fa-times-circle"></i> out of stock</button>
                            @endif

                            <form method="POST" action="{{ route('wishlist.remove', $item['id']) }}" style="display:inline-block; margin-left:0.5rem;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mobile-card-container" id="mobile-cards">
            @foreach ($items as $item)
            <div class="mobile-card" data-id="{{ $item['id'] }}">
                <div class="mobile-card-content">
                    <div class="mobile-card-header">
                        @if ($item['image_url'])
                            <img src="{{ $item['image_url'] }}" class="product-image" alt="{{ $item['name'] }}">
                        @else
                            <div class="image-placeholder"><i class="fas fa-camera"></i></div>
                        @endif
                        <div class="mobile-card-info">
                            <div>{{ $item['name'] }}</div>
                            <div>${{ number_format($item['price'], 2) }}</div>
                            <span class="stock-status {{ $item['in_stock'] ? 'stock-in' : 'stock-out' }}">
                                {{ $item['in_stock'] ? ($item['stock'] . ' in stock') : 'Out of Stock' }}
                            </span>
                        </div>
                    </div>
                    <div class="btn-action">
                            @if ($item['in_stock'])
                            <form method="POST" action="{{ route('cart.add', $item['product_id']) }}" style="display:inline-block;">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] ?? 1 }}">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-shopping-cart"></i> Add to cart</button>
                            </form>
                        @else
                            <button class="btn btn-disabled" disabled><i class="fas fa-times-circle"></i> Out of stock</button>
                        @endif

                        <form method="POST" action="{{ route('wishlist.remove', $item['id']) }}" style="display:inline-block; margin-left:0.5rem;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Remove</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <div class="wishlist-footer">
        <a href="{{ url('/products') }}" class="footer-btn footer-btn-secondary"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
        <a href="{{ route('cart.index') }}" class="footer-btn footer-btn-primary"><i class="fas fa-shopping-cart"></i> View Cart</a>
    </div>
</div>

<script>

function updateCount() {
    // Prefer counting items that are actually visible to avoid double-counting
    const tableContainer = document.querySelector('.wishlist-table-container');
    const mobileContainer = document.getElementById('mobile-cards');
    let totalItems = 0;

    const isVisible = (el) => {
        if (!el) return false;
        return window.getComputedStyle(el).display !== 'none';
    };

    if (isVisible(tableContainer)) {
        totalItems = tableContainer.querySelectorAll('tbody tr').length;
    } else if (isVisible(mobileContainer)) {
        totalItems = mobileContainer.querySelectorAll('.mobile-card').length;
    } else {
        // Fallback: count unique data-id attributes across both lists to avoid duplicates
        const ids = new Set();
        document.querySelectorAll('#wishlist-table tbody tr').forEach(tr => ids.add(tr.dataset.id));
        document.querySelectorAll('#mobile-cards .mobile-card').forEach(card => ids.add(card.dataset.id));
        totalItems = ids.size;
    }

    const countEl = document.getElementById('wishlist-count');
    if (countEl) countEl.textContent = totalItems + ' items';

    // Simple check to show/hide empty state
    if (totalItems === 0) {
        if (tableContainer) tableContainer.style.display = 'none';
        if (mobileContainer) mobileContainer.style.display = 'none';

        const emptyState = document.querySelector('.empty-wishlist');
        if (emptyState) emptyState.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Initial call in case items are loaded
    updateCount();
});
</script>
@endsection