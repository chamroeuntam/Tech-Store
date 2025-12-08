@extends('layouts.app')
@section('title', 'Manage Shipping Methods')

@section('content')
<div class="shipping-container py-4">

    <div class="shipping-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Manage Shipping Methods</h1>
        <a href="{{ route('dashboard.shipping.create') }}" class="btn-modern-primary">
            <i class="fa fa-plus me-1"></i> Add New
        </a>
        <a href="{{ route('dashboard.home') }}" class="btn-modern-primary" style="background: #9ca3af;"> 
            <i class="fa fa-arrow-right-from-bracket me-1"></i> Back to Dashboard
        </a>
    </div>
    <div class="shipping-card table-responsive">
        <table class="table modern-table align-middle">
            <thead>
                <tr class="text-center">
                    <th>#</th>
                    <th>Shipping Name</th>
                    <th>Cost</th>
                    <th>Estimated Delivery</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($methods as $method)
                <tr class="text-center">
                    <td>{{ $method->id }}</td>
                    <td class="text-left">{{ $method->name }}</td>
                    <td>${{ number_format($method->cost, 2) }}</td>
                    <td>{{ $method->estimated_days }} days</td>
                    <td>
                        @if($method->is_active)
                            <span class="badge bg-success px-3 py-2">Active</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">Inactive</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <a href="{{ route('dashboard.shipping.edit', $method->id) }}" class="btn-modern-sm btn-edit">
                            <i class="fa fa-pencil"></i>
                        </a>

                        <form action="{{ route('dashboard.shipping.destroy', $method->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this shipping method?')" 
                                    class="btn-modern-sm btn-delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection


<style>
/* Page container */
.shipping-container {
    max-width: 1000px;
    margin: auto;
    padding: 2rem 1rem 3rem 1rem;
}

/* Header */
.page-title {
    font-size: 2.1rem;
    font-weight: 900;
    color: #6366f1;
    letter-spacing: 0.01em;
}

.shipping-header {
    margin-bottom: 2.5rem;
    padding: 1.2rem 1rem;
    background: linear-gradient(120deg, #f5f6ff 60%, #e0e7ff 100%);
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(99,102,241,0.08);
}

/* Card */
.shipping-card {
    background: #fff;
    padding: 2.2rem 1.2rem;
    border-radius: 18px;
    border: 1px solid #e0e7ff;
    box-shadow: 0 8px 32px rgba(99,102,241,0.10);
}

/* Table */
.modern-table {
    border-collapse: separate;
    border-spacing: 0 14px;
    width: 100%;
}

.modern-table thead tr {
    background: #f5f6ff;
    border-radius: 10px;
}

.modern-table thead th {
    padding: 16px;
    font-weight: 800;
    color: #6366f1;
    border-bottom: none !important;
    font-size: 1.08rem;
}

.modern-table tbody tr {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(99,102,241,0.06);
    transition: box-shadow 0.2s, transform 0.2s;
}

.modern-table tbody tr:hover {
    box-shadow: 0 6px 24px rgba(99,102,241,0.12);
    transform: scale(1.01);
}

.modern-table tbody td {
    padding: 16px;
    border-bottom: none !important;
    font-size: 1.02rem;
}

/* Badges */
.badge {
    font-size: 1rem;
    font-weight: 700;
    border-radius: 8px;
    padding: 7px 22px;
    letter-spacing: 0.03em;
}

/* Buttons */
.btn-modern-primary {
    background: linear-gradient(135deg, #6366f1, #10b981);
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    color: white !important;
    font-weight: 800;
    font-size: 1.15rem;
    box-shadow: 0 4px 16px #6366f140;
    transition: 0.25s;
    letter-spacing: 0.01em;
    text-decoration: none;
}


/* Small action buttons */
.btn-modern-sm {
    padding: 10px 14px;
    border-radius: 10px;
    border: none;
    font-size: 1rem;
    color: white;
    font-weight: 700;
    transition: 0.25s;
    box-shadow: 0 2px 8px rgba(99,102,241,0.08);
}

.btn-edit {
    background: #3b82f6;
}

.btn-edit:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-delete {
    background: #ef4444;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

/* Responsive Styles */
@media(max-width: 600px) {
    .shipping-container { padding: 1rem 0.2rem; }
    .shipping-header { flex-direction: column; gap: 1rem; padding: 1rem 0.5rem; }
    .page-title { font-size: 1.3rem; }
    .shipping-card { padding: 1rem 0.2rem; }
    .modern-table thead th, .modern-table tbody td { padding: 10px; font-size: 0.95rem; }
    .btn-modern-primary { font-size: 1rem; padding: 10px 16px; }
    .btn-modern-sm { font-size: 0.9rem; padding: 8px 10px; }
}
</style>
