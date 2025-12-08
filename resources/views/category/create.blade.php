@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex flex-column">
    <main class="flex-grow-1 container py-5">
        <div class="mx-auto category-card-shadow" style="max-width: 600px; border: none; border-radius: 16px; padding: 2rem;">
            <h1 class="mb-4 fw-bold display-5"><i class="bi bi-folder-plus mr-2"></i> Create Category</h1>
            @if(session('success'))
                <div class="alert alert-success align-items-center" role="alert">
                    <strong class="me-2">Success!</strong>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" aria-label="Close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form method="POST" action="{{ route('dashboard.categories.store') }}" autocomplete="off">
                @csrf
                <div class="mb-3 text-start">
                    <label for="name" class="form-label fw-bold">Category Name</label>
                    <input id="name" name="name" type="text" required
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Enter category name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 text-start">
                    <label for="description" class="form-label fw-bold">Description <span class="text-secondary">(Optional)</span></label>
                    <textarea id="description" name="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Describe this category (optional)">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end gap-3 mt-2">
                    <a href="{{ route('dashboard.categories.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-plus-circle"></i> Create Category
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<style>
    .category-card-shadow {
        box-shadow: 0 4px 24px 0 rgba(0,0,0,0.12), 0 1.5px 6px 0 rgba(0,0,0,0.08);
        background: #fff;
        transition: box-shadow 0.2s;
    }
    .category-card-shadow:hover {
        box-shadow: 0 8px 32px 0 rgba(0,0,0,0.18), 0 3px 12px 0 rgba(0,0,0,0.12);
    }
</style>
@endsection