@extends('layouts.app')
@section('title', 'Manage Shipping Methods')

@section('content')
<div class="shipping-container py-4">
    <div class="shipping-card">

        <div class="shipping-header">
            <h2 class="shipping-title"><i class="fa fa-truck-fast me-2"></i> Edit Shipping Method</h2>
            <p class="shipping-subtitle">Modify delivery options for your store</p>
        </div>

        <div class="shipping-body">
            <form action="{{ route('dashboard.shipping.update', $method->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Shipping Name</label>
                    <input type="text" class="form-control modern-input" id="name" name="name" required value="{{ old('name', $method->name) }}">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control modern-input" id="description" name="description" rows="3">{{ old('description', $method->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="cost" class="form-label">Cost (USD)</label>
                    <input type="number" step="0.01" class="form-control modern-input" id="cost" name="cost" required value="{{ old('cost', $method->cost) }}">
                </div>

                <div class="mb-3">
                    <label for="estimated_delivery_time" class="form-label">Estimated Delivery Time (Days)</label>
                    <input type="text" class="form-control modern-input" id="estimated_delivery_time" name="estimated_days" placeholder="e.g. 2–4 days" value="{{ old('estimated_days', $method->estimated_days) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input modern-checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $method->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn-modern-primary w-100">
                    <i class="fa fa-save me-1"></i> Update Shipping Method
                </button>

            </form>
        </div>
    </div>
</div>
@endsection


<style>
/* Container center */
.shipping-container {
    max-width: 620px;
    margin: auto;
}

/* Modern Card */
.shipping-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
    border: 1px solid #e8e8e8;
}

/* Header */
.shipping-header {
    text-align: center;
    margin-bottom: 22px;
}

.shipping-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
}

.shipping-subtitle {
    color: #6c757d;
    font-size: 0.95rem;
    margin-top: 4px;
}

/* Labels */
.form-label {
    font-weight: 600;
    margin-bottom: 6px;
}

/* Inputs */
.modern-input {
    border-radius: 8px;
    border: 1px solid #d0d0d0;
    padding: 10px 12px;
    transition: 0.2s;
}

.modern-input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    outline: none;
}

/* Checkbox */
.modern-checkbox {
    cursor: pointer;
}

/* Primary Button */
.btn-modern-primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none;
    padding: 12px 16px;
    border-radius: 10px;
    color: #fff;
    font-size: 1rem;
    font-weight: 600;
    transition: 0.25s;
}

.btn-modern-primary:hover {
    background: linear-gradient(135deg, #4f46e5, #4338ca);
    transform: translateY(-1px);
}

</style>
