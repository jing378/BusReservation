<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Trips - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
	<style>
	/* Scoped booking form styles to mirror login inputs */
	.booking-form { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
	@media (max-width: 720px){ .booking-form { grid-template-columns: 1fr; } }
	
	/* Status Badge Styles - matching {{ route('admin.users') }} styling */
	.status-badge {
		padding: 0.25rem 0.5rem;
		border-radius: 12px;
		font-size: 0.75rem;
		font-weight: 500;
		text-transform: uppercase;
		display: inline-block;
	}
	
	.status-badge.scheduled {
		background: #ffc107;
		color: #000;
	}
	
	.status-badge.departed {
		background: #6366f1;
		color: white;
	}
	
	.status-badge.cancelled {
		background: #dc3545;
		color: white;
	}
	
	.status-badge.completed {
		background: #17a2b8;
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
	.input-container {
		position: relative; display: flex; align-items: center;
		background: rgba(255,255,255,0.04);
		border: 1px solid rgba(255,255,255,0.08);
		border-radius: 12px; padding: 12px 14px;
		transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
		overflow: hidden;
	}
	.input-container:focus-within {
		border-color: rgba(59,130,246,0.6);
		box-shadow: 0 0 0 4px rgba(59,130,246,0.15);
		background: rgba(255,255,255,0.06);
	}
	.input-icon { color: #9ca3af; margin-right: 10px; font-size: 14px; }
	.input-container input,
	.input-container select {
		flex: 1; background: transparent; border: none; outline: none;
		color: #e5e7eb; font-size: 14px;
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
	}
	.input-container select {
		padding-right: 28px;
		cursor: pointer;
		transition: all 0.3s ease;
	}
	.input-container select:hover {
		color: #f0f9ff;
	}
	.input-container select option {
		background-color: #1a222c;
		color: #e5e7eb;
		padding: 10px 8px;
		line-height: 1.5;
		transition: all 0.2s ease;
	}
	.input-container select option:checked {
		background: linear-gradient(#3b82f6, #3b82f6);
		background-color: #3b82f6 !important;
		color: white;
	}
	.input-container select:focus option:checked {
		background: #3b82f6;
		color: white;
	}
	.input-container select option:hover {
		background-color: #2a3441;
		color: #f0f9ff;
	}
	.input-container {
		position: relative;
	}
	.dropdown-arrow {
		position: absolute;
		right: 14px;
		top: 50%;
		transform: translateY(-50%);
		width: 16px;
		height: 16px;
		background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat center;
		pointer-events: none;
		transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
	}
	.dropdown-arrow.active {
		transform: translateY(-50%) rotate(180deg);
		filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.6));
	}
	.dropdown-arrow.hover {
		filter: drop-shadow(0 0 2px rgba(59, 130, 246, 0.5));
	}
	
	/* Fix autofill styling */
	.input-container input:-webkit-autofill,
	.input-container input:-webkit-autofill:hover,
	.input-container input:-webkit-autofill:focus,
	.input-container input:-webkit-autofill:active {
		-webkit-box-shadow: 0 0 0 30px rgba(255,255,255,0.04) inset !important;
		-webkit-text-fill-color: #e5e7eb !important;
	}
	
	.input-container input:-webkit-autofill {
		-webkit-transition-delay: 99999s;
	}
	.input-focus-line {
		position: absolute; left: 14px; right: 14px; bottom: 6px; height: 2px;
		background: linear-gradient(90deg, transparent, rgba(59,130,246,0.7), transparent);
		opacity: 0; transform: scaleX(0.6);
		transition: transform .2s ease, opacity .2s ease;
	}
	.input-container:focus-within .input-focus-line { opacity: 1; transform: scaleX(1); }
	.booking-actions { grid-column: 1 / -1; display: flex; justify-content: flex-end; align-items: end; }
	.booking-actions .btn { width: auto; }
	
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
	
	/* Trip Modal Styles - Matching Bus/Route Modal Design */
	.trip-modal-enhanced {
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
	
	.trip-modal-header {
		display: flex;
		align-items: center;
		gap: 12px;
		margin-bottom: 16px;
	}
	
	.trip-modal-icon-wrapper {
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
	
	.trip-modal-header h3 {
		font-size: 18px;
		font-weight: 600;
		color: #e5e7eb;
		margin: 0;
	}
	
	.trip-modal-subtitle {
		font-size: 13px;
		color: #9ca3af;
		margin: 4px 0 0 0;
	}
	
	.trip-modal-header .close {
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
	
	.trip-modal-header .close:hover {
		background: rgba(255, 255, 255, 0.1);
		color: #e5e7eb;
	}
	
	.trip-modal-body {
		color: #9ca3af;
		margin-bottom: 20px;
	}
	
	.trip-modal-footer {
		display: flex;
		gap: 12px;
		justify-content: flex-end;
		margin-top: 20px;
		padding-top: 20px;
		border-top: 1px solid rgba(255, 255, 255, 0.1);
	}
	
	.trip-modal-footer .btn {
		padding: 10px 18px;
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
		white-space: nowrap;
	}
	
	.trip-modal-footer .btn-primary {
		background: linear-gradient(135deg, #3b82f6, #2563eb);
		color: #fff;
		border: 1px solid rgba(59, 130, 246, 0.6);
	}
	
	.trip-modal-footer .btn-primary:hover:not(:disabled) {
		background: linear-gradient(135deg, #2563eb, #1d4ed8);
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
	}
	
	.trip-modal-footer .btn-primary:disabled {
		opacity: 0.5;
		cursor: not-allowed;
	}
	
	.trip-modal-footer .btn-outline {
		background: rgba(255, 255, 255, 0.1);
		color: #e5e7eb;
		border: 1px solid rgba(255, 255, 255, 0.2);
	}
	
	.trip-modal-footer .btn-outline:hover {
		background: rgba(255, 255, 255, 0.15);
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
				<div class="welcome"><h2>Trips</h2><p>Schedule and manage trips</p></div>
				<div class="actions"></div>
			</header>
			@if ($ok)<div class="qa" style="justify-content:flex-start;color:#22c55e;margin:1rem 0"><i class="fas fa-check-circle"></i><span>{{ $ok }}</span></div>@endif
			<section class="panel">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
					<h3 style="margin: 0;">Upcoming and Recent Trips</h3>
					<button type="button" class="create-booking-btn" onclick="openAddTripModal()"><i class="fas fa-plus"></i> Add Trip</button>
				</div>
				@if (!empty($formErrors['general']))<div class="qa" style="justify-content:flex-start;color:#ef4444;margin-bottom:1rem"><i class="fas fa-circle-exclamation"></i><span>{{ $formErrors['general'] }}</span></div>@elseif ($formErrors)<div class="qa" style="justify-content:flex-start;color:#ef4444;margin-bottom:1rem"><i class="fas fa-circle-exclamation"></i><span>Please fix the form errors.</span></div>@endif
				<div class="table-wrap">
					<table class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Route</th>
								<th>Bus</th>
								<th>Departure</th>
								<th>Arrival</th>
								<th>Price</th>
								<th>Status</th>
								<th>Created</th>
								<th class="actions">Actions</th>
							</tr>
						</thead>
						<tbody>
							@if (!$trips)
							<tr><td colspan="9" style="text-align:center">No trips yet.</td></tr>
							@else
                            @foreach ($trips as $t)
							<tr>
								<td>{!! (int)$t['id'] !!}</td>
								<td>{{ $t['route_name'] }}</td>
								<td>{{ $t['plate_number'] }}</td>
								<td>{{ $t['dep_time'] }}</td>
								<td>{{ $t['arr_time'] ?? '' }}</td>
								<td>₱{!! number_format($t['price'], 2) !!}</td>
								<td><span class="status-badge {{ $t['status'] }}">{{ $t['status'] }}</span></td>
								<td>{{ $t['created_at'] }}</td>
								<td class="actions">
									<button class="btn btn-danger" title="Delete" onclick="deleteTrip({!! (int)$t['id'] !!}, '{{ $t['route_name'] }}', '{{ $t['plate_number'] }}', '{{ $t['dep_time'] }}')"><i class="fas fa-trash"></i></button>
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

	<!-- Add Trip Modal -->
	<div id="tripModal" class="modal">
		<div class="trip-modal-enhanced">
			<div class="trip-modal-header">
				<div class="trip-modal-icon-wrapper">
					<i class="fas fa-clock"></i>
				</div>
				<div style="flex: 1;">
					<h3 id="tripModalTitle">Add Trip</h3>
					<p class="trip-modal-subtitle">Schedule a new trip</p>
				</div>
				<button class="close" onclick="closeTripModal()" title="Close">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<form id="tripForm" method="post" action="">
    @csrf
				<input type="hidden" name="action" value="create">
				<div class="trip-modal-body">
					<div class="booking-form">
						<div class="field">
							<label for="trip_route_id">Select Route</label>
							<div class="input-container">
								<i class="fas fa-route input-icon"></i>
								<select name="route_id" id="trip_route_id" required>
									<option value="">Select Route</option>
									@foreach ($routes as $r)
									<option value="{!! (int)$r['id'] !!}">{{ $r['name'] }}</option>
									@endforeach
								</select>
								<div class="dropdown-arrow"></div>
								<div class="input-focus-line"></div>
							</div>
						</div>

						<div class="field">
							<label for="trip_bus_id">Select Bus</label>
							<div class="input-container">
								<i class="fas fa-bus input-icon"></i>
								<select name="bus_id" id="trip_bus_id" required>
									<option value="">Select Bus</option>
									@foreach ($buses as $b)
									<option value="{!! (int)$b['id'] !!}">{{ $b['name'] }}</option>
									@endforeach
								</select>
								<div class="dropdown-arrow"></div>
								<div class="input-focus-line"></div>
							</div>
						</div>

						<div class="field">
							<label for="trip_departure_time">Departure Time</label>
							<div class="input-container">
								<i class="fas fa-clock input-icon"></i>
								<input type="datetime-local" id="trip_departure_time" name="departure_time" required>
								<div class="input-focus-line"></div>
							</div>
						</div>

						<div class="field">
							<label for="trip_arrival_time">Arrival Time (optional)</label>
							<div class="input-container">
								<i class="fas fa-clock-rotate-left input-icon"></i>
								<input type="datetime-local" id="trip_arrival_time" name="arrival_time">
								<div class="input-focus-line"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="trip-modal-footer">
					<button type="button" class="btn btn-outline" onclick="closeTripModal()">
						<i class="fas fa-times"></i> Cancel
					</button>
					<button type="submit" class="btn btn-primary" id="tripSubmitBtn">
						<i class="fas fa-check"></i> Add Trip
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Add Confirmation Modal -->
	<div id="confirmModal" class="confirm-modal">
		<div class="confirm-modal-content">
			<div class="confirm-modal-header">
				<div class="confirm-modal-icon">
					<i class="fas fa-question-circle"></i>
				</div>
				<h3 class="confirm-modal-title" id="confirmModalTitle">Confirm Create</h3>
			</div>
			<div class="confirm-modal-body">
				<p id="confirmModalMessage">Are you sure you want to create this trip?</p>
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
				<h3 class="delete-modal-title" id="deleteModalTitle">Delete Trip</h3>
			</div>
			<div class="delete-modal-body">
				<p id="deleteModalMessage">Are you sure you want to delete this trip?</p>
				<div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;" id="deleteModalDetails">
					<div><strong>Route:</strong> <span id="deleteTripRoute"></span></div>
					<div><strong>Bus:</strong> <span id="deleteTripBus"></span></div>
					<div><strong>Departure:</strong> <span id="deleteTripDeparture"></span></div>
				</div>
				<p style="margin-top: 12px; color: #ef4444; font-size: 13px;" id="deleteModalWarning">
					<i class="fas fa-info-circle"></i> This action cannot be undone.
				</p>
			</div>
			<div class="delete-modal-footer">
				<button type="button" class="delete-modal-btn delete-modal-btn-secondary" onclick="hideDeleteConfirmModal()">
					<i class="fas fa-times"></i> Close
				</button>
				<form method="post" action="" style="display: inline;" id="deleteForm">
    @csrf
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="trip_id" id="deleteTripId">
					<button type="submit" class="delete-modal-btn delete-modal-btn-danger" id="deleteSubmitBtn">
						<i class="fas fa-trash"></i> Delete Trip
					</button>
				</form>
			</div>
		</div>
	</div>

    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
	<script>
	function openAddTripModal() {
		document.getElementById('tripForm').reset();
		clearTripValidationErrors();
		
		// Set minimum date/time to current date and time
		const now = new Date();
		const year = now.getFullYear();
		const month = String(now.getMonth() + 1).padStart(2, '0');
		const day = String(now.getDate()).padStart(2, '0');
		const hours = String(now.getHours()).padStart(2, '0');
		const minutes = String(now.getMinutes()).padStart(2, '0');
		const minDateTime = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
		
		document.getElementById('trip_departure_time').min = minDateTime;
		document.getElementById('trip_arrival_time').min = minDateTime;
		
		const submitBtn = document.getElementById('tripSubmitBtn');
		// Initialize button state as disabled since form is empty
		submitBtn.disabled = true;
		submitBtn.style.opacity = '0.5';
		submitBtn.style.cursor = 'not-allowed';
		if (window.animateModalOpen) {
			animateModalOpen('#tripModal');
		} else {
			document.getElementById('tripModal').style.display = 'flex';
		}
	}
	
	function closeTripModal() {
		if (window.animateModalClose) {
			animateModalClose('#tripModal');
		} else {
			document.getElementById('tripModal').style.display = 'none';
		}
	}
	
	// Update modal display style
	const tripModal = document.getElementById('tripModal');
	if (tripModal) {
		tripModal.style.display = 'none';
		tripModal.style.position = 'fixed';
		tripModal.style.top = '0';
		tripModal.style.left = '0';
		tripModal.style.width = '100%';
		tripModal.style.height = '100%';
		tripModal.style.background = 'rgba(0, 0, 0, 0.7)';
		tripModal.style.zIndex = '1000';
		tripModal.style.backdropFilter = 'blur(4px)';
		tripModal.style.alignItems = 'center';
		tripModal.style.justifyContent = 'center';
	}
	
	// ============ VALIDATION FUNCTIONS ============
	function validateTripForm() {
		const errors = {};
		
		const routeId = document.getElementById('trip_route_id').value.trim();
		const busId = document.getElementById('trip_bus_id').value.trim();
		const departureTime = document.getElementById('trip_departure_time').value.trim();
		
		// Route validation
		if (routeId === '') {
			errors['trip_route_id'] = 'Route is required.';
		}
		
		// Bus validation
		if (busId === '') {
			errors['trip_bus_id'] = 'Bus is required.';
		}
		
		// Departure time validation
		if (departureTime === '') {
			errors['trip_departure_time'] = 'Departure time is required.';
		} else {
			// Validate departure time is not in the past
			const departureDate = new Date(departureTime);
			const now = new Date();
			if (departureDate <= now) {
				errors['trip_departure_time'] = 'Departure time cannot be in the past.';
			}
		}
		
		return errors;
	}
	
	function displayTripValidationErrors(errors) {
		// Clear previous errors first
		clearTripValidationErrors();
		
		for (let fieldId in errors) {
			const field = document.getElementById(fieldId);
			if (field) {
				const errorElement = document.createElement('span');
				errorElement.className = 'error trip-error-' + fieldId;
				errorElement.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errors[fieldId];
				
				// Insert after the input wrapper
				const wrapper = field.closest('.input-container');
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
	
	function clearTripValidationErrors() {
		// Remove all error messages
		document.querySelectorAll('.trip-error-trip_route_id, .trip-error-trip_bus_id, .trip-error-trip_departure_time').forEach(el => el.remove());
		
		// Reset field styling
		const fields = ['trip_route_id', 'trip_bus_id', 'trip_departure_time'];
		fields.forEach(fieldId => {
			const field = document.getElementById(fieldId);
			if (field) {
				field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
				field.style.background = 'rgba(255, 255, 255, 0.05)';
			}
		});
	}
	
	function updateTripSubmitButtonState() {
		const validationErrors = validateTripForm();
		const submitBtn = document.getElementById('tripSubmitBtn');
		
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
	
	function checkDuplicateTrip() {
		const routeId = document.getElementById('trip_route_id').value.trim();
		const busId = document.getElementById('trip_bus_id').value.trim();
		const departureTime = document.getElementById('trip_departure_time').value.trim();
		
		// Only check if all required fields are filled
		if (routeId === '' || busId === '' || departureTime === '') {
			return Promise.resolve(false);
		}
		
		return fetch('{{ route('api.check-duplicate-trip') }}?route_id=' + routeId + '&bus_id=' + busId + '&departure_time=' + encodeURIComponent(departureTime))
			.then(response => response.json())
			.then(data => {
				const errorElement = document.getElementById('trip_duplicate_error');
				const submitBtn = document.getElementById('tripSubmitBtn');
				const departureField = document.getElementById('trip_departure_time');
				
				if (data.isDuplicate) {
					// Show duplicate error below the departure time input
					if (!errorElement) {
						const error = document.createElement('span');
						error.id = 'trip_duplicate_error';
						error.className = 'error trip-error-duplicate';
						error.style.cssText = 'display:flex;align-items:center;gap:6px;margin-top:6px;font-size:12px;color:#ef4444;';
						error.innerHTML = '<i class="fas fa-exclamation-circle" style="font-size:12px;"></i> <span>Information already existed</span>';
						
						const departureWrapper = departureField.closest('.input-container');
						if (departureWrapper && departureWrapper.parentNode) {
							departureWrapper.parentNode.insertBefore(error, departureWrapper.nextElementSibling);
						}
					}
					
					// Highlight the departure time field
					departureField.style.borderColor = '#ef4444';
					departureField.style.background = 'rgba(239, 68, 68, 0.05)';
					
					// Make submit button completely non-clickable
					submitBtn.disabled = true;
					submitBtn.style.opacity = '0.5';
					submitBtn.style.cursor = 'not-allowed';
					submitBtn.style.pointerEvents = 'none';
					submitBtn.style.background = 'rgba(59, 130, 246, 0.3)';
					submitBtn.title = 'Information Already Existed - Cannot Add Duplicate Trip';
					
					return true; // Duplicate found
				} else {
					// Remove duplicate error if it exists
					if (errorElement) {
						errorElement.remove();
					}
					
					// Reset departure time field styling
					departureField.style.borderColor = 'rgba(255, 255, 255, 0.1)';
					departureField.style.background = 'rgba(255, 255, 255, 0.05)';
					
					// Re-enable button if no other validation errors
					submitBtn.disabled = false;
					submitBtn.style.opacity = '1';
					submitBtn.style.cursor = 'pointer';
					submitBtn.style.pointerEvents = 'auto';
					submitBtn.style.background = '';
					submitBtn.title = 'Add Trip';
					
					updateTripSubmitButtonState();
					return false; // No duplicate
				}
			})
			.catch(error => {
				console.error('Error checking for duplicate trip:', error);
				return false; // If API fails, allow submission to proceed (server-side will catch it)
			});
	}
	
	function deleteTrip(id, route, bus, departure) {
		// Fetch booking count for this trip
		fetch('{{ route('api.check-trip-bookings') }}?trip_id=' + id)
			.then(response => response.json())
			.then(data => {
				document.getElementById('deleteTripId').value = id;
				document.getElementById('deleteTripRoute').textContent = route;
				document.getElementById('deleteTripBus').textContent = bus;
				document.getElementById('deleteTripDeparture').textContent = departure;
				
				const deleteModalTitle = document.getElementById('deleteModalTitle');
				const deleteModalMessage = document.getElementById('deleteModalMessage');
				const deleteModalWarning = document.getElementById('deleteModalWarning');
				const deleteModalIcon = document.getElementById('deleteModalIcon');
				const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');
				const deleteForm = document.getElementById('deleteForm');
				
				
				if (data.hasBookings) {
					// Show error state
					deleteModalTitle.textContent = 'Cannot Delete Trip';
					deleteModalMessage.innerHTML = '<i class="fas fa-lock" style="color: #ef4444; margin-right: 8px;"></i><strong>This trip cannot be deleted because it has active bookings.</strong>';
					deleteModalWarning.innerHTML = '<i class="fas fa-info-circle"></i> You must cancel or complete all bookings before deleting this trip.';
					deleteModalIcon.innerHTML = '<i class="fas fa-ban"></i>';
					deleteModalIcon.style.background = 'rgba(239, 68, 68, 0.2)';
					deleteModalIcon.style.color = '#ef4444';
					deleteSubmitBtn.disabled = true;
					deleteSubmitBtn.style.opacity = '0.5';
					deleteSubmitBtn.style.cursor = 'not-allowed';
					deleteForm.style.display = 'none';
				} else {
					// Show normal delete state
					deleteModalTitle.textContent = 'Delete Trip';
					deleteModalMessage.textContent = 'Are you sure you want to delete this trip?';
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
				console.error('Error checking bookings:', error);
				// Fall back to normal delete modal
				document.getElementById('deleteTripId').value = id;
				document.getElementById('deleteTripRoute').textContent = route;
				document.getElementById('deleteTripBus').textContent = bus;
				document.getElementById('deleteTripDeparture').textContent = departure;
				
				if (window.animateModalOpen) {
					animateModalOpen('#deleteConfirmModal');
				} else {
					document.getElementById('deleteConfirmModal').style.display = 'block';
				}
			});
	}
	
	function hideDeleteConfirmModal() {
		if (window.animateModalClose) {
			animateModalClose('#deleteConfirmModal');
		} else {
			document.getElementById('deleteConfirmModal').style.display = 'none';
		}
	}
	
	// Confirmation modal functions
	function showConfirmModal(details) {
		document.getElementById('confirmModalTitle').textContent = 'Confirm Create';
		document.getElementById('confirmModalMessage').textContent = 'Are you sure you want to create this trip?';
		
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
	document.getElementById('tripForm').addEventListener('submit', function(e) {
		e.preventDefault();
		
		// Check for duplicate error element - if it exists, don't proceed
		const duplicateError = document.getElementById('trip_duplicate_error');
		if (duplicateError) {
			return; // Don't proceed if duplicate error is showing
		}
		
		// Validate form
		const validationErrors = validateTripForm();
		
		if (Object.keys(validationErrors).length > 0) {
			// Display errors and prevent submission
			displayTripValidationErrors(validationErrors);
			return; // Don't proceed
		}
		
		// Clear any previous error messages
		clearTripValidationErrors();
		
		// Check for duplicate trip
		checkDuplicateTrip().then(isDuplicate => {
			if (isDuplicate) {
				return; // Don't proceed if duplicate found
			}
			
			const routeSelect = document.getElementById('trip_route_id');
			const busSelect = document.getElementById('trip_bus_id');
			const departureTime = document.getElementById('trip_departure_time').value;
			const arrivalTime = document.getElementById('trip_arrival_time').value;
			
			const routeText = routeSelect.options[routeSelect.selectedIndex].text;
			const busText = busSelect.options[busSelect.selectedIndex].text;
			
			let details = '<div><strong>Route:</strong> ' + routeText + '</div>';
			details += '<div><strong>Bus:</strong> ' + busText + '</div>';
			details += '<div><strong>Departure:</strong> ' + departureTime + '</div>';
			if (arrivalTime) {
				details += '<div><strong>Arrival:</strong> ' + arrivalTime + '</div>';
			}
			
			showConfirmModal(details);
			
			// Store form reference for submission after confirmation
			window.pendingTripForm = this;
		});
	});
	
	// Set up real-time validation listeners
	const tripFields = ['trip_route_id', 'trip_bus_id', 'trip_departure_time'];
	tripFields.forEach(fieldId => {
		const field = document.getElementById(fieldId);
		if (field) {
			// Handle dropdown arrow animations
			if (fieldId === 'trip_route_id' || fieldId === 'trip_bus_id') {
				const container = field.closest('.input-container');
				const arrow = container.querySelector('.dropdown-arrow');
				
				if (arrow) {
					let isOpen = false;
					
					field.addEventListener('mousedown', function(e) {
						// Toggle arrow rotation on click
						if (isOpen) {
							arrow.classList.remove('active');
							isOpen = false;
						} else {
							arrow.classList.add('active');
							isOpen = true;
						}
					});
					
					field.addEventListener('change', function() {
						// Reset arrow and state when option is selected
						arrow.classList.remove('active');
						isOpen = false;
						// Check for duplicate when any field changes
						checkDuplicateTrip();
					});
					
					field.addEventListener('blur', function() {
						// Reset arrow and state when dropdown loses focus
						arrow.classList.remove('active');
						isOpen = false;
					});
					
					field.addEventListener('mouseenter', function() {
						arrow.classList.add('hover');
					});
					
					field.addEventListener('mouseleave', function() {
						arrow.classList.remove('hover');
					});
				}
			} else if (fieldId === 'trip_departure_time') {
				// Add live validation for datetime input
				let debounceTimer;
				field.addEventListener('input', function() {
					clearTimeout(debounceTimer);
					debounceTimer = setTimeout(() => {
						checkDuplicateTrip();
					}, 500); // Debounce 500ms
				});
				
				field.addEventListener('change', function() {
					checkDuplicateTrip();
				});
			}
			
			// On change/blur - validate required fields
			field.addEventListener('change', function() {
				const validationErrors = validateTripForm();
				if (validationErrors[fieldId]) {
					const errorElement = document.querySelector('.trip-error-' + fieldId);
					if (!errorElement) {
						const error = document.createElement('span');
						error.className = 'error trip-error-' + fieldId;
						error.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + validationErrors[fieldId];
						const wrapper = field.closest('.input-container');
						if (wrapper && wrapper.parentNode) {
							wrapper.parentNode.insertBefore(error, wrapper.nextElementSibling);
						}
					}
					field.style.borderColor = '#ef4444';
					field.style.background = 'rgba(239, 68, 68, 0.05)';
				} else {
					const errorElement = document.querySelector('.trip-error-' + fieldId);
					if (errorElement) errorElement.remove();
					field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
					field.style.background = 'rgba(255, 255, 255, 0.05)';
				}
				updateTripSubmitButtonState();
			});
			
			// On focus - clear error
			field.addEventListener('focus', function() {
				const errorElement = document.querySelector('.trip-error-' + fieldId);
				if (errorElement) errorElement.remove();
				field.style.borderColor = 'rgba(255, 255, 255, 0.1)';
				field.style.background = 'rgba(255, 255, 255, 0.05)';
			});
		}
	});
	
	// Handle confirmation
	document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
		if (window.pendingTripForm) {
			hideConfirmModal();
			closeTripModal();
			window.pendingTripForm.submit();
			window.pendingTripForm = null;
		}
	});
	
	// Close modal when clicking outside
	window.onclick = function(event) {
		const tripModal = document.getElementById('tripModal');
		const deleteModal = document.getElementById('deleteConfirmModal');
		const confirmModal = document.getElementById('confirmModal');
		if (event.target === tripModal) {
			closeTripModal();
		}
		if (event.target === deleteModal) {
			hideDeleteConfirmModal();
		}
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





