<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
    <style>
    /* Match bookings/trips input design */
    .profile-form .field { display: flex; flex-direction: column; gap: 6px; }
    .profile-form .field label { font-size: 13px; color: #b8c7db; }
    .profile-form .input-container {
        position: relative; display: flex; align-items: center;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px; padding: 12px 14px;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .profile-form .input-container:focus-within {
        border-color: rgba(59,130,246,0.6);
        box-shadow: 0 0 0 4px rgba(59,130,246,0.15);
        background: rgba(255,255,255,0.06);
    }
    .profile-form .input-container input,
    .profile-form .input-container select {
        flex: 1; background: transparent; border: none; outline: none;
        color: #e5e7eb; font-size: 14px;
    }
    .profile-form .input-container input[type="password"] {
        padding-right: 40px; /* Space for password toggle button */
    }
    /* Select specific tweaks for visibility and native list contrast */
    .profile-form .input-container select {
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        padding-right: 28px; cursor: pointer;
    }
    .profile-form .input-container select:focus { outline: none; }
    .profile-form .input-container .select-caret {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; pointer-events: none; font-size: 14px;
    }
    /* Option colors in supported browsers */
    .profile-form select option { background: #111827; color: #e5e7eb; }
    .profile-form .input-focus-line {
        position: absolute; left: 14px; right: 14px; bottom: 6px; height: 2px;
        background: linear-gradient(90deg, transparent, rgba(59,130,246,0.7), transparent);
        opacity: 0; transform: scaleX(0.6);
        transition: transform .2s ease, opacity .2s ease;
    }
    .profile-form .input-container:focus-within .input-focus-line { opacity: 1; transform: scaleX(1); }
    
    /* Override browser autofill styles to maintain design */
    .profile-form .input-container input:-webkit-autofill,
    .profile-form .input-container input:-webkit-autofill:hover,
    .profile-form .input-container input:-webkit-autofill:focus,
    .profile-form .input-container input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px rgba(255,255,255,0.04) inset !important;
        -webkit-text-fill-color: #e5e7eb !important;
        background-color: transparent !important;
        background-clip: content-box !important;
        border: none !important;
        box-shadow: 0 0 0 30px rgba(255,255,255,0.04) inset !important;
        transition: background-color 5000s ease-in-out 0s, color 5000s ease-in-out 0s !important;
    }
    .profile-form .input-container input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 30px rgba(255,255,255,0.06) inset !important;
        box-shadow: 0 0 0 30px rgba(255,255,255,0.06) inset !important;
    }
    /* Firefox autofill override */
    .profile-form .input-container input:-moz-autofill {
        background-color: transparent !important;
        color: #e5e7eb !important;
        border: none !important;
        box-shadow: none !important;
    }
    /* Ensure autofill doesn't break container design */
    .profile-form .input-container:has(input:-webkit-autofill) {
        border-color: rgba(255,255,255,0.08) !important;
        background: rgba(255,255,255,0.04) !important;
        box-shadow: none !important;
    }
    .profile-form .input-container:has(input:-webkit-autofill):focus-within {
        border-color: rgba(59,130,246,0.6) !important;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.15) !important;
        background: rgba(255,255,255,0.06) !important;
    }
    /* Prevent any browser default styling on autofilled inputs */
    .profile-form .input-container input:-webkit-autofill {
        caret-color: #e5e7eb !important;
    }
    /* Remove any outline or border that browsers might add */
    .profile-form .input-container input:-webkit-autofill,
    .profile-form .input-container input:-webkit-autofill:hover,
    .profile-form .input-container input:-webkit-autofill:focus,
    .profile-form .input-container input:-webkit-autofill:active {
        outline: none !important;
        outline-offset: 0 !important;
        border: none !important;
        -webkit-appearance: none !important;
        appearance: none !important;
    }
    /* Prevent browser validation from showing red borders on autofill */
    .profile-form .input-container input:invalid:not(:focus):not(:placeholder-shown) {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
    .profile-form .input-container input:valid:not(:focus) {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
    /* Override browser default validation styling */
    .profile-form .input-container input:required {
        box-shadow: none !important;
    }
    .profile-form .input-container input:required:invalid {
        box-shadow: none !important;
    }
    /* Ensure autofilled inputs maintain container styling */
    .profile-form .input-container:has(input:-webkit-autofill:invalid) {
        border-color: rgba(255,255,255,0.08) !important;
        background: rgba(255,255,255,0.04) !important;
    }
    .profile-form .input-container:has(input:-webkit-autofill:valid) {
        border-color: rgba(255,255,255,0.08) !important;
        background: rgba(255,255,255,0.04) !important;
    }
    
    /* Confirmation modal styles - matching delete modal design */
    .confirm-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        backdrop-filter: blur(4px);
        animation: fadeIn 0.2s ease-out;
    }
    
    .confirm-modal-content {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #1a222c;
        border-radius: 12px;
        padding: 24px;
        max-width: 400px;
        width: 90%;
        z-index: 1001;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        animation: modalSlideInScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes modalSlideInScale {
        from {
            opacity: 0;
            transform: translate(-50%, -60%) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }
    
    .confirm-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .confirm-modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 18px;
    }
    
    .confirm-modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #e5e7eb;
        margin: 0;
    }
    
    .confirm-modal-body {
        color: #9ca3af;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .confirm-modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    
    .confirm-modal-btn {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 120px;
        justify-content: center;
    }
    
    .confirm-modal-btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .confirm-modal-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    .confirm-modal-btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: 1px solid rgba(59, 130, 246, 0.6);
    }
    
    .confirm-modal-btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* Password toggle button styles */
    .profile-form .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px 6px;
        display: none;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        opacity: 0.8;
        transition: opacity 0.2s ease, color 0.2s ease;
        z-index: 10;
    }
    .profile-form .password-toggle:hover {
        opacity: 1;
        color: #e5e7eb;
    }
    .profile-form .password-toggle:active {
        transform: translateY(-50%) scale(0.95);
    }
    .profile-form .password-toggle.show,
    .profile-form .input-container:has(input[type="password"]:not(:placeholder-shown)) .password-toggle {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    /* Layout */
    .profile-panel { max-width: 960px; margin-left: auto; margin-right: auto; }
    .profile-header {
        display: flex; gap: 16px; align-items: center;
        padding: 4px 0 16px; margin-bottom: 16px; border-bottom: 1px solid var(--line);
    }
    .profile-avatar {
        flex: 0 0 64px; width: 64px; height: 64px; border-radius: 50%;
        background: linear-gradient(135deg, #38bdf8, #2563eb); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 20px;
    }
    .profile-identity { min-width: 0; }
    .profile-name { font-size: 18px; font-weight: 600; line-height: 1.2; overflow-wrap: anywhere; }
    .profile-meta { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 8px; }
    .profile-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.05); border: 1px solid var(--line); color: #cbd5e1;
        border-radius: 999px; padding: 4px 10px; font-size: 12px; line-height: 1.3;
    }
    .profile-pill.is-active { background: rgba(34,197,94,0.12); border-color: rgba(34,197,94,0.35); color: #4ade80; }
    .profile-pill.is-disabled { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.35); color: #f87171; }

    .profile-form { display: flex; flex-direction: column; gap: 20px; }
    .profile-section-title {
        display: flex; align-items: center; gap: 8px; margin: 0 0 4px;
        font-size: 14px; font-weight: 600; color: #e5e7eb;
    }
    .profile-section-title i { color: #3b82f6; }
    .profile-section-hint { margin: 0 0 12px; font-size: 12px; color: var(--muted); }
    .profile-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .profile-section + .profile-section { padding-top: 20px; border-top: 1px solid var(--line); }
    .profile-form .field { min-width: 0; }
    .profile-form .input-container input,
    .profile-form .input-container select { min-width: 0; width: 100%; }
    .profile-form .input-container input[readonly] { color: #94a3b8; cursor: not-allowed; }
    .profile-actions { display: flex; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--line); }
    .profile-actions .btn { min-height: 44px; padding: 10px 18px; }

    @media (max-width: 720px) {
        .profile-grid { grid-template-columns: minmax(0, 1fr); gap: 12px; }
        .profile-actions .btn { width: 100%; justify-content: center; }
        .profile-form .password-toggle { width: 40px; height: 40px; }
        .profile-form .input-container input#password,
        .profile-form .input-container input#confirm_password { padding-right: 48px; }
    }

    @media (max-width: 420px) {
        .profile-header { gap: 12px; }
        .profile-avatar { flex-basis: 52px; width: 52px; height: 52px; font-size: 17px; }
        .profile-name { font-size: 16px; }
    }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/mobile.css') }}">
    <script src="{{ asset('assets/js/mobile-responsive.js') }}" defer></script>
</head>
<body>
    <div class="dash-wrap">
        @include('partials.sidebar')
        <button class="btn-ghost hamburger-btn" id="mobileMenuToggle" title="Toggle menu"><i class="fas fa-bars"></i></button>
        <main class="content">
            <header class="topbar">
                <div class="welcome"><h2>User Profile</h2><p>Profile & Application preferences</p></div>
                <div class="actions"></div>
            </header>
            <section class="panel profile-panel">
                <h3>My Profile</h3>
                @if ($profile)
                @if ($profileOk)<div class="qa" style="justify-content:flex-start;color:#22c55e"><i class="fas fa-check-circle"></i><span>{{ $profileOk }}</span></div>@endif
                @if ($profileErrors)<div class="qa" style="justify-content:flex-start;color:#ef4444"><i class="fas fa-circle-exclamation"></i><span>Please fix the form errors.</span></div>@endif
                <div class="profile-header">
                    <div class="profile-avatar">
                        {{ $initials ?: 'U' }}
                    </div>
                    <div class="profile-identity">
                        <div class="profile-name">
                            {{ $profile['full_name'] }}
                        </div>
                        <div class="profile-meta">
                            <span class="profile-pill"><i class="fas fa-calendar-check"></i> Member since {{ date('M d, Y', strtotime($profile['created_at'])) }}</span>
                            <span class="profile-pill"><i class="fas fa-tag"></i> {{ $profile['user_type'] }}</span>
                            @if ((int) $profile['is_active'] === 1)
                                <span class="profile-pill is-active"><i class="fas fa-circle-check"></i> Active</span>
                            @else
                                <span class="profile-pill is-disabled"><i class="fas fa-ban"></i> Disabled</span>
                            @endif
                        </div>
                    </div>
                </div>

                <form method="post" action="{{ route('customer.profile.store') }}" class="profile-form" id="profileForm">
    @csrf
                    <input type="hidden" name="action" value="update_profile">

                    <div class="profile-section">
                        <h4 class="profile-section-title"><i class="fas fa-id-card"></i> Account details</h4>
                        <p class="profile-section-hint">Your name and contact details as they appear on bookings.</p>
                        <div class="profile-grid">
                    <div class="field">
                        <label>Full Name</label>
                        <div class="input-container">
                            <input type="text" name="full_name" value="{{ $profile['full_name'] }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['full_name']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['full_name'] }}</div>@endif
                    </div>

                    <div class="field">
                        <label>Username</label>
                        <div class="input-container">
                            <input type="text" name="username" value="{{ $profile['username'] }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['username']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['username'] }}</div>@endif
                    </div>

                    <div class="field">
                        <label>Email</label>
                        <div class="input-container">
                            <input type="email" name="email" value="{{ $profile['email'] }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['email']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['email'] }}</div>@endif
                    </div>

                    <div class="field">
                        <label>Phone</label>
                        <div class="input-container">
                            <input type="text" name="phone" value="{{ $profile['phone'] ?? '' }}">
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['phone']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['phone'] }}</div>@endif
                    </div>

                    <div class="field">
                        <label>User Type</label>
                        <div class="input-container">
                            @if (auth()->user()->isAdmin())
                            <select name="user_type">
                                <option value="Student" {!! ($profile['user_type']==='Student')?'selected':'' !!}>Student</option>
                                <option value="Regular" {!! ($profile['user_type']==='Regular')?'selected':'' !!}>Regular</option>
                                <option value="Senior Citizen/PWD" {!! ($profile['user_type']==='Senior Citizen/PWD')?'selected':'' !!}>Senior Citizen/PWD</option>
                            </select>
                            <i class="fas fa-chevron-down select-caret"></i>
                            @else
                            <input type="text" value="{{ $profile['user_type'] }}" readonly>
                            <input type="hidden" name="user_type" value="{{ $profile['user_type'] }}">
                            @endif
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                        </div>
                    </div>

                    <div class="profile-section">
                        <h4 class="profile-section-title"><i class="fas fa-lock"></i> Change password</h4>
                        <p class="profile-section-hint">Leave both fields blank to keep your current password.</p>
                        <div class="profile-grid">
                    <div class="field">
                        <label>New Password</label>
                        <div class="input-container">
                            <input type="password" id="password" name="password" placeholder="Leave blank to keep current">
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['password']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['password'] }}</div>@endif
                    </div>

                    <div class="field">
                        <label>Confirm Password</label>
                        <div class="input-container">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password">
                            <button type="button" class="password-toggle" id="confirmPasswordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['confirm_password']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['confirm_password'] }}</div>@endif
                    </div>

                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="submit" class="btn" style="display:inline-flex; align-items:center; gap:8px;"><i class="fas fa-save"></i> Save Changes</button>
                    </div>
                </form>

                @else
                <div class="qa" style="justify-content:flex-start"><i class="fas fa-circle-info"></i><span>Profile not found.</span></div>
                @endif
            </section>
        </main>
    </div>
    
    <!-- Confirmation Modal -->
    <div id="confirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="confirm-modal-title" id="confirmModalTitle">Confirm Update</h3>
            </div>
            <div class="confirm-modal-body">
                <p id="confirmModalMessage">Are you sure you want to save these profile changes?</p>
                <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;" id="confirmModalDetails">
                </div>
            </div>
            <div class="confirm-modal-footer">
                <button type="button" class="confirm-modal-btn confirm-modal-btn-secondary" onclick="hideConfirmModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="confirm-modal-btn confirm-modal-btn-primary" id="confirmSubmitBtn">
                    <i class="fas fa-check"></i> Confirm
                </button>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
    <script>
    // Password toggle functionality
    document.addEventListener('DOMContentLoaded', function () {
        function setupPasswordToggle(inputId, toggleId) {
            var passwordInput = document.getElementById(inputId);
            var passwordToggle = document.getElementById(toggleId);
            
            if (!passwordInput || !passwordToggle) return;
            
            // Ensure positioning and transform are set immediately
            passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
            passwordToggle.style.setProperty('position', 'absolute', 'important');
            passwordToggle.style.setProperty('right', '12px', 'important');
            passwordToggle.style.setProperty('top', '50%', 'important');
            
            // Set up click handler for toggle
            passwordToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                passwordToggle.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
                passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
                passwordInput.focus();
            });
            
            // Show/hide toggle based on input value
            function updateToggle() {
                var hasValue = passwordInput.value && passwordInput.value.length > 0;
                if (hasValue) {
                    passwordToggle.classList.add('show');
                    passwordToggle.style.setProperty('display', 'inline-flex', 'important');
                    passwordToggle.style.setProperty('visibility', 'visible', 'important');
                    passwordToggle.style.setProperty('opacity', '1', 'important');
                    passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
                } else {
                    passwordToggle.classList.remove('show');
                    passwordToggle.style.setProperty('display', 'none', 'important');
                    passwordToggle.style.setProperty('visibility', 'hidden', 'important');
                    passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
                }
            }
            
            // Bind events
            passwordInput.addEventListener('input', updateToggle);
            passwordInput.addEventListener('blur', updateToggle);
            passwordInput.addEventListener('focus', updateToggle);
            passwordInput.addEventListener('change', updateToggle);
            passwordInput.addEventListener('paste', function() {
                setTimeout(updateToggle, 10);
            });
            passwordInput.addEventListener('keydown', function() {
                setTimeout(updateToggle, 0);
            });
            
            // Initial check
            updateToggle();
            
            // Check for autofill
            setTimeout(updateToggle, 100);
            var autofillCheckInterval = setInterval(function() {
                if (passwordInput.value && passwordInput.value.length > 0) {
                    updateToggle();
                    clearInterval(autofillCheckInterval);
                }
            }, 200);
            setTimeout(function() {
                clearInterval(autofillCheckInterval);
            }, 5000);
        }
        
        // Setup both password toggles
        setupPasswordToggle('password', 'passwordToggle');
        setupPasswordToggle('confirm_password', 'confirmPasswordToggle');
    });
    
    // Confirmation modal functions
    function showConfirmModal(details) {
        document.getElementById('confirmModalTitle').textContent = 'Confirm Update';
        document.getElementById('confirmModalMessage').textContent = 'Are you sure you want to save these profile changes?';
        
        const detailsDiv = document.getElementById('confirmModalDetails');
        detailsDiv.innerHTML = details;
        
        if (window.animateModalOpen) {
            animateModalOpen('#confirmModal');
        } else {
            document.getElementById('confirmModal').style.display = 'block';
        }
    }
    
    function hideConfirmModal() {
        if (window.animateModalClose) {
            animateModalClose('#confirmModal');
        } else {
            document.getElementById('confirmModal').style.display = 'none';
        }
    }
    
    // Intercept form submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fullName = document.querySelector('input[name="full_name"]').value;
        const email = document.querySelector('input[name="email"]').value;
        const username = document.querySelector('input[name="username"]').value;
        const phone = document.querySelector('input[name="phone"]').value;
        const userType = document.querySelector('select[name="user_type"]') ? document.querySelector('select[name="user_type"]').value : document.querySelector('input[name="user_type"]').value;
        const password = document.getElementById('password').value;
        
        let details = '<div><strong>Full Name:</strong> ' + fullName + '</div>';
        details += '<div><strong>Email:</strong> ' + email + '</div>';
        details += '<div><strong>Username:</strong> ' + username + '</div>';
        if (phone) {
            details += '<div><strong>Phone:</strong> ' + phone + '</div>';
        }
        details += '<div><strong>User Type:</strong> ' + userType + '</div>';
        if (password) {
            details += '<div><strong>Password:</strong> <span style="color: #3b82f6;">Will be updated</span></div>';
        }
        
        showConfirmModal(details);
        
        // Store form reference for submission after confirmation
        window.pendingProfileForm = this;
    });
    
    // Handle confirmation
    document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
        if (window.pendingProfileForm) {
            hideConfirmModal();
            window.pendingProfileForm.submit();
            window.pendingProfileForm = null;
        }
    });
    
    // Close confirmation modal when clicking outside
    window.onclick = function(event) {
        const confirmModal = document.getElementById('confirmModal');
        if (event.target === confirmModal) {
            hideConfirmModal();
        }
    }
    </script>
    
    <!-- Logout Confirmation Modal -->
    <div id="logoutConfirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h3 class="confirm-modal-title">Confirm Logout</h3>
            </div>
            <div class="confirm-modal-body">
                <p>Are you sure you want to logout?</p>
                <p style="margin-top: 12px; color: #9ca3af; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> You will need to login again to access your account.
                </p>
            </div>
            <div class="confirm-modal-footer">
                <button type="button" class="confirm-modal-btn confirm-modal-btn-secondary" onclick="hideLogoutConfirmModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="confirm-modal-btn confirm-modal-btn-primary" id="logoutConfirmBtn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Logout confirmation modal
        const logoutLink = document.querySelector('.logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.animateModalOpen) {
                    animateModalOpen('#logoutConfirmModal');
                } else {
                    document.getElementById('logoutConfirmModal').style.display = 'block';
                }
            });
        }
        
        function hideLogoutConfirmModal() {
            if (window.animateModalClose) {
                animateModalClose('#logoutConfirmModal');
            } else {
                document.getElementById('logoutConfirmModal').style.display = 'none';
            }
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('logoutConfirmModal');
            if (event.target === modal) {
                hideLogoutConfirmModal();
            }
        });
    </script>
</body>
</html>


