@extends('layouts.app')
@section('title', 'User Profile - IT Store')
@section('content')
@vite(['resources/css/edit-profile.css'])
<div class="edit-profile-wrapper">
    <div class="edit-profile-container">
        <div class="edit-header">
            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
            <p>Update your personal information and account settings</p>
            
        </div>
        
        @if(session('status'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif
        
        <form method="POST" enctype="multipart/form-data" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            
            <div class="form-content">
                <div class="form-section">
                    <h3>
                        <span class="section-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        Account Information
                    </h3>
                    <div class="form-group">
                                                <label for="telegram_id">Telegram ID</label>
                                                <input type="text" name="telegram_id" id="telegram_id" class="form-control" value="{{ old('telegram_id', $user->telegram_id ?? '') }}" placeholder="e.g. 123456789">
                                                <small class="form-text">Enter your Telegram ID to receive notifications from our bot. You can get your Telegram ID from @userinfobot in Telegram.</small>
                                                @error('telegram_id')
                                                    <div class="error-list">{{ $message }}</div>
                                                @enderror
                            <div class="current-picture-container" id="drop-area" style="display: flex; flex-direction: column; align-items: center; justify-content: flex-start; margin: 1.5rem 0 1rem 0; cursor: pointer; position: relative;">
                                <img id="profile-picture-preview"
                                    src="{{ !empty($user->profile_picture) ? (filter_var($user->profile_picture, FILTER_VALIDATE_URL) ? $user->profile_picture : asset('storage/' . $user->profile_picture)) : asset('storage/assets/default-avt.png') }}"
                                    alt="Profile Preview"
                                    class="current-picture"
                                    style="display: block; max-width: 140px; max-height: 140px; border-radius: 50%; margin-bottom: 0.5rem; border: 2px dashed #a3a3a3; background: #f9fafb; object-fit: cover;">
                                <div class="picture-label" id="picture-label" style="text-align: center;">Click or drag an image here</div>
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name ?? '') }}">
                            @error('first_name')
                                <div class="error-list">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name ?? '') }}">
                            @error('last_name')
                                <div class="error-list">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? '') }}">
                        @error('email')
                            <div class="error-list">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>
                        <span class="section-icon">
                            <i class="fas fa-id-card"></i>
                        </span>
                        Profile Information
                    </h3>
                    @if(isset($canEditRole) && $canEditRole)
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="customer" @selected(old('role', $user->role ?? '') == 'customer')>Customer</option>
                            <option value="staff" @selected(old('role', $user->role ?? '') == 'staff')>Staff</option>
                            <option value="manager" @selected(old('role', $user->role ?? '') == 'manager')>Manager</option>
                            <option value="admin" @selected(old('role', $user->role ?? '') == 'admin')>Admin</option>
                        </select>
                        @error('role')
                            <div class="error-list">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Only owners and superusers can change user roles.</small>
                    </div>
                    @endif
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number ?? '') }}">
                            @error('phone_number')
                                <div class="error-list">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth ?? '') }}">
                            @error('date_of_birth')
                                <div class="error-list">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $user->address ?? '') }}">
                        @error('address')
                            <div class="error-list">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <!-- Profile picture input moved to top -->
                        @error('profile_picture')
                            <div class="error-list">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Profile
                </button>
                <a href="{{ route('profile.user_profile') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form interactions
    const formControls = document.querySelectorAll('.form-control');
    // Profile image live preview, click, and drag-and-drop
    const fileInputImg = document.getElementById('profile_picture');
    const previewImg = document.getElementById('profile-picture-preview');
    const pictureLabel = document.getElementById('picture-label');
    const dropArea = document.getElementById('drop-area');
    if (fileInputImg && previewImg && dropArea) {
        // Clickable image area
        dropArea.addEventListener('click', function(e) {
            if (e.target !== fileInputImg) fileInputImg.click();
        });

        // Drag and drop support
        dropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropArea.style.borderColor = '#6366f1';
            previewImg.style.borderColor = '#6366f1';
        });
        dropArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropArea.style.borderColor = '#a3a3a3';
            previewImg.style.borderColor = '#a3a3a3';
        });
        dropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            dropArea.style.borderColor = '#a3a3a3';
            previewImg.style.borderColor = '#a3a3a3';
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInputImg.files = e.dataTransfer.files;
                const event = new Event('change');
                fileInputImg.dispatchEvent(event);
            }
        });

        // File input change (preview)
        fileInputImg.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    if (pictureLabel) pictureLabel.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                previewImg.src = '';
                previewImg.style.display = 'none';
                if (pictureLabel) pictureLabel.style.display = 'none';
            }
        });
    }
    
    formControls.forEach(control => {
        // Add floating label effect
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        control.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Initialize state
        if (control.value) {
            control.parentElement.classList.add('focused');
        }
    });
    
    // File input enhancement
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'Choose file';
            const label = document.createElement('span');
            label.textContent = fileName;
            label.style.marginLeft = '0.5rem';
            label.style.color = 'var(--gray-600)';
            
            // Remove existing label
            const existingLabel = this.parentElement.querySelector('span');
            if (existingLabel) {
                existingLabel.remove();
            }
            
            this.parentElement.appendChild(label);
        });
    }
    
    // Smooth scrolling for form sections
    const sections = document.querySelectorAll('.form-section');
    sections.forEach((section, index) => {
        section.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Form validation feedback
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('.btn-primary');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            submitBtn.disabled = true;
        });
    }
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Enhanced mobile interactions
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        const interactiveElements = document.querySelectorAll('.btn, .form-control, .current-picture');
        
        interactiveElements.forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    }
});
</script>
@endsection