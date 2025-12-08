@extends('layouts.app')
@section('title', 'Cart')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
/* (same CSS as before; kept short here) */
body { background: transparent; margin:0; padding:0; color:#232b3b; }
.cart-container { max-width:1200px; margin:2rem auto; padding:1rem; }
.cart-header { display:flex; justify-content:center; align-items:center; padding:1rem 2rem; background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius:12px 12px 0 0; color:#fff; position:relative; }
.header-icon { position:absolute; left:2rem; font-size:1.5rem; }
.cart-title { font-size:1.5rem; font-weight:700; }
.item-count { color:#fff; font-size:1.1rem; opacity:0.95; margin-left:0.5rem; }
.cart-content { display:flex; gap:2rem; margin-top:1rem; }
.cart-items { flex:2; min-width:300px; background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); overflow:hidden; }
.items-header { display:grid; grid-template-columns:3fr 1fr 1.5fr 1fr auto; font-weight:600; padding:1rem 1.5rem; background:#aeb6be; border-bottom:1px solid #e9ecef; }
.cart-item { display:grid; grid-template-columns:3fr 1fr 1.5fr 1fr auto; align-items:center; padding:1rem 1.5rem; border-bottom:1px solid #e9ecef; }
.product-image img { width:60px; height:60px; object-fit:cover; border-radius:6px; }
.quantity-controls { display:flex; gap:0.25rem; justify-content:center; align-items:center; }
.qty-btn { width:30px; height:30px; border-radius:50%; border:none; background:#e9ecef; cursor:pointer; }
.qty-input { width:60px; text-align:center; border-radius:6px; border:1px solid #ccc; padding:0.25rem;}
.item-total, .item-price { text-align:center; }
.order-summary { flex:1; background:#fff; padding:1rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); height:fit-content; }
.empty-cart { text-align:center; background:#fff; padding:2rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
@media(max-width:992px){ .items-header{display:none;} .cart-item{grid-template-columns:1fr 1fr; grid-template-rows:auto auto auto;} }
</style>

<div class="cart-container">
    
    <div class="cart-header">
        <i class="fas fa-shopping-cart header-icon"></i>
        <h1 class="cart-title" style="color: #f8f9fa; padding-top: 1rem;">Items in Cart</h1>
        <div style="position:absolute; right:2rem; ">
            <spann style="color: #ffffff; padding: 0.5rem; border-radius: 50px; border: 0.2rem solid #ffffff" class="item-count">{{ $products->sum('quantity') }}</span>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="empty-cart mt-4">
            <div class="empty-cart-icon"><i class="fas fa-shopping-cart"></i></div>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
        </div>
    @else
    <div class="cart-content">
        <div class="cart-items">
            <div class="items-header">
                <span>Product</span><span>Price</span><span>Quantity</span><span>Total</span><span></span>
            </div>

            @foreach($products as $product)
            <div class="cart-item" data-item-id="{{ $product['id'] }}">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <div class="product-image">
                        @if($product['image_url'])
                            <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}">
                        @else
                            <div style="width:60px;height:60px;background:#e9ecef;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#aaa;">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div style="font-weight:700;">{{ $product['name'] }}</div>
                        <div style="font-size:0.85rem;color:#666;">{{ $product['category'] }} • {{ $product['stock'] }} in stock</div>
                    </div>
                </div>

                <div class="item-price">${{ number_format($product['price'], 2) }}</div>

                <div class="item-quantity">
                    <div class="quantity-controls" data-item-id="{{ $product['id'] }}">
                        <button type="button" class="qty-btn minus">-</button>
                        <input type="number" class="qty-input" value="{{ $product['quantity'] }}" min="1" max="{{ $product['stock'] }}">
                        <button type="button" class="qty-btn plus">+</button>
                    </div>
                </div>

                <div class="item-total">${{ number_format($product['price'] * $product['quantity'], 2) }}</div>

                <div class="item-action">
                    <button class="remove-btn" data-item-id="{{ $product['id'] }}" style="background:transparent;border:none;color:#dc3545;cursor:pointer;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="mb-2">Subtotal: <span class="total-with-tax">${{ number_format($subtotal, 2) }}</span></div>
            <!-- Add more summary details if needed -->
            <a href="{{ route('checkout.index') }}" class="btn btn-success w-100" style="margin-top:1.5rem;font-size:1.15rem;font-weight:700;">
                <i class="fa fa-arrow-right me-1"></i> Proceed to Checkout
            </a>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // helper: recalc subtotal based on DOM item totals
    function recalcFromDom() {
        let subtotal = 0;
        let count = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const totalText = item.querySelector('.item-total').textContent.replace(/[$,]/g,'') || '0';
            subtotal += parseFloat(totalText);
            const qty = parseInt(item.querySelector('.qty-input').value) || 0;
            count += qty;
        });
        const subEl = document.querySelector('.total-with-tax');
        if (subEl) subEl.textContent = `$${subtotal.toFixed(2)}`;
        const countEl = document.querySelector('.item-count');
        if (countEl) countEl.textContent = count;
    }

    // update cart endpoint
    function updateCart(itemId, quantity) {
        fetch(`/cart/update/${itemId}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity })
        })
        .then(res => res.json())
        .then(data => {
            if (!data || !data.success) {
                // show message or clamp to max if provided by server
                if (data && data.max) {
                    const input = document.querySelector(`.cart-item[data-item-id="${itemId}"] .qty-input`);
                    input.value = data.max;
                }
                return;
            }

            // update item total from server-calculated item_total if available
            const row = document.querySelector(`.cart-item[data-item-id="${itemId}"]`);
            if (row && typeof data.item_total !== 'undefined') {
                row.querySelector('.item-total').textContent = `$${(parseFloat(data.item_total) || 0).toFixed(2)}`;
            } else if (row) {
                // fallback: compute from DOM price * qty
                const price = parseFloat(row.querySelector('.item-price').textContent.replace(/[$,]/g,'')) || 0;
                row.querySelector('.item-total').textContent = `$${(price * quantity).toFixed(2)}`;
            }

            // update subtotal & item count using server values if present
            if (typeof data.subtotal !== 'undefined') {
                document.querySelector('.total-with-tax').textContent = `$${parseFloat(data.subtotal).toFixed(2)}`;
            } else {
                recalcFromDom();
            }

            if (typeof data.item_count !== 'undefined') {
                document.querySelector('.item-count').textContent = data.item_count;
            } else {
                recalcFromDom();
            }
        })
        .catch(err => {
            console.error('Update cart error', err);
        });
    }

    // attach quantity controls
    document.querySelectorAll('.quantity-controls').forEach(control => {
        const input = control.querySelector('.qty-input');
        const minus = control.querySelector('.minus');
        const plus = control.querySelector('.plus');
        const itemId = control.dataset.itemId;

        const clampAndUpdate = (val) => {
            if (!val || isNaN(val)) val = 1;
            val = parseInt(val);
            const max = parseInt(input.getAttribute('max')) || 9999;
            if (val < 1) val = 1;
            if (val > max) val = max;
            input.value = val;
            updateCart(itemId, val);
        };

        minus.addEventListener('click', () => clampAndUpdate(parseInt(input.value) - 1));
        plus.addEventListener('click', () => clampAndUpdate(parseInt(input.value) + 1));
        input.addEventListener('change', () => clampAndUpdate(parseInt(input.value)));
    });

    // remove item
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.itemId;
            if (!confirm('Are you sure you want to remove this item?')) return;

            fetch(`/cart/remove/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    const el = document.querySelector(`.cart-item[data-item-id="${id}"]`);
                    if (el) el.remove();

                    // update subtotal & count from server
                    if (typeof data.subtotal !== 'undefined') {
                        document.querySelector('.total-with-tax').textContent = `$${parseFloat(data.subtotal).toFixed(2)}`;
                    } else {
                        recalcFromDom();
                    }
                    if (typeof data.item_count !== 'undefined') {
                        document.querySelector('.item-count').textContent = data.item_count;
                    } else {
                        recalcFromDom();
                    }

                    // if no items, show empty state
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        const items = document.querySelector('.cart-items');
                        const summary = document.querySelector('.order-summary');
                        if (items) items.style.display = 'none';
                        if (summary) summary.style.display = 'none';
                        // optionally reload to show empty-cart blade
                        location.reload();
                    }
                }
            })
            .catch(err => console.error(err));
        });
    });

    // initial recalc (in case server-side & display mismatch)
    recalcFromDom();
});
</script>

@endsection
