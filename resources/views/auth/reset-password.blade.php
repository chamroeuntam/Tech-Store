<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/login.css'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h2>
                    <span class="icon"><i class="fas fa-key"></i></span>
                    Reset Password
                </h2>
                <p>Enter your email and new password to reset your account.</p>
            </div>

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

            <form method="POST" action="{{ route('password.store') }}" id="reset-password-form">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrapper">
                        <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-wrapper">
                        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="reset-btn">
                    <span id="reset-btn-text"><i class="fas fa-key"></i> Reset Password</span>
                    <span id="reset-btn-spinner" class="spinner hidden"></span>
                </button>
            </form>

            <div class="auth-links">
                <p>Remember your password?</p>
                <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Back to Login</a>
            </div>
        </div>
    </div>

    <script>
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
            const form = document.getElementById('reset-password-form');
            const btn = document.getElementById('reset-btn');
            const btnText = document.getElementById('reset-btn-text');
            const btnSpinner = document.getElementById('reset-btn-spinner');
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
