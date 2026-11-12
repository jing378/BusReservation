<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Reservation System - Login</title>
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
                <a href="{{ route('home') }}" class="back-link" aria-label="Back to home"><i class="fas fa-arrow-left"></i> Back</a>
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-bus"></i>
                        <h1>BusReserve</h1>
                    </div>
                    <p class="subtitle">Your Journey Starts Here</p>
                </div>

                @if ($message !== '')
                <div class="php-message {{ $messageType }}" id="phpMessage">
                    <i class="fas {!! $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' !!}"></i>
                    <span>{{ $message }}</span>
                </div>
                @elseif (session('logged_out'))
                <div class="php-message success" id="phpMessage">
                    <i class="fas fa-check-circle"></i>
                    <span>You have been logged out.</span>
                </div>
                @endif

                <form class="login-form" id="loginForm" method="post" action="">
    @csrf
                    <div class="form-group">
                        <div class="input-container">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Username or Email" value="{{ request()->input('username') ?? (request()->cookie('demo_username') ?? '') }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-container">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" id="rememberMe" name="remember" {!! request()->has('remember') || request()->hasCookie('demo_username') ? 'checked' : '' !!}>
                            <span class="checkmark"></span>
                            Remember me <span style="color: #94a3b8; font-size: 0.85rem;"></span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">
                        <span class="btn-text">Sign In</span>
                        <div class="btn-loader">
                            <div class="spinner"></div>
                        </div>
                    </button>

                    

                    <div class="signup-link">
                        <p>Don't have an account? <a href="{{ route('register') }}" class="signup-btn">Sign up here</a></p>
                    </div>
                </form>
            </div>
        </div>

        <div class="message-container" id="messageContainer">
            <div class="message" id="message">
                <i class="fas fa-check-circle"></i>
                <span class="message-text"></span>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/login.js') }}"></script>
    <script>
    // Auto-redirect on success by role
    @if ($messageType === 'success' && $message !== '')
    setTimeout(function(){
        @if (auth()->user()?->role === 'admin')
        window.location.href = '{{ route('admin.dashboard') }}';
        @else
        window.location.href = '{{ route('customer.dashboard') }}';
        @endif
    }, 1200);
    @endif
    </script>
</body>
</html>


