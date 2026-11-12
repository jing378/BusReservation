<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Logs - Bus Reservation System</title>
	<link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
	<style>
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
			transform: translate(-50%, -50%) scale(0.9);
		}
		to {
			opacity: 1;
			transform: translate(-50%, -50%) scale(1);
		}
	}
	
	.delete-modal-content.is-animating-out {
		animation: modalSlideOutCentered 0.22s ease-in forwards;
	}
	
	@keyframes modalSlideOutCentered {
		from {
			opacity: 1;
			transform: translate(-50%, -50%) scale(1);
		}
		to {
			opacity: 0;
			transform: translate(-50%, calc(-50% - 10px)) scale(0.98);
		}
	}
	
	.delete-modal.is-hiding {
		animation: overlayFadeOut 0.2s ease-in forwards;
	}
	
	@keyframes overlayFadeOut {
		from {
			opacity: 1;
		}
		to {
			opacity: 0;
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
	
	/* Logout confirmation modal styles - matching delete modal */
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
		opacity: 0;
		pointer-events: none;
		transition: opacity 0.2s ease-out;
	}
	
	.confirm-modal.is-visible {
		opacity: 1;
		pointer-events: auto;
		animation: none;
	}
	
	.confirm-modal.is-hiding {
		opacity: 0;
		pointer-events: none;
	}
	
	@keyframes fadeOut {
		from {
			opacity: 1;
		}
		to {
			opacity: 0;
		}
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
		opacity: 0;
		transform: translate(-50%, -50%) scale(0.9);
		transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
	}
	
	.confirm-modal.is-visible .confirm-modal-content {
		opacity: 1;
		transform: translate(-50%, -50%) scale(1);
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
		display: inline-flex;
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
		text-decoration: none;
	}
	
	.confirm-modal-btn-primary:hover {
		background: linear-gradient(135deg, #2563eb, #1d4ed8);
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
	}
	
	/* Panel Header and Filter Styles */
	.panel-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 1rem;
	}
	
	.search-sort {
		position: relative;
	}
	
	.search-form {
		display: flex;
		gap: 0.5rem;
		align-items: center;
	}
	
	.search-input {
		padding: 8px 12px;
		border: 1px solid rgba(255,255,255,0.08);
		border-radius: 8px;
		background: rgba(255,255,255,0.04);
		color: #e5e7eb;
		font-size: 13px;
		min-width: 200px;
	}
	
	.search-input::placeholder {
		color: #6b7280;
	}
	
	.btn-secondary {
		background: rgba(255,255,255,0.04);
		border: 1px solid rgba(255,255,255,0.08);
		border-radius: 8px;
		color: #e5e7eb;
		font-size: 13px;
		padding: 8px 12px;
		cursor: pointer;
		transition: all 0.2s ease;
	}
	
	.btn-secondary:hover {
		background: rgba(255,255,255,0.08);
		border-color: rgba(255,255,255,0.15);
	}
	
	.filter-panel {
		display: none;
		position: absolute;
		top: calc(100% + 8px);
		right: 0;
		z-index: 2000;
		background: #1a222c;
		border: 1px solid rgba(255,255,255,0.1);
		border-radius: 12px;
		box-shadow: 0 12px 28px rgba(0,0,0,0.35);
		padding: 12px;
		min-width: 520px;
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		gap: 12px;
	}
	
	.filter-row label {
		display: block;
		font-size: 12px;
		color: #9ca3af;
		margin-bottom: 6px;
	}
	
	.filter-row select,
	.filter-row input {
		width: 100%;
		padding: 10px;
		border: 1px solid rgba(255,255,255,0.08);
		border-radius: 6px;
		background: rgba(255,255,255,0.04);
		color: #e5e7eb;
		font-size: 13px;
	}
	
	.filter-row select:focus,
	.filter-row input:focus {
		outline: none;
		border-color: #3b82f6;
		background: rgba(255,255,255,0.08);
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
                <div class="welcome"><h2>Activity Logs</h2><p>System events and actions</p></div>
                <div class="actions">
                    <button type="button" class="btn btn-danger" onclick="showDeleteConfirmModal()">
                        <i class="fas fa-trash"></i> Delete Logs
                    </button>
                </div>
            </header>
            
            @if ($cleared)
            <div class="qa" style="justify-content:flex-start;color:#22c55e;margin:1rem 0"><i class="fas fa-check-circle"></i><span>All logs deleted.</span></div>
            @endif
            
            <section class="panel" id="logsSection">
                <div class="panel-header">
                    <h3>Activity Logs</h3>
                    <div class="search-sort">
                        <form method="get" class="search-form">
                            <input type="text" name="search" placeholder="Search logs..." value="{{ $search_term }}" class="search-input">
                            <button type="button" class="btn btn-secondary" id="filterBtn" title="Filter"><i class="fas fa-filter"></i></button>
                            @if ($search_term)<a href="{{ route('admin.logs') }}" class="btn btn-outline">Clear</a>@endif
                        </form>
                        <div class="filter-panel" id="filterPanel" style="display:none">
                            <div class="filter-row">
                                <label>Event Type</label>
                                <select name="filter_event" onchange="this.form.submit()">
                                    <option value="">All Events</option>
                                    @foreach ($all_events as $evt)
                                    <option value="{{ $evt }}" {!! $filter_event === $evt ? 'selected' : '' !!}>
                                        {{ \App\Services\ActivityLogger::displayName($evt) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-row">
                                <label>User</label>
                                <select name="filter_user" onchange="this.form.submit()">
                                    <option value="">All Users</option>
                                    @foreach ($all_users as $usr)
                                    <option value="{{ $usr }}" {!! $filter_user === $usr ? 'selected' : '' !!}>
                                        {{ $usr }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-row">
                                <label>Date (YYYY-MM-DD)</label>
                                <input type="text" name="filter_date" placeholder="2025-11-16" value="{{ $filter_date }}" style="width: 100%; padding: 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; background: rgba(255,255,255,0.05); color: #e5e7eb; font-size: 13px;">
                            </div>
                        </div>
                    </div>
                </div>
                
			<section class="panel">
				<h3>Activity Log Entries</h3>
				<div style="font-size: 13px; color: #9ca3af; margin-bottom: 16px;">
                    Showing {!! $offset + 1 !!} - {!! min($offset + $per_page, $total) !!} of {!! $total !!} entries
                    @if ($filter_event || $filter_user || $filter_date || $search_term)
                    <span style="color: #3b82f6;"> (filtered)</span>
                    @endif
                </div>
				<div class="table-wrap">
					<table class="table">
						<thead>
							<tr>
								<th>Timestamp</th>
								<th>User</th>
								<th>Event</th>
								<th>Details</th>
							</tr>
						</thead>
						<tbody>
							@if (!$slice)
							<tr><td colspan="4" style="text-align:center">No log entries found.</td></tr>
							@else
							@foreach ($slice as $row)
                            @php
                                $severity = \App\Services\ActivityLogger::severity($row['event'] ?? '');
                                $display_name = \App\Services\ActivityLogger::displayName($row['event'] ?? '');
                                $badge_color = match($severity) {
                                    'success' => 'color: #22c55e;',
                                    'error' => 'color: #ef4444;',
                                    default => 'color: #3b82f6;'
                                };
                            @endphp
							<tr>
								<td>{{ $row['timestamp'] ?? $row['time'] ?? '' }}</td>
								<td>{{ ($row['username'] ?? '') ?: ('#' . ($row['user_id'] ?? '-')) }}</td>
								<td><span style="{!! $badge_color !!}">{!! $display_name !!}</span></td>
								<td>
                                    <details style="cursor: pointer;">
                                        <summary style="color: #3b82f6; font-size: 12px;">View Details</summary>
                                        <pre style="background: rgba(255,255,255,0.02); padding: 8px; border-radius: 6px; font-size: 11px; overflow-x: auto; margin-top: 8px; color: #b8c7db;">
{{ json_encode(\Illuminate\Support\Arr::except($row, ['timestamp', 'time', 'event', 'user_id', 'username', 'ip_address', 'ip', 'user_agent', 'ua', 'method', 'role']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                </td>
							</tr>
							@endforeach
                            @endif
						</tbody>
					</table>
				</div>
				@if ($total_pages > 1)
				<div class="pagination">
					<div class="group left">
						@if ($page > 1)
							<a href="?page=1" class="btn btn-outline" title="First"><i class="fas fa-angles-left"></i> First</a>
							<a href="?page={!! $page-1 !!}" class="btn btn-outline" title="Previous"><i class="fas fa-chevron-left"></i> Previous</a>
						@endif
					</div>
					<div class="group center"><span class="page-info">Page {!! $page !!} of {!! $total_pages !!} ({!! $total !!} entries)</span></div>
					<div class="group right">
						@if ($page < $total_pages)
							<a href="?page={!! $page+1 !!}" class="btn btn-outline" title="Next">Next <i class="fas fa-chevron-right"></i></a>
							<a href="?page={!! $total_pages !!}" class="btn btn-outline" title="Last">Last <i class="fas fa-angles-right"></i></a>
						@endif
					</div>
				</div>
				@endif
			</section>

            <!-- Delete Confirmation Modal -->
            <div id="deleteConfirmModal" class="delete-modal">
                <div class="delete-modal-content">
                    <div class="delete-modal-header">
                        <div class="delete-modal-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="delete-modal-title">Delete All Logs</h3>
                    </div>
                    <div class="delete-modal-body">
                        <p>Are you sure you want to delete ALL log entries?</p>
                        <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;">
                            <div><strong>Action:</strong> Clear all activity logs</div>
                            <div><strong>Impact:</strong> All log entries will be permanently removed</div>
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
                            <input type="hidden" name="action" value="clear_logs">
                            <button type="submit" class="delete-modal-btn delete-modal-btn-danger">
                                <i class="fas fa-trash"></i> Delete All
                            </button>
                        </form>
                    </div>
                </div>
            </div>
		</main>
	</div>
	<script src="{{ asset('assets/js/sidebar.js') }}"></script>
	<script src="{{ asset('assets/js/modal-animations.js') }}"></script>

    <script>
        // Filter toggle functionality
        (function(){
            const filterBtn = document.getElementById('filterBtn');
            const filterPanel = document.getElementById('filterPanel');
            
            if (filterBtn && filterPanel) {
                filterBtn.addEventListener('click', function(e){
                    e.stopPropagation();
                    filterPanel.style.display = (filterPanel.style.display === 'none' || !filterPanel.style.display) ? 'grid' : 'none';
                });
                
                // Close panel when clicking outside
                document.addEventListener('click', function(e){
                    if (!filterPanel.contains(e.target) && !filterBtn.contains(e.target)) {
                        filterPanel.style.display = 'none';
                    }
                });
            }
        })();
        
        const modal = document.getElementById('deleteConfirmModal');
        
        function showDeleteConfirmModal() {
            if (window.animateModalOpen) {
                animateModalOpen(modal);
            } else {
                modal.style.display = 'flex';
            }
        }
        
        function hideDeleteConfirmModal() {
            if (window.animateModalClose) {
                animateModalClose(modal);
            } else {
                modal.style.display = 'none';
            }
        }
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideDeleteConfirmModal();
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
        const logoutConfirmModal = document.getElementById('logoutConfirmModal');
        
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                animateModalOpen(logoutConfirmModal);
            });
        }
        
        function hideLogoutConfirmModal() {
            animateModalClose(logoutConfirmModal);
        }
        
        // Close modal when clicking outside
        logoutConfirmModal.addEventListener('click', function(event) {
            if (event.target === logoutConfirmModal) {
                hideLogoutConfirmModal();
            }
        });
    </script>
</body>
</html>


