<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buses - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
    <style>
    /* Bus Modal Styles - Matching Routes/Users Modal Design */
    .bus-modal-enhanced {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #1a222c;
        border-radius: 12px;
        padding: 24px;
        max-width: 600px;
        width: 90%;
        z-index: 1001;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        max-height: 90vh;
        overflow-y: auto;
        animation: modalSlideInScale 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
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
    
    .bus-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .modal-icon-wrapper {
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
    
    .bus-modal-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #e5e7eb;
        margin: 0;
    }
    
    .modal-subtitle {
        font-size: 13px;
        color: #9ca3af;
        margin: 4px 0 0 0;
    }
    
    .bus-modal-header .close {
        margin-left: auto;
        background: none;
        border: none;
        color: #9ca3af;
        font-size: 22px;
        cursor: pointer;
        padding: 4px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .bus-modal-header .close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
    }
    
    .bus-modal-body {
        color: #9ca3af;
        margin-bottom: 20px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .form-group-enhanced {
        margin-bottom: 0;
    }
    
    .form-group-enhanced label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-weight: 500;
        color: #e5e7eb;
        font-size: 14px;
    }
    
    .form-group-enhanced label i {
        color: #3b82f6;
        font-size: 14px;
    }
    
    .input-wrapper {
        position: relative;
    }
    
    .form-group-enhanced input,
    .form-group-enhanced select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.05);
        color: #e5e7eb;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .form-group-enhanced input:focus,
    .form-group-enhanced select:focus {
        outline: none;
        border-color: #3b82f6;
        background: rgba(255, 255, 255, 0.08);
    }
    
    .form-group-enhanced input::placeholder {
        color: #6b7280;
    }
    
    .form-group-enhanced .error {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 12px;
        color: #ef4444;
    }
    
    .form-group-enhanced .error i {
        font-size: 12px;
    }
    
    .bus-modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .bus-modal-footer .btn {
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
    
    .bus-modal-footer .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: 1px solid rgba(59, 130, 246, 0.6);
    }
    
    .bus-modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .bus-modal-footer .btn-outline {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .bus-modal-footer .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .bus-modal-enhanced {
            max-width: 95%;
            padding: 20px;
        }
    }
    
    /* Scoped booking form styles (match Trips/Add Booking) */
    .booking-form { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 12px; }
    @media (max-width: 900px){ .booking-form { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 600px){ .booking-form { grid-template-columns: 1fr; } }
    
    /* Status Badge Styles - matching {{ route('admin.users') }} styling */
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        display: inline-block;
    }
    
    .status-badge.active {
        background: #28a745;
        color: white;
    }
    
    .status-badge.maintenance {
        background: #ffc107;
        color: #000;
    }
    
    .status-badge.inactive {
        background: #6c757d;
        color: white;
    }
    
    /* Comprehensive Responsive Design */
    @media (max-width: 992px) {
        .booking-form {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .field {
            grid-column: 1 / -1;
        }
        
        .table {
            font-size: 13px;
        }
        
        .table th,
        .table td {
            padding: 8px;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    }
    
    @media (max-width: 768px) {
        .booking-form {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .field {
            grid-column: 1 / -1;
        }
        
        .input-container {
            font-size: 14px;
        }
        
        .input-container input,
        .input-container select {
            padding: 10px 12px;
        }
        
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
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .btn {
            padding: 8px 12px;
            font-size: 13px;
        }
    }
    
    @media (max-width: 576px) {
        .booking-form {
            gap: 0.5rem;
        }
        
        .input-container {
            font-size: 13px;
        }
        
        .input-container input,
        .input-container select {
            padding: 8px 10px;
        }
        
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
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .btn {
            padding: 6px 10px;
            font-size: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .booking-form {
            gap: 0.4rem;
        }
        
        .input-container {
            font-size: 12px;
        }
        
        .input-container input,
        .input-container select {
            padding: 6px 8px;
        }
        
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
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .btn {
            padding: 5px 8px;
            font-size: 11px;
        }
    }
    
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-size: 13px; color: #b8c7db; }
    .input-container { position: relative; display: flex; align-items: center; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 12px 14px; transition: border-color .2s ease, box-shadow .2s ease, background .2s ease; }
    .input-container:focus-within { border-color: rgba(59,130,246,0.6); box-shadow: 0 0 0 4px rgba(59,130,246,0.15); background: rgba(255,255,255,0.06); }
    .input-icon { color: #9ca3af; margin-right: 10px; font-size: 14px; }
    .input-container input, .input-container select { flex: 1; background: transparent; border: none; outline: none; color: #e5e7eb; font-size: 14px; }
    .input-focus-line { position: absolute; left: 14px; right: 14px; bottom: 6px; height: 2px; background: linear-gradient(90deg, transparent, rgba(59,130,246,0.7), transparent); opacity: 0; transform: scaleX(0.6); transition: transform .2s ease, opacity .2s ease; }
    .input-container:focus-within .input-focus-line { opacity: 1; transform: scaleX(1); }
    .booking-actions { display: flex; align-items: end; justify-content: flex-end; }
    
    /* Override browser autofill styles to maintain design */
    .input-container input:-webkit-autofill,
    .input-container input:-webkit-autofill:hover,
    .input-container input:-webkit-autofill:focus,
    .input-container input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px rgba(255,255,255,0.04) inset !important;
        -webkit-text-fill-color: #e5e7eb !important;
        background-color: transparent !important;
        background-clip: content-box !important;
        border: none !important;
        box-shadow: 0 0 0 30px rgba(255,255,255,0.04) inset !important;
        transition: background-color 5000s ease-in-out 0s, color 5000s ease-in-out 0s !important;
    }
    .input-container input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 30px rgba(255,255,255,0.06) inset !important;
        box-shadow: 0 0 0 30px rgba(255,255,255,0.06) inset !important;
    }
    /* Firefox autofill override */
    .input-container input:-moz-autofill {
        background-color: transparent !important;
        color: #e5e7eb !important;
        border: none !important;
        box-shadow: none !important;
    }
    /* Ensure autofill doesn't break container design */
    .input-container:has(input:-webkit-autofill) {
        border-color: rgba(255,255,255,0.08) !important;
        background: rgba(255,255,255,0.04) !important;
        box-shadow: none !important;
    }
    .input-container:has(input:-webkit-autofill):focus-within {
        border-color: rgba(59,130,246,0.6) !important;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.15) !important;
        background: rgba(255,255,255,0.06) !important;
    }
    /* Prevent any browser default styling on autofilled inputs */
    .input-container input:-webkit-autofill {
        caret-color: #e5e7eb !important;
    }
    /* Remove any outline or border that browsers might add */
    .input-container input:-webkit-autofill,
    .input-container input:-webkit-autofill:hover,
    .input-container input:-webkit-autofill:focus,
    .input-container input:-webkit-autofill:active {
        outline: none !important;
        outline-offset: 0 !important;
        border: none !important;
        -webkit-appearance: none !important;
        appearance: none !important;
    }
    /* Prevent browser validation from showing red borders on autofill */
    .input-container input:invalid:not(:focus):not(:placeholder-shown) {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
    .input-container input:valid:not(:focus) {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
    /* Override browser default validation styling */
    .input-container input:required {
        box-shadow: none !important;
    }
    .input-container input:required:invalid {
        box-shadow: none !important;
    }
    /* Ensure autofilled inputs maintain container styling */
    .input-container:has(input:-webkit-autofill:invalid) {
        border-color: rgba(255,255,255,0.08) !important;
        background: rgba(255,255,255,0.04) !important;
    }
    .input-container:has(input:-webkit-autofill:valid) {
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
    
    /* Delete confirmation modal styles - matching booking modals */
    .delete-modal {
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
    
    .delete-modal-content {
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
    
    .delete-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .delete-modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        font-size: 18px;
    }
    
    .delete-modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #e5e7eb;
        margin: 0;
    }
    
    .delete-modal-body {
        color: #9ca3af;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .delete-modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    
    .delete-modal-btn {
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
    
    .delete-modal-btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .delete-modal-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    .delete-modal-btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border: 1px solid rgba(239, 68, 68, 0.6);
    }
    
    .delete-modal-btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .create-booking-btn {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: 1px solid rgba(59, 130, 246, 0.6);
        border-radius: 6px;
        cursor: pointer;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .create-booking-btn:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
                <div class="welcome"><h2>Buses</h2><p>Manage fleet and capacities</p></div>
                <div class="actions"></div>
            </header>
            @if ($ok)<div class="qa" style="justify-content:flex-start;color:#22c55e;margin:1rem 0"><i class="fas fa-check-circle"></i><span>{{ $ok }}</span></div>@endif
            @if ($formErrors)<div class="qa" style="justify-content:flex-start;color:#ef4444;margin:1rem 0"><i class="fas fa-circle-exclamation"></i><span>{{ $formErrors['general'] ?? 'Please fix the form errors.' }}</span></div>@endif
            <section class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0;">Fleet Overview</h3>
                    <button type="button" class="create-booking-btn" onclick="openCreateBus()"><i class="fas fa-plus"></i> Add Bus</button>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plate</th>
                                <th>Model</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$buses)
                            <tr><td colspan="7" style="text-align:center">No buses yet.</td></tr>
                            @else
                            @foreach ($buses as $b)
                            <tr>
                                <td>{!! (int)$b['id'] !!}</td>
                                <td>{{ $b['plate_number'] }}</td>
                                <td>{{ $b['model'] ?? '' }}</td>
                                <td>{!! (int)$b['capacity'] !!}</td>
                                <td><span class="status-badge {{ $b['status'] }}">{{ $b['status'] }}</span></td>
                                <td>{{ $b['created_at'] }}</td>
                                <td class="actions">
                                    <button class="btn btn-edit" title="Edit" onclick="editBus({!! (int)$b['id'] !!})"><i class="fas fa-pen"></i></button>
                                    <button class="btn btn-danger" title="Delete" onclick="deleteBus({!! (int)$b['id'] !!}, '{{ $b['plate_number'] }}')"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <!-- Create/Edit Bus Modal -->
    <div id="busModal" class="modal">
        <div class="bus-modal-enhanced">
            <div class="bus-modal-header">
                <div class="modal-icon-wrapper">
                    <i class="fas fa-bus"></i>
                </div>
                <div style="flex: 1;">
                    <h3 id="busModalTitle">Add Bus</h3>
                    <p class="modal-subtitle">Create a new bus</p>
                </div>
                <button class="close" onclick="closeBusModal()" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="busForm" method="post" action="">
    @csrf
                <input type="hidden" name="action" id="busFormAction" value="create">
                <input type="hidden" name="bus_id" id="bus_id" value="">
                <div class="bus-modal-body">
                    <div class="form-row">
                        <div class="form-group form-group-enhanced">
                            <label for="bus_plate_number">
                                <i class="fas fa-id-card"></i>
                                <span>Plate Number *</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" name="plate_number" id="bus_plate_number" placeholder="Enter plate number" required>
                            </div>
                            @if (isset($formErrors['plate_number']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['plate_number'] }}</span>@endif
                        </div>
                        <div class="form-group form-group-enhanced">
                            <label for="bus_model">
                                <i class="fas fa-bus-simple"></i>
                                <span>Model</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" name="model" id="bus_model" placeholder="Enter bus model">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-group-enhanced">
                            <label for="bus_capacity">
                                <i class="fas fa-chair"></i>
                                <span>Capacity *</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="number" name="capacity" id="bus_capacity" placeholder="Enter capacity" min="1" required>
                            </div>
                            @if (isset($formErrors['capacity']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['capacity'] }}</span>@endif
                        </div>
                        <div class="form-group form-group-enhanced" id="busStatusRow" style="display:none">
                            <label for="bus_status">
                                <i class="fas fa-toggle-on"></i>
                                <span>Status</span>
                            </label>
                            <div class="input-wrapper">
                                <select name="status" id="bus_status">
                                    <option value="active">Active</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bus-modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeBusModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="busSubmitBtn">
                        <i class="fas fa-check"></i> <span>Add Bus</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add/Edit Confirmation Modal -->
    <div id="confirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="confirm-modal-title" id="confirmModalTitle">Confirm Action</h3>
            </div>
            <div class="confirm-modal-body">
                <p id="confirmModalMessage">Are you sure you want to proceed with this action?</p>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="delete-modal">
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <div class="delete-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="delete-modal-title">Delete Bus</h3>
            </div>
            <div class="delete-modal-body">
                <p>Are you sure you want to delete this bus?</p>
                <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <div><strong>Plate Number:</strong> <span id="deleteBusPlate"></span></div>
                </div>
                <p style="margin-top: 12px; color: #ef4444; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="delete-modal-footer">
                <button type="button" class="delete-modal-btn delete-modal-btn-secondary" onclick="hideDeleteConfirmModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <form method="post" style="display: inline;">
    @csrf
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="bus_id" id="deleteBusId">
                    <button type="submit" class="delete-modal-btn delete-modal-btn-danger">
                        <i class="fas fa-trash"></i> Delete Bus
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
    <script>
    // ============ VALIDATION FUNCTIONS ============
    function validateAddBusForm() {
        const errors = {};
        
        const plate = document.getElementById('bus_plate_number').value.trim();
        const capacity = document.getElementById('bus_capacity').value.trim();
        
        // Plate number validation
        if (plate === '') {
            errors['bus_plate_number'] = 'Plate number is required.';
        } else if (plate.length < 3) {
            errors['bus_plate_number'] = 'Plate number must be at least 3 characters.';
        }
        
        // Capacity validation
        if (capacity === '') {
            errors['bus_capacity'] = 'Capacity is required.';
        } else if (!Number.isInteger(Number(capacity)) || Number(capacity) < 1) {
            errors['bus_capacity'] = 'Capacity must be a positive integer.';
        }
        
        return errors;
    }
    
    function validateEditBusForm() {
        return validateAddBusForm(); // Same validation rules for create and edit
    }
    
    function updateBusSubmitButtonState() {
        const validationErrors = validateAddBusForm();
        const submitBtn = document.getElementById('busSubmitBtn');
        
        if (Object.keys(validationErrors).length > 0) {
            // Disable button if there are errors
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            // Enable button if all validations pass
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    }
    
    function displayBusValidationErrors(errors) {
        // Clear previous errors first
        clearBusValidationErrors();
        
        for (let fieldId in errors) {
            const field = document.getElementById(fieldId);
            if (field) {
                const errorElement = document.createElement('span');
                errorElement.className = 'error bus-error-' + fieldId;
                errorElement.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errors[fieldId];
                
                // Insert after the input wrapper
                const wrapper = field.closest('.input-wrapper');
                if (wrapper && wrapper.nextElementSibling) {
                    wrapper.parentNode.insertBefore(errorElement, wrapper.nextElementSibling);
                } else if (field.parentNode) {
                    field.parentNode.appendChild(errorElement);
                }
                
                // Highlight the field
                field.style.borderColor = '#ef4444';
                field.style.background = 'rgba(239, 68, 68, 0.05)';
            }
        }
    }
    
    function clearBusValidationErrors() {
        // Remove all error messages
        document.querySelectorAll('.bus-error-bus_plate_number, .bus-error-bus_capacity').forEach(el => el.remove());
        
        // Reset field styling
        const fields = ['bus_plate_number', 'bus_capacity'];
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                field.style.background = 'rgba(255, 255, 255, 0.05)';
            }
        });
    }
    
    function hasEditBusChanges() {
        if (!window.editBusOriginalData) return true;
        
        const currentData = {
            plate_number: document.getElementById('bus_plate_number').value.trim(),
            model: document.getElementById('bus_model').value.trim(),
            capacity: document.getElementById('bus_capacity').value.trim(),
            status: document.getElementById('bus_status').value
        };
        
        // Compare with original data
        for (let key in currentData) {
            if (currentData[key] !== window.editBusOriginalData[key]) {
                return true;
            }
        }
        
        return false;
    }
    
    function showNoChangesModal() {
        document.getElementById('confirmModalTitle').textContent = 'No Changes Made';
        document.getElementById('confirmModalMessage').textContent = 'No changes detected. You can still proceed with the update if needed.';
        
        const detailsDiv = document.getElementById('confirmModalDetails');
        detailsDiv.innerHTML = '';
        
        // Show the confirm button so user can proceed
        document.getElementById('confirmSubmitBtn').style.display = 'inline-flex';
        
        // Update the icon to info
        const iconDiv = document.querySelector('.confirm-modal-icon');
        if (iconDiv) {
            iconDiv.innerHTML = '<i class="fas fa-info-circle"></i>';
            iconDiv.style.background = 'rgba(34, 197, 94, 0.1)';
            iconDiv.style.color = '#22c55e';
        }
        
        if (window.animateModalOpen) {
            animateModalOpen('#confirmModal');
        } else {
            document.getElementById('confirmModal').style.display = 'block';
        }
    }
    
    // Handle Add Bus Form Submission (main form at top)
    document.getElementById('addBusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const plate = document.getElementById('plate_number').value.trim();
        const model = document.getElementById('model').value.trim();
        const capacity = document.getElementById('capacity').value.trim();
        
        if (!plate || !capacity) {
            alert('Please fill in all required fields');
            return;
        }
        
        let details = '<div style="display: flex; flex-direction: column; gap: 8px;">';
        details += '<div><strong>Plate Number:</strong> ' + plate + '</div>';
        details += '<div><strong>Model:</strong> ' + (model || 'N/A') + '</div>';
        details += '<div><strong>Capacity:</strong> ' + capacity + ' seats</div>';
        details += '</div>';
        
        showMainFormConfirmModal('Add Bus', 'Are you sure you want to add this bus?', details);
        
        // Store form reference for submission after confirmation
        window.pendingMainForm = this;
    });
    
    // Confirmation modal for main form
    function showMainFormConfirmModal(title, message, details) {
        document.getElementById('confirmModalTitle').textContent = title;
        document.getElementById('confirmModalMessage').textContent = message;
        document.getElementById('confirmModalDetails').innerHTML = details;
        
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
    
    // Handle confirmation
    document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
        if (window.pendingMainForm) {
            hideConfirmModal();
            window.pendingMainForm.submit();
            window.pendingMainForm = null;
        } else if (window.pendingBusForm) {
            hideConfirmModal();
            closeBusModal();
            window.pendingBusForm.submit();
            window.pendingBusForm = null;
        }
    });
    </script>
    <script>
    // ============ MODAL FUNCTIONS ============
    function openCreateBus() {
        document.getElementById('busModalTitle').textContent = 'Add Bus';
        const subtitle = document.querySelector('.bus-modal-header .modal-subtitle');
        if (subtitle) subtitle.textContent = 'Create a new bus';
        document.getElementById('busFormAction').value = 'create';
        document.getElementById('bus_id').value = '';
        document.getElementById('busForm').reset();
        document.getElementById('busStatusRow').style.display = 'none';
        clearBusValidationErrors();
        const submitBtn = document.getElementById('busSubmitBtn');
        if (!submitBtn.querySelector('span')) {
            submitBtn.innerHTML = '<i class="fas fa-check"></i> <span>Add Bus</span>';
        } else {
            submitBtn.querySelector('span').textContent = 'Add Bus';
        }
        // Initialize button state as disabled since form is empty
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
        if (window.animateModalOpen) {
            animateModalOpen('#busModal');
        } else {
            document.getElementById('busModal').style.display = 'flex';
        }
    }
    
    function editBus(id){
        fetch('?action=get_bus&id=' + id)
            .then(r => r.json())
            .then(bus => {
                document.getElementById('busModalTitle').textContent = 'Edit Bus';
                const subtitle = document.querySelector('.bus-modal-header .modal-subtitle');
                if (subtitle) subtitle.textContent = 'Update bus information';
                document.getElementById('busFormAction').value = 'update';
                document.getElementById('bus_id').value = bus.id;
                document.getElementById('bus_plate_number').value = bus.plate_number || '';
                document.getElementById('bus_model').value = bus.model || '';
                document.getElementById('bus_capacity').value = bus.capacity || '';
                document.getElementById('bus_status').value = bus.status || 'active';
                
                // Store original data for change detection
                window.editBusOriginalData = {
                    plate_number: bus.plate_number || '',
                    model: bus.model || '',
                    capacity: String(bus.capacity || ''),
                    status: bus.status || 'active'
                };
                
                document.getElementById('busStatusRow').style.display = 'block';
                clearBusValidationErrors();
                const submitBtn = document.getElementById('busSubmitBtn');
                if (!submitBtn.querySelector('span')) {
                    submitBtn.innerHTML = '<i class="fas fa-check"></i> <span>Update Bus</span>';
                } else {
                    submitBtn.querySelector('span').textContent = 'Update Bus';
                }
                // Enable button for edit mode since form is populated with valid data
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                if (window.animateModalOpen) {
                    animateModalOpen('#busModal');
                } else {
                    document.getElementById('busModal').style.display = 'flex';
                }
            })
            .catch(() => alert('Failed to load bus'));
    }
    function closeBusModal(){ 
        if (window.animateModalClose) {
            animateModalClose('#busModal');
        } else {
            document.getElementById('busModal').style.display = 'none'; 
        }
    }
    
    // Update modal display style
    const busModal = document.getElementById('busModal');
    if (busModal) {
        busModal.style.display = 'none';
        busModal.style.position = 'fixed';
        busModal.style.top = '0';
        busModal.style.left = '0';
        busModal.style.width = '100%';
        busModal.style.height = '100%';
        busModal.style.background = 'rgba(0, 0, 0, 0.7)';
        busModal.style.zIndex = '1000';
        busModal.style.backdropFilter = 'blur(4px)';
        busModal.style.alignItems = 'center';
        busModal.style.justifyContent = 'center';
    }
    function deleteBus(id, plate){
        document.getElementById('deleteBusId').value = id;
        document.getElementById('deleteBusPlate').textContent = plate;
        if (window.animateModalOpen) {
            animateModalOpen('#deleteConfirmModal');
        } else {
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }
    }
    function hideDeleteConfirmModal(){
        if (window.animateModalClose) {
            animateModalClose('#deleteConfirmModal');
        } else {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }
    }
    // Close modals when clicking outside
    
    // Confirmation modal functions
    function showConfirmModal(action, details) {
        const isEdit = action === 'update';
        document.getElementById('confirmModalTitle').textContent = isEdit ? 'Edit Bus' : 'Add Bus';
        document.getElementById('confirmModalMessage').textContent = isEdit 
            ? 'Are you sure you want to update this bus?' 
            : 'Are you sure you want to add this bus?';
        
        const detailsDiv = document.getElementById('confirmModalDetails');
        detailsDiv.innerHTML = '<div style="display: flex; flex-direction: column; gap: 8px;">' + details + '</div>';
        
        if (window.animateModalOpen) {
            animateModalOpen('#confirmModal');
        } else {
            document.getElementById('confirmModal').style.display = 'block';
        }
    }
        // Intercept form submission
    document.getElementById('busForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const action = document.getElementById('busFormAction').value;
        
        // Check for duplicate error element - if it exists, don't proceed
        const plateDuplicateError = document.getElementById('bus_duplicate_error_plate');
        if (plateDuplicateError) {
            return; // Don't proceed if duplicate exists
        }
        
        // Validate form
        const validationErrors = validateAddBusForm();
        
        if (Object.keys(validationErrors).length > 0) {
            // Display errors and prevent submission
            displayBusValidationErrors(validationErrors);
            return; // Don't proceed
        }
        
        // Clear any previous error messages
        clearBusValidationErrors();
        
        // For edit action, check if there are any changes and show informational message
        let showNoChangesMessage = false;
        if (action === 'update' && !hasEditBusChanges()) {
            showNoChangesMessage = true;
            // Show no changes message in confirmation modal
            document.getElementById('confirmModalTitle').textContent = 'No Changes Made';
            document.getElementById('confirmModalMessage').textContent = 'No changes detected. You can still proceed with the update if needed.';
            
            const detailsDiv = document.getElementById('confirmModalDetails');
            detailsDiv.innerHTML = '';
            
            // Show the confirm button so user can proceed
            document.getElementById('confirmSubmitBtn').style.display = 'inline-flex';
            
            // Update the icon to info
            const iconDiv = document.querySelector('.confirm-modal-icon');
            if (iconDiv) {
                iconDiv.innerHTML = '<i class="fas fa-info-circle"></i>';
                iconDiv.style.background = 'rgba(34, 197, 94, 0.1)';
                iconDiv.style.color = '#22c55e';
            }
        } else {
            // Normal confirmation modal
            const plate = document.getElementById('bus_plate_number').value;
            const model = document.getElementById('bus_model').value;
            const capacity = document.getElementById('bus_capacity').value;
            const status = document.getElementById('bus_status') ? document.getElementById('bus_status').value : 'active';
            
            let details = '<div style="display: flex; flex-direction: column; gap: 8px;">';
            details += '<div><strong>Plate Number:</strong> ' + plate + '</div>';
            details += '<div><strong>Model:</strong> ' + (model || 'N/A') + '</div>';
            details += '<div><strong>Capacity:</strong> ' + capacity + ' seats</div>';
            if (action === 'update') {
                details += '<div><strong>Status:</strong> ' + status + '</div>';
            }
            details += '</div>';
            
            // Reset confirm button visibility and styling
            document.getElementById('confirmSubmitBtn').style.display = 'inline-flex';
            const iconDiv = document.querySelector('.confirm-modal-icon');
            if (iconDiv) {
                iconDiv.innerHTML = '<i class="fas fa-question-circle"></i>';
                iconDiv.style.background = 'rgba(59, 130, 246, 0.1)';
                iconDiv.style.color = '#3b82f6';
            }
            
            showConfirmModal(action, details);
        }
        
        if (showNoChangesMessage) {
            if (window.animateModalOpen) {
                animateModalOpen('#confirmModal');
            } else {
                document.getElementById('confirmModal').style.display = 'block';
            }
        }
        
        // Store form reference for submission after confirmation
        window.pendingBusForm = this;
    });
    
    // Handle confirmation
    document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
        if (window.pendingBusForm) {
            hideConfirmModal();
            closeBusModal();
            window.pendingBusForm.submit();
            window.pendingBusForm = null;
        }
    });
    
    window.onclick = function(e){ 
        var busModal = document.getElementById('busModal');
        var deleteModal = document.getElementById('deleteConfirmModal');
        var confirmModal = document.getElementById('confirmModal');
        if (e.target === busModal) { 
            closeBusModal(); 
        }
        if (e.target === deleteModal) { 
            hideDeleteConfirmModal();
        }
        if (e.target === confirmModal) {
            hideConfirmModal();
        }
    }
    
    function checkDuplicateBus() {
        const plateNumber = document.getElementById('bus_plate_number').value.trim();
        const busId = document.getElementById('bus_id').value; // For edit mode
        
        // Only check if plate number is filled
        if (plateNumber === '') {
            return Promise.resolve(false);
        }
        
        const params = `plate_number=${encodeURIComponent(plateNumber)}`;
        const url = busId ? `{{ route('api.check-duplicate-bus') }}?${params}&bus_id=${busId}` : `{{ route('api.check-duplicate-bus') }}?${params}`;
        
        return fetch(url)
            .then(response => response.json())
            .then(data => {
                const plateErrorElement = document.getElementById('bus_duplicate_error_plate');
                const submitBtn = document.getElementById('busSubmitBtn');
                const plateField = document.getElementById('bus_plate_number');
                
                if (data.isDuplicate) {
                    // Show duplicate error below the plate number input
                    if (!plateErrorElement) {
                        const plateError = document.createElement('span');
                        plateError.id = 'bus_duplicate_error_plate';
                        plateError.className = 'error bus-error-duplicate';
                        plateError.style.cssText = 'display:flex;align-items:center;gap:6px;margin-top:6px;font-size:12px;color:#ef4444;';
                        plateError.innerHTML = '<i class="fas fa-exclamation-circle" style="font-size:12px;"></i> <span>Information already existed</span>';
                        
                        const plateWrapper = plateField.closest('.input-wrapper');
                        if (plateWrapper && plateWrapper.parentNode) {
                            plateWrapper.parentNode.insertBefore(plateError, plateWrapper.nextElementSibling);
                        }
                    }
                    
                    // Highlight the plate field
                    plateField.style.borderColor = '#ef4444';
                    plateField.style.background = 'rgba(239, 68, 68, 0.05)';
                    
                    // Make submit button completely non-clickable
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                    submitBtn.style.cursor = 'not-allowed';
                    submitBtn.style.pointerEvents = 'none';
                    submitBtn.style.background = 'rgba(59, 130, 246, 0.3)';
                    submitBtn.title = 'Information Already Existed - Cannot Add Duplicate Bus';
                    
                    return true; // Duplicate found
                } else {
                    // Remove duplicate error if it exists
                    if (plateErrorElement) {
                        plateErrorElement.remove();
                    }
                    
                    // Reset plate field styling
                    plateField.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    plateField.style.background = 'rgba(255, 255, 255, 0.05)';
                    
                    // Re-enable button if no other validation errors
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                    submitBtn.style.pointerEvents = 'auto';
                    submitBtn.style.background = '';
                    submitBtn.title = 'Add Bus';
                    
                    updateBusSubmitButtonState();
                    return false; // No duplicate
                }
            })
            .catch(error => {
                console.error('Error checking for duplicate bus:', error);
                return false; // If API fails, allow submission to proceed (server-side will catch it)
            });
    }
    
    // Set up real-time validation listeners
    const busFields = ['bus_plate_number', 'bus_capacity'];
    let duplicateCheckTimeout;
    
    busFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // On blur - validate
            field.addEventListener('blur', function() {
                const validationErrors = validateAddBusForm();
                if (validationErrors[fieldId]) {
                    const errorElement = document.querySelector('.bus-error-' + fieldId);
                    if (!errorElement) {
                        const error = document.createElement('span');
                        error.className = 'error bus-error-' + fieldId;
                        error.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + validationErrors[fieldId];
                        const wrapper = field.closest('.input-wrapper');
                        if (wrapper && wrapper.parentNode) {
                            wrapper.parentNode.insertBefore(error, wrapper.nextElementSibling);
                        }
                    }
                    field.style.borderColor = '#ef4444';
                    field.style.background = 'rgba(239, 68, 68, 0.05)';
                } else {
                    const errorElement = document.querySelector('.bus-error-' + fieldId);
                    if (errorElement) errorElement.remove();
                    field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    field.style.background = 'rgba(255, 255, 255, 0.05)';
                }
                updateBusSubmitButtonState();
                
                // Check for duplicate on blur for plate number
                if (fieldId === 'bus_plate_number') {
                    clearTimeout(duplicateCheckTimeout);
                    duplicateCheckTimeout = setTimeout(() => {
                        checkDuplicateBus();
                    }, 300);
                }
            });
            
            // On focus - clear error
            field.addEventListener('focus', function() {
                const errorElement = document.querySelector('.bus-error-' + fieldId);
                if (errorElement) errorElement.remove();
                field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                field.style.background = 'rgba(255, 255, 255, 0.05)';
            });
            
            // On input - validate
            field.addEventListener('input', function() {
                const validationErrors = validateAddBusForm();
                if (validationErrors[fieldId]) {
                    const errorElement = document.querySelector('.bus-error-' + fieldId);
                    if (!errorElement) {
                        const error = document.createElement('span');
                        error.className = 'error bus-error-' + fieldId;
                        error.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + validationErrors[fieldId];
                        const wrapper = field.closest('.input-wrapper');
                        if (wrapper && wrapper.parentNode) {
                            wrapper.parentNode.insertBefore(error, wrapper.nextElementSibling);
                        }
                    }
                    field.style.borderColor = '#ef4444';
                    field.style.background = 'rgba(239, 68, 68, 0.05)';
                } else {
                    const errorElement = document.querySelector('.bus-error-' + fieldId);
                    if (errorElement) errorElement.remove();
                    field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    field.style.background = 'rgba(255, 255, 255, 0.05)';
                }
                updateBusSubmitButtonState();
                
                // Check for duplicate on input for plate number with debouncing
                if (fieldId === 'bus_plate_number') {
                    clearTimeout(duplicateCheckTimeout);
                    duplicateCheckTimeout = setTimeout(() => {
                        checkDuplicateBus();
                    }, 500);
                }
            });
        }
    });
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


