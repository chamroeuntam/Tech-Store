<script>
document.addEventListener('DOMContentLoaded', function() {
    var filterBtn = document.querySelector('#search-form button[type="submit"]');
    if (filterBtn) {
        filterBtn.click();
    }
});
</script>
@extends('layouts.app')

@section('content')
<script>window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
@vite(['resources/css/product-index.css'])
@php
    $sliders = \App\Models\Slider::get();
@endphp
@include('slider.index', ['sliders' => $sliders])

<div class="container py-4">
    <!-- Notification (visible fallback if layout doesn't expose showNotification) -->
    <div id="product-notification" style="position:fixed;top:24px;right:-420px;z-index:9999;min-width:240px;max-width:420px;padding:14px 18px;color:#fff;border-radius:10px;box-shadow:0 8px 40px rgba(25,118,210,0.12);font-size:1rem;display:none;transition:right .32s ease,opacity .2s ease;opacity:0;text-align:left;">
        <span id="product-notif-icon" style="font-size:1.2rem;vertical-align:middle;margin-right:10px;">&nbsp;</span>
        <span id="product-notif-message" style="font-weight:600;">&nbsp;</span>
        <button id="product-notif-close" style="background:none;border:none;color:#fff;font-size:1.1rem;float:right;cursor:pointer;margin-left:12px;">&times;</button>
    </div>

    <div class="page-header mb-4 text-center">
        <h1 class="page-title">Our Products</h1>
        <p class="page-subtitle text-muted">Find products at great prices</p>

        <form id="search-form" method="GET" class="d-flex flex-wrap justify-content-center align-items-center gap-3 mt-3" autocomplete="off">
            <input type="text" name="search" id="search-input" class="form-control" style="max-width:220px;" placeholder="Search products..." value="{{ request('search') }}">
            <select name="category_id" id="category-select" class="form-select" style="max-width:170px;">
                <option value="">Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="number" name="price_min" id="price-min" class="form-control" style="max-width:110px;" placeholder="Min" min="0" value="{{ request('price_min') }}">
            <input type="number" name="price_max" id="price-max" class="form-control" style="max-width:110px;" placeholder="Max" min="0" value="{{ request('price_max') }}">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear</a>
        </form>
    </div>

    <div id="product-results">
        @if ($products->isEmpty())
            <p class="text-center fs-5">No products match your filter criteria.</p>
        @else
            <div class="product-grid" id="product-grid">
                @foreach ($products as $product)
                    <article class="product-card">
                        <a href="{{ route('products.show', $product->id) }}" class="product-card-link" style="text-decoration:none;color:inherit;">
                            <div class="product-image-container">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="product-image">
                                @endif
                            </div>
                        </a>

                        <div class="product-info">
                            <a href="{{ route('products.show', $product->id) }}" style="text-decoration:none;color:inherit;">
                                <h3 class="product-name text-truncate" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $product->name }}</h3>
                            </a>

                            <div class="product-meta-row">
                                @php $qty = (int)($product->quantity ?? 0); @endphp
                                <div class="d-flex justify-content-between align-items-center w-100" style="gap:1.5rem;">
                                    <span class="product-price" style="font-weight:700;font-size:1.1rem;color:#1976d2;">${{ number_format($product->price, 2) }}</span>
                                    <span class="d-flex align-items-center" style="gap:0.5rem;">
                                        <span class="product-quantity"><small>{{ $product->quantity ?? '—' }} left</small></span>
                                        <span class="stock-status {{ $qty > 5 ? 'stock-in' : ($qty > 0 ? 'stock-low' : 'stock-out') }}" style="font-size:0.95rem;">
                                            {{ $qty > 5 ? 'In Stock' : ($qty > 0 ? 'Low' : 'Out') }}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="product-category-row">
                                <span class="category-text">
                                    <i class="fa fa-tags"></i> {{ $product->category?->name ?? 'Uncategorized' }}
                                </span>
                                <form action="{{ route('wishlist.add', $product->id) }}" method="POST" class="wishlist-form m-0">
                                    @csrf
                                    <button type="submit" class="wishlist-btn" title="Add to wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                </form>
                            </div>

                            <p class="product-description line-clamp-2">{{ $product->description ?? 'No description available.' }}</p>

                            <div class="product-actions">
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form m-0">
                                    @csrf
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-cart-plus"></i> Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>

