<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Routes - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
    <style>
    /* Route Modal Styles - Matching Delete Modal Design */
    .route-modal-enhanced {
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
    
    .route-modal-header {
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
    
    .route-modal-header h3 {
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
    
    .route-modal-header .close {
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
    
    .route-modal-header .close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
    }
    
    .route-modal-body {
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
    
    .route-modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .route-modal-footer .btn {
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
    
    .route-modal-footer .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: 1px solid rgba(59, 130, 246, 0.6);
    }
    
    .route-modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    .route-modal-footer .btn-outline {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .route-modal-footer .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .route-modal-enhanced {
            max-width: 95%;
            padding: 20px;
        }
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
		opacity: 0;
		pointer-events: none;
		transition: opacity 0.2s ease-out;
	}
	
	.delete-modal.is-visible {
		opacity: 1;
		pointer-events: auto;
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
    
    /* Comprehensive Responsive Design */
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
    
    .status-badge.inactive {
        background: #6c757d;
        color: white;
    }
    
    .status-badge.status-active {
        background: #28a745;
        color: white;
    }
    
    .status-badge.status-inactive {
        background: #6c757d;
        color: white;
    }
    
    @media (max-width: 992px) {
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
        
        .delete-modal-content {
            max-width: 350px;
            padding: 20px;
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
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .btn {
            padding: 8px 12px;
            font-size: 13px;
        }
        
        .delete-modal-content {
            max-width: 320px;
            padding: 16px;
        }
        
        .delete-modal-title {
            font-size: 16px;
        }
        
        .delete-modal-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
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
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .btn {
            padding: 6px 10px;
            font-size: 12px;
        }
        
        .delete-modal-content {
            max-width: 280px;
            padding: 12px;
        }
        
        .delete-modal-title {
            font-size: 14px;
        }
        
        .delete-modal-icon {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
        
        .delete-modal-btn {
            padding: 6px 12px;
            font-size: 12px;
            min-width: 100px;
        }
        
        .delete-modal-footer {
            flex-direction: column;
            gap: 8px;
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
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .btn {
            padding: 5px 8px;
            font-size: 11px;
        }
        
        .delete-modal-content {
            max-width: 260px;
            padding: 10px;
        }
        
        .delete-modal-title {
            font-size: 13px;
        }
        
        .delete-modal-icon {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
        
        .delete-modal-btn {
            padding: 5px 10px;
            font-size: 11px;
            min-width: 80px;
        }
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
                <div class="welcome"><h2>Routes</h2><p>Define and manage routes</p></div>
                <div class="actions"></div>
            </header>
            @if ($ok)<div class="qa" style="justify-content:flex-start;color:#22c55e;margin:1rem 0"><i class="fas fa-check-circle"></i><span>{{ $ok }}</span></div>@endif
            @if ($formErrors)<div class="qa" style="justify-content:flex-start;color:#ef4444;margin:1rem 0"><i class="fas fa-circle-exclamation"></i><span>{{ $formErrors['general'] ?? 'Please fix the form errors.' }}</span></div>@endif
            <section class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0;">Routes</h3>
                    <button type="button" class="create-booking-btn" onclick="openCreateRoute()"><i class="fas fa-plus"></i> Add Route</button>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Distance (km)</th>
                                <th>Base Price</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$routes)
                            <tr><td colspan="8" style="text-align:center">No routes found.</td></tr>
                            @else
                            @foreach ($routes as $r)
                            <tr>
                                <td>{!! (int)$r['id'] !!}</td>
                                <td>{{ $r['origin'] }}</td>
                                <td>{{ $r['destination'] }}</td>
                                <td>{!! $r['distance_km'] !== null ? number_format((float)$r['distance_km'], 2) : '—' !!}</td>
                                <td>{!! number_format((float)$r['base_price'], 2) !!}</td>
                                <td>
                                    @php($isActive = ($r['status'] === 'active'))
                                    <span class="status-badge {!! $isActive ? 'status-active' : 'status-inactive' !!}">{{ $r['status'] }}</span>
                                </td>
                                <td>{{ $r['created_at'] }}</td>
                                <td class="actions">
                                    <button class="btn btn-edit" onclick="editRoute({!! (int)$r['id'] !!})"><i class="fas fa-pen"></i></button>
                                    <button class="btn btn-danger" title="Delete" onclick="deleteRoute({!! (int)$r['id'] !!}, '{{ $r['origin'] }}', '{{ $r['destination'] }}')"><i class="fas fa-trash"></i></button>
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
    
    <!-- Create/Edit Route Modal -->
    <div id="routeModal" class="modal">
        <div class="route-modal-enhanced">
            <div class="route-modal-header">
                <div class="modal-icon-wrapper">
                    <i class="fas fa-route"></i>
                </div>
                <div style="flex: 1;">
                    <h3 id="routeModalTitle">Add Route</h3>
                    <p class="modal-subtitle">Create a new bus route</p>
                </div>
                <button class="close" onclick="closeRouteModal()" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="routeForm" method="post" action="">
    @csrf
                <input type="hidden" name="action" id="routeFormAction" value="create">
                <input type="hidden" name="route_id" id="route_id" value="">
                <div class="route-modal-body">
                    <div class="form-row">
                        <div class="form-group form-group-enhanced">
                            <label for="origin">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Origin *</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" name="origin" id="origin" placeholder="Enter origin city" required>
                            </div>
                            @if (isset($formErrors['origin']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['origin'] }}</span>@endif
                        </div>
                        <div class="form-group form-group-enhanced">
                            <label for="destination">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Destination *</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text" name="destination" id="destination" placeholder="Enter destination city" required>
                            </div>
                            @if (isset($formErrors['destination']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['destination'] }}</span>@endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-group-enhanced">
                            <label for="distance_km">
                                <i class="fas fa-ruler-horizontal"></i>
                                <span>Distance (km)</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="number" step="0.01" name="distance_km" id="distance_km" placeholder="0.00">
                            </div>
                            @if (isset($formErrors['distance_km']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['distance_km'] }}</span>@endif
                        </div>
                        <div class="form-group form-group-enhanced">
                            <label for="base_price">
                                <i class="fas fa-peso-sign"></i>
                                <span>Base Price *</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="number" step="0.01" name="base_price" id="base_price" placeholder="0.00" required>
                            </div>
                            @if (isset($formErrors['base_price']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['base_price'] }}</span>@endif
                        </div>
                    </div>
                    <div class="form-group form-group-enhanced" id="statusRow" style="display:none">
                        <label for="status">
                            <i class="fas fa-toggle-on"></i>
                            <span>Status</span>
                        </label>
                        <div class="input-wrapper">
                            <select name="status" id="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer route-modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeRouteModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="routeSubmitBtn">
                        <i class="fas fa-check"></i> <span>Create Route</span>
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
                <div class="delete-modal-icon" id="deleteModalIcon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="delete-modal-title" id="deleteModalTitle">Delete Route</h3>
            </div>
            <div class="delete-modal-body">
                <p id="deleteModalMessage">Are you sure you want to delete this route?</p>
                <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;" id="deleteModalDetails">
                    <div><strong>Route:</strong> <span id="deleteRouteInfo"></span></div>
                </div>
                <p style="margin-top: 12px; color: #ef4444; font-size: 13px;" id="deleteModalWarning">
                    <i class="fas fa-info-circle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="delete-modal-footer">
                <button type="button" class="delete-modal-btn delete-modal-btn-secondary" onclick="hideDeleteConfirmModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <form method="post" style="display: inline;" id="deleteForm">
    @csrf
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="route_id" id="deleteRouteId">
                    <button type="submit" class="delete-modal-btn delete-modal-btn-danger" id="deleteSubmitBtn">
                        <i class="fas fa-trash"></i> Delete Route
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // ============ VALIDATION FUNCTIONS ============
    function validateAddRouteForm() {
        const errors = {};
        
        const origin = document.getElementById('origin').value.trim();
        const destination = document.getElementById('destination').value.trim();
        const distanceKm = document.getElementById('distance_km').value.trim();
        const basePrice = document.getElementById('base_price').value.trim();
        
        // Origin validation
        if (origin === '') {
            errors['origin'] = 'Origin is required.';
        } else if (origin.length < 2) {
            errors['origin'] = 'Origin must be at least 2 characters.';
        }
        
        // Destination validation
        if (destination === '') {
            errors['destination'] = 'Destination is required.';
        } else if (destination.length < 2) {
            errors['destination'] = 'Destination must be at least 2 characters.';
        }
        
        // Base price validation
        if (basePrice === '') {
            errors['base_price'] = 'Base price is required.';
        } else if (isNaN(basePrice) || parseFloat(basePrice) < 0) {
            errors['base_price'] = 'Base price must be a non-negative number.';
        }
        
        // Distance validation (optional, but must be valid if provided)
        if (distanceKm !== '' && (isNaN(distanceKm) || parseFloat(distanceKm) < 0)) {
            errors['distance_km'] = 'Distance must be a non-negative number.';
        }
        
        return errors;
    }
    
    function validateEditRouteForm() {
        return validateAddRouteForm(); // Same validation rules for create and edit
    }
    
    function updateRouteSubmitButtonState() {
        const validationErrors = validateAddRouteForm();
        const submitBtn = document.getElementById('routeSubmitBtn');
        
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
    
    function displayRouteValidationErrors(errors) {
        // Clear previous errors first
        clearRouteValidationErrors();
        
        for (let fieldId in errors) {
            const field = document.getElementById(fieldId);
            if (field) {
                const errorElement = document.createElement('span');
                errorElement.className = 'error route-error-' + fieldId;
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
    
    function clearRouteValidationErrors() {
        // Remove all error messages
        document.querySelectorAll('.route-error-origin, .route-error-destination, .route-error-distance_km, .route-error-base_price').forEach(el => el.remove());
        
        // Reset field styling
        const fields = ['origin', 'destination', 'distance_km', 'base_price'];
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                field.style.background = 'rgba(255, 255, 255, 0.05)';
            }
        });
    }
    
    function hasEditRouteChanges() {
        if (!window.editRouteOriginalData) return true;
        
        const currentData = {
            origin: document.getElementById('origin').value.trim(),
            destination: document.getElementById('destination').value.trim(),
            distance_km: document.getElementById('distance_km').value.trim(),
            base_price: document.getElementById('base_price').value.trim(),
            status: document.getElementById('status').value
        };
        
        // Compare with original data
        for (let key in currentData) {
            if (currentData[key] !== window.editRouteOriginalData[key]) {
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
        
        // Show the confirm button so user can still proceed
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
    
    function openCreateRoute(){
        document.getElementById('routeModalTitle').textContent = 'Add Route';
        const subtitle = document.querySelector('.route-modal-header .modal-subtitle');
        if (subtitle) subtitle.textContent = 'Create a new bus route';
        document.getElementById('routeFormAction').value = 'create';
        document.getElementById('route_id').value = '';
        document.getElementById('routeForm').reset();
        document.getElementById('statusRow').style.display = 'none';
        clearRouteValidationErrors();
        const submitBtn = document.getElementById('routeSubmitBtn');
        const submitBtnText = submitBtn.querySelector('span') || document.createElement('span');
        if (!submitBtn.querySelector('span')) {
            submitBtn.innerHTML = '<i class="fas fa-check"></i> <span>Create Route</span>';
        } else {
            submitBtn.querySelector('span').textContent = 'Create Route';
        }
        // Initialize button state as disabled since form is empty
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
        if (window.animateModalOpen) {
            animateModalOpen('#routeModal');
        } else {
            document.getElementById('routeModal').style.display = 'flex';
        }
    }
    function editRoute(id){
        fetch('?action=get_route&id=' + id)
            .then(r => r.json())
            .then(route => {
                document.getElementById('routeModalTitle').textContent = 'Edit Route';
                const subtitle = document.querySelector('.route-modal-header .modal-subtitle');
                if (subtitle) subtitle.textContent = 'Update route information';
                document.getElementById('routeFormAction').value = 'update';
                document.getElementById('route_id').value = route.id;
                document.getElementById('origin').value = route.origin;
                document.getElementById('destination').value = route.destination;
                document.getElementById('distance_km').value = route.distance_km !== null ? route.distance_km : '';
                document.getElementById('base_price').value = route.base_price;
                document.getElementById('status').value = route.status;
                
                // Store original data for change detection
                window.editRouteOriginalData = {
                    origin: route.origin,
                    destination: route.destination,
                    distance_km: route.distance_km !== null ? String(route.distance_km) : '',
                    base_price: String(route.base_price),
                    status: route.status
                };
                
                document.getElementById('statusRow').style.display = 'block';
                clearRouteValidationErrors();
                const submitBtn = document.getElementById('routeSubmitBtn');
                const submitBtnText = submitBtn.querySelector('span') || document.createElement('span');
                if (!submitBtn.querySelector('span')) {
                    submitBtn.innerHTML = '<i class="fas fa-check"></i> <span>Update Route</span>';
                } else {
                    submitBtn.querySelector('span').textContent = 'Update Route';
                }
                // Enable button for edit mode since form is populated with valid data
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                if (window.animateModalOpen) {
                    animateModalOpen('#routeModal');
                } else {
                    document.getElementById('routeModal').style.display = 'flex';
                }
            })
            .catch(() => alert('Failed to load route'));
    }
    function closeRouteModal(){
        if (window.animateModalClose) {
            animateModalClose('#routeModal');
        } else {
            document.getElementById('routeModal').style.display = 'none';
        }
    }
    
    // Update modal display style
    const routeModal = document.getElementById('routeModal');
    if (routeModal) {
        routeModal.style.display = 'none';
        routeModal.style.position = 'fixed';
        routeModal.style.top = '0';
        routeModal.style.left = '0';
        routeModal.style.width = '100%';
        routeModal.style.height = '100%';
        routeModal.style.background = 'rgba(0, 0, 0, 0.7)';
        routeModal.style.zIndex = '1000';
        routeModal.style.backdropFilter = 'blur(4px)';
        routeModal.style.alignItems = 'center';
        routeModal.style.justifyContent = 'center';
    }
    
    // Confirmation modal functions
    function showConfirmModal(action, details) {
        const isEdit = action === 'update';
        document.getElementById('confirmModalTitle').textContent = isEdit ? 'Confirm Update' : 'Confirm Create';
        document.getElementById('confirmModalMessage').textContent = isEdit 
            ? 'Are you sure you want to update this route?' 
            : 'Are you sure you want to create this route?';
        
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
    document.getElementById('routeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const action = document.getElementById('routeFormAction').value;
        
        // Check for duplicate error elements - if they exist, don't proceed
        const originDuplicateError = document.getElementById('route_duplicate_error_origin');
        const destinationDuplicateError = document.getElementById('route_duplicate_error_destination');
        if (originDuplicateError || destinationDuplicateError) {
            return; // Don't proceed if duplicate exists
        }
        
        // Validate form
        const validationErrors = validateAddRouteForm();
        
        if (Object.keys(validationErrors).length > 0) {
            // Display errors and prevent submission
            displayRouteValidationErrors(validationErrors);
            return; // Don't proceed
        }
        
        // Clear any previous error messages
        clearRouteValidationErrors();
        
        // Show confirmation modal
        const origin = document.getElementById('origin').value;
        const destination = document.getElementById('destination').value;
        const distance = document.getElementById('distance_km').value;
        const price = document.getElementById('base_price').value;
        const status = document.getElementById('status').value;
        
        // For edit action, check if there are any changes and show informational message
        let showNoChangesMessage = false;
        if (action === 'update' && !hasEditRouteChanges()) {
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
            let details = '<div><strong>Origin:</strong> ' + origin + '</div>';
            details += '<div><strong>Destination:</strong> ' + destination + '</div>';
            details += '<div><strong>Base Price:</strong> $' + parseFloat(price).toFixed(2) + '</div>';
            if (distance) {
                details += '<div><strong>Distance:</strong> ' + distance + ' km</div>';
            }
            if (action === 'update') {
                details += '<div><strong>Status:</strong> ' + status + '</div>';
            }
            
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
        window.pendingRouteForm = this;
    });
    
    // Handle confirmation
    document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
        if (window.pendingRouteForm) {
            hideConfirmModal();
            closeRouteModal();
            window.pendingRouteForm.submit();
            window.pendingRouteForm = null;
        }
    });
    
    function deleteRoute(id, origin, destination){
        // Fetch trip count for this route
        fetch('{{ route('api.check-route-trips') }}?route_id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('deleteRouteId').value = id;
                document.getElementById('deleteRouteInfo').textContent = origin + ' → ' + destination;
                
                const deleteModalTitle = document.getElementById('deleteModalTitle');
                const deleteModalMessage = document.getElementById('deleteModalMessage');
                const deleteModalWarning = document.getElementById('deleteModalWarning');
                const deleteModalIcon = document.getElementById('deleteModalIcon');
                const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');
                const deleteForm = document.getElementById('deleteForm');
                
                if (data.hasTrips) {
                    // Show error state
                    deleteModalTitle.textContent = 'Cannot Delete Route';
                    deleteModalMessage.innerHTML = '<i class="fas fa-lock" style="color: #ef4444; margin-right: 8px;"></i><strong>This route cannot be deleted because it has ' + data.tripCount + ' associated trip(s).</strong>';
                    deleteModalWarning.innerHTML = '<i class="fas fa-info-circle"></i> You must delete or update all trips using this route before deletion.';
                    deleteModalIcon.innerHTML = '<i class="fas fa-ban"></i>';
                    deleteModalIcon.style.background = 'rgba(239, 68, 68, 0.2)';
                    deleteModalIcon.style.color = '#ef4444';
                    deleteSubmitBtn.disabled = true;
                    deleteSubmitBtn.style.opacity = '0.5';
                    deleteSubmitBtn.style.cursor = 'not-allowed';
                    deleteForm.style.display = 'none';
                } else {
                    // Show normal delete state
                    deleteModalTitle.textContent = 'Delete Route';
                    deleteModalMessage.textContent = 'Are you sure you want to delete this route?';
                    deleteModalWarning.innerHTML = '<i class="fas fa-info-circle"></i> This action cannot be undone.';
                    deleteModalIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                    deleteModalIcon.style.background = 'rgba(239, 68, 68, 0.1)';
                    deleteModalIcon.style.color = '#ef4444';
                    deleteSubmitBtn.disabled = false;
                    deleteSubmitBtn.style.opacity = '1';
                    deleteSubmitBtn.style.cursor = 'pointer';
                    deleteForm.style.display = 'inline';
                }
                
                if (window.animateModalOpen) {
                    animateModalOpen('#deleteConfirmModal');
                } else {
                    document.getElementById('deleteConfirmModal').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error checking trips:', error);
                // Fall back to normal delete modal
                document.getElementById('deleteRouteId').value = id;
                document.getElementById('deleteRouteInfo').textContent = origin + ' → ' + destination;
                
                if (window.animateModalOpen) {
                    animateModalOpen('#deleteConfirmModal');
                } else {
                    document.getElementById('deleteConfirmModal').style.display = 'block';
                }
            });
    }
    
    function hideDeleteConfirmModal(){
        if (window.animateModalClose) {
            animateModalClose('#deleteConfirmModal');
        } else {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }
    }
    
    window.onclick = function(e){
        var routeModal = document.getElementById('routeModal');
        var deleteModal = document.getElementById('deleteConfirmModal');
        var confirmModal = document.getElementById('confirmModal');
        if (e.target === routeModal) { closeRouteModal(); }
        if (e.target === deleteModal) { hideDeleteConfirmModal(); }
        if (e.target === confirmModal) { hideConfirmModal(); }
    }
    
    function checkDuplicateRoute() {
        const origin = document.getElementById('origin').value.trim();
        const destination = document.getElementById('destination').value.trim();
        const routeId = document.getElementById('route_id').value; // For edit mode
        
        // Only check if both origin and destination are filled
        if (origin === '' || destination === '') {
            return Promise.resolve(false);
        }
        
        const params = `origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destination)}`;
        const url = routeId ? `{{ route('api.check-duplicate-route') }}?${params}&route_id=${routeId}` : `{{ route('api.check-duplicate-route') }}?${params}`;
        
        return fetch(url)
            .then(response => response.json())
            .then(data => {
                const originErrorElement = document.getElementById('route_duplicate_error_origin');
                const destinationErrorElement = document.getElementById('route_duplicate_error_destination');
                const submitBtn = document.getElementById('routeSubmitBtn');
                const originField = document.getElementById('origin');
                const destinationField = document.getElementById('destination');
                
                if (data.isDuplicate) {
                    // Show duplicate error below the origin input
                    if (!originErrorElement) {
                        const originError = document.createElement('span');
                        originError.id = 'route_duplicate_error_origin';
                        originError.className = 'error route-error-duplicate';
                        originError.style.cssText = 'display:flex;align-items:center;gap:6px;margin-top:6px;font-size:12px;color:#ef4444;';
                        originError.innerHTML = '<i class="fas fa-exclamation-circle" style="font-size:12px;"></i> <span>Information already existed</span>';
                        
                        const originWrapper = originField.closest('.input-wrapper');
                        if (originWrapper && originWrapper.parentNode) {
                            originWrapper.parentNode.insertBefore(originError, originWrapper.nextElementSibling);
                        }
                    }
                    
                    // Show duplicate error below the destination input
                    if (!destinationErrorElement) {
                        const destinationError = document.createElement('span');
                        destinationError.id = 'route_duplicate_error_destination';
                        destinationError.className = 'error route-error-duplicate';
                        destinationError.style.cssText = 'display:flex;align-items:center;gap:6px;margin-top:6px;font-size:12px;color:#ef4444;';
                        destinationError.innerHTML = '<i class="fas fa-exclamation-circle" style="font-size:12px;"></i> <span>Information already existed</span>';
                        
                        const destinationWrapper = destinationField.closest('.input-wrapper');
                        if (destinationWrapper && destinationWrapper.parentNode) {
                            destinationWrapper.parentNode.insertBefore(destinationError, destinationWrapper.nextElementSibling);
                        }
                    }
                    
                    // Highlight both fields
                    originField.style.borderColor = '#ef4444';
                    originField.style.background = 'rgba(239, 68, 68, 0.05)';
                    destinationField.style.borderColor = '#ef4444';
                    destinationField.style.background = 'rgba(239, 68, 68, 0.05)';
                    
                    // Make submit button completely non-clickable
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                    submitBtn.style.cursor = 'not-allowed';
                    submitBtn.style.pointerEvents = 'none';
                    submitBtn.style.background = 'rgba(59, 130, 246, 0.3)';
                    submitBtn.title = 'Information Already Existed - Cannot Add Duplicate Route';
                    
                    return true; // Duplicate found
                } else {
                    // Remove duplicate errors if they exist
                    if (originErrorElement) {
                        originErrorElement.remove();
                    }
                    if (destinationErrorElement) {
                        destinationErrorElement.remove();
                    }
                    
                    // Reset both fields styling
                    originField.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    originField.style.background = 'rgba(255, 255, 255, 0.05)';
                    destinationField.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    destinationField.style.background = 'rgba(255, 255, 255, 0.05)';
                    
                    // Re-enable button if no other validation errors
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                    submitBtn.style.pointerEvents = 'auto';
                    submitBtn.style.background = '';
                    submitBtn.title = 'Add Route';
                    
                    updateRouteSubmitButtonState();
                    return false; // No duplicate
                }
            })
            .catch(error => {
                console.error('Error checking for duplicate route:', error);
                return false; // If API fails, allow submission to proceed (server-side will catch it)
            });
    }
    
    // Set up real-time validation listeners
    const routeFields = ['origin', 'destination', 'distance_km', 'base_price'];
    let duplicateCheckTimeout;
    
    routeFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // On blur - validate
            field.addEventListener('blur', function() {
                const validationErrors = validateAddRouteForm();
                if (validationErrors[fieldId]) {
                    const errorElement = document.querySelector('.route-error-' + fieldId);
                    if (!errorElement) {
                        const error = document.createElement('span');
                        error.className = 'error route-error-' + fieldId;
                        error.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + validationErrors[fieldId];
                        const wrapper = field.closest('.input-wrapper');
                        if (wrapper && wrapper.parentNode) {
                            wrapper.parentNode.insertBefore(error, wrapper.nextElementSibling);
                        }
                    }
                    field.style.borderColor = '#ef4444';
                    field.style.background = 'rgba(239, 68, 68, 0.05)';
                } else {
                    const errorElement = document.querySelector('.route-error-' + fieldId);
                    if (errorElement) errorElement.remove();
                    field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    field.style.background = 'rgba(255, 255, 255, 0.05)';
                }
                updateRouteSubmitButtonState();
                
                // Check for duplicate on blur for origin/destination
                if ((fieldId === 'origin' || fieldId === 'destination')) {
                    clearTimeout(duplicateCheckTimeout);
                    duplicateCheckTimeout = setTimeout(() => {
                        checkDuplicateRoute();
                    }, 300);
                }
            });
            
            // On focus - clear error
            field.addEventListener('focus', function() {
                const errorElement = document.querySelector('.route-error-' + fieldId);
                if (errorElement) errorElement.remove();
                field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                field.style.background = 'rgba(255, 255, 255, 0.05)';
            });
            
            // On input - validate
            field.addEventListener('input', function() {
                const validationErrors = validateAddRouteForm();
                if (validationErrors[fieldId]) {
                    const errorElement = document.querySelector('.route-error-' + fieldId);
                    if (!errorElement) {
                        const error = document.createElement('span');
                        error.className = 'error route-error-' + fieldId;
                        error.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + validationErrors[fieldId];
                        const wrapper = field.closest('.input-wrapper');
                        if (wrapper && wrapper.parentNode) {
                            wrapper.parentNode.insertBefore(error, wrapper.nextElementSibling);
                        }
                    }
                    field.style.borderColor = '#ef4444';
                    field.style.background = 'rgba(239, 68, 68, 0.05)';
                } else {
                    const errorElement = document.querySelector('.route-error-' + fieldId);
                    if (errorElement) errorElement.remove();
                    field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
                    field.style.background = 'rgba(255, 255, 255, 0.05)';
                }
                updateRouteSubmitButtonState();
                
                // Check for duplicate on input for origin/destination with debouncing
                if ((fieldId === 'origin' || fieldId === 'destination')) {
                    clearTimeout(duplicateCheckTimeout);
                    duplicateCheckTimeout = setTimeout(() => {
                        checkDuplicateRoute();
                    }, 500);
                }
            });
        }
    });
    </script>
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
    
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


