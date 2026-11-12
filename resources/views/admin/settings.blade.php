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
    
    /* Comprehensive Responsive Design */
    @media (max-width: 992px) {
        .table {
            font-size: 13px;
        }
        
        .table th,
        .table td {
            padding: 8px;
        }
        
        .status-badge {
            font-size: 11px;
            padding: 3px 8px;
        }
    }
    
    @media (max-width: 768px) {
        .table {
            font-size: 12px;
        }
        
        .table th,
        .table td {
            padding: 6px;
        }
        
        .panel h3 {
            font-size: 1rem;
        }
        
        .status-badge {
            font-size: 10px;
            padding: 2px 6px;
        }
        
        .btn {
            padding: 8px 12px;
            font-size: 13px;
        }
        
        .input-container {
            font-size: 14px;
        }
        
        .input-container input,
        .input-container select {
            padding: 10px 12px;
        }
    }
    
    @media (max-width: 576px) {
        .table {
            font-size: 11px;
        }
        
        .table th,
        .table td {
            padding: 4px;
        }
        
        .panel {
            padding: 0.75rem;
        }
        
        .panel h3 {
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }
        
        .status-badge {
            font-size: 9px;
            padding: 1px 4px;
        }
        
        .btn {
            padding: 6px 10px;
            font-size: 12px;
        }
        
        .input-container {
            font-size: 13px;
        }
        
        .input-container input,
        .input-container select {
            padding: 8px 10px;
        }
    }
    
    @media (max-width: 480px) {
        .table {
            font-size: 10px;
        }
        
        .table th,
        .table td {
            padding: 3px;
        }
        
        .panel {
            padding: 0.5rem;
        }
        
        .panel h3 {
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        
        .status-badge {
            font-size: 8px;
            padding: 1px 3px;
        }
        
        .btn {
            padding: 5px 8px;
            font-size: 11px;
        }
        
        .input-container {
            font-size: 12px;
        }
        
        .input-container input,
        .input-container select {
            padding: 6px 8px;
        }
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
                <div class="welcome"><h2>Settings</h2><p>Profile & Application preferences</p></div>
                <div class="actions"></div>
            </header>
            <section class="panel" style="margin-bottom:16px;">
                <h3>My Profile</h3>
                @if ($profile)
                @if ($profileOk)<div class="qa" style="justify-content:flex-start;color:#22c55e"><i class="fas fa-check-circle"></i><span>{{ $profileOk }}</span></div>@endif
                @if ($profileErrors)<div class="qa" style="justify-content:flex-start;color:#ef4444"><i class="fas fa-circle-exclamation"></i><span>Please fix the form errors.</span></div>@endif
                <div style="display:flex; gap:20px; align-items:center; margin-bottom:16px;">
                    <div style="width:64px; height:64px; border-radius:50%; background:#0ea5e9; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:20px;">
                        {{ $initials ?: 'U' }}
                    </div>
                    <div>
                        <div style="font-size:18px; font-weight:600; line-height:1.2;">
                            {{ $profile['full_name'] }}
                        </div>
                        <div style="margin-top:6px; display:inline-flex; align-items:center; gap:8px;">
                            <span style="background:#111827; color:#f9fafb; border-radius:999px; padding:4px 10px; font-size:12px; text-transform:uppercase; letter-spacing:.04em;">Member since {{ date('M d, Y', strtotime($profile['created_at'])) }}</span>
                        </div>
                    </div>
                </div>

                <form method="post" action="" class="profile-form" id="profileForm" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:16px;">
    @csrf
                    <input type="hidden" name="action" value="update_profile">

                    <div class="field" style="background:#111827; border-radius:12px; padding:16px;">
                        <label>Full Name</label>
                        <div class="input-container">
                            <input type="text" name="full_name" value="{{ $profile['full_name'] }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['full_name']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['full_name'] }}</div>@endif
                    </div>

                    <div class="field" style="background:#111827; border-radius:12px; padding:16px;">
                        <label>Username</label>
                        <div class="input-container">
                            <input type="text" name="username" value="{{ $profile['username'] }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['username']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['username'] }}</div>@endif
                    </div>

                    <div class="field" style="background:#111827; border-radius:12px; padding:16px;">
                        <label>Email</label>
                        <div class="input-container">
                            <input type="email" name="email" value="{{ $profile['email'] }}" required>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['email']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['email'] }}</div>@endif
                    </div>

                    <div class="field" style="background:#111827; border-radius:12px; padding:16px;">
                        <label>Phone</label>
                        <div class="input-container">
                            <input type="text" name="phone" value="{{ $profile['phone'] ?? '' }}">
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['phone']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['phone'] }}</div>@endif
                    </div>

                    <div class="field" style="background:#111827; border-radius:12px; padding:16px;">
                        <label>User Type</label>
                        <div class="input-container">
							<select name="user_type">
                                <option value="Student" {!! ($profile['user_type']==='Student')?'selected':'' !!}>Student</option>
                                <option value="Regular" {!! ($profile['user_type']==='Regular')?'selected':'' !!}>Regular</option>
                                <option value="Senior Citizen/PWD" {!! ($profile['user_type']==='Senior Citizen/PWD')?'selected':'' !!}>Senior Citizen/PWD</option>
                            </select>
							<i class="fas fa-chevron-down select-caret"></i>
                            <div class="input-focus-line"></div>
                        </div>
                    </div>

                    <div class="field" style="background:#111827; border-radius:12px; padding:16px;">
                        <label>New Password (optional)</label>
                        <div class="input-container">
                            <input type="password" id="password" name="password" placeholder="Leave blank to keep current">
                            <button type="button" class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="input-focus-line"></div>
                        </div>
                        @if (isset($profileErrors['password']))<div style="color:#ef4444; font-size:12px; margin-top:6px;">&nbsp;{{ $profileErrors['password'] }}</div>@endif
                    </div>

                    <div class="field" style="background:#111827; border-radius:12px; padding:16px;">
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

                    <div style="background:#111827; border-radius:12px; padding:16px;">
                        <label style="color:#9ca3af; font-size:12px; text-transform:uppercase; letter-spacing:.06em; display:block;">Status</label>
                        <div style="margin-top:6px; font-weight:500;">
                            @if ((int)$profile['is_active'] === 1)
                                <span style="background:#065f46; color:#ecfdf5; padding:4px 10px; border-radius:999px; font-size:12px;">Active</span>
                            @else
                                <span style="background:#7c2d12; color:#fff7ed; padding:4px 10px; border-radius:999px; font-size:12px;">Disabled</span>
                            @endif
                        </div>
                    </div>

                    <div style="background:transparent; padding:0; grid-column: 1 / -1; display:flex; justify-content:flex-end;">
                        <button type="submit" class="btn" style="display:inline-flex; align-items:center; gap:8px;"><i class="fas fa-save"></i> Save Changes</button>
                    </div>
                </form>

                @else
                <div class="qa" style="justify-content:flex-start"><i class="fas fa-circle-info"></i><span>Profile not found.</span></div>
                @endif
            </section>

            <!-- System Maintenance Section -->
            <section class="panel">
                <h3><i class="fas fa-cog text-blue-500"></i> System Maintenance</h3>
                
                @if ($cleanupResult)
                    <div class="qa" style="justify-content:flex-start; margin-bottom:16px; color:{!! $cleanupResult['success'] ? '#22c55e' : '#ef4444' !!}">
                        <i class="fas fa-{!! $cleanupResult['success'] ? 'check-circle' : 'circle-exclamation' !!}"></i>
                        <span>{{ $cleanupResult['message'] }}</span>
                    </div>
                @endif

                <!-- Active Trips Status -->
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Active Trips</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px;">
                        <div style="padding: 12px 14px; border-radius: 8px; border-left: 3px solid #3b82f6; background: rgba(59,130,246,0.08);">
                            <div style="font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Scheduled</div>
                            <div style="font-size: 24px; font-weight: 700; color: #3b82f6;">{!! $cleanupStats['scheduled_trips'] !!}</div>
                        </div>
                        <div style="padding: 12px 14px; border-radius: 8px; border-left: 3px solid #eab308; background: rgba(234,179,8,0.08);">
                            <div style="font-size: 11px; color: #9ca3af; margin-bottom: 6px;">In Transit</div>
                            <div style="font-size: 24px; font-weight: 700; color: #eab308;">{!! $cleanupStats['departed_trips'] !!}</div>
                        </div>
                        <div style="padding: 12px 14px; border-radius: 8px; border-left: 3px solid #22c55e; background: rgba(34,197,94,0.08);">
                            <div style="font-size: 11px; color: #9ca3af; margin-bottom: 6px;">Completed</div>
                            <div style="font-size: 24px; font-weight: 700; color: #22c55e;">{!! $cleanupStats['completed_trips'] !!}</div>
                        </div>
                    </div>
                </div>

                <!-- Data Cleanup Status -->
                <div style="margin-bottom: 24px; padding: 16px; background: linear-gradient(135deg, rgba(168,85,247,0.05), rgba(99,102,241,0.05)); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Data Management</div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px;">
                        <div>
                            <div style="font-size: 13px; color: #e5e7eb; font-weight: 600; margin-bottom: 4px;">Completed Trips</div>
                            <div style="font-size: 28px; font-weight: 700; color: #a855f7;">{!! $cleanupStats['completed_awaiting_cleanup'] !!}</div>
                            <div style="font-size: 11px; color: #d8b4fe; margin-top: 4px;">Auto-cleaned in 24h</div>
                        </div>
                        <div>
                            <div style="font-size: 13px; color: #e5e7eb; font-weight: 600; margin-bottom: 4px;">Completed Bookings</div>
                            <div style="font-size: 28px; font-weight: 700; color: #a855f7;">{!! $cleanupStats['completed_bookings'] !!}</div>
                            <div style="font-size: 11px; color: #d8b4fe; margin-top: 4px;">Auto-cleaned in 24h</div>
                        </div>
                        <div>
                            <div style="font-size: 13px; color: #e5e7eb; font-weight: 600; margin-bottom: 4px;">Cancelled Bookings</div>
                            <div style="font-size: 28px; font-weight: 700; color: #ef4444;">{!! $cleanupStats['cancelled_bookings'] !!}</div>
                            <div style="font-size: 11px; color: #fca5a5; margin-top: 4px;">Auto-cleaned</div>
                        </div>
                        <div>
                            <div style="font-size: 13px; color: #e5e7eb; font-weight: 600; margin-bottom: 4px;">Archived Records</div>
                            <div style="font-size: 28px; font-weight: 700; color: #8b5cf6;">{!! $cleanupStats['archived_bookings'] !!}</div>
                            <div style="font-size: 11px; color: #ddd6fe; margin-top: 4px;">Historical data</div>
                        </div>
                    </div>
                </div>

                <!-- Cleanup Action -->
                <div style="display: flex; gap: 16px; align-items: center; padding: 14px; background: rgba(168,85,247,0.05); border-radius: 8px; border: 1px solid rgba(168,85,247,0.2);">
                    <div style="display: flex; gap: 12px; align-items: center; flex: 1;">
                        <button type="button" class="btn" onclick="showCleanupConfirmation()" style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #a855f7, #9333ea); border: 1px solid rgba(168,85,247,0.6); padding: 10px 18px; font-size: 13px; font-weight: 500; white-space: nowrap; cursor: pointer;">
                            <i class="fas fa-trash-alt"></i> Clean Data
                        </button>
                        <div style="font-size: 12px; color: #b8c7db; line-height: 1.5;">
                            <div style="color: #9ca3af; margin-bottom: 2px;">Last run</div>
                            <div style="color: #d8b4fe; font-weight: 500;">{!! $cleanupStats['last_maintenance_run'] !!}</div>
                        </div>
                    </div>
                    <div style="height: 30px; width: 1px; background: rgba(255,255,255,0.1);"></div>
                    <div style="font-size: 12px; color: #b8c7db; text-align: right; line-height: 1.6;">
                        <div style="color: #9ca3af;">Auto-runs every 24 hours</div>
                        <div style="margin-top: 4px;">✓ Only removes old completed trips</div>
                    </div>
                </div>

                <!-- Hidden form for cleanup submission -->
                <form id="cleanupForm" method="post" action="" style="display: none;">
    @csrf
                    <input type="hidden" name="action" value="cleanup">
                </form>
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


