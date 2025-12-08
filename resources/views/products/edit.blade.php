@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex flex-column">
    <!-- Main Content -->
    <main class="flex-grow-1 container py-5">
        <div class="mx-auto" style="max-width: 900px;">
            <h1 class="mb-4 fw-bold display-5">Edit Product</h1>

            <form method="POST" action="{{ route('dashboard.products.update', $product->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <!-- Left column -->
                    <div class="col-12 col-md-6">
                        <div class="mb-3 text-start">
                            <label for="name" class="form-label fw-bold">Product Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="e.g., My Product" value="{{ old('name', $product->name) }}"/>
                            {{-- @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror --}}
                        </div>

                        <div class="mb-3 text-start">
                            <label for="price" class="form-label fw-bold">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" id="price" placeholder="e.g., 19.99" value="{{ old('price', $product->price) }}"/>
                           
                        </div>

                        <div class="mb-3 text-start">
                            <label for="quantity" class="form-label fw-bold">Quantity</label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" id="quantity" placeholder="e.g., 100" value="{{ old('quantity', $product->quantity) }}"/>
                            
                        </div>


                        <div class="mb-3 text-start">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Select a category</option>

                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>               
                        </div>                  
                    </div>
                    <!-- Right column -->
                    <div class="col-12 col-md-6">
                        <div class="mb-3 text-start">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" class="description form-control @error('description') is-invalid @enderror" id="description" placeholder="វាយបញ្ចូលការពិពណ៌នា..." rows="10">{{ old('description', $product->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3 text-start">
                            <label class="form-label fw-bold">រូបភាព</label>
                            <div class="file-input-container @if($product->image) has-image @endif" id="file-input-container">
                                <input type="file" id="featured-image" name="image" accept="image/*"/>
                                <div class="placeholder" id="file-placeholder">
                                    <span class="fw-bold material-symbols-outlined fs-1 text-primary">Choose Image</span>
                                    <p class="mt-2 mb-0 text-secondary">Drag &amp; Drop or <span class="fw-semibold text-primary">Click to Upload</span></p>
                                </div>
                                <img alt="Image Preview" class="image-preview" id="image-preview" @if($product->image) src="{{ asset('storage/' . $product->image) }}" style="display:block;" @endif/>
                            </div>
                            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <!-- Action buttons -->
                    <div class="col-12 d-flex justify-content-end gap-3 mt-2">
                        <a href="{{ route('dashboard.home') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">Update Product</button>
                    </div>                    
                </div>
            </form>
        </div>
    </main>
</div>
<script>
    // Image preview and container state
    const featuredImageEl = document.getElementById('featured-image');
    const preview = document.getElementById('image-preview');
    const fileContainer = document.getElementById('file-input-container');
    featuredImageEl.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            fileContainer.classList.add('has-image');
        } else {
            preview.src = '';
            fileContainer.classList.remove('has-image');
        }
    });

    // Drag & drop handling so dropping a file updates the input and preview
    ['dragenter', 'dragover'].forEach(evt => {
        fileContainer.addEventListener(evt, function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileContainer.classList.add('drag-over');
        });
    });
    ['dragleave', 'dragend', 'drop'].forEach(evt => {
        fileContainer.addEventListener(evt, function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (evt !== 'dragover') fileContainer.classList.remove('drag-over');
        });
    });
    fileContainer.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            // use DataTransfer to set the input files programmatically
            const file = dt.files[0];
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            featuredImageEl.files = dataTransfer.files;
            // trigger change handler
            featuredImageEl.dispatchEvent(new Event('change'));
        }
    });


    // ensure tags are synced before form submit (in case of browser quirks)
    // Only run if tag UI exists
    const formEl = document.querySelector('form');
    if (formEl) {
        formEl.addEventListener('submit', function() {
            if (typeof hiddenTagsInput !== 'undefined' && Array.isArray(window.tagsList)) {
                try { hiddenTagsInput.value = window.tagsList.join(','); } catch (e) { /* ignore */ }
            }
        });
    }
</script>
<style>
    body { font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif; }
    .description {
        min-height: 122px !important;
        max-height: 300px;
        overflow-y: auto;
    }
    .chip {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        background: #e7f1ff;
        color: #0d6efd;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
        cursor: pointer;
    }
    .chip-close {
        margin-left: 0.4rem;
        cursor: pointer;
        font-size: 1rem;
        color: #0d6efd;
    }
    .file-input-container {
        position: relative;
        border: 2px dashed #ced4da;
        border-radius: 0.75rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, padding 0.15s;
        overflow: hidden;
        min-height: 140px;
    }
    .file-input-container:hover {
        border-color: #0d6efd;
    }
    .file-input-container.drag-over {
        border-color: #0b5ed7;
        background: rgba(13,110,253,0.03);
    }
    .file-input-container input[type="file"] {
        position: absolute;
        inset: 0; /* top:0; right:0; bottom:0; left:0; */
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 5; /* keep input above placeholder and image so clicks hit it */
    }
    .file-input-container .placeholder { margin: 0; background-color: transparent; pointer-events: none; }
    .file-input-container img.image-preview {
        display: none;
        width: 100%;
        height: 100%;
        max-height: none;
        object-fit: cover;
        border-radius: 0.5rem;
        pointer-events: none; /* allow clicks to pass through to the invisible input */
    }
    /* When an image is present we collapse padding and show image filling the box */
    .file-input-container.has-image {
        padding: 0.25rem;
        border-style: solid;
    }
    .file-input-container.has-image .placeholder { display: none; }
    .file-input-container.has-image img.image-preview { display: block; }
</style>
@endsection