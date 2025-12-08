@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
@php
    $products = isset($products) ? $products : [];
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
:root {
    --primary-color: #6366f1;
    --primary-dark: #4f46e5;
    --primary-light: #a5b4fc;
    --secondary-color: #f1f5f9;
    --success-color: #10b981;
    --success-dark: #059669;
    --warning-color: #f59e0b;
    --warning-light: #fde68a;
    --danger-color: #ef4444;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --border-color: #e2e8f0;
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    min-height: 100vh;
}

.checkout-container {
    max-width: 1400px;
    margin: 2rem auto 0;
    padding: 0 1rem 4rem;
}

.checkout-title {
    text-align: center;
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 3rem;
    background: linear-gradient(135deg, var(--primary-color), var(--success-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

@media (min-width: 768px) {
    .checkout-container {
        padding: 0 2rem 4rem;
    }
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 1024px) {
    .checkout-grid {
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
    }
}

.checkout-main {
    order: 1;
}

.checkout-summary-col {
    order: 2;
    position: relative;
}

@media (min-width: 1024px) {
    .checkout-main {
        order: 1;
    }
    
    .checkout-summary-col {
        order: 2;
        position: sticky;
        top: 2rem;
        align-self: start;
    }
}

.checkout-section {
    background: var(--white);
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
    padding: 2rem;
    border: 1px solid var(--border-color);
    position: relative;
    transition: var(--transition);
}

.checkout-section:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.checkout-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
    border-radius: var(--radius-2xl) var(--radius-2xl) 0 0;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.section-title::before {
    content: '';
    width: 6px;
    height: 6px;
    background: var(--primary-color);
    border-radius: 50%;
    flex-shrink: 0;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

@media (min-width: 640px) {
    .form-row {
        grid-template-columns: 1fr 1fr;
    }
}

.form-col {
    display: flex;
    flex-direction: column;
}

.form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
    letter-spacing: 0.025em;
}

.form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: var(--radius-lg);
    font-size: 1rem;
    background: var(--white);
    transition: var(--transition);
    color: var(--text-primary);
}

.form-control:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    transform: translateY(-1px);
}

.form-control:hover {
    border-color: var(--primary-light);
}

.shipping-method {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: var(--transition);
    background: var(--white);
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}

.shipping-method::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--border-color);
    transition: var(--transition);
}

.shipping-method.selected {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(16, 185, 129, 0.05));
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.shipping-method.selected::before {
    background: linear-gradient(180deg, var(--primary-color), var(--success-color));
}

.shipping-method:hover {
    border-color: var(--primary-light);
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

.shipping-method input[type="radio"] {
    width: 1.25rem;
    height: 1.25rem;
    accent-color: var(--primary-color);
    margin: 0;
    flex-shrink: 0;
}

.shipping-method label {
    flex: 1;
    cursor: pointer;
    margin: 0;
    line-height: 1.5;
}

.shipping-method strong {
    color: var(--text-primary);
    font-size: 1.125rem;
    display: block;
    margin-bottom: 0.25rem;
}

.shipping-method small {
    color: var(--text-secondary);
    font-size: 0.875rem;
}
.order-summary {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 1rem;
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
    max-width: 580px;
    min-width: 450px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.order-summary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--success-color));
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem 0;
    font-size: 1.125rem;
    border-bottom: 1px solid var(--border-color);
}

.summary-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.summary-total {
    border-top: 2px solid var(--primary-color);
    padding-top: 1.5rem;
    margin-top: 1rem;
    font-weight: 700;
    font-size: 1.5rem;
    color: var(--primary-color);
    background: linear-gradient(135deg, var(--primary-color), var(--success-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

#discount-row {
    display: none;
    color: var(--success-color);
    font-weight: 600;
}

#discount-row.show {
    display: flex;
}

.coupon-section {
    background: linear-gradient(135deg, var(--warning-light) 0%, #fef3c7 100%);
    border: 1px solid var(--warning-color);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}

.coupon-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--warning-color);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border: none;
    border-radius: 9999px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    white-space: nowrap;
    text-decoration: none;
    margin: 0.25rem;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    box-shadow: var(--shadow-md);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--white);
}

.btn-success {
    background: linear-gradient(135deg, var(--success-color), var(--success-dark));
    color: var(--white);
    box-shadow: var(--shadow-md);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--white);
}

