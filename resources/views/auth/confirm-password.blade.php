<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/register.css'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                <h2>
                    <span class="icon"><i class="fas fa-lock"></i></span>
                    Confirm Password
                </h2>
                <p>This is a secure area of the application. Please confirm your password before continuing.</p>
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

            <form method="POST" action="{{ route('password.confirm') }}" id="confirm-password-form">
                @csrf
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="confirm-btn">
                    <span id="confirm-btn-text"><i class="fas fa-lock"></i> Confirm</span>
                    <span id="confirm-btn-spinner" class="spinner hidden"></span>
                </button>
            </form>
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
            const form = document.getElementById('confirm-password-form');
            const btn = document.getElementById('confirm-btn');
            const btnText = document.getElementById('confirm-btn-text');
            const btnSpinner = document.getElementById('confirm-btn-spinner');
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
