<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="bg-animation">
            <div class="floating-bus"><i class="fas fa-bus"></i></div>
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
                        <h1>Create Account</h1>
                    </div>
                    <p class="subtitle">Join BusReserve to book your trips</p>
                </div>

                @if ($success)
                <div class="php-message success"><i class="fas fa-check-circle"></i><span>{{ $success }}</span></div>
                <script>
                    // Redirect to login page after 1.5 seconds
                    setTimeout(function() {
                        window.location.href = '{{ route('login') }}';
                    }, 1500);
                </script>
                @endif

                @if (isset($formErrors['general']))
                <div class="php-message error"><i class="fas fa-exclamation-circle"></i><span>{{ $formErrors['general'] }}</span></div>
                @endif

                <form class="login-form" id="registerForm" method="post" action="">
    @csrf
                    <div class="form-group">
                        <div class="field-hint" id="hint-fullName">Please enter your complete name</div>
                        <div class="input-container {!! isset($formErrors['fullName']) ? 'error' : '' !!}">
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" id="fullName" name="fullName" placeholder="Full name" value="{{ request()->input('fullName') ?? '' }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($formErrors['fullName']))<small class="field-error">{{ $formErrors['fullName'] }}</small>@endif
                    </div>

                    <div class="form-group">
                        <div class="field-hint" id="hint-email">Please enter a valid email address</div>
                        <div class="input-container {!! isset($formErrors['email']) ? 'error' : '' !!}">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" placeholder="Email address" value="{{ request()->input('email') ?? '' }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        <small class="field-error" id="email-error" style="display: {!! isset($formErrors['email']) ? 'block' : 'none' !!}">{{ $formErrors['email'] ?? '' }}</small>
                    </div>

                    <div class="form-group">
                        <div class="field-hint" id="hint-phone">Please enter valid phone number</div>
                        <div class="input-container {!! isset($formErrors['phone']) ? 'error' : '' !!}">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" id="phone" name="phone" placeholder="Phone number" value="{{ request()->input('phone') ?? '' }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($formErrors['phone']))<small class="field-error">{{ $formErrors['phone'] }}</small>@endif
                    </div>

                    <div class="form-group">
                        <div class="field-hint" id="hint-username">Please enter a unique username</div>
                        <div class="input-container {!! isset($formErrors['username']) ? 'error' : '' !!}">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Username" value="{{ request()->input('username') ?? '' }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        <small class="field-error" id="username-error" style="display: {!! isset($formErrors['username']) ? 'block' : 'none' !!}">{{ $formErrors['username'] ?? '' }}</small>
                    </div>

                    <div class="form-group">
                        <div class="field-hint" id="hint-userType">Please select your user type</div>
                        <div class="input-container">
                            <i class="fas fa-users input-icon"></i>
                            <select id="userType" name="userType" required>
                                <option value="">Select User Type</option>
                                <option value="Student" {!! (request()->has('userType') && request()->input('userType') === 'Student') ? 'selected' : '' !!}>Student</option>
                                <option value="Regular" {!! (request()->has('userType') && request()->input('userType') === 'Regular') ? 'selected' : '' !!}>Regular</option>
                                <option value="Senior Citizen/PWD" {!! (request()->has('userType') && request()->input('userType') === 'Senior Citizen/PWD') ? 'selected' : '' !!}>Senior Citizen/PWD</option>
                            </select>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($formErrors['userType']))<small class="field-error">{{ $formErrors['userType'] }}</small>@endif
                    </div>

                    

                    <div class="form-group">
                        <div class="field-hint" id="hint-password">Please enter a secure password (min 8 characters)</div>
                        <div class="input-container {!! isset($formErrors['password']) ? 'error' : '' !!}">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Password" required>
                            <button type="button" class="password-toggle" id="passwordToggle1"><i class="fas fa-eye"></i></button>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($formErrors['password']))<small class="field-error">{{ $formErrors['password'] }}</small>@endif
                    </div>

                    <div class="form-group">
                        <div class="field-hint" id="hint-confirm">Please confirm your password</div>
                        <div class="input-container {!! isset($formErrors['confirm']) ? 'error' : '' !!}">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="confirm" name="confirm" placeholder="Confirm password" required>
                            <button type="button" class="password-toggle" id="passwordToggle2"><i class="fas fa-eye"></i></button>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($formErrors['confirm']))<small class="field-error">{{ $formErrors['confirm'] }}</small>@endif
                    </div>

                    <div class="form-options" style="align-items:flex-start;">
                        <label class="remember-me">
                            <input type="checkbox" id="newsletter" name="newsletter" {!! request()->has('newsletter') ? 'checked' : '' !!}>
                            <span class="checkmark"></span>
                            Subscribe to our newsletter for updates and promotions
                        </label>
                    </div>

                    <div class="form-options" style="align-items:flex-start;">
                        <label class="remember-me">
                            <input type="checkbox" id="terms" name="terms" {!! request()->has('terms') ? 'checked' : '' !!}>
                            <span class="checkmark"></span>
                            I agree to the <a href="#" id="termsLink" style="color:#fff; text-decoration:underline;">Terms and Conditions</a>
                        </label>
                    </div>
                    @if (isset($formErrors['terms']))<small class="field-error" style="margin-top:-6px; display:block;">{{ $formErrors['terms'] }}</small>@endif

                    <button type="submit" class="login-btn" id="registerBtn">
                        <span class="btn-text">Create Account</span>
                        <div class="btn-loader"><div class="spinner"></div></div>
                    </button>

                    <div class="signup-link">
                        <p>Already have an account? <a href="{{ route('login') }}" class="signup-btn">Sign in</a></p>
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

    <!-- Terms and Conditions Modal -->
    <div class="modal" id="termsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Terms and Conditions</h2>
                <button class="close" id="closeModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <h3>1. Acceptance of Terms</h3>
                <p>By accessing and using the Bus Reservation System, you agree to be bound by these Terms and Conditions.</p>

                <h3>2. User Registration</h3>
                <p>Users must provide accurate, current, and complete information during registration. Users are responsible for maintaining the confidentiality of their account credentials.</p>

                <h3>3. Booking and Cancellation</h3>
                <p>- Bookings are subject to seat availability<br>
                - Cancellations must be made at least 24 hours before departure<br>
                - Refunds will be processed according to our refund policy</p>

                <h3>4. User Categories and Discounts</h3>
                <p>- Student rates require valid student ID<br>
                - Senior Citizen/PWD rates require valid government-issued ID<br>
                - Discounts cannot be combined with other promotions</p>

                <h3>5. Privacy Policy</h3>
                <p>We collect and process personal information in accordance with our Privacy Policy. By using our service, you consent to such processing.</p>

                <h3>6. Prohibited Activities</h3>
                <p>Users shall not:<br>
                - Share account credentials<br>
                - Submit false booking information<br>
                - Engage in any activity that disrupts our service</p>

                <h3>7. Modifications to Service</h3>
                <p>We reserve the right to modify or discontinue any aspect of our service at any time.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModalBtn">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" id="agreeButton" class="btn">
                    <i class="fas fa-check"></i> I Agree
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/register.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
    <script>
        // Dynamic field hints functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Terms and Conditions Modal
            const modal = document.getElementById('termsModal');
            const termsLink = document.getElementById('termsLink');
            const closeModal = document.getElementById('closeModal');
            const closeModalBtn = document.getElementById('closeModalBtn');

            termsLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.animateModalOpen) {
                    if (!modal.dataset.modalDisplay) modal.dataset.modalDisplay = 'flex';
                    animateModalOpen(modal);
                } else {
                    modal.style.display = 'block';
                }
                // Prevent body scroll while modal is open
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
            });

            // Handle agree button click
            const agreeButton = document.getElementById('agreeButton');
            const termsCheckbox = document.getElementById('terms');
            
            agreeButton.addEventListener('click', function() {
                termsCheckbox.checked = true;
                if (window.animateModalClose) {
                    animateModalClose(modal);
                } else {
                    modal.style.display = 'none';
                }
                // Restore overflow after animation completes
                setTimeout(() => {
                    document.documentElement.style.overflow = '';
                    document.body.style.overflow = '';
                }, 250);
                // Remove error if present
                const errorElement = termsCheckbox.closest('.form-options').nextElementSibling;
                if (errorElement && errorElement.classList.contains('field-error')) {
                    errorElement.style.display = 'none';
                }
            });

            // Handle close button click
            function handleCloseModal() {
                // Apply animation to content element
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.classList.add('is-animating-out');
                }
                
                if (window.animateModalClose) {
                    animateModalClose(modal);
                } else {
                    modal.style.display = 'none';
                }
                // Restore overflow after animation completes
                setTimeout(() => {
                    document.documentElement.style.overflow = '';
                    document.body.style.overflow = '';
                }, 250);
            }

            closeModal.addEventListener('click', handleCloseModal);
            closeModalBtn.addEventListener('click', handleCloseModal);

            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    // Apply animation to content element
                    const modalContent = modal.querySelector('.modal-content');
                    if (modalContent) {
                        modalContent.classList.add('is-animating-out');
                    }
                    
                    if (window.animateModalClose) {
                        animateModalClose(modal);
                    } else {
                        modal.style.display = 'none';
                    }
                    // Restore overflow after animation completes
                    setTimeout(() => {
                        document.documentElement.style.overflow = '';
                        document.body.style.overflow = '';
                    }, 250);
                }
            });

            // Handle email and username error clearing
            const emailInput = document.getElementById('email');
            const usernameInput = document.getElementById('username');
            const emailError = document.getElementById('email-error');
            const usernameError = document.getElementById('username-error');

            function clearError(input, errorElement) {
                if (input.value.trim() === '') {
                    errorElement.style.display = 'none';
                    input.closest('.input-container').classList.remove('error');
                }
            }

            emailInput.addEventListener('input', () => clearError(emailInput, emailError));
            usernameInput.addEventListener('input', () => clearError(usernameInput, usernameError));

            const fields = [
                { input: 'fullName', hint: 'hint-fullName' },
                { input: 'email', hint: 'hint-email' },
                { input: 'phone', hint: 'hint-phone' },
                { input: 'username', hint: 'hint-username' },
                { input: 'userType', hint: 'hint-userType' },
                { input: 'password', hint: 'hint-password' },
                { input: 'confirm', hint: 'hint-confirm' }
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.input);
                const hint = document.getElementById(field.hint);
                
                if (input && hint) {
                    // Show hint on focus
                    input.addEventListener('focus', function() {
                        hint.classList.add('show');
                    });
                    
                    // Hide hint on blur (if field is empty)
                    input.addEventListener('blur', function() {
                        if (this.value.trim() === '') {
                            hint.classList.remove('show');
                        }
                    });
                    
                    // Hide hint when user starts typing
                    input.addEventListener('input', function() {
                        if (this.value.trim() !== '') {
                            hint.classList.remove('show');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>