.btn.w-100 {
    width: 100%;
    justify-content: center;
    padding: 1.25rem 2rem;
    font-size: 1.125rem;
    margin-top: 1.5rem;
}

.btn-use-saved {
    background: var(--success-color);
    color: var(--white);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    font-weight: 600;
    transition: var(--transition);
}

.btn-use-saved:hover {
    background: var(--success-dark);
    transform: translateY(-1px);
}

.error-message {
    color: var(--danger-color);
    font-size: 0.875rem;
    margin-top: 0.5rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.error-message::before {
    content: '⚠';
    color: var(--danger-color);
}
.order-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
    transition: var(--transition);
}

.order-item:last-child {
    border-bottom: none;
}

.order-item:hover {
    background: var(--gray-50);
    margin: 0 -1rem;
    padding: 1rem;
    border-radius: var(--radius-lg);
}

.item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: var(--radius-lg);
    margin-right: 1rem;
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--border-color);
    transition: var(--transition);
}

.item-image:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.item-image .fa-image {
    font-size: 1.5rem;
    color: var(--text-muted);
}

.item-details {
    flex: 1;
}

.item-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--text-primary);
    font-size: 1.125rem;
}

.item-price {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.item-total {
    font-weight: 700;
    color: var(--primary-color);
    font-size: 1.25rem;
}

.save-info-group {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
    border-radius: var(--radius-xl);
    border-left: 4px solid var(--primary-color);
    border: 1px solid var(--border-color);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
}

.checkbox-label:hover {
    color: var(--primary-color);
}

.checkbox-label input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
    cursor: pointer;
    accent-color: var(--primary-color);
    border-radius: var(--radius-sm);
}

.saved-info-section {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: var(--transition);
}

.saved-info-section:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.saved-info-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 1rem;
}

.saved-info-header i {
    color: var(--success-color);
    font-size: 1.25rem;
}

.saved-info-header span {
    font-weight: 600;
    color: var(--success-color);
    flex: 1;
    font-size: 1.125rem;
}

.saved-info-preview {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.6;
}

.saved-info-preview p {
    margin: 0.5rem 0;
}

.saved-info-preview strong {
    color: var(--text-primary);
    font-weight: 600;
}

/* Hidden elements */
.hidden {
    display: none !important;
}

#discount-row {
    display: none;
}

#discount-row.active {
    display: flex;
}

