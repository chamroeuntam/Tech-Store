<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/login.css'])
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-container">

        <div class="auth-header">
            <h1><i class="fas fa-user-plus icon"></i> Create Account</h1>
            <p>Join us and start your shopping journey</p>
        </div>

        <!-- FORM START -->
        <form id="register-form" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>

            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" name="first_name" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" name="last_name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password1" required>
                    <button type="button" class="toggle-password" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="password-strength" id="password-strength"></div>
                <div class="password-strength-text" id="password-strength-text"></div>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password2" required>
                    <button type="button" class="toggle-password" onclick="togglePassword(this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
            <div class="divider">or</div>
                <div class="social-login">
                    <a href="{{ url('auth/google') }}" class="btn-google" style="display: flex; align-items: center; text-decoration: none; justify-content: center;">
                        <span style="display: inline-block; width: 22px; height: 27px; vertical-align: middle; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                            </svg>
                        </span>
                        &nbsp;Sign up with Google
                    </a>
                </div>
        </form>
        <!-- FORM END -->

        <div class="auth-links">
            <p>Already have an account?</p>
            <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Sign In</a>
        </div>

    </div>
</div>

<script>
    function togglePassword(btn) {
        const input = btn.parentElement.querySelector('input');
        const icon = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function checkPasswordStrength(password) {
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        if (password.length === 0)
            return { class: '', text: '' };

        if (strength <= 2)
            return { class: 'weak', text: 'Weak password' };

        if (strength === 3)
            return { class: 'fair', text: 'Fair password' };

        if (strength === 4)
            return { class: 'good', text: 'Good password' };

        return { class: 'strong', text: 'Strong password' };
    }

    document.addEventListener('DOMContentLoaded', () => {
        const passwordInput = document.querySelector('input[name="password1"]');
        const bar = document.getElementById('password-strength');
        const text = document.getElementById('password-strength-text');

        passwordInput.addEventListener('input', function () {
            const result = checkPasswordStrength(this.value);
            bar.className = 'password-strength ' + result.class;
            text.textContent = result.text;
        });
    });
</script>

</body>
</html>