(function () {
    // Prevent double registration when blade is included multiple times
    if (window._productsIndexInit) return;
    window._productsIndexInit = true;

    // Provide a fallback showNotification function if the layout doesn't provide one.
    if (typeof window.showNotification !== 'function') {
        const notifEl = document.getElementById('product-notification');
        const notifIcon = document.getElementById('product-notif-icon');
        const notifMessage = document.getElementById('product-notif-message');
        const notifClose = document.getElementById('product-notif-close');
        let notifTimer = null;

        window.showNotification = function (message, isError = false) {
            console.log('[DEBUG] showNotification called', { message, isError });
            if (!notifEl) {
                alert(message);
                return;
            }
            notifIcon.innerHTML = isError ? '<i class="fa fa-times-circle"></i>' : '<i class="fa fa-check-circle"></i>';
            notifMessage.textContent = message;
            notifEl.style.background = isError ? '#b91c1c' : '#1976d2';
            notifEl.style.boxShadow = isError ? '0 4px 24px rgba(185,28,28,0.15)' : '0 4px 24px rgba(25,118,210,0.15)';
            notifEl.style.display = 'block';
            // slide in
            requestAnimationFrame(() => {
                notifEl.style.right = '24px';
                notifEl.style.opacity = '1';
            });
            if (notifTimer) clearTimeout(notifTimer);
            notifTimer = setTimeout(() => {
                notifEl.style.right = '-420px';
                notifEl.style.opacity = '0';
                setTimeout(() => { notifEl.style.display = 'none'; }, 320);
            }, 3800);
        };

        if (notifClose) {
            notifClose.addEventListener('click', function () {
                notifEl.style.right = '-420px';
                notifEl.style.opacity = '0';
                setTimeout(() => { notifEl.style.display = 'none'; }, 320);
            });
        }
    }

    // Utility to escape text inserted into DOM via JS
    function escapeHtml(unsafe) {
        if (!unsafe && unsafe !== 0) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Robust form submit for AJAX: attempts to parse JSON, but gracefully handles HTML/text responses
    async function submitFormAjax(form) {
        const action = form.getAttribute('action') || window.location.href;
        const method = (form.getAttribute('method') || 'POST').toUpperCase();
        const tokenInput = form.querySelector('input[name="_token"]');
        const token = tokenInput ? tokenInput.value : (window.Laravel && window.Laravel.csrfToken ? window.Laravel.csrfToken : '');

        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            // include form fields
            body: new URLSearchParams(new FormData(form))
        };

        try {
            const res = await fetch(action, options);

            const contentType = res.headers.get('content-type') || '';
            let parsed = null;

            if (contentType.includes('application/json')) {
                parsed = await res.json();
            } else {
                // read as text (could be HTML redirect page)
                const txt = await res.text();
                parsed = { __text: txt };
            }

            if (!res.ok) {
                // show error (prefer JSON.message)
                const errMsg = (parsed && parsed.message) ? parsed.message : (parsed && parsed.__text ? parsed.__text.replace(/(<([^>]+)>)/gi, ' ').trim().slice(0, 300) : `Request failed: ${res.status}`);
                window.showNotification(errMsg, true);
                return { ok: false, parsed };
            }

            // success path
            if (parsed && typeof parsed === 'object' && 'success' in parsed) {
                const message = parsed.message || (parsed.success ? 'Action completed.' : 'Operation failed.');
                window.showNotification(message, !parsed.success);
                // update cart counter if returned
                if (parsed.cartCount !== undefined) {
                    const c = document.getElementById('cart-item-count');
                    if (c) c.textContent = parsed.cartCount;
                }
                return { ok: !!parsed.success, parsed };
            }

            // fallback: show extracted text or generic success
            if (parsed && parsed.__text) {
                const snippet = parsed.__text.replace(/(<([^>]+)>)/gi, ' ').trim().slice(0, 300);
                const msg = snippet || 'Action completed successfully.';
                window.showNotification(msg, false);
            } else {
                window.showNotification('Action completed successfully.', false);
            }

            return { ok: true, parsed };

        } catch (err) {
            console.error('submitFormAjax error', err);
            window.showNotification(err && err.message ? err.message : 'An unexpected error occurred', true);
            return { ok: false, error: err };
        }
    }

    // Delegated submit handler for add-to-cart and wishlist forms
    document.body.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        if (form.classList.contains('add-to-cart-form') || form.classList.contains('wishlist-form')) {
            console.log('[DEBUG] AJAX form submit intercepted', { form });
            e.preventDefault();
            e.stopPropagation();

            // disable submit button while request runs
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            const prevDisabled = submitBtn ? submitBtn.disabled : false;
            if (submitBtn) submitBtn.disabled = true;

            submitFormAjax(form).finally(() => {
                if (submitBtn) submitBtn.disabled = prevDisabled;
            });
        }
    });

    // AJAX filter handler: replaces product grid and notifications should continue to work because of delegation above
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const params = new URLSearchParams(new FormData(this)).toString();
            fetch('{{ url('/products/ajax-filter') }}?' + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(async r => {
                if (!r.ok) {
                    const txt = await r.text().catch(()=> '');
                    const msg = txt ? txt.slice(0,120) : `Request failed: ${r.status}`;
                    window.showNotification(msg, true);
                    return null;
                }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                const products = data.products || [];
                let html = '';
                if (products.length === 0) {
                    html = '<p class="text-center fs-5">No products match your filter criteria.</p>';
                } else {
                    html = '<div class="product-grid">';
                    products.forEach(product => {
                        const cat = product.category && product.category.name ? product.category.name : 'Uncategorized';
                        const qty = Number(product.quantity || 0);
                        const stockClass = qty > 5 ? 'stock-in' : (qty > 0 ? 'stock-low' : 'stock-out');
                        const stockText  = qty > 5 ? 'In Stock' : (qty > 0 ? 'Low' : 'Out');
                        const noimage = '{{ asset('storage/assets/default-image.jpg') }}';
                        const img = product.image_url || noimage;
                        const desc = product.description ?? 'No description available.';
                        html += `
                            <article class="product-card">
                            <a href="/products/${product.id}" class="product-card-link" style="text-decoration:none;color:inherit;">
                                <div class="product-image-container">
                                <img src="${img}" alt="${escapeHtml(product.name)}" class="product-image">
                                </div>
                            </a>
                            <div class="product-info">
                                <a href="/products/${product.id}" style="text-decoration:none;color:inherit;">
                                <h3 class="product-name text-truncate" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(product.name)}</h3>
                                </a>
                                <div class="product-meta-row">
                                    <div class="d-flex justify-content-between align-items-center w-100" style="gap:1.5rem;">
                                        <span class="product-price" style="font-weight:700;font-size:1.1rem;color:#1976d2;">$${Number(product.price).toFixed(2)}</span>
                                        <span class="d-flex align-items-center" style="gap:0.5rem;">
                                            <span class="product-quantity"><small>${product.quantity ?? '—'} left</small></span>
                                            <span class="stock-status ${stockClass}" style="font-size:0.95rem;">${stockText}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="product-category-row">
                                <span class="category-text"><i class="fa fa-tags"></i> ${escapeHtml(cat)}</span>
                                <form action="/wishlist/add/${product.id}" method="POST" class="wishlist-form m-0">
                                    <input type="hidden" name="_token" value="${window.Laravel.csrfToken}">
                                    <button type="submit" class="wishlist-btn"><i class="fa fa-heart"></i></button>
                                </form>
                                </div>
                                <p class="product-description line-clamp-2">${escapeHtml(desc)}</p>
                                <div class="product-actions">
                                    <form action="/cart/add/${product.id}" method="POST" class="add-to-cart-form m-0">
                                        <input type="hidden" name="_token" value="${window.Laravel.csrfToken}">
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-cart-plus"></i> Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                            </article>`;
                    });
                    html += '</div>';
                }
                document.getElementById('product-results').innerHTML = html;
                // delegation stays active so forms in new HTML are already handled
            })
            .catch(err => {
                console.error('Filter error', err);
                window.showNotification('Failed to load products', true);
            });
        });
    }

})();
</script>
@endpush

@endsection