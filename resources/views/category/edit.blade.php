@extends('layouts.app')

@section('content')
@vite(['resources/css/user-management.css'])
<div class="user-mgmt-wrapper">
    <div class="user-mgmt-container">
        <div class="user-mgmt-header">
            <h1><i class="bi bi-pencil me-2"></i> Edit Category</h1>
        </div>
        <form method="POST" action="{{ route('dashboard.categories.update', $category->id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Category Name</label>
                <input id="name" name="name" type="text" required
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $category->name) }}" placeholder="Enter category name">
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label fw-bold">Description <span class="text-muted">(Optional)</span></label>
                <textarea id="description" name="description" rows="4"
                          class="form-control @error('description') is-invalid @enderror"
                          placeholder="Describe this category (optional)">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('dashboard.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
