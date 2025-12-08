@extends('layouts.app')

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

@section('content')
<style>
/* your full CSS unchanged */
:root{
    --bg: #f3f4f6;
    --card-bg: rgba(255,255,255,0.75);
    --glass-blur: 8px;
    --primary: #1463e1;
    --primary-600: #0f4fc4;
    --accent: #f59e0b;
    --accent-600: #d97706;
    --success: #10b981;
    --muted: #6b7280;
    --radius: 12px;
    --shadow-sm: 0 4px 16px rgba(16,24,40,0.06);
    --shadow-md: 0 10px 30px rgba(16,24,40,0.10);
    --glass-border: rgba(255,255,255,0.6);
    --glass-outline: rgba(255,255,255,0.25);
    --transition: 220ms cubic-bezier(.2,.9,.3,1);
}

/* your styles remain unchanged */
body { background: var(--bg); font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color: #0f172a; }

.product-container { max-width:1100px; margin:2.4rem auto; padding:1rem; }

.product-card {
    display:grid;
    grid-template-columns:minmax(360px,430px) 1fr;
    gap:2rem;
    padding:1.25rem;
    border-radius:calc(var(--radius)+6px);
    background: linear-gradient(135deg, rgba(255,255,255,0.85), rgba(248,250,252,0.85));
    backdrop-filter: blur(var(--glass-blur));
    box-shadow: var(--shadow-md);
    border:1px solid var(--glass-outline);
    overflow:hidden;
    align-items:start;
}

@media(max-width:900px){ .product-card{grid-template-columns:1fr; gap:1rem; padding:1rem;} }

.product-media {
    background: linear-gradient(180deg, rgba(240,247,255,0.65), rgba(250,250,255,0.4));
    border-radius: calc(var(--radius)-4px);
    display:flex; align-items:center; justify-content:center;
    padding:2rem; min-height:320px;
    border:1px solid rgba(16,24,40,0.03);
}

.product-media__img { max-width:100%; max-height:460px; object-fit:contain; border-radius:10px; transition: transform var(--transition); box-shadow: var(--shadow-sm); }
.product-media__img:hover { transform: translateY(-6px) scale(1.02); }

.product-info { display:flex; flex-direction:column; gap:0.9rem; }
.product-title { font-size:clamp(1.25rem,2.8vw,2rem); color: var(--primary-600); font-weight:800; letter-spacing:-0.6px; }
.product-sub { color: var(--muted); font-size:0.95rem; }
.product-price { font-size:clamp(1.1rem,2vw,1.8rem); font-weight:900; color:transparent; -webkit-background-clip:text; background-image:linear-gradient(90deg,var(--accent),#f97316 60%,var(--primary)); letter-spacing:-0.4px; }

.meta { margin-top:.6rem; display:grid; gap:.55rem; }
.meta__row { display:flex; justify-content:space-between; align-items:center; padding:.35rem .5rem; border-radius:10px; background: rgba(255,255,255,0.6); border:1px solid rgba(16,24,40,0.02); }
.meta__label { color:#0b1220; font-weight:700; font-size:0.95rem; }
.meta__value { color: var(--primary); font-weight:800; }

.badge { padding:.35rem .65rem; border-radius:999px; font-size:0.76rem; font-weight:800; text-transform:uppercase; }
.badge--in { color:#fff; background:linear-gradient(90deg,#10b981,#0ea5a0); }
.badge--low { color:#111827; background:linear-gradient(90deg,#fcd34d,#f59e0b); }
.badge--out { color:#fff; background:linear-gradient(90deg,#ef4444,#dc2626); }

.product-actions { margin-top:1rem; display:flex; gap:.8rem; align-items:center; flex-wrap:wrap; }

.qty { display:flex; align-items:center; gap:.5rem; }
.qty-wrap { display:flex; align-items:center; border-radius:10px; overflow:hidden; border:1px solid rgba(16,24,40,0.06); background:rgba(255,255,255,0.9); }
.qty input[type="number"] { width:80px; height:44px; border:none; text-align:center; font-weight:700; font-size:1rem; background:transparent; color: var(--primary-600); }
.qty button.qty-btn { background:transparent; border:none; width:44px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.05rem; transition: var(--transition); }
.qty button.qty-btn:disabled { opacity:.45; cursor:not-allowed; }

.actions__buttons { display:flex; gap:.6rem; align-items:center; flex-wrap:wrap; }

.btn { border:none; padding:.65rem 1rem; border-radius:10px; font-weight:700; font-size:0.98rem; cursor:pointer; display:flex; align-items:center; gap:.6rem; box-shadow: var(--shadow-sm); min-height:44px; }
.btn:disabled{opacity:.6; cursor:not-allowed; transform:none;}

.btn--primary{ background-image:linear-gradient(90deg, rgba(20,99,225,1), rgba(12,78,200,1)); color:#fff; box-shadow:0 8px 30px rgba(20,99,225,0.16);}
.btn--accent{ background-image:linear-gradient(90deg,var(--accent),var(--accent-600)); color:#fff; }

.btn--ghost{ background:linear-gradient(180deg, rgba(255,255,255,0.65), rgba(255,255,255,0.55)); color: var(--primary); border:2px solid rgba(20,99,225,0.12); }

.spinner { width:18px; height:18px; border:3px dotted rgba(206, 47, 47, 0.9); border-top:3px solid rgba(255,255,255,0.35); border-radius:50%; animation:spin 0.8s linear infinite; display:none;}
@keyframes spin{to{transform:rotate(360deg);}}
.action-btn.loading .btn-text { display:none; }
.action-btn.loading i { display:none; }
.action-btn.loading .spinner { display:inline-block; }
</style>

<div class="product-page">
    <div class="product-container">
        <div class="product-card">

            <!-- Image -->
            <div class="product-media">
                @if(!empty($product->image_url))
                    <img src="{{ $product->image_url }}" class="product-media__img">
                @else
                    <div class="text-center">
                        <i class="bi bi-camera" style="font-size:3rem;color:#9ca3af;"></i>
                        <p style="color:var(--muted);">No image</p>
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="product-info">
                <h1 class="product-title">{{ $product->name }}</h1>
                <div class="product-sub">{{ $product->short_description }}</div>
                <div class="product-price">${{ number_format($product->price,2) }}</div>

                @php $qty = (int) $product->quantity; @endphp

                <section class="meta">
                    <div class="meta__row">
                        <div class="meta__label">Category</div>
                        <div class="meta__value">{{ $product->category->name ?? 'Uncategorized' }}</div>
                    </div>

                    <div class="meta__row">
                        <div class="meta__label">Stock</div>
                        <div>
                            @if($qty <= 0)
                                <span class="badge badge--out">Out of stock</span>
                            @elseif($qty <= 5)
                                <span class="badge badge--low">Low stock</span>
                            @else
                                <span class="badge badge--in">In stock</span>
                            @endif
                        </div>
                    </div>

                    <div class="meta__row">
                        <div class="meta__label">Available</div>
                        <div class="meta__value">{{ $qty > 0 ? $qty : '—' }}</div>
                    </div>
                </section>

                <div class="product-desc">
                    {!! nl2br(e($product->description ?? 'No description')) !!}
                </div>

                <!-- Quantity -->
                <div class="qty">
                    <div class="qty-wrap">
                        <button type="button" class="qty-btn" id="decrement" @if($qty<=1) disabled @endif>
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <input id="quantity-input" type="number" min="1" max="{{ $qty }}" value="{{ $qty>0?1:0 }}" @if($qty<=0) disabled @endif>
                        <button type="button" class="qty-btn" id="increment" @if($qty<=1) disabled @endif>
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="product-actions">
                    <div class="actions__buttons">

                        <!-- Wishlist -->
                        <form method="POST" class="btn-form" data-action="wishlist"
                            action="{{ route('wishlist.add', $product->id) }}">
                            @csrf
                            <button type="submit" class="btn btn--ghost action-btn" @if($qty<=0) disabled @endif>
                                <i class="bi bi-heart"></i> Wishlist
                            </button>
                        </form>

                        <!-- Add to Cart -->
                        <form method="POST" class="btn-form" data-action="cart"
                            action="{{ route('cart.add', $product->id) }}">
                            @csrf
                            <input type="hidden" name="quantity" class="cart-quantity" value="1">
                            <button type="submit" class="btn btn--primary action-btn" @if($qty<=0) disabled @endif>
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </form>

                        <!-- Buy Now -->
                        <form method="GET" action="{{ route('checkout.index') }}" class="btn-form" data-action="buy-now" style="display:inline;">
                            <input type="hidden" name="product" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="buy-now-quantity" value="1">
                            <button type="submit" class="btn btn--accent action-btn" @if($qty<=0) disabled @endif>
                                <i class="bi bi-lightning-charge"></i> Buy Now
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Quantity Logic
document.addEventListener("DOMContentLoaded", () => {
    const qtyInput = document.getElementById('quantity-input');
    const decBtn = document.getElementById('decrement');
    const incBtn = document.getElementById('increment');
    const cartQty = document.querySelector('.cart-quantity');
    const buyNowQty = document.getElementById('buy-now-quantity');

    function updateQty() {
        let v = Math.max(1, Math.min(Number(qtyInput.value), Number(qtyInput.max)));
        qtyInput.value = v;
        cartQty.value = v;
        if (buyNowQty) buyNowQty.value = v;

        decBtn.disabled = v <= 1;
        incBtn.disabled = v >= Number(qtyInput.max);
    }

    incBtn?.addEventListener('click', () => { qtyInput.value++; updateQty(); });
    decBtn?.addEventListener('click', () => { qtyInput.value--; updateQty(); });

    qtyInput?.addEventListener('input', updateQty);
    updateQty();


    // FORM HANDLING
    document.querySelectorAll('.btn-form').forEach(form => {
        form.addEventListener('submit', e => {
            const action = form.dataset.action;
            // Only block POST forms for animation, let GET submit normally
            if (form.method.toLowerCase() === 'get') {
                return;
            }
            // Let wishlist & cart submit normally
            if (action === 'wishlist' || action === 'cart') {
                return;
            }
            // Animation for POST forms only
            e.preventDefault();
            const btn = form.querySelector('.action-btn');
            btn.classList.add('loading');
            btn.disabled = true;
            setTimeout(() => {
                btn.classList.remove('loading');
                btn.disabled = false;
            }, 900);
        });
    });
});
</script>

@endsection
