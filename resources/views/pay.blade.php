@extends('layouts.app')

<title>Payment QR</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300..700&display=swap" rel="stylesheet">

<style>

    .payment-card {

        background: #ffffff;
        border-radius: 20px;
        padding: 32px;
        width: 100%;
        max-width: 420px;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,.35);
        animation: fadeUp .6s ease;
    }

    @keyframes fadeUp {
        from {opacity: 0; transform: translateY(20px);}
        to {opacity: 1; transform: translateY(0);}
    }

    .title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #0f172a;
    }

    .subtitle {
        font-size: 15px;
        color: #64748b;
        margin-bottom: 20px;
    }

    .qr-box {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 32px 0 24px 0;
        width: 100%;
    }

    .qr-box img {
        width: 230px;
        max-width: 90vw;
        background: #fff;
        padding: 10px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(0,0,0,.15);
        margin: 0 auto;
        display: block;
    }

    .amount {
        font-size: 22px;
        font-weight: 700;
        margin-top: 10px;
        color: #020617;
    }

    .status {
        margin-top: 12px;
        display: inline-block;
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
    }

    .pending { background: #fef3c7; color: #92400e; }
    .paid { background: #dcfce7; color: #166534; }
    .expired { background: #fee2e2; color: #991b1b; }

    .countdown {
        margin-top: 16px;
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }

    .expire-text {
        margin-top: 6px;
        font-size: 13px;
        color: #64748b;
    }

    .expired-box {
        text-align: center;
        color: #991b1b;
        font-weight: 700;
    }

    .btn {
        margin-top: 22px;
        display: inline-block;
        padding: 10px 22px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(37,99,235,.4);
        transition: .2s;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
</style>

@section('content')

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="payment-card">

    <div class="title">Scan to Pay</div>
    <div class="subtitle">Please scan with your banking app</div>

    @if($payment->status === 'expired')

        <div class="expired-box">
            <p>Payment Expired</p>
            @if($payment->expires_at)
                <p class="expire-text">
                    {{ \Carbon\Carbon::parse($payment->expires_at)->format('Y-m-d H:i:s') }}
                </p>
            @endif
            @if($payment->order_id)
                <form method="POST" action="/pay" style="margin-top:16px;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $payment->order_id }}">
                    <input type="hidden" name="amount" value="{{ $payment->amount }}">
                    <input type="hidden" name="payment_method" value="{{ $payment->payment_method }}">
                    <button type="submit" class="btn">Generate New QR for This Order</button>
                </form>
            @else
                <a href="/pay" class="btn">Generate New QR</a>
            @endif
        </div>

    @else

        {{-- QR IMAGE --}}
        <div class="qr-box">
            <img src="data:image/png;base64,{{ $payment->qr_image }}">
        </div>

        <div class="amount">
            {{ number_format($payment->amount) }} KHR
        </div>

        <div id="status"
             class="status {{ $payment->status }}">
            {{ $payment->status }}
        </div>

        <div class="countdown">
            Expires in: <span id="countdown">--:--</span>
        </div>

        @if($payment->expires_at)
            <div class="expire-text">
                Expiry: {{ \Carbon\Carbon::parse($payment->expires_at)->format('Y-m-d H:i:s') }}
            </div>
        @endif

    @endif


    </div>
</div>

<script>
const invoice = "{{ $payment->invoice_id }}";
const paymentStatus = "{{ $payment->status }}";
const expiresAtMs = {{ $payment->expires_at ? \Carbon\Carbon::parse($payment->expires_at)->getTimestamp() * 1000 : 'null' }};
const checkUrl = "/pay-check/" + invoice;
const pollIntervalMs = 3000;

let countdownTimer = null;

function formatDuration(ms) {
    if (ms <= 0) return "00:00";
    const totalSeconds = Math.floor(ms / 1000);
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    return `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
}

function startCountdown(expiryMs) {
    if (!expiryMs) return;

    function update() {
        const diff = expiryMs - Date.now();
        const el = document.getElementById("countdown");

        if (diff <= 0) {
            if (el) el.innerText = "Expired";
            clearInterval(countdownTimer);
            return;
        }

        if (el) el.innerText = formatDuration(diff);
    }

    update();
    countdownTimer = setInterval(update, 1000);
}

function checkStatus() {
    fetch(checkUrl, { cache: "no-store" })
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById("status");

            if (el) {
                el.innerText = data.status;
                el.className = "status " + data.status;
            }

            if (data.status === "pending") {
                setTimeout(checkStatus, pollIntervalMs);
            }
            else if (data.status === "paid") {
                window.location.href = "/pay/" + invoice + "/success";
            }
            else if (data.status === "expired") {
                window.location.reload();
            }
        })
        .catch(() => {
            setTimeout(checkStatus, pollIntervalMs * 2);
        });
}

document.addEventListener("DOMContentLoaded", function () {
    if (paymentStatus === "pending" && expiresAtMs) {
        startCountdown(expiresAtMs);
    }

    checkStatus();
});
</script>

@endsection
