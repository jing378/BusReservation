<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Bus Reservation System</title>
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
                        <i class="fas fa-key"></i>
                        <h1>Forgot Password</h1>
                    </div>
                    <p class="subtitle">Enter your email to reset your password</p>
                </div>

                @if ($message !== '')
                <div class="php-message {{ $messageType }}" style="margin-bottom: 20px;">
                    <i class="fas {!! $messageType === 'success' ? 'fa-check-circle' : ($messageType === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle') !!}"></i>
                    <span>{!! $message !!}</span>
                </div>
                @endif

                <form class="login-form" method="post" action="">
    @csrf
                    <div class="form-group">
                        <div class="input-container">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email" placeholder="Email address" value="{{ request()->input('email') ?? '' }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <button type="submit" class="login-btn">
                        <span class="btn-text">Send Reset Link</span>
                    </button>

                    <div class="signup-link">
                        <p><a href="{{ route('login') }}" class="signup-btn">Back to Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

