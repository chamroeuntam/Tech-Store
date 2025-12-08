<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#667eea">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - IT Store</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/login.css'])


    
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h1>
                    <span class="icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </span>
                    Welcome Back
                </h1>
                <p>Sign in to your account to continue</p>
            </div>
            
            {{-- Laravel Session Messages --}}
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            {{-- General Validation Errors (if not tied to a field) --}}
            @if ($errors->any() && !$errors->has('username') && !$errors->has('password'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('login') }}" autocomplete="on" id="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="text" 
                               name="email" 
                               id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="Enter email" 
                               required 
                               autocomplete="username" 
                               autofocus>
                    </div>
                    @error('email')
                        <div class="error-list">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Enter password" 
                               required 
                               autocomplete="current-password">
                        <button type="button" class="toggle-password" tabindex="-1" onclick="togglePassword(this)" title="Toggle password visibility" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-list">{{ $message }}</div>
                    @enderror
                    <p class="forgot-password">
                        {{-- Use Laravel's standard password reset route --}}
                        <a href="{{ route('password.request') }}">Forgot your password?</a>
                    </p>
                </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal;">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            Remember Me
                        </label>
                    </div>
                
                <button type="submit" class="btn-primary" id="login-btn">
                    <span id="login-btn-text">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </span>
                    <span id="login-btn-spinner" class="spinner hidden"></span>
                </button>
                <div class="divider">or</div>
                <div class="social-login">
                    <a href="{{ url('auth/google') }}" class="btn-google" style="display: flex; align-items: center; text-decoration: none; justify-content: center;">
                        <span style="display: inline-block; width: 22px; height: 27px; vertical-align: middle; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                            </svg>
                        </span>
                        &nbsp;Sign in with Google
                    </a>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account yet?</p>
                {{-- Use Laravel's standard register route --}}
                <a href="{{ route('register') }}">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </a>
            </div>
        </div>
    </div>

<script>
// All JavaScript is vanilla and requires no changes.
function togglePassword(btn) {
    const input = btn.parentElement.querySelector('input[type="password"], input[type="text"]');
    const icon = btn.querySelector('i');
    if (!input) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        btn.title = 'Hide password';
        btn.setAttribute('aria-label', 'Hide password');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        btn.title = 'Show password';
        btn.setAttribute('aria-label', 'Show password');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const btn = document.getElementById('login-btn');
    const btnText = document.getElementById('login-btn-text');
    const btnSpinner = document.getElementById('login-btn-spinner');
    
    if (form && btn && btnText && btnSpinner) {
        form.addEventListener('submit', function() {
            btn.disabled = true;
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
        });
    }
    
    const formControls = document.querySelectorAll('.form-control');
    
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        control.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        if (control.value) {
            control.parentElement.classList.add('focused');
        }
    });
    
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease'; // Added transition
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        const interactiveElements = document.querySelectorAll('.btn-primary, .toggle-password, .auth-links a');
        
        interactiveElements.forEach(element => {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            }, { passive: true }); // Added passive
            
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            e.preventDefault();
            if (btn && !btn.disabled) {
                btn.click();
            }
        }
    });
    
    // Auto-add 'is-invalid' class from server-side error
    @error('username')
        document.getElementById('username').classList.add('is-invalid');
    @enderror
    @error('password')
        document.getElementById('password').classList.add('is-invalid');
    @enderror

    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            const isValid = this.checkValidity();
            if (this.value) { // Only validate if there is a value
                this.classList.toggle('is-valid', isValid);
                this.classList.toggle('is-invalid', !isValid);
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                const isValid = this.checkValidity();
                this.classList.toggle('is-valid', isValid);
                this.classList.toggle('is-invalid', !isValid);
            }
        });
    });
});
</script>
</body>
</html>