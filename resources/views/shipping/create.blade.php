@extends('layouts.app')
@section('title', 'Manage Shipping Methods')
@vite('resources/css/shipping-create.css')
@section('content')
<div class="shipping-container py-4">
    <div class="shipping-card">

        <div class="shipping-header">
            <h2 class="shipping-title"><i class="fa fa-truck-fast me-2"></i> Create New Shipping Method</h2>
            <p class="shipping-subtitle">Add delivery options for your store</p>
        </div>

        <div class="shipping-body">
            <form action="{{ route('dashboard.shipping.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Shipping Name</label>
                    <input type="text" class="form-control modern-input" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control modern-input" id="description" name="description" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="cost" class="form-label">Cost (USD)</label>
                    <input type="number" step="0.01" class="form-control modern-input" id="cost" name="cost" required>
                </div>

                <div class="mb-3">
                    <label for="estimated_delivery_time" class="form-label">Estimated Delivery Time (Days)</label>
                    <input type="text" class="form-control modern-input" id="estimated_delivery_time" name="estimated_days" placeholder="e.g. 2–4 days">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input modern-checkbox" id="is_active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <button type="submit" class="btn-modern-primary w-100">
                    <i class="fa fa-plus-circle me-1"></i> Add Shipping Method
                </button>

            </form>
        </div>
    </div>
</div>
@endsection
