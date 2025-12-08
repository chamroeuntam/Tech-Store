@extends('layouts.app')
@section('title', 'User Profile - IT Store')

@section('content')
@vite(['resources/css/profile.css'])
<div class="profile-wrapper">
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-picture-container">
                @php
                    $authUser = Auth::user();
                    $profilePic = $authUser->profile_picture ?? null;
                    $isUrl = $profilePic && Str::startsWith($profilePic, ['http://', 'https://']);
                @endphp
                @if($profilePic)
                    <img src="{{ $isUrl ? $profilePic : asset('storage/' . ltrim($profilePic, '/')) }}" alt="Profile Picture" class="profile-picture">
                @else
                    <div class="default-avatar">
                        {{ Str::upper(Str::substr($authUser->first_name ?? $authUser->username, 0, 1)) }}
                    </div>
                @endif
                <div class="profile-status-badge" title="Active User"></div>
            </div>
            <h2>{{ $user->full_name ?? $user->username }}</h2>
            <p>
                <i class="fas fa-calendar-alt"></i>
                Member since
               {{ $user->created_at ? $user->created_at->format('F d, Y') : 'Unknown' }}
            </p>
            @if(!$user->hasVerifiedEmail())
                <div class=" email-verification-alert">
                    <p style="color: red;">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        Please verify your email address. <a href="{{ route('verification.notice') }}" style="text-decoration: underline;">Resend verification email</a>
                    </p>   
                </div>
            @endif
        </div>
        
        @if(session('messages'))
            <div class="profile-content">
                @foreach(session('messages') as $message)
                    <div class="alert alert-{{ $message['tags'] }}">
                        <i class="fas fa-@if($message['tags'] == 'success')check-circle@elseif($message['tags'] == 'error')exclamation-circle@else info-circle @endif"></i>
                        {{ $message['message'] }}
                    </div>
                @endforeach
            </div>
        @endif
        
        <div class="profile-content">
            <!-- Profile Statistics -->
            <div class="profile-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-value">{{ $total_orders ?? 0 }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-value">{{ $total_wishlist_items ?? 0 }}</div>
                    <div class="stat-label">Wishlist Items</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value">{{ $total_cart_items ?? 0 }}</div>
                    <div class="stat-label">Cart Items</div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="profile-info">
                <div class="info-group">
                    <h3>
                        <span class="section-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        Account Information
                    </h3>
                    <div class="info-item">
                        <span class="info-label">Username</span>
                        <span class="info-value">{{ $user->username }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value @if(!$user->email) empty @endif">
                            {{ $user->email ?? "Not provided" }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">First Name</span>
                        <span class="info-value @if(!$user->first_name) empty @endif">
                            {{ $user->first_name ?? "Not provided" }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Name</span>
                        <span class="info-value @if(!$user->last_name) empty @endif">
                            {{ $user->last_name ?? "Not provided" }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Verification</span>
                        <span class="info-value color-status">
                            @if($user->hasVerifiedEmail())
                                <i class="fas fa-check-circle status-active"></i> Verified
                            @else
                                <i class="fas fa-times-circle status-inactive"></i> Not Verified
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="info-group">
                    <h3>
                        <span class="section-icon">
                            <i class="fas fa-id-card"></i>
                        </span>
                        Profile Information
                    </h3>
                    <div class="info-item">
                        <span class="info-label">Role</span>
                        <span class="info-value">
                            <i class="fas fa-@if($profile->role == 'admin')user-shield@elseif($profile->role == 'manager')user-tie@else user @endif role-icon"></i>
                            {{ $profile->role_display }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value @if(!$profile->phone_number) empty @endif">
                            {{ $profile->phone_number ?? "Not provided" }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date of Birth</span>
                        <span class="info-value @if(!$profile->date_of_birth) empty @endif">
                            {{ $profile->date_of_birth ?? "Not provided" }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address</span>
                        <span class="info-value @if(!$profile->address) empty @endif">
                            {{ $profile->address ?? "Not provided" }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Profile Created</span>
                        <span class="info-value">
                            <i class="fas fa-calendar-plus role-icon"></i>
                            {{ $profile->created_at->format('F d, Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="btn-group">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Edit Profile
            </a>
            <a href="#" class="btn btn-secondary">
                <i class="fas fa-store"></i>
                Back to Store
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced interactions and animations
    const infoItems = document.querySelectorAll('.info-item');
    const statCards = document.querySelectorAll('.stat-card');
    
    // Add hover effects to info items
    infoItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Add click effects to stat cards
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
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
        const interactiveElements = document.querySelectorAll('.btn, .stat-card, .profile-picture, .default-avatar');
        
        interactiveElements.forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    }
    
    // Intersection Observer for animations
    if ('IntersectionObserver' in window) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe profile sections
        document.querySelectorAll('.info-group, .stat-card').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(section);
        });
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // E key to edit profile
        if (e.key === 'e' && e.ctrlKey) {
            e.preventDefault();
            const editBtn = document.querySelector('a[href*="edit_profile"]');
            if (editBtn) {
                editBtn.click();
            }
        }
        
        // H key to go to store
        if (e.key === 'h' && e.ctrlKey) {
            e.preventDefault();
            const storeBtn = document.querySelector('a[href*="store"]');
            if (storeBtn) {
                storeBtn.click();
            }
        }
    });
    
    // Copy email to clipboard functionality
    const emailElement = document.querySelector('.info-item:nth-child(2) .info-value');
    if (emailElement && emailElement.textContent.trim() !== 'Not provided') {
        emailElement.style.cursor = 'pointer';
        emailElement.title = 'Click to copy email';
        
        emailElement.addEventListener('click', function() {
            const email = this.textContent.trim();
            navigator.clipboard.writeText(email).then(() => {
                // Show temporary success message
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check" style="color: var(--success-color);"></i> Copied!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            }).catch(() => {
                console.log('Failed to copy email');
            });
        });
    }
    
    // Profile picture upload preview (if edit functionality is added)
    const profilePicture = document.querySelector('.profile-picture, .default-avatar');
    if (profilePicture) {
        profilePicture.addEventListener('click', function() {
            // Add a subtle animation to indicate interactivity
            this.style.animation = 'pulse 0.5s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        });
    }
});

// Add pulse animation for profile picture
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>
@endsection