@media (max-width: 600px) {
    .checkout-section { 
        padding: 13px 7px 9px 7px; 
    }
    
    .checkout-container {
        margin: 0 0.5rem;
    }
    
    .row {
        margin: 0;
    }
    
    .col-md-8, .col-md-4 {
        padding: 0 0.5rem;
    }
    
    .section-header {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .btn {
        width: 100%;
        padding: 0.875rem;
        font-size: 1rem;
    }
    
    .shipping-method {
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .shipping-method h5 {
        font-size: 1rem;
    }
    
    .order-summary {
        margin-top: 2rem;
        padding: 1rem;
    }
    
    .summary-item {
        padding: 0.75rem 0;
        font-size: 0.9rem;
    }
    
    .summary-total {
        font-size: 1.125rem;
    }
    
    .order-item {
        padding: 0.75rem 0;
    }
    
    .item-image {
        width: 50px;
        height: 50px;
        margin-right: 0.75rem;
    }
    
    .item-name {
        font-size: 1rem;
    }
    
    .item-total {
        font-size: 1rem;
    }
    
    .save-info-group {
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .saved-info-section {
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    /* Improve touch targets on mobile */
    .form-control, .form-select {
        min-height: 44px;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .checkbox-label input[type="checkbox"] {
        width: 1.5rem;
        height: 1.5rem;
    }
    
    /* Better spacing for mobile */
    .mb-3 {
        margin-bottom: 1.5rem !important;
    }
    
    .mb-4 {
        margin-bottom: 2rem !important;
    }
}

@media (max-width: 480px) {
    .checkout-container {
        margin: 0 0.25rem;
    }
    
    .section-header {
        font-size: 1.125rem;
        text-align: center;
    }
    
    .shipping-method {
        padding: 0.875rem;
    }
    
    .order-summary {
        padding: 0.875rem;
    }
    
    .summary-item {
        font-size: 0.875rem;
    }
    
    .btn {
        font-size: 0.95rem;
        padding: 1rem;
    }
    
    /* Stack form elements on very small screens */
    .form-row .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .item-details {
        min-width: 0; /* Allow text truncation */
    }
    
    .item-name {
        font-size: 0.9rem;
        line-height: 1.3;
    }
}
</style>

<div class="checkout-container">
    <h1 class="checkout-title text-center mb-4">Checkout</h1>

    @php
        // Determine the user object
        $user = $user ?? auth()->user();

        // Robustly load the cart and cart items:
        // - Prefer $cart variable passed to the view
        // - Then check session('cart') (common for session-based carts)
        // - Then check session('cart.items') or session('cart.content') if packages store differently
        // - Finally, fall back to empty array
        $cartFromView = $cart ?? null;
        $sessionCart = session('cart');

        $cartObject = $cartFromView ?? $sessionCart;

        // If a shoppingcart package (like darryldecode/cart or Gloudemans) stores different props,
        // try common fallbacks: content(), items, or ->items
        $cartItems = [];

        if ($cartObject) {
            // If it's an array
            if (is_array($cartObject)) {
                // If it has 'items' key
                if (isset($cartObject['items']) && is_array($cartObject['items'])) {
                    $cartItems = $cartObject['items'];
                } else {
                    // assume it's already a list of items
                    $cartItems = $cartObject;
                }
            } elseif (is_object($cartObject)) {
                // If it's a collection
                if (method_exists($cartObject, 'count') && method_exists($cartObject, 'toArray') && $cartObject->count()) {
                    // If it's a collection of items
                    $cartItems = $cartObject instanceof \Illuminate\Support\Collection ? $cartObject->toArray() : (array) $cartObject;
                }
                // If object has items property
                if (empty($cartItems) && property_exists($cartObject, 'items')) {
                    $cartItems = $cartObject->items;
                }
                // If object has content() method (some packages)
                if (empty($cartItems) && method_exists($cartObject, 'content')) {
                    $content = $cartObject->content();
                    if (is_iterable($content)) {
                        $cartItems = is_object($content) && method_exists($content, 'toArray') ? $content->toArray() : (array) $content;
                    }
                }
            }
        }

        // If still an Illuminate collection instance, convert it to array so Blade foreach works consistently
        if ($cartItems instanceof \Illuminate\Support\Collection) {
            $cartItems = $cartItems->toArray();
        }

        // Normalize cart items so we can compute totals reliably
        $normalizedCartItems = [];
        foreach ($cartItems as $it) {
            // Accept either arrays or objects (from different cart implementations)
            if (is_array($it)) {
                // common keys: 'product', 'qty' or 'quantity', 'price', 'total', 'rowId'
                $product = $it['product'] ?? ($it['model'] ?? null);
                $quantity = $it['quantity'] ?? $it['qty'] ?? ($it['qty'] ?? 1);
                $price = $it['price'] ?? ($product['price'] ?? ($it['unit_price'] ?? 0));
                $total = $it['total_price'] ?? $it['total'] ?? ($price * $quantity);
                $normalizedCartItems[] = [
                    'product' => $product,
                    'quantity' => (int) $quantity,
                    'price' => (float) $price,
                    'total' => (float) $total,
                ];
            } elseif (is_object($it)) {
                $product = property_exists($it, 'product') ? $it->product : (property_exists($it, 'model') ? $it->model : null);
                $quantity = $it->quantity ?? $it->qty ?? ($it->qty ?? 1);
                $price = $it->price ?? ($product->price ?? ($it->unit_price ?? 0));
                $total = $it->total_price ?? $it->total ?? ($price * $quantity);
                $normalizedCartItems[] = [
                    'product' => $product,
                    'quantity' => (int) $quantity,
                    'price' => (float) $price,
                    'total' => (float) $total,
                ];
            }
        }

        // Final cart items for the view
        $cartItems = $normalizedCartItems;

        // Always compute subtotal from $products for accuracy
        $subtotal = 0.0;
        foreach ($products as $prod) {
            $subtotal += floatval(($prod['price'] ?? 0) * ($prod['quantity'] ?? 1));
        }

        $cartIsEmpty = empty($products) || (is_array($products) && count($products) === 0);
    @endphp

    @if($cartIsEmpty)
        <div class="alert alert-danger text-center">Your cart is empty.</div>
    @else
    {{-- Post to current URL by default; change action to a named route if you prefer --}}
    <script>
(function() {
    // Only run if not already submitted
    if (!window.__shippingPersisted) {
        window.__shippingPersisted = true;
        var saved = localStorage.getItem('checkout_selected_shipping_method');
        // Wait for DOMContentLoaded to ensure input exists
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.getElementById('persisted_shipping_method');
            var form = document.getElementById('checkout-form');
            if (saved && input && form && input.value !== saved) {
                input.value = saved;
                // don't auto-submit immediately; allow user to review. If you do want auto-submit, uncomment:
                // form.submit();
            }
        });
    }
})();
</script>
    <form method="POST" id="checkout-form" action="{{ route('checkout.process') }}">
        <input type="hidden" name="persisted_shipping_method" id="persisted_shipping_method" value="{{ old('shipping_method', isset($selectedShippingMethod) ? $selectedShippingMethod : '') }}">
        @if(request('product'))
            <input type="hidden" name="product" value="{{ request('product') }}">
        @endif
        @if(request('quantity'))
            <input type="hidden" name="quantity" value="{{ request('quantity') }}">
        @endif
        @csrf
        <div class="checkout-grid">
            <!-- Main Form Column -->
            <div class="checkout-main">
                <!-- Billing Info -->
                <div class="checkout-section">
                    <h3 class="section-title">Billing Information</h3>

                    @if(optional($user->profile)->billing_first_name)
                        <div class="saved-info-section">
                            <div class="saved-info-header">
                                <i class="fas fa-bookmark"></i>
                                <span>Use Saved Billing Information</span>
                                <button type="button" class="btn-use-saved" onclick="useSavedBilling()">Use Saved</button>
                            </div>
                            <div class="saved-info-preview">
                                <p><strong>{{ $user->profile->billing_first_name }} {{ $user->profile->billing_last_name }}</strong></p>
                                <p>{{ $user->profile->billing_phone }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label" for="first_name">First Name *</label>
                                <input id="first_name" name="first_name" required type="text" class="form-control" value="{{ old('first_name', optional($user->profile)->billing_first_name) }}">
                                @error('first_name')
                                    @if($errors->any())
                                        <div class="error-message">{{ $message }}</div>
                                    @endif
                                @enderror
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label" for="last_name">Last Name *</label>
                                <input id="last_name" name="last_name" required type="text" class="form-control" value="{{ old('last_name', optional($user->profile)->billing_last_name) }}">
                                @error('last_name')
                                    @if($errors->any())
                                        <div class="error-message">{{ $message }}</div>
                                    @endif
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address *</label>
                            <input id="email" name="email" required type="email" class="form-control" value="{{ old('email', optional($user->profile)->billing_email) }}">
                            @error('email')
                                <div class="error-message">{{ $message }}</div> 
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number *</label>
                            <input id="phone" name="phone" required type="tel" class="form-control" value="{{ old('phone', optional($user->profile)->billing_phone) }}">
                            @error('phone')
                                @if($errors->any())
                                    <div class="error-message">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group save-info-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="save_billing_info" id="save_billing_info" {{ old('save_billing_info') ? 'checked' : '' }}>
                            <i class="fas fa-bookmark"></i> Save this billing information for future orders
                        </label>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="checkout-section">
                    <h3 class="section-title">Shipping Information</h3>

                    @if(optional($user->profile)->shipping_address)
                        <div class="saved-info-section">
                            <div class="saved-info-header">
                                <i class="fas fa-bookmark"></i>
                                <span>Use Saved Shipping Information</span>
                                <button type="button" class="btn-use-saved" onclick="useSavedShipping()">Use Saved</button>
                            </div>
                            <div class="saved-info-preview">
                                <p><strong>{{ $user->profile->shipping_first_name }} {{ $user->profile->shipping_last_name }}</strong></p>
                                <p>{{ $user->profile->shipping_address }}</p>
                                <p>{{ $user->profile->shipping_city }}, {{ $user->profile->shipping_state }} {{ $user->profile->shipping_postal_code }}</p>
                                <p>{{ $user->profile->shipping_country }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label" for="shipping_first_name">First Name *</label>
                                <input id="shipping_first_name" name="shipping_first_name" required type="text" class="form-control" value="{{ old('shipping_first_name', optional($user->profile)->shipping_first_name) }}">
                                @error('shipping_first_name')
                                    @if($errors->any())
                                        <div class="error-message">{{ $message }}</div>
                                    @endif
                                @enderror
                            </div>
                        </div>

                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label" for="shipping_last_name">Last Name *</label>
                                <input id="shipping_last_name" name="shipping_last_name" required type="text" class="form-control" value="{{ old('shipping_last_name', optional($user->profile)->shipping_last_name) }}">
                                @error('shipping_last_name')
                                    @if($errors->any())
                                        <div class="error-message">{{ $message }}</div>
                                    @endif
                                @enderror
                            </div>
                        </div>
                    </div>

                    
                        
                    <div class="form-group">
                        <label class="form-label" for="shipping_phone">Phone Number *</label>
                        <input id="shipping_phone" name="shipping_phone" required type="tel" class="form-control" value="{{ old('shipping_phone', optional($user->profile)->shipping_phone) }}">
                        @error('shipping_phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                        
                    

                    <div class="form-group">
                        <label class="form-label" for="shipping_address">Address *</label>
                        <textarea id="shipping_address" name="shipping_address" required class="form-control">{{ old('shipping_address', optional($user->profile)->shipping_address) }}</textarea>
                        @error('shipping_address')
                            @if($errors->any())
                                <div class="error-message">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="form-group save-info-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="save_shipping_info" id="save_shipping_info" {{ old('save_shipping_info') ? 'checked' : '' }}>
                            <i class="fas fa-bookmark"></i> Save this shipping information for future orders
                        </label>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="checkout-section">
                    <h3 class="section-title">Shipping Method</h3>
                    @foreach($shipping_methods as $shipping_method)
                    <div class="shipping-method" data-cost="{{ $shipping_method->cost }}" data-id="{{ $shipping_method->id }}">
                        <input type="radio"
                               name="shipping_method"
                               value="{{ $shipping_method->id }}"
                               id="shipping_{{ $shipping_method->id }}"
                               @if(old('shipping_method', request()->input('persisted_shipping_method', isset($selectedShippingMethod) ? $selectedShippingMethod : null)) == $shipping_method->id)
                                    checked
                               @elseif(!$loop->index && !old('shipping_method') && !request()->input('persisted_shipping_method')) checked @endif
                               required>
                        <label for="shipping_{{ $shipping_method->id }}">
                            <strong>{{ $shipping_method->name }}</strong> - ${{ number_format($shipping_method->cost, 2) }}
                            <br>
                            <small class="text-muted">{{ $shipping_method->description }} ({{ $shipping_method->estimated_days }} days)</small>
                        </label>
                    </div>
                    @endforeach
                    @error('shipping_method')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="checkout-section">
                <h3 class="section-title">Payment Method</h3>
                    @foreach($payment_methods as $payment_method)
                    <div class="shipping-method">
                        <input type="radio"
                               name="payment_method"
                               value="{{ $payment_method->id }}"
                               id="payment_{{ $payment_method->id }}"
                               @if (old('payment_method', isset($selectedPaymentMethod) ? $selectedPaymentMethod : null) == $payment_method->id)
                                    checked
                               @elseif(!$loop->index && !old('payment_method')) checked @endif
                               required>
                        <label for="payment_{{ $payment_method->id }}">
                            <strong>{{ $payment_method->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $payment_method->description }}</small>
                        </label>
                    </div>
                    @endforeach
                </div>

                <!-- Order Notes -->
                <div class="checkout-section">
                    <h3 class="section-title">Order Notes (Optional)</h3>
                    <div class="form-group">
                        <label class="form-label" for="order_notes">Additional Notes</label>
                        <textarea id="order_notes" name="order_notes" class="form-control" rows="4">{{ old('order_notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary Column -->
            <div class="checkout-summary-col">
                <div class="checkout-section">
                    <h3 class="section-title">Order Summary</h3>
                    <div class="order-summary">
                        <!-- Cart Items -->
                        @foreach($products as $product)
                        @php
                            $quantity = $product['quantity'] ?? 1;
                            $total_price = ($product['price'] ?? 0) * $quantity;
                        @endphp
                        <div class="order-item">
                            @if($product['image_url'])
                                <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] ?? '' }}" class="item-image">
                            @else
                                <div class="item-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            <div class="item-details">
                                <div class="item-name" style="font-size:1.15rem;font-weight:700;line-height:1.3;color:#6366f1;margin-bottom:0.25rem;white-space:normal;word-break:break-word;">
                                    {{ $product['name'] ?? 'Unknown Product' }}
                                </div>
                                <div class="item-price" style="font-size:1rem;color:#64748b;margin-bottom:0.15rem;">Qty: {{ $quantity }} × <span style="color:#059669;font-weight:700;">${{ number_format($product['price'] ?? 0, 2) }}</span></div>
                            </div>
                            <div class="item-total" style="font-size:1.15rem;font-weight:700;color:#059669;">${{ number_format($total_price, 2) }}</div>
                        </div>
                        @endforeach
                        
                        <!-- Order Totals -->
                        <div class="summary-item">
                            <span>Subtotal:</span>
                            <span id="subtotal">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="summary-item">
                            <span>Shipping:</span>
                            <span id="shipping-cost" aria-live="polite">$0.00</span>
                        </div>
                        <div class="summary-item summary-total">
                            <span>Total:</span>
                            <span id="grand-total" aria-live="polite">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3" id="place-order-btn">
                            <i class="fas fa-lock"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    var radios = document.querySelectorAll('input[name="shipping_method"]');
    var saved = localStorage.getItem('checkout_selected_shipping_method');
    var found = false;
    if (saved && radios.length) {
        radios.forEach(function(radio) {
            if (radio.value === saved) {
                radio.checked = true;
                found = true;
            }
        });
    }
    if (!found && radios.length) {
        radios[0].checked = true;
    }

    // Always recalculate and update summary after restoring selection
    var shippingCostEl = document.getElementById('shipping-cost');
    var subtotalEl = document.getElementById('subtotal');
    var grandTotalEl = document.getElementById('grand-total');
    var selectedRadio = Array.from(radios).find(r => r.checked);
    var cost = 0;
    if (selectedRadio) {
        var container = selectedRadio.closest('.shipping-method');
        if (container && container.dataset.cost) {
            cost = parseFloat(container.dataset.cost) || 0;
        }
    }
    if (shippingCostEl) shippingCostEl.textContent = '$' + cost.toFixed(2);
    if (subtotalEl && grandTotalEl) {
        var subtotal = parseFloat(subtotalEl.textContent.replace(/[$,]/g, '')) || 0;
        grandTotalEl.textContent = '$' + (subtotal + cost).toFixed(2);
    }

    // Save selection and update summary on change
    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (radio.checked) {
                localStorage.setItem('checkout_selected_shipping_method', radio.value);
                var container = radio.closest('.shipping-method');
                var cost = 0;
                if (container && container.dataset.cost) {
                    cost = parseFloat(container.dataset.cost) || 0;
                }
                if (shippingCostEl) shippingCostEl.textContent = '$' + cost.toFixed(2);
                if (subtotalEl && grandTotalEl) {
                    var subtotal = parseFloat(subtotalEl.textContent.replace(/[$,]/g, '')) || 0;
                    grandTotalEl.textContent = '$' + (subtotal + cost).toFixed(2);
                }
                // Keep the persisted hidden input in sync so server can know user selection if necessary
                var persistedInput = document.getElementById('persisted_shipping_method');
                if (persistedInput) persistedInput.value = radio.value;
            }
        });
    });
});

