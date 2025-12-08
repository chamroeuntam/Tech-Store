@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex flex-column bg-light">
    <main class="flex-grow-1 container py-5">
        <div class="mx-auto" style="max-width: 900px;">
            <h1 class="mb-4 fw-bold display-5 text-primary"><i class="bi bi-images me-2"></i> Create New Slider</h1>
            @if(session('success'))
                <div class="alert alert-success align-items-center d-flex" role="alert">
                    <strong class="me-2">Success!</strong>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" aria-label="Close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form method="POST" action="{{ route('dashboard.sliders.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="slider-title" class="form-label fw-bold">Slider Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="slider-title" placeholder="e.g., Welcome Banner" value="{{ old('title') }}"/>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Image</label>
                            <div class="file-input-container" id="file-input-container">
                                <input type="file" id="slider-image" name="image" accept="image/*"/>
                                <div class="placeholder" id="file-placeholder">
                                    <span class="fw-bold material-symbols-outlined fs-1 text-primary">Upload Image</span>
                                    <p class="mt-2 mb-0 text-secondary">Drag & drop or <span class="fw-semibold text-primary">click to upload</span></p>
                                </div>
                                <img alt="Image Preview" class="image-preview" id="image-preview"/>
                            </div>
                            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description" placeholder="Describe your slider..." rows="8">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="link" class="form-label fw-bold">Link (Optional)</label>
                            <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" id="link" placeholder="https://example.com" value="{{ old('link') }}"/>
                            @error('link')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-3 mt-2">
                        <button type="button" class="btn btn-outline-secondary px-4">Save Draft</button>
                        <button type="submit" class="btn btn-primary px-4">Publish Slider</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
<script>
    // Image preview and container state
    const sliderImageEl = document.getElementById('slider-image');
    const preview = document.getElementById('image-preview');
    const fileContainer = document.getElementById('file-input-container');
    sliderImageEl.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            fileContainer.classList.add('has-image');
        } else {
            preview.src = '';
            fileContainer.classList.remove('has-image');
        }
    });
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
            const file = dt.files[0];
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            sliderImageEl.files = dataTransfer.files;
            sliderImageEl.dispatchEvent(new Event('change'));
        }
    });
</script>
<style>
    body { font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', sans-serif; }
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
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 5;
    }
    .file-input-container .placeholder { margin: 0; background-color: transparent; pointer-events: none; }
    .file-input-container img.image-preview {
        display: none;
        width: 100%;
        height: 100%;
        max-height: none;
        object-fit: cover;
        border-radius: 0.5rem;
        pointer-events: none;
    }
    .file-input-container.has-image {
        padding: 0.25rem;
        border-style: solid;
    }
    .file-input-container.has-image .placeholder { display: none; }
    .file-input-container.has-image img.image-preview { display: block; }
</style>
@endsection