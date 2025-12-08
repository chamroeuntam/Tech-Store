@extends('layouts.app')
@section('title', 'Edit Payment Method')
@section('content')

<style>
    .pm-bg {
        background: #f7f8fa;
        min-height: 100vh;
        padding: 2rem 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .pm-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        max-width: 400px;
        width: 100%;
        padding: 2rem;
    }
    .pm-title {
        font-size: 1.7rem;
        font-weight: bold;
        color: #222;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    .pm-subtitle {
        color: #6b7280;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .pm-form-group {
        margin-bottom: 1.2rem;
    }
    .pm-label {
        display: block;
        font-size: 1rem;
        color: #333;
        margin-bottom: 0.4rem;
    }
    .pm-input, .pm-textarea {
        width: 100%;
        padding: 0.6rem 0.8rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        color: #222;
        background: #f9fafb;
        transition: border-color 0.2s;
    }
    .pm-input:focus, .pm-textarea:focus {
        border-color: #2563eb;
        outline: none;
        background: #fff;
    }
    .pm-checkbox {
        width: 18px;
        height: 18px;
        accent-color: #2563eb;
        margin-right: 0.5rem;
    }
    .pm-btn {
        width: 100%;
        padding: 0.7rem 0;
        background: #2563eb;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(37,99,235,0.08);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: background 0.2s;
        margin-bottom: 0.5rem;
    }
    .pm-btn:hover {
        background: #1d4ed8;
    }
    .pm-cancel {
        width: 100%;
        padding: 0.7rem 0;
        background: #e5e7eb;
        color: #222;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        text-decoration: none;
        margin-bottom: 0.5rem;
    }
    .pm-cancel:hover {
        background: #d1d5db;
    }
    .pm-error {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #b91c1c;
        padding: 0.7rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 0.97rem;
    }
</style>

<div class="pm-bg">
    <div class="pm-card">
        <div class="pm-title">
            <i class="fa fa-credit-card" style="margin-right:8px;"></i> Edit Payment Method
        </div>
        <div class="pm-subtitle">Update payment options for your store</div>

        <form method="POST" action="{{ route('dashboard.payment-method.update', $paymentMethod->id) }}">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="pm-error">
                    <ul style="padding-left: 1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="pm-form-group">
                <label for="name" class="pm-label">Payment Name</label>
                <input type="text" name="name" id="name" class="pm-input" value="{{ old('name', $paymentMethod->name) }}" required autocomplete="off">
                @error('name')
                    <div class="pm-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="pm-form-group">
                <label for="description" class="pm-label">Description</label>
                <textarea name="description" id="description" class="pm-textarea">{{ old('description', $paymentMethod->description) }}</textarea>
                @error('description')
                    <div class="pm-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="pm-form-group" style="display: flex; align-items: center;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }} class="pm-checkbox">
                <label for="is_active" style="margin-bottom:0;">Active</label>
            </div>
            <button type="submit" class="pm-btn">
                <i class="fa fa-save" style="margin-right:8px;"></i> Update Payment Method
            </button>
            <a href="{{ route('dashboard.payment-method.index') }}" class="pm-cancel">Cancel</a>
        </form>
    </div>
</div>
@endsection