// Small helpers for "Use Saved" buttons to copy profile values into the form
function useSavedBilling() {
    @if(optional($user->profile)->billing_first_name)
        document.getElementById('first_name').value = @json($user->profile->billing_first_name);
    @endif
    @if(optional($user->profile)->billing_last_name)
        document.getElementById('last_name').value = @json($user->profile->billing_last_name);
    @endif
    @if(optional($user->profile)->billing_email)
        document.getElementById('email').value = @json($user->profile->billing_email);
    @endif
    @if(optional($user->profile)->billing_phone)
        document.getElementById('phone').value = @json($user->profile->billing_phone);
    @endif
    document.getElementById('save_billing_info').checked = true;
}

function useSavedShipping() {
    @if(optional($user->profile)->shipping_first_name)
        document.getElementById('shipping_first_name').value = @json($user->profile->shipping_first_name);
    @endif
    @if(optional($user->profile)->shipping_last_name)
        document.getElementById('shipping_last_name').value = @json($user->profile->shipping_last_name);
    @endif
    @if(optional($user->profile)->shipping_phone)
        document.getElementById('shipping_phone').value = @json($user->profile->shipping_phone);
    @endif
    @if(optional($user->profile)->shipping_address)
        document.getElementById('shipping_address').value = @json($user->profile->shipping_address);
    @endif
    document.getElementById('save_shipping_info').checked = true;
}
</script>