<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/login.css'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-header">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <i class="icon fas fa-envelope modern-icon" style="margin: 0 auto;"></i>
                        <h2>Verify Your Email</h2>
                        <p>Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
                    </div>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> A new verification link has been sent to the email address you provided during registration.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4" id="verify-email-form">
                @csrf
                <button type="submit" class="btn-primary" id="verify-btn">
                    <span id="verify-btn-text"><i class="fas fa-paper-plane"></i> Resend Verification Email</span>
                    <span id="verify-btn-spinner" class="spinner hidden"></span>
                </button>
            </form>

            <div class="auth-links">
                
                    <a href="{{ route('profile.user_profile') }}" class="btn-primary" style="background: #000000; margin-top: 1rem;">
                        <i class="fas fa-arrow-left"></i> Back 
                    </a>
               
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('verify-email-form');
            const btn = document.getElementById('verify-btn');
            const btnText = document.getElementById('verify-btn-text');
            const btnSpinner = document.getElementById('verify-btn-spinner');
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
