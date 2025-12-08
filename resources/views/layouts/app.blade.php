@php use Illuminate\Support\Str; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#14b8a6"> <meta name="apple-mobile-web-app-title" content="Tech Store">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css'])      
    <!-- Bootstrap 5 JS Bundle (for dropdowns, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <a href="{{ route('products.index') }}" class="logo">Tech Store
                    <span class="logo-icon">
                        {{-- <img src="{{ asset('storage/assets/logo.png') }}" alt="Tech Store Logo"/> --}}
                    </span>
                </a>
                
                <button class="nav-toggle" id="navToggle" aria-label="Menu" aria-controls="mainNav" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                
                <ul class="nav-menu" id="mainNav">
                    <li><a href="{{ route('products.index') }}"><i class="fa fa-home"></i><span>Home</span></a></li>
                    <li class="dropdown">
                        <a href="#"><i class="fa fa-box"></i><span>Products</span><i class="fa fa-caret-down"></i></a>
                        <div class="dropdown-content">
                            <a href="{{ route('products.index')}}"><i class="fa fa-list"></i><span>Product List</span></a>
                            
                            @if(Auth::check() && in_array(Auth::user()->role, ['staff', 'admin']))
                            <a href="{{ route('dashboard.categories.index') }}"><i class="fa fa-tags"></i><span>Category</span></a>
                                <a href="{{ route('dashboard.products.create') }}"><i class="fa fa-plus"></i><span>Add Product</span></a>
                                <a href="{{ route('dashboard.categories.create') }}"><i class="fa fa-tags"></i><span>Add Category</span></a>
                                <a href="{{ route('dashboard.sliders.create') }}"><i class="fa fa-images"></i><span>Add Slider</span></a>
                            @endif
                        </div>
                    </li>
                    
                    <li class="dropdown">
                        <a href="#"><i class="fa fa-shopping-cart"></i><span>Shopping</span><i class="fa fa-caret-down"></i></a>
                        <div class="dropdown-content">
                            <a href="{{ route('cart.index') }}"><i class="fa fa-shopping-basket"></i><span>View Cart</span></a>
                            <a href="{{ route('wishlist.index') }}"><i class="fa fa-heart"></i><span>Wishlist</span></a>
                            <a href="{{ route('orders.index') }}"><i class="fa fa-history"></i><span>Order History</span></a>
                        </div>
                    </li>
                   
                    @if(Auth::check() && in_array(Auth::user()->role, ['staff', 'admin']))
                    <li class="dropdown">
                        <a href="#"><i class="fa fa-cog"></i><span>Management</span><i class="fa fa-caret-down"></i></a>
                        <div class="dropdown-content">
                            <a href="{{ route('dashboard.home') }}"><i class="fa fa-tachometer-alt"></i><span>Dashboard</span></a>
                            <a href="{{ route('admin.manage.orders') }}"><i class="fa fa-box"></i><span>Manage Orders</span></a>
                            <a href="{{ route('report.index') }}"><i class="fa fa-chart-bar"></i><span>Reports</span></a>
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('user.user_management') }}"><i class="fa fa-users"></i><span>Manage Users</span></a>
                            @endif
                        </div>
                    </li>
                    @endif
                </ul>
                
                <div class="auth-nav">
                    @auth
                    <div class="account-dropdown" id="accountDropdown">
                        <button class="account-btn" aria-haspopup="true">

                            @php $profile = Auth::user(); @endphp
                            @if(Auth::user()->profile_picture)
                                @php
                                    $isUrl = Str::startsWith($profile->profile_picture, ['http://', 'https://']);
                                @endphp
                                <img src="{{ $isUrl ? $profile->profile_picture : asset('storage/' . $profile->profile_picture) }}" alt="Profile" class="user-avatar">
                            @else
                                <div class="user-default-avatar">
                                    <img src="{{ Str::upper(Str::substr(Auth::user()->first_name ?? Auth::user()->username, 0, 1)) }}" 
                                    alt="Profile" class="user-avatar">  
                                </div>
                            @endif

                            <span>{{ Auth::user()->first_name ?? Auth::user()->name }}</span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="account-dropdown-content">
                            <div class="account-info">
                                <div class="user-info">
                                     @php $profile = Auth::user(); @endphp
                                        @if(Auth::user()->profile_picture)
                                            @php
                                                $isUrl = Str::startsWith($profile->profile_picture, ['http://', 'https://']);
                                            @endphp
                                            <img src="{{ $isUrl ? $profile->profile_picture : asset('storage/' . $profile->profile_picture) }}" alt="Profile" class="user-avatar">
                                        @else
                                            <div class="user-default-avatar">
                                                <img src="{{ Str::upper(Str::substr(Auth::user()->first_name ?? Auth::user()->username, 0, 1)) }}" 
                                                alt="Profile" class="user-avatar">  
                                            </div>
                                        @endif
                                    <div>
                                        <div>{{ Auth::user()->first_name ?? Auth::user()->name }}</div>
                                        <div class="user-role">Role: {{ Auth::user()->role ?? 'Customer' }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="{{ route('profile.user_profile') }}"><i class="fa fa-user"></i>Profile</a>                         
                            <a href="#"
                               onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) { document.getElementById('logout-form-desktop').submit(); }"
                               class="logout-btn">
                               <i class="fa fa-right-from-bracket"></i>Logout
                            </a>
                           
                        </div>
                    </div>
                    <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    
                    @elseguest
                    <div class="auth-btns">
                        <a href="{{ route('login') }}" class="login">
                            <i class="fa fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                        <a href="{{ route('register') }}" class="register">
                            <i class="fa fa-user-plus"></i>
                            <span>Register</span>
                        </a>
                    </div>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
    
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <li><a href="{{ route('products.index') }}">
                <span class="icon-text"><i class="fa fa-home"></i><span>Home</span></span>
            </a></li>
            
            <li class="dropdown">
                <a href="#" role="button" aria-expanded="false">
                    <span class="icon-text"><i class="fa fa-box"></i><span>Products</span></span>
                    <i class="fa fa-caret-down"></i>
                </a>
                <div class="dropdown-content">
                    <a href="#"><i class="fa fa-list"></i><span>Product List</span></a>
                    <a href="#"><i class="fa fa-tags"></i><span>Category</span></a>
                </div>
            </li>
            
            <li class="dropdown">
                <a href="#" role="button" aria-expanded="false">
                    <span class="icon-text"><i class="fa fa-shopping-cart"></i><span>Shopping</span></span>
                    <i class="fa fa-caret-down"></i>
                </a>
                <div class="dropdown-content">
                    <a href="#"><i class="fa fa-shopping-basket"></i><span>View Cart</span></a>
                    <a href="#"><i class="fa fa-heart"></i><span>Wishlist</span></a>
                    <a href="#"><i class="fa fa-history"></i><span>Order History</span></a>
                </div>
            </li>
            
            
            @guest
            <li><a href="{{ route('login') }}">
                <span class="icon-text"><i class="fa fa-sign-in-alt"></i><span>Login</span></span>
            </a></li>
            <li><a href="{{ route('register') }}">
                <span class="icon-text"><i class="fa fa-user-plus"></i><span>Register</span></span>
            </a></li>
            @else
            <li><a href="#">
                <span class="icon-text"><i class="fa fa-user"></i><span>Profile</span></span>
            </a></li>
            <li>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) { document.getElementById('logout-form-mobile').submit(); }">
                    <span class="icon-text"><i class="fa fa-right-from-bracket"></i><span>Logout</span></span>
                </a>
            </li>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @endguest
        </ul>
    </div>
    <main>
        <div class="container">
            <div class="messages">
                @if ($message = session('success'))
                    <div class="alert alert-success">{{ $message }}</div>
                @endif
                @if ($message = session('error'))
                    <div class="alert alert-error">{{ $message }}</div>
                @endif
                @if ($message = session('info'))
                    <div class="alert alert-info">{{ $message }}</div>
                @endif
                @if ($message = session('warning'))
                    <div class="alert alert-warning">{{ $message }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            @if(Auth::check() && !Auth::user()->hasVerifiedEmail())
                <div class="email-verification-alert mb-4 alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    Please verify your email address. <a href="{{ route('verification.notice') }}">Resend verification email</a>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
    <footer background="#14b8a6" class="footer">
        <div class="footer-container container text-center">
            <p>&copy; {{ date('Y') }} Tech Store. All rights reserved.</p>
        </div>
    </footer>
    {{-- @stack('scripts') --}}
    <script>
        // Modern mobile navigation with better performance
        class MobileNav {
            constructor() {
                this.navToggle = document.getElementById('navToggle');
                this.mobileMenu = document.getElementById('mobileMenu');
                this.mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
                this.accountDropdown = document.getElementById('accountDropdown');
                this.isOpen = false;
                
                this.init();
            }
            
            init() {
                if (this.navToggle && this.mobileMenu) {
                    this.navToggle.addEventListener('click', this.toggleMenu.bind(this));
                    this.mobileMenuOverlay.addEventListener('click', this.closeMenu.bind(this));
                    
                    // Close menu when clicking on navigation links
                    this.mobileMenu.addEventListener('click', (e) => {
                        if (e.target.tagName === 'A' && !e.target.closest('.dropdown')) {
                            this.closeMenu();
                        }
                    });
                    
                    // Handle dropdown clicks in mobile menu
                    this.mobileMenu.querySelectorAll('.dropdown > a').forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            const dropdown = e.target.closest('.dropdown');
                            const isOpen = dropdown.classList.contains('open');
                            
                            // Close other dropdowns
                            this.mobileMenu.querySelectorAll('.dropdown').forEach(otherDropdown => {
                                if (otherDropdown !== dropdown) {
                                    otherDropdown.classList.remove('open');
                                    const otherLink = otherDropdown.querySelector('a[role="button"]');
                                    if (otherLink) {
                                        otherLink.setAttribute('aria-expanded', 'false');
                                    }
                                }
                            });
                            
                            // Toggle current dropdown
                            if (isOpen) {
                                dropdown.classList.remove('open');
                                link.setAttribute('aria-expanded', 'false');
                            } else {
                                dropdown.classList.add('open');
                                link.setAttribute('aria-expanded', 'true');
                            }
                        });
                    });
                    
                    // Handle dropdown content clicks (don't close menu)
                    this.mobileMenu.querySelectorAll('.dropdown-content a').forEach(link => {
                        link.addEventListener('click', (e) => {
                            // Allow navigation but close menu after a short delay
                            setTimeout(() => {
                                this.closeMenu();
                            }, 100);
                        });
                    });
                    
                    // Add touch support for mobile dropdowns
                    this.mobileMenu.querySelectorAll('.dropdown > a').forEach(link => {
                        link.addEventListener('touchstart', (e) => {
                            // Add visual feedback for touch
                            link.style.backgroundColor = 'rgba(0,0,0,0.05)';
                        }, { passive: true });
                        
                        link.addEventListener('touchend', (e) => {
                            // Remove visual feedback
                            setTimeout(() => {
                                link.style.backgroundColor = '';
                            }, 150);
                        }, { passive: true });
                    });
                }
                
                // Account dropdown functionality
                if (this.accountDropdown) {
                    this.accountDropdown.querySelector('.account-btn').addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.accountDropdown.classList.toggle('open');
                    });
                    
                    // Close account dropdown when clicking outside
                    document.addEventListener('click', () => {
                        this.accountDropdown.classList.remove('open');
                    });
                }
                
                // Handle window resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024 && this.isOpen) {
                        this.closeMenu();
                    }
                });
                
                // Handle orientation change
                window.addEventListener('orientationchange', () => {
                    setTimeout(() => {
                        if (this.isOpen) {
                            this.closeMenu();
                        }
                    }, 500);
                });
            }
            
            toggleMenu() {
                if (this.isOpen) {
                    this.closeMenu();
                } else {
                    this.openMenu();
                }
            }
            
            openMenu() {
                this.isOpen = true;
                this.mobileMenu.classList.add('open');
                this.mobileMenuOverlay.classList.add('open');
                this.navToggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }
            
            closeMenu() {
                this.isOpen = false;
                this.mobileMenu.classList.remove('open');
                this.mobileMenuOverlay.classList.remove('open');
                this.navToggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        }
        
        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            new MobileNav();
            
            // Auto-hide messages
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 4000);
            
            // Dynamic viewport height for mobile browsers
            const setVH = () => {
                const vh = window.innerHeight * 0.01;
                document.documentElement.style.setProperty('--vh', `${vh}px`);
            };
            
            setVH();
            window.addEventListener('resize', setVH);
            window.addEventListener('orientationchange', setVH);
                // Global AJAX handler for cart/wishlist forms
                function showNotification(message, isError = false) {
                    let notif = document.getElementById('notification');
                    if (!notif) {
                        notif = document.createElement('div');
                        notif.id = 'notification';
                        notif.style.position = 'fixed';
                        notif.style.top = '20px';
                        notif.style.right = '20px';
                        notif.style.zIndex = '9999';
                        notif.style.padding = '16px 24px';
                        notif.style.background = '#333';
                        notif.style.color = '#fff';
                        notif.style.borderRadius = '8px';
                        notif.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
                        notif.style.fontSize = '1rem';
                        notif.style.minWidth = '200px';
                        notif.style.textAlign = 'center';
                        notif.style.display = 'none';
                        document.body.appendChild(notif);
                    }
                    notif.textContent = message;
                    notif.style.background = isError ? '#c0392b' : '#333';
                    notif.style.display = 'block';
                    setTimeout(() => { notif.style.display = 'none'; }, 2000);
                }

                function handleAjaxForm(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const form = e.target;
                    const action = form.getAttribute('action');
                    const tokenInput = form.querySelector('input[name="_token"]');
                    const token = tokenInput ? tokenInput.value : (window.Laravel ? window.Laravel.csrfToken : '');
                    fetch(action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(form)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message || 'Success!');
                            if (form.classList.contains('add-to-cart-form')) {
                                const cartCounter = document.getElementById('cart-item-count');
                                if (cartCounter && data.cartCount) {
                                    cartCounter.textContent = data.cartCount;
                                }
                            }
                        } else {
                            showNotification(data.message || 'Error', true);
                        }
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        let errorMessage = 'An unexpected error occurred.';
                        if (error && error.message) {
                            errorMessage = error.message;
                        }
                        showNotification(`Error: ${errorMessage}`, true);
                    });
                }

                window.bindGlobalAjaxForms = function() {
                    document.querySelectorAll('form.add-to-cart-form, form.wishlist-form').forEach(form => {
                        // Remove previous handler to avoid duplicate events
                        form.removeEventListener('submit', handleAjaxForm);
                        form.addEventListener('submit', handleAjaxForm);
                    });
                };
                window.bindGlobalAjaxForms();
        });
        
        // Improved touch handling for iOS
        if ('ontouchstart' in window) {
            document.addEventListener('touchstart', function() {}, { passive: true });
        }
        
        // Service Worker registration for better mobile performance (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // Uncomment if you have a service worker
                // navigator.serviceWorker.register('/sw.js');
            });
        }
</script>
   
</body>
 @stack('scripts')
</html>