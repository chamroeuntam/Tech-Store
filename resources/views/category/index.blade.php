@extends('layouts.app')
@section('title', 'Dashboard - Category Management')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
body {
    font-family: 'Kantumruy Pro', sans-serif;
    background: #f8f9fa;
    color: #23272f;
    margin: 0;
    padding: 0;
}
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}
.dashboard-header {
    text-align: center;
    margin-bottom: 2rem;
}
.dashboard-title {
    font-size: 2rem;
    font-weight: 700;
    color: #6133ea;
}
.dashboard-subtitle {
    color: #888;
    font-size: 1rem;
    opacity: 0.8;
}
.dashboard-actions {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
}
.btn {
    padding: 0.6rem 1.2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
}
.btn-search {
    padding: 0.5rem 1.2rem;
    background: #e0e0e0;
    color: #333;
    font-size: 1.1rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-primary { background: #6133ea; color: #fff; }
.btn-primary:hover { background: #4b25be; transform: translateY(-2px); }
.btn-success { background: #10b981; color: #fff; }
.btn-success:hover { background: #059669; transform: translateY(-2px); }
.btn-danger { background: #dc3545; color: #fff; }
.btn-danger:hover { background: #c82333; transform: translateY(-2px); }
.search-form input { border-radius: 8px; padding: 0.4rem 0.8rem; border: 1px solid #ccc; }
.search-form button { border-radius: 8px; padding: 0.4rem 0.8rem; }

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px,1fr));
    gap: 1.5rem;
}
.category-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.2rem;
    box-shadow: 0 2px 16px rgba(80,68,196,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}
.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(80,68,196,0.15);
}
.category-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}
.category-name { font-weight: 700; font-size: 1.1rem; color: #23272f; }
.category-description { font-size: 0.95rem; color: #888; margin-bottom: 0.75rem; min-height: 48px; }
.category-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius:16px;
    box-shadow:0 2px 16px rgba(80,68,196,0.08);
    border:1px solid rgba(0,0,0,0.05);
}
.empty-state-icon {
    width: 60px; height: 60px;
    background: rgba(99,102,241,0.1);
    border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    margin:0 auto 1rem;
    font-size:1.5rem;
    color:#6133ea;
}
@media(max-width:768px){ .categories-grid { grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap:1rem; } }
@media(max-width:480px){ .categories-grid { grid-template-columns:1fr; gap:1rem; } }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title"><i class="fa fa-folder2-open me-2"></i> Categories</h1>
        <p class="dashboard-subtitle">Manage all your product categories here.</p>
        <div class="dashboard-actions">
            <form method="GET" action="" class="search-form d-flex align-items-center gap-2">
                <input type="text" name="search" placeholder="Search categories..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
            </form>
            <a href="{{ route('dashboard.categories.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Add Category</a>
        </div>
    </div>

    @if($categories->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fa fa-folder-open"></i></div>
            <h3>No Categories Found</h3>
            <p>You haven't added any categories yet. Click the button below to start organizing your products.</p>
            <a href="{{ route('dashboard.categories.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Add Your First Category</a>
        </div>
    @else
        <div class="categories-grid">
            @foreach($categories as $category)
                <div class="category-card">
                    <div class="category-header">
                        <span class="category-name">{{ $category->name }}</span>
                        <div class="category-actions">
                            <a href="{{ route('dashboard.categories.edit', $category->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                            <form action="{{ route('dashboard.categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="category-description">{{ $category->description ?? '—' }}</div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-end">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
