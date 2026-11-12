<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Bus Reservation System</title>
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
    
    /* Settings page layout */
    .settings-page { display: flex; flex-direction: column; gap: 18px; }
    .settings-page .panel { margin: 0; padding: 22px 24px; border-radius: 16px; }
    .settings-page .panel > h3 { display: flex; align-items: center; gap: 10px; font-size: 16px; margin: 0 0 18px; }
    .settings-page .panel > h3 i { color: #3b82f6; }
    .settings-alert { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; border: 1px solid; }
    .settings-alert.is-success { color: #86efac; background: rgba(34,197,94,.08); border-color: rgba(34,197,94,.3); }
    .settings-alert.is-error { color: #fca5a5; background: rgba(239,68,68,.08); border-color: rgba(239,68,68,.3); }
    .settings-alert.is-info { color: #cbd5e1; background: rgba(255,255,255,.04); border-color: var(--line); }

    /* Profile header */
    .profile-head { display: flex; align-items: center; gap: 16px; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid var(--line); }
    .profile-avatar { width: 60px; height: 60px; flex: 0 0 60px; border-radius: 50%; background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: #fff; display: grid; place-items: center; font-weight: 700; font-size: 20px; box-shadow: 0 6px 16px rgba(14,165,233,.25); }
    .profile-identity { flex: 1 1 auto; min-width: 0; }
    .profile-name { font-size: 18px; font-weight: 600; line-height: 1.25; overflow-wrap: anywhere; }
    .profile-since { margin-top: 4px; font-size: 12.5px; color: var(--muted); display: inline-flex; align-items: center; gap: 6px; }
    .status-pill { flex: 0 0 auto; display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    .status-pill.is-active { background: rgba(34,197,94,.12); color: #86efac; border: 1px solid rgba(34,197,94,.35); }
    .status-pill.is-disabled { background: rgba(249,115,22,.12); color: #fdba74; border: 1px solid rgba(249,115,22,.35); }
    .status-pill::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

    /* Profile form */
    .profile-form { display: flex; flex-direction: column; gap: 22px; }
    .form-section-title { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; margin: 0 0 12px; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 18px; }
    .profile-form .field { min-width: 0; }
    .profile-form .input-container { padding: 11px 14px; }
    .profile-form .input-container input,
    .profile-form .input-container select { font-family: inherit; width: 100%; min-width: 0; }
    .profile-form .input-container input::placeholder { color: #64748b; }
    .profile-form .field label .optional { color: #64748b; }
    .field-error { color: #f87171; font-size: 12px; }
    .form-actions { display: flex; justify-content: flex-end; padding-top: 18px; border-top: 1px solid var(--line); }
    .form-actions .btn { padding: 11px 20px; font-weight: 500; }

    /* System maintenance */
    .maint-label { font-size: 12px; color: #9ca3af; margin: 0 0 10px; text-transform: uppercase; letter-spacing: .06em; font-weight: 600; }
    .maint-block + .maint-block { margin-top: 22px; }
    .trip-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .trip-stat { padding: 14px 16px; border-radius: 10px; border-left: 3px solid var(--c); background: color-mix(in srgb, var(--c) 9%, transparent); min-width: 0; }
    .trip-stat span { display: block; font-size: 12px; color: #9ca3af; margin-bottom: 4px; }
    .trip-stat strong { font-size: 26px; font-weight: 700; color: var(--c); line-height: 1.1; }
    .data-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; padding: 16px; border-radius: 12px; background: linear-gradient(135deg, rgba(168,85,247,.06), rgba(99,102,241,.06)); border: 1px solid rgba(255,255,255,.05); }
    .data-stat { min-width: 0; }
    .data-stat .data-stat-title { font-size: 13px; color: #e5e7eb; font-weight: 600; }
    .data-stat strong { display: block; font-size: 26px; font-weight: 700; color: var(--c); margin: 2px 0; line-height: 1.2; }
    .data-stat small { font-size: 11.5px; color: var(--muted); }
    .cleanup-bar { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; padding: 14px 16px; border-radius: 12px; background: rgba(168,85,247,.06); border: 1px solid rgba(168,85,247,.22); }
    .cleanup-bar .btn-clean { background: linear-gradient(135deg, #a855f7, #9333ea); border: 1px solid rgba(168,85,247,.6); padding: 10px 18px; font-size: 13px; font-weight: 500; white-space: nowrap; }
    .cleanup-last { font-size: 12px; line-height: 1.5; color: #9ca3af; }
    .cleanup-last strong { display: block; color: #d8b4fe; font-weight: 500; }
    .cleanup-notes { margin-left: auto; padding-left: 16px; border-left: 1px solid rgba(255,255,255,.1); font-size: 12px; color: #b8c7db; line-height: 1.6; text-align: right; }
    .cleanup-notes i { color: #86efac; margin-right: 4px; }

    @media (max-width: 1100px) {
        .data-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 16px; }
    }
    @media (max-width: 720px) {
        .settings-page .panel { padding: 18px 16px; }
        .form-grid { grid-template-columns: 1fr; gap: 14px; }
        .profile-head { flex-wrap: wrap; }
        .profile-head .status-pill { margin-left: 76px; }
        .form-actions .btn { width: 100%; justify-content: center; }
        .cleanup-bar { flex-direction: column; align-items: stretch; gap: 12px; }
        .cleanup-bar .btn-clean { width: 100%; justify-content: center; }
        .cleanup-notes { margin-left: 0; padding-left: 0; padding-top: 12px; border-left: 0; border-top: 1px solid rgba(255,255,255,.1); text-align: left; }
    }
    @media (max-width: 480px) {
        .trip-stats { gap: 8px; }
        .trip-stat { padding: 10px 12px; }
        .trip-stat strong { font-size: 22px; }
        .data-stat strong { font-size: 22px; }
        .profile-avatar { width: 52px; height: 52px; flex-basis: 52px; font-size: 18px; }
        .profile-head .status-pill { margin-left: 68px; }
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
                <div class="welcome"><h2>Settings</h2><p>Profile &amp; application preferences</p></div>
            </header>

            <div class="settings-page">
            <section class="panel">
                <h3><i class="fas fa-user-gear"></i> My Profile</h3>
                @if ($profile)
                @if ($profileOk)<div class="settings-alert is-success"><i class="fas fa-check-circle"></i><span>{{ $profileOk }}</span></div>@endif
                @if ($profileErrors)<div class="settings-alert is-error"><i class="fas fa-circle-exclamation"></i><span>Please fix the form errors.</span></div>@endif

                <div class="profile-head">
                    <div class="profile-avatar">{{ $initials ?: 'U' }}</div>
                    <div class="profile-identity">
                        <div class="profile-name">{{ $profile['full_name'] }}</div>
                        <div class="profile-since"><i class="fas fa-calendar"></i> Member since {{ date('M d, Y', strtotime($profile['created_at'])) }}</div>
                    </div>
                    @if ((int)$profile['is_active'] === 1)
                        <span class="status-pill is-active">Active</span>
                    @else
                        <span class="status-pill is-disabled">Disabled</span>
                    @endif
                </div>

                <form method="post" action="" class="profile-form" id="profileForm">
                    @csrf
                    <input type="hidden" name="action" value="update_profile">

                    <div>
                        <h4 class="form-section-title">Account details</h4>
                        <div class="form-grid">
                            <div class="field">
                                <label for="full_name">Full Name</label>
                                <div class="input-container">
                                    <input type="text" id="full_name" name="full_name" value="{{ $profile['full_name'] }}" required>
                                    <div class="input-focus-line"></div>
                                </div>
                                @if (isset($profileErrors['full_name']))<div class="field-error">{{ $profileErrors['full_name'] }}</div>@endif
                            </div>
                            <div class="field">
                                <label for="username">Username</label>
                                <div class="input-container">
                                    <input type="text" id="username" name="username" value="{{ $profile['username'] }}" required>
                                    <div class="input-focus-line"></div>
                                </div>
                                @if (isset($profileErrors['username']))<div class="field-error">{{ $profileErrors['username'] }}</div>@endif
                            </div>
                            <div class="field">
                                <label for="email">Email</label>
                                <div class="input-container">
                                    <input type="email" id="email" name="email" value="{{ $profile['email'] }}" required>
                                    <div class="input-focus-line"></div>
                                </div>
                                @if (isset($profileErrors['email']))<div class="field-error">{{ $profileErrors['email'] }}</div>@endif
                            </div>
                            <div class="field">
                                <label for="phone">Phone</label>
                                <div class="input-container">
                                    <input type="text" id="phone" name="phone" value="{{ $profile['phone'] ?? '' }}">
                                    <div class="input-focus-line"></div>
                                </div>
                                @if (isset($profileErrors['phone']))<div class="field-error">{{ $profileErrors['phone'] }}</div>@endif
                            </div>
                            <div class="field">
                                <label for="user_type">User Type</label>
                                <div class="input-container">
                                    <select id="user_type" name="user_type">
                                        <option value="Student" {!! ($profile['user_type']==='Student')?'selected':'' !!}>Student</option>
                                        <option value="Regular" {!! ($profile['user_type']==='Regular')?'selected':'' !!}>Regular</option>
                                        <option value="Senior Citizen/PWD" {!! ($profile['user_type']==='Senior Citizen/PWD')?'selected':'' !!}>Senior Citizen/PWD</option>
                                    </select>
                                    <i class="fas fa-chevron-down select-caret"></i>
                                    <div class="input-focus-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="form-section-title">Change password</h4>
                        <div class="form-grid">
                            <div class="field">
                                <label for="password">New Password <span class="optional">(optional)</span></label>
                                <div class="input-container">
                                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current">
                                    <button type="button" class="password-toggle" id="passwordToggle" aria-label="Show password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="input-focus-line"></div>
                                </div>
                                @if (isset($profileErrors['password']))<div class="field-error">{{ $profileErrors['password'] }}</div>@endif
                            </div>
                            <div class="field">
                                <label for="confirm_password">Confirm Password</label>
                                <div class="input-container">
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password">
                                    <button type="button" class="password-toggle" id="confirmPasswordToggle" aria-label="Show password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div class="input-focus-line"></div>
                                </div>
                                @if (isset($profileErrors['confirm_password']))<div class="field-error">{{ $profileErrors['confirm_password'] }}</div>@endif
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
                    </div>
                </form>

                @else
                <div class="settings-alert is-info"><i class="fas fa-circle-info"></i><span>Profile not found.</span></div>
                @endif
            </section>

            <!-- System Maintenance Section -->
            <section class="panel">
                <h3><i class="fas fa-screwdriver-wrench"></i> System Maintenance</h3>

                @if ($cleanupResult)
                    <div class="settings-alert {{ $cleanupResult['success'] ? 'is-success' : 'is-error' }}">
                        <i class="fas fa-{{ $cleanupResult['success'] ? 'check-circle' : 'circle-exclamation' }}"></i>
                        <span>{{ $cleanupResult['message'] }}</span>
                    </div>
                @endif

                <div class="maint-block">
                    <h4 class="maint-label">Active Trips</h4>
                    <div class="trip-stats">
                        <div class="trip-stat" style="--c:#3b82f6"><span>Scheduled</span><strong>{{ $cleanupStats['scheduled_trips'] }}</strong></div>
                        <div class="trip-stat" style="--c:#eab308"><span>In Transit</span><strong>{{ $cleanupStats['departed_trips'] }}</strong></div>
                        <div class="trip-stat" style="--c:#22c55e"><span>Completed</span><strong>{{ $cleanupStats['completed_trips'] }}</strong></div>
                    </div>
                </div>

                <div class="maint-block">
                    <h4 class="maint-label">Data Management</h4>
                    <div class="data-stats">
                        <div class="data-stat" style="--c:#a855f7"><div class="data-stat-title">Completed Trips</div><strong>{{ $cleanupStats['completed_awaiting_cleanup'] }}</strong><small>Auto-cleaned in 24h</small></div>
                        <div class="data-stat" style="--c:#a855f7"><div class="data-stat-title">Completed Bookings</div><strong>{{ $cleanupStats['completed_bookings'] }}</strong><small>Auto-cleaned in 24h</small></div>
                        <div class="data-stat" style="--c:#ef4444"><div class="data-stat-title">Cancelled Bookings</div><strong>{{ $cleanupStats['cancelled_bookings'] }}</strong><small>Auto-cleaned</small></div>
                        <div class="data-stat" style="--c:#8b5cf6"><div class="data-stat-title">Archived Records</div><strong>{{ $cleanupStats['archived_bookings'] }}</strong><small>Historical data</small></div>
                    </div>
                </div>

                <div class="maint-block">
                    <div class="cleanup-bar">
                        <button type="button" class="btn btn-clean" onclick="showCleanupConfirmation()">
                            <i class="fas fa-trash-alt"></i> Clean Data
                        </button>
                        <div class="cleanup-last">Last run<strong>{{ $cleanupStats['last_maintenance_run'] }}</strong></div>
                        <div class="cleanup-notes">
                            <div>Auto-runs every 24 hours</div>
                            <div><i class="fas fa-check"></i>Only removes old completed trips</div>
                        </div>
                    </div>
                </div>

                <!-- Hidden form for cleanup submission -->
                <form id="cleanupForm" method="post" action="" style="display: none;">
                    @csrf
                    <input type="hidden" name="action" value="cleanup">
                </form>
            </section>
            </div>
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

        // Cleanup confirmation functions
        function showCleanupConfirmation() {
            const modal = document.getElementById('cleanupConfirmModal');
            if (window.animateModalOpen) {
                animateModalOpen('#cleanupConfirmModal');
            } else {
                modal.style.display = 'block';
            }
        }

        function hideCleanupConfirmation() {
            const modal = document.getElementById('cleanupConfirmModal');
            if (window.animateModalClose) {
                animateModalClose('#cleanupConfirmModal');
            } else {
                modal.style.display = 'none';
            }
        }

        function proceedWithCleanup() {
            hideCleanupConfirmation();
            document.getElementById('cleanupForm').submit();
        }

        // Close cleanup modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('cleanupConfirmModal');
            if (modal && event.target === modal) {
                hideCleanupConfirmation();
            }
        });
    </script>

    <!-- Cleanup Confirmation Modal -->
    <div id="cleanupConfirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon" style="background: rgba(239,68,68,0.2); color: #ef4444;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="confirm-modal-title">Confirm Data Cleanup</h3>
            </div>
            <div class="confirm-modal-body">
                <p style="font-weight: 500; color: #e5e7eb; margin-bottom: 12px;">This action will permanently delete old data.</p>
                <div style="background: rgba(239,68,68,0.1); border-left: 3px solid #ef4444; padding: 12px 14px; border-radius: 4px; font-size: 13px; color: #b8c7db; line-height: 1.6;">
                    <strong style="color: #e5e7eb; display: block; margin-bottom: 6px;">What will be deleted:</strong>
                    <div style="margin-bottom: 6px;">✓ Completed trips older than 24 hours</div>
                    <div style="margin-bottom: 6px;">✓ Completed bookings associated with those trips</div>
                    <div>✓ All cancelled bookings</div>
                    <div style="margin-top: 10px; color: #9ca3af;">All data will be archived first for record-keeping.</div>
                </div>
            </div>
            <div class="confirm-modal-footer">
                <button type="button" class="confirm-modal-btn confirm-modal-btn-secondary" onclick="hideCleanupConfirmation()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="confirm-modal-btn confirm-modal-btn-primary" onclick="proceedWithCleanup()" style="background: linear-gradient(135deg, #ef4444, #dc2626); border-color: rgba(239,68,68,0.6);">
                    <i class="fas fa-trash-alt"></i> Delete Old Data
                </button>
            </div>
        </div>
    </div>
</body>
</html>


