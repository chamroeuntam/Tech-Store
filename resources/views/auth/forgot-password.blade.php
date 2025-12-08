<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/login.css'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h2>
                    <span class="icon"><i class="fas fa-unlock-alt"></i></span>
                    Forgot Password
                </h2>
                <p>No worries! Enter your email and we’ll send you a link to reset your password.</p>
            </div>

            <!-- Show Laravel session status -->
            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <!-- Show validation errors -->
            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <button type="submit" class="btn-primary" id="forgot-btn">
                    <span id="forgot-btn-text">
                        <i class="fas fa-paper-plane"></i> Email Password Reset Link
                    </span>
                    <span id="forgot-btn-spinner" class="spinner hidden"></span>
                </button>
            </form>

            <div class="auth-links">
                <p>Remember your password?</p>
                <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgot-password-form');
            const btn = document.getElementById('forgot-btn');
            const btnText = document.getElementById('forgot-btn-text');
            const btnSpinner = document.getElementById('forgot-btn-spinner');
            if (form && btn && btnText && btnSpinner) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btnText.classList.add('hidden');
                    btnSpinner.classList.remove('hidden');
                });
            }
        });
    </script>
</body>
</html>
