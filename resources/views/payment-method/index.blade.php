@extends('layouts.app')
@section('title', 'Manage Payment Methods')

@section('content')
<style>
    /* Body Background */
    .pm-bg {
        background: #f9fafb;
        min-height: 100vh;
        padding: 3rem 1rem;
        font-family: 'Poppins', sans-serif;
    }

    /* Page Title */
    .pm-title {
        font-size: 2.4rem;
        font-weight: 800;
        color: #4f46e5;
        margin-bottom: 2.5rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
    }

    /* Buttons */
    .pm-btn {
        border: none;
        border-radius: 12px;
        padding: 10px 22px;
        font-size: 1.05rem;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.2s;
        text-decoration: none;
    }

    .pm-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(79,70,229,0.2);
    }

    .pm-btn-add {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
    }

    .pm-btn-back {
        background: #9ca3af;
    }

    /* Payment Card Grid */
    .payment-methods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    /* Payment Card */
    .payment-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.8rem 1.5rem;
        box-shadow: 0 6px 24px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        transition: all 0.3s;
    }

    .payment-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.1);
    }

    .payment-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .payment-card-icon {
        background: linear-gradient(135deg, #6366f1, #10b981);
        color: #fff;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .payment-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #111827;
    }

    .payment-card-description {
        font-size: 1rem;
        color: #6b7280;
    }

    .payment-card-status {
        display: inline-block;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 6px 18px;
        border-radius: 8px;
        margin-top: 0.5rem;
    }

    .payment-card-status.active {
        background: #d1fae5;
        color: #059669;
    }

    .payment-card-status.inactive {
        background: #e5e7eb;
        color: #374151;
    }

    .payment-card-actions {
        display: flex;
        gap: 0.8rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .pm-btn-edit {
        background: #10b981;
    }

    .pm-btn-edit:hover {
        background: #059669;
    }

    .pm-btn-delete {
        background: #f87171;
    }

    .pm-btn-delete:hover {
        background: #e11d48;
    }

    @media(max-width: 640px) {
        .payment-methods-grid {
            grid-template-columns: 1fr;
            gap: 1.2rem;
        }

        .pm-title { font-size: 1.8rem; }
    }
</style>

<div class="pm-bg">
    <div class="container" style="max-width:1200px;margin:auto;">

        <div class="pm-title">
            <i class="fa fa-credit-card"></i> Manage Payment Methods
        </div>

        <div style="display:flex; gap: 1rem; flex-wrap: wrap; justify-content:center; margin-bottom:2rem;">
            <a href="{{ route('dashboard.payment-method.create') }}" class="pm-btn pm-btn-add">
                <i class="fa fa-plus"></i> Add Payment Method
            </a>
            <a href="{{ route('dashboard.home') }}" class="pm-btn pm-btn-back">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        @if($paymentMethods->isEmpty())
            <p class="text-center text-gray-500">No payment methods available.</p>
        @else
            <div class="payment-methods-grid">
                @foreach($paymentMethods as $method)
                    <div class="payment-card">
                        <div class="payment-card-header">
                            <div class="payment-card-icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="payment-card-title">{{ $method->name }}</div>
                        </div>
                        <div class="payment-card-description">{{ $method->description }}</div>
                        <span class="payment-card-status {{ $method->is_active ? 'active' : 'inactive' }}">
                            {{ $method->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <div class="payment-card-actions">
                            <a href="{{ route('dashboard.payment-method.edit', $method->id) }}" class="pm-btn pm-btn-edit">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <form action="{{ route('dashboard.payment-method.destroy', $method->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this payment method?')" class="pm-btn pm-btn-delete">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
