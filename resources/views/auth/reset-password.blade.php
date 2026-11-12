<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
</head>
<body class="page-login">
    <div class="container">
        <div class="bg-animation">
            <div class="floating-bus">
                <i class="fas fa-bus"></i>
            </div>
            <div class="floating-clouds">
                <div class="cloud cloud1"></div>
                <div class="cloud cloud2"></div>
                <div class="cloud cloud3"></div>
            </div>
        </div>

        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-lock"></i>
                        <h1>Reset Password</h1>
                    </div>
                    <p class="subtitle">Enter your new password</p>
                </div>

                @if ($message !== '')
                <div class="php-message {{ $messageType }}" style="margin-bottom: 20px;">
                    <i class="fas {!! $messageType === 'success' ? 'fa-check-circle' : ($messageType === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') !!}"></i>
                    <span>{!! $message !!}</span>
                </div>
                @endif
                
                @if ($token && !$validToken)
                <div class="php-message error" style="margin-bottom: 20px;">
                    <i class="fas fa-info-circle"></i>
                    <span>Debug: Token received: {!! htmlspecialchars(substr($token, 0, 20)) . '...' !!}</span>
                </div>
                @endif

                @if ($validToken)
                <form class="login-form" method="post" action="">
    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="form-group">
                        <div class="input-container">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="password" placeholder="New password" required>
                            <button type="button" class="password-toggle" id="passwordToggle1">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-container">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="confirm" id="confirm" placeholder="Confirm new password" required>
                            <button type="button" class="password-toggle" id="passwordToggle2">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">
                        <span class="btn-text">Reset Password</span>
                    </button>

                    <div class="signup-link">
                        <p><a href="{{ route('login') }}" class="signup-btn">Back to Login</a></p>
                    </div>
                </form>
                @else
                <div class="signup-link">
                    <p><a href="{{ route('password.request') }}" class="signup-btn">Request New Reset Link</a></p>
                    <p style="margin-top: 10px;"><a href="{{ route('login') }}" class="signup-btn">Back to Login</a></p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const passwordToggle1 = document.getElementById('passwordToggle1');
            const passwordToggle2 = document.getElementById('passwordToggle2');
            const passwordInput1 = document.getElementById('password');
            const passwordInput2 = document.getElementById('confirm');

            // Function to show/hide toggle based on input
            function bindToggleVisibility(input, toggleBtn) {
                if (!input || !toggleBtn) return;
                // Hide toggle initially
                toggleBtn.style.display = 'none';
                
                function update() {
                    const has = input.value && input.value.length > 0;
                    // Use class-based approach with absolute positioning
                    if (has) {
                        toggleBtn.classList.add('show');
                        toggleBtn.style.setProperty('display', 'inline-flex', 'important');
                        toggleBtn.style.setProperty('visibility', 'visible', 'important');
                        toggleBtn.style.setProperty('opacity', '1', 'important');
                        // Preserve transform for centering
                        if (!toggleBtn.style.transform || !toggleBtn.style.transform.includes('translateY')) {
                            toggleBtn.style.transform = 'translateY(-50%)';
                        }
                    } else {
                        toggleBtn.classList.remove('show');
                        toggleBtn.style.setProperty('display', 'none', 'important');
                        toggleBtn.style.setProperty('visibility', 'hidden', 'important');
                    }
                }
                update();
                input.addEventListener('input', update);
                input.addEventListener('blur', update);
                input.addEventListener('focus', update);
            }

            if (passwordToggle1 && passwordInput1) {
                passwordToggle1.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const type = passwordInput1.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput1.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                    // Refocus input after toggle
                    passwordInput1.focus();
                });
                bindToggleVisibility(passwordInput1, passwordToggle1);
            }

            if (passwordToggle2 && passwordInput2) {
                passwordToggle2.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const type = passwordInput2.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput2.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                    // Refocus input after toggle
                    passwordInput2.focus();
                });
                bindToggleVisibility(passwordInput2, passwordToggle2);
            }
        });
    </script>
</body>
</html>

