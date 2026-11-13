<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Users - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mobile.css') }}">
    <script src="{{ asset('assets/js/mobile-responsive.js') }}" defer></script>
</head>
<body>
	<div class="dash-wrap">
		@include('partials.sidebar')
		<button class="btn-ghost hamburger-btn" id="mobileMenuToggle" title="Toggle menu"><i class="fas fa-bars"></i></button>
		<main class="content">
			<header class="topbar">
				<div class="welcome"><h2>Users</h2><p>Manage system users</p></div>
			</header>
			
			@if ($ok)<div class="qa" style="justify-content:flex-start;color:#22c55e;margin:1rem 0"><i class="fas fa-check-circle"></i><span>{{ $ok }}</span></div>@endif
			@if ($formErrors)<div class="qa" style="justify-content:flex-start;color:#ef4444;margin:1rem 0"><i class="fas fa-circle-exclamation"></i><span>{{ $formErrors['general'] ?? 'Please fix the form errors.' }}</span></div>@endif
			
			<section class="panel" id="usersSection">
				<div class="panel-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
					<div style="flex: 1;">
						<h3 style="margin: 0 0 12px 0;">Users Management</h3>
						<div class="search-sort">
							<form method="get" class="search-form">
								<input type="text" name="search" placeholder="Search users..." value="{{ $search }}" class="search-input">
								<button type="button" class="btn btn-secondary" id="filterBtn" title="Filter"><i class="fas fa-filter"></i></button>
								@if ($search)<a href="{{ route('admin.users') }}" class="btn btn-outline">Clear</a>@endif
							</form>
							<div class="filter-panel" id="filterPanel" style="display:none">
								<div class="filter-row">
									<label>Role</label>
									<select id="filterRole">
										<option value="">All</option>
										<option value="admin">Admin</option>
										<option value="staff">Staff</option>
										<option value="customer">Customer</option>
									</select>
								</div>
								<div class="filter-row">
									<label>Status</label>
									<select id="filterStatus">
										<option value="">All</option>
										<option value="Active">Active</option>
										<option value="Disabled">Disabled</option>
									</select>
								</div>
								<div class="filter-row">
									<label>Type</label>
									<select id="filterType">
										<option value="">All</option>
										<option value="Regular">Regular</option>
										<option value="Student">Student</option>
										<option value="Senior Citizen/PWD">Senior Citizen/PWD</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<button class="btn btn-primary" onclick="openCreateModal()" style="align-self: center; margin-top: 0;"><i class="fas fa-plus"></i> Add User</button>
				</div>
				
				<div class="table-wrap">
					<table class="table">
						<thead>
                            <tr>
                                <th>#</th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'full_name', 'order' => $sort === 'full_name' && $order === 'ASC' ? 'DESC' : 'ASC'])) !!}">Name <i class="fas fa-sort"></i></a></th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'username', 'order' => $sort === 'username' && $order === 'ASC' ? 'DESC' : 'ASC'])) !!}">Username <i class="fas fa-sort"></i></a></th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'email', 'order' => $sort === 'email' && $order === 'ASC' ? 'DESC' : 'ASC'])) !!}">Email <i class="fas fa-sort"></i></a></th>
								<th>Phone</th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'role', 'order' => $sort === 'role' && $order === 'ASC' ? 'DESC' : 'ASC'])) !!}">Role <i class="fas fa-sort"></i></a></th>
								<th>Type</th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'is_active', 'order' => $sort === 'is_active' && $order === 'ASC' ? 'DESC' : 'ASC'])) !!}">Status <i class="fas fa-sort"></i></a></th>
								<th><a href="?{!! http_build_query(array_merge(request()->query(), ['sort' => 'created_at', 'order' => $sort === 'created_at' && $order === 'ASC' ? 'DESC' : 'ASC'])) !!}">Created <i class="fas fa-sort"></i></a></th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
                            @if (!$users)
							<tr><td colspan="10" style="text-align:center">No users found.</td></tr>
                            @else
                            @php($i = $offset + 1)
                            @foreach ($users as $u)
							<tr>
                                <td>{!! $i++ !!}</td>
								<td>{{ $u['full_name'] }}</td>
								<td>{{ $u['username'] }}</td>
								<td>{{ $u['email'] }}</td>
								<td>{{ $u['phone'] ?? '' }}</td>
								<td><span class="role-badge role-{{ $u['role'] }}">{{ $u['role'] }}</span></td>
								<td>{{ $u['user_type'] ?? 'Regular' }}</td>
								<td><span class="status-badge status-{!! ((int)$u['is_active'] === 1) ? 'active' : 'inactive' !!}">{!! ((int)$u['is_active'] === 1) ? 'Active' : 'Disabled' !!}</span></td>
								<td>{!! date('M j, Y', strtotime($u['created_at'])) !!}</td>
								<td class="actions">
						<button class="btn btn-sm btn-edit" onclick="editUser({!! $u['id'] !!})" title="Edit User"><i class="fas fa-pen"></i></button>
									<button class="btn btn-sm btn-danger" title="Delete User" onclick="deleteUser({!! $u['id'] !!}, '{{ $u['full_name'] }}', '{{ $u['email'] }}', '{{ $u['role'] }}')"><i class="fas fa-trash"></i></button>
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
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => 1])) !!}" class="btn btn-outline" title="First"><i class="fas fa-angles-left"></i> First</a>
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => $page - 1])) !!}" class="btn btn-outline" title="Previous"><i class="fas fa-chevron-left"></i> Previous</a>
						@endif
					</div>
					<div class="group center">
						<span class="page-info">Page {!! $page !!} of {!! $total_pages !!} ({!! $total_users !!} total users)</span>
					</div>
					<div class="group right">
						@if ($page < $total_pages)
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => $page + 1])) !!}" class="btn btn-outline" title="Next">Next <i class="fas fa-chevron-right"></i></a>
							<a href="?{!! http_build_query(array_merge(request()->query(), ['page' => $total_pages])) !!}" class="btn btn-outline" title="Last">Last <i class="fas fa-angles-right"></i></a>
						@endif
					</div>
				</div>
				@endif
			</section>
		</main>
	</div>

	<!-- Create/Edit User Modal -->
	<div id="userModal" class="modal">
		<div class="user-modal-enhanced">
			<div class="user-modal-header">
				<div class="modal-icon-wrapper">
					<i class="fas fa-user-plus"></i>
				</div>
				<div style="flex: 1;">
					<h3 id="modalTitle">Add User</h3>
					<p class="modal-subtitle">Create a new system user</p>
				</div>
				<button class="close" onclick="closeModal()" title="Close">
					<i class="fas fa-times"></i>
				</button>
			</div>
			<form id="userForm" method="post" action="">
    @csrf
				<input type="hidden" name="action" id="formAction" value="create">
				<input type="hidden" name="user_id" id="userId" value="">
				<div class="user-modal-body">
					<div class="form-row">
						<div class="form-group form-group-enhanced">
							<label for="full_name">
								<i class="fas fa-id-card"></i>
								<span>Full Name *</span>
							</label>
							<div class="input-wrapper">
								<input type="text" name="full_name" id="full_name" placeholder="Enter full name" required>
							</div>
							@if (isset($formErrors['full_name']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['full_name'] }}</span>@endif
						</div>
						<div class="form-group form-group-enhanced">
							<label for="phone">
								<i class="fas fa-phone"></i>
								<span>Phone</span>
							</label>
							<div class="input-wrapper">
								<input type="text" name="phone" id="phone" placeholder="Enter phone number">
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group form-group-enhanced">
							<label for="email">
								<i class="fas fa-envelope"></i>
								<span>Email *</span>
							</label>
							<div class="input-wrapper">
								<input type="email" name="email" id="email" placeholder="user@example.com" required>
							</div>
							@if (isset($formErrors['email']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['email'] }}</span>@endif
						</div>
						<div class="form-group form-group-enhanced">
							<label for="username">
								<i class="fas fa-user"></i>
								<span>Username *</span>
							</label>
							<div class="input-wrapper">
								<input type="text" name="username" id="username" placeholder="Enter username" required>
							</div>
							@if (isset($formErrors['username']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['username'] }}</span>@endif
						</div>
					</div>
					<div class="form-row">
						<div class="form-group form-group-enhanced">
							<label for="password">
								<i class="fas fa-lock"></i>
								<span>Password <span id="passwordNote">*</span></span>
							</label>
							<div class="input-wrapper">
								<div class="password-input-container">
									<input type="password" name="password" id="password" placeholder="Enter password">
									<button type="button" class="password-toggle" id="passwordToggle">
										<i class="fas fa-eye"></i>
									</button>
								</div>
							</div>
							@if (isset($formErrors['password']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['password'] }}</span>@endif
							<small id="passwordHelp" class="help-text" style="display: none;"><i class="fas fa-info-circle"></i> Leave blank to keep current password when editing</small>
						</div>
						<div class="form-group form-group-enhanced">
							<label for="confirm_password">
								<i class="fas fa-lock"></i>
								<span>Confirm Password <span id="confirmPasswordNote">*</span></span>
							</label>
							<div class="input-wrapper">
								<div class="password-input-container">
									<input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password">
									<button type="button" class="password-toggle" id="confirmPasswordToggle">
										<i class="fas fa-eye"></i>
									</button>
								</div>
							</div>
							@if (isset($formErrors['confirm_password']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['confirm_password'] }}</span>@endif
						</div>
					</div>
					<div class="form-row">
						<div class="form-group form-group-enhanced">
							<label for="role">
								<i class="fas fa-user-shield"></i>
								<span>Role *</span>
							</label>
							<div class="input-wrapper">
								<select name="role" id="role" required>
									<option value="customer">Customer</option>
									<option value="staff">Staff</option>
									<option value="admin">Admin</option>
								</select>
							</div>
							@if (isset($formErrors['role']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['role'] }}</span>@endif
						</div>
						<div class="form-group form-group-enhanced">
							<label for="user_type">
								<i class="fas fa-tag"></i>
								<span>User Type *</span>
							</label>
							<div class="input-wrapper">
								<select name="user_type" id="user_type" required>
									<option value="Regular">Regular</option>
									<option value="Student">Student</option>
									<option value="Senior Citizen/PWD">Senior Citizen/PWD</option>
								</select>
							</div>
							@if (isset($formErrors['user_type']))<span class="error"><i class="fas fa-exclamation-circle"></i> {{ $formErrors['user_type'] }}</span>@endif
						</div>
					</div>
					<div class="form-group form-group-enhanced" id="statusGroup" style="display: none;">
						<label for="is_active">
							<i class="fas fa-toggle-on"></i>
							<span>Status</span>
						</label>
						<div class="input-wrapper">
							<select name="is_active" id="is_active">
								<option value="1">Active</option>
								<option value="0">Disabled</option>
							</select>
						</div>
					</div>
				</div>
				
				<div class="user-modal-footer">
					<button type="button" class="btn btn-outline" onclick="closeModal()">
						<i class="fas fa-times"></i> Cancel
					</button>
					<button type="submit" class="btn btn-primary" id="submitBtn">
						<i class="fas fa-check"></i> <span>Create User</span>
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
	<div id="deleteModal" class="delete-modal">
		<div class="delete-modal-content">
			<div class="delete-modal-header">
				<div class="delete-modal-icon">
					<i class="fas fa-exclamation-triangle"></i>
				</div>
				<h3 class="delete-modal-title">Delete User</h3>
			</div>
			<div class="delete-modal-body">
				<p>Are you sure you want to delete this user?</p>
				<div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;">
					<div><strong>User:</strong> <span id="deleteUserName"></span></div>
					<div><strong>Email:</strong> <span id="deleteUserEmail"></span></div>
					<div><strong>Role:</strong> <span id="deleteUserRole"></span></div>
				</div>
				<p style="margin-top: 12px; color: #ef4444; font-size: 13px;">
					<i class="fas fa-info-circle"></i> This action cannot be undone.
				</p>
			</div>
			<div class="delete-modal-footer">
				<button type="button" class="delete-modal-btn delete-modal-btn-secondary" onclick="closeDeleteModal()">
					<i class="fas fa-times"></i> Cancel
				</button>
				<button type="button" class="delete-modal-btn delete-modal-btn-danger" id="confirmDeleteBtn">
					<i class="fas fa-trash"></i> Delete User
				</button>
			</div>
		</div>
	</div>

    <style>
        /* User Modal Styles - Matching Delete Modal Design */
        .user-modal-enhanced {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1a222c;
            border-radius: 12px;
            padding: 24px;
            max-width: 700px;
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
        
        .user-modal-header {
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
        
        .user-modal-header h3 {
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
        
        .user-modal-header .close {
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
        
        .user-modal-header .close:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
        }
        
        .user-modal-body {
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
            border: 1px solid #2c384bff;
            border-radius: 6px;
            background: #1e293b;
            color: #e5e7eb;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .form-group-enhanced input:focus,
        .form-group-enhanced select:focus {
            outline: none;
            border-color: #2c384bff;
            background: #0f172a;
        }
        
        .form-group-enhanced input::placeholder {
            color: #6b7280;
        }
        
        .form-group-enhanced select option {
            background: #1e293b;
            color: #e5e7eb;
        }
        
        .form-group-enhanced select option:checked {
            background: #2c384bff;
            color: #e5e7eb;
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
        
        .help-text {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            font-size: 12px;
            color: #9ca3af;
        }
        
        .help-text i {
            font-size: 11px;
        }
        
        .user-modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .user-modal-footer .btn {
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
        
        .user-modal-footer .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            border: 1px solid rgba(59, 130, 246, 0.6);
        }
        
        .user-modal-footer .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .user-modal-footer .btn-outline {
            background: rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-modal-footer .btn-outline:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        /* Enhanced password toggle */
        .password-input-container {
            position: relative;
        }
        
        .password-input-container input {
            padding-right: 45px;
        }
        
        .password-toggle {
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
            width: 32px;
            height: 32px;
            border-radius: 6px;
            transition: all 0.2s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #e5e7eb;
        }
        
        .password-toggle.show,
        .password-input-container:has(input[type="password"]:not(:placeholder-shown)) .password-toggle,
        .password-input-container:has(input[type="text"][id="password"]:not(:placeholder-shown)) .password-toggle {
            display: inline-flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .user-modal-enhanced {
                max-width: 95%;
                padding: 20px;
            }
        }
        
        /* Enhanced Styles */
		.panel-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 1rem;
		}
		
		.search-form {
			display: flex;
			gap: 0.5rem;
			align-items: center;
		}
		
		.search-input {
			padding: 10px 12px;
			border: 1px solid rgba(255, 255, 255, 0.1);
			border-radius: 6px;
			min-width: 200px;
			background: rgba(255, 255, 255, 0.05);
			color: #e5e7eb;
			font-size: 14px;
			transition: all 0.2s ease;
		}
		
		.search-input:focus {
			outline: none;
			border-color: #3b82f6;
			background: rgba(255, 255, 255, 0.08);
		}
		
		.search-input::placeholder {
			color: #6b7280;
		}
		
		.table th a {
			color: inherit;
			text-decoration: none;
			display: flex;
			align-items: center;
			gap: 0.25rem;
		}
		
		.table th a:hover {
			color: #007bff;
		}
		
		.role-badge, .status-badge {
			padding: 0.25rem 0.5rem;
			border-radius: 12px;
			font-size: 0.75rem;
			font-weight: 500;
			text-transform: uppercase;
		}
		
		.role-admin { background: #dc3545; color: white; }
		.role-staff { background: #ffc107; color: #000; }
		.role-customer { background: #6c757d; color: white; }
		
		.status-active { background: #28a745; color: white; }
		.status-inactive { background: #6c757d; color: white; }
		
		.actions {
			display: flex;
			gap: 0.25rem;
		}
		
		.btn-sm {
			padding: 0.25rem 0.5rem;
			font-size: 0.75rem;
		}
		
		.pagination {
			display: grid;
			grid-template-columns: 1fr auto 1fr;
			align-items: center;
			gap: 8px;
			margin-top: 1rem;
			padding: 1rem 0;
		}
		.pagination .group.left { justify-self: start; display: flex; gap: 6px; }
		.pagination .group.center { justify-self: center; }
		.pagination .group.right { justify-self: end; display: flex; gap: 6px; }
		
		.page-info {
			color: #6c757d;
			font-size: 0.9rem;
		}
		
		/* Filter panel - floating dropdown */
		.search-sort{position:relative}
		.search-form{position:relative;display:flex;gap:0.5rem;align-items:center}
		#filterBtn{position:relative;background:#1e293b;border:1px solid #2c384bff;color:#e5e7eb;transition:all 0.2s ease}
		#filterBtn:hover{background:#0f172a;border-color:#2c384bff}
		#filterBtn:active{transform:scale(0.95)}
		.filter-panel{
			display:none;
			position:absolute;
			top:-40px;
			bottom: auto;
			left: 260px;
			z-index:2000;
			background:#1e293b;
			border:2px solid #2c384bff;
			border-radius:12px;
			box-shadow:0 12px 28px rgba(0,0,0,.35), 0 0 20px rgba(6, 182, 212, 0.2);
			padding:8px;
			min-width:480px;
			display:grid;
			grid-template-columns:repeat(3,1fr);
			gap:8px;
		}
		.filter-row label{display:block;font-size:12px;color:#e5e7eb;margin-bottom:6px;font-weight:500}
		.filter-row select{width:100%;padding:10px;border:1px solid #2c384bff;border-radius:10px;background:#1e293b;color:#e5e7eb;transition:all 0.2s ease}
		.filter-row select:focus{outline:none;border-color:#2c384bff;background:#1e293b}
		
        /* Modal styles moved to shared CSS (dashboard.css) */
        
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
        
        /* Delete confirmation modal */
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
        
        .delete-modal-content {
            padding: 24px;
            max-width: 400px;
            width: 90%;
            z-index: 1001;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
		
		.warning {
			color: #dc3545;
			font-weight: 500;
		}
		
		.btn-primary { 
			background: linear-gradient(135deg, #3b82f6, #2563eb); 
			color: white; 
			border: 1px solid rgba(59, 130, 246, 0.6);
			border-radius: 6px;
			padding: 10px 16px;
			font-size: 14px;
			font-weight: 500;
			transition: all 0.2s ease;
		}
		
		.btn-primary:hover {
			background: linear-gradient(135deg, #2563eb, #1d4ed8);
			transform: translateY(-1px);
			box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
		}
		.btn-secondary { background: #6c757d; color: white; }
		.btn-warning { background: #ffc107; color: #000; }
		.btn-danger { background: #dc3545; color: white; }
		.btn-outline { background: rgba(255,255,255,.04); border: 1px solid var(--line); color: var(--text); }
		
		.btn:hover {
			opacity: 0.9;
		}
		
		/* Password toggle styles */
		.password-input-container {
			position: relative;
			display: flex;
			align-items: center;
		}
		
		.password-input-container input[type="password"],
		.password-input-container input[type="text"] {
			width: 100%;
			padding-right: 40px; /* Space for toggle button */
		}
		
		.password-toggle {
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
		
		.password-toggle:hover {
			opacity: 1;
			color: #e5e7eb;
		}
		
		.password-toggle:active {
			transform: translateY(-50%) scale(0.95);
		}
		
		.password-toggle.show,
		.password-input-container:has(input[type="password"]:not(:placeholder-shown)) .password-toggle,
		.password-input-container:has(input[type="text"][id="password"]:not(:placeholder-shown)) .password-toggle {
			display: inline-flex !important;
			visibility: visible !important;
			opacity: 1 !important;
		}

		/* ---------- Tablet & phone ---------- */
		@media (max-width: 980px) {
			/* Filter dropdown opens under the search row instead of off-screen to the right. */
			.filter-panel {
				top: calc(100% + 8px);
				left: 0;
				min-width: 0;
				width: min(480px, 100%);
			}
		}

		/* ---------- Phones (shared card / button rules live in mobile.css) ---------- */
		@media (max-width: 640px) {
			.table td:first-child:not([colspan]) {
				display: none;
			}
			.table td:nth-child(2) {
				font-size: 15px;
				font-weight: 600;
			}
			.password-toggle {
				width: 40px;
				height: 40px;
			}
			.password-input-container input[type="password"],
			.password-input-container input[type="text"] {
				padding-right: 56px;
			}
		}

	</style>

	<script>
		// Client-side live filter (no server requests while typing)
		(function(){
			var form = document.querySelector('.search-form');
			if(!form) return;
			var searchInput = form.querySelector('input[name="search"]');
			var table = document.querySelector('#usersSection .table');
			var tbody = table ? table.querySelector('tbody') : null;
			var filterBtn = document.getElementById('filterBtn');
			var filterPanel = document.getElementById('filterPanel');
			var roleSel = document.getElementById('filterRole');
			var statusSel = document.getElementById('filterStatus');
			var typeSel = document.getElementById('filterType');
			function normalize(s){ return (s||'').toString().toLowerCase(); }
			function filterRows(){
				if(!tbody) return;
				var q = normalize(searchInput.value);
				var r = normalize(roleSel && roleSel.value);
				var st = statusSel && statusSel.value; // exact label
				var t = typeSel && typeSel.value;
				Array.prototype.forEach.call(tbody.querySelectorAll('tr'), function(tr){
					var text = normalize(tr.textContent);
					var passText = !q || text.indexOf(q) !== -1;
					var passRole = !r || (tr.querySelector('.role-badge') && normalize(tr.querySelector('.role-badge').textContent) === r);
					var passStatus = !st || (tr.querySelector('.status-badge') && tr.querySelector('.status-badge').textContent.trim() === st);
					var passType = !t || (tr.children[6] && tr.children[6].textContent.trim() === t);
					tr.style.display = (passText && passRole && passStatus && passType) ? '' : 'none';
				});
			}
			searchInput && searchInput.addEventListener('input', filterRows);
			roleSel && roleSel.addEventListener('change', filterRows);
			statusSel && statusSel.addEventListener('change', filterRows);
			typeSel && typeSel.addEventListener('change', filterRows);
			filterBtn && filterBtn.addEventListener('click', function(e){
				e.stopPropagation();
				if (!filterPanel) return;
				filterPanel.style.display = (filterPanel.style.display === 'none' || !filterPanel.style.display) ? 'grid' : 'none';
			});
			document.addEventListener('click', function(e){
				if (!filterPanel || !filterBtn) return;
				if (!filterPanel.contains(e.target) && !filterBtn.contains(e.target)) {
					filterPanel.style.display = 'none';
				}
			});
		})();

		// Modal functions
		function openCreateModal() {
			document.getElementById('modalTitle').textContent = 'Add User';
			const subtitle = document.querySelector('.user-modal-header .modal-subtitle');
			if (subtitle) subtitle.textContent = 'Create a new system user';
			document.getElementById('formAction').value = 'create';
			document.getElementById('userId').value = '';
			document.getElementById('userForm').reset();
			clearUserValidationErrors();
			touchedUserFields.clear(); // Clear touched fields
			document.getElementById('password').required = true;
			document.getElementById('confirm_password').required = true;
			document.getElementById('passwordNote').textContent = '*';
			document.getElementById('confirmPasswordNote').textContent = '*';
			document.getElementById('passwordHelp').style.display = 'none';
			document.getElementById('statusGroup').style.display = 'none';
			const submitBtn = document.getElementById('submitBtn');
			// Initialize button as disabled since form is empty
			submitBtn.disabled = true;
			submitBtn.style.opacity = '0.5';
			submitBtn.style.cursor = 'not-allowed';
			if (!submitBtn.querySelector('span')) {
				submitBtn.innerHTML = '<i class="fas fa-check"></i> <span>Create User</span>';
			} else {
				submitBtn.querySelector('span').textContent = 'Create User';
			}
			if (window.animateModalOpen) {
				animateModalOpen('#userModal');
			} else {
				document.getElementById('userModal').style.display = 'flex';
			}
			
			// Setup password toggle after modal is fully rendered
			setTimeout(function() {
				setupPasswordToggle();
			}, 150);
		}
		
        function editUser(userId) {
            // Fetch user data via AJAX instead of redirecting
            fetch('?action=get_user&id=' + userId)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('modalTitle').textContent = 'Edit User';
                    const subtitle = document.querySelector('.user-modal-header .modal-subtitle');
                    if (subtitle) subtitle.textContent = 'Update user information';
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('full_name').value = user.full_name;
                    document.getElementById('email').value = user.email;
                    document.getElementById('phone').value = user.phone || '';
                    document.getElementById('username').value = user.username;
                    document.getElementById('role').value = user.role;
                    document.getElementById('user_type').value = user.user_type || 'Regular';
                    document.getElementById('is_active').value = user.is_active;
                    document.getElementById('password').required = false;
                    document.getElementById('password').value = ''; // Clear password field for security
                    document.getElementById('confirm_password').required = false;
                    document.getElementById('confirm_password').value = ''; // Clear confirm password field
                    document.getElementById('passwordNote').textContent = '';
                    document.getElementById('confirmPasswordNote').textContent = '';
                    document.getElementById('passwordHelp').style.display = 'block';
                    document.getElementById('statusGroup').style.display = 'block';
					clearUserValidationErrors();
					touchedUserFields.clear(); // Clear touched fields
					// Initialize button as disabled
					const submitBtn = document.getElementById('submitBtn');
					submitBtn.disabled = true;
					submitBtn.style.opacity = '0.5';
					submitBtn.style.cursor = 'not-allowed';
					if (!submitBtn.querySelector('span')) {
						submitBtn.innerHTML = '<i class="fas fa-check"></i> <span>Update User</span>';
					} else {
						submitBtn.querySelector('span').textContent = 'Update User';
					}
					if (window.animateModalOpen) {
						animateModalOpen('#userModal');
					} else {
						document.getElementById('userModal').style.display = 'flex';
					}
                    
                    // Setup password toggle after modal is shown and data is loaded
                    setTimeout(function() {
                        setupPasswordToggle();
                    }, 350);

                    // Store original values for comparison
                    const form = document.getElementById('userForm');
                    form.setAttribute('data-original', JSON.stringify({
                        full_name: user.full_name,
                        email: user.email,
                        phone: user.phone || '',
                        username: user.username,
                        role: user.role,
                        user_type: user.user_type || 'Regular',
                        is_active: user.is_active
                    }));

                    // Add form submission handler
                    form.onsubmit = function(e) {
                        // Prevent double submission
                        if (form.getAttribute('data-submitting')) {
                            return; // Prevent double submission
                        }

                        form.setAttribute('data-submitting', 'true');
                        document.getElementById('submitBtn').disabled = true;
                        document.getElementById('submitBtn').textContent = 'Updating...';
                    };
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading user data');
                });
		}		function closeModal() {
			if (window.animateModalClose) {
				animateModalClose('#userModal');
			} else {
				document.getElementById('userModal').style.display = 'none';
			}
		}
		
		// Update modal display style
		const userModal = document.getElementById('userModal');
		if (userModal) {
			userModal.style.display = 'none';
			userModal.style.position = 'fixed';
			userModal.style.top = '0';
			userModal.style.left = '0';
			userModal.style.width = '100%';
			userModal.style.height = '100%';
			userModal.style.background = 'rgba(0, 0, 0, 0.7)';
			userModal.style.zIndex = '1000';
			userModal.style.backdropFilter = 'blur(4px)';
			userModal.style.alignItems = 'center';
			userModal.style.justifyContent = 'center';
		}
		
		
		function deleteUser(userId, userName, userEmail, userRole) {
			document.getElementById('deleteUserName').textContent = userName;
			document.getElementById('deleteUserEmail').textContent = userEmail;
			document.getElementById('deleteUserRole').textContent = userRole;
			document.getElementById('confirmDeleteBtn').onclick = function() {
				const form = document.createElement('form');
				form.method = 'POST';
				form.innerHTML = `
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="user_id" value="${userId}">
				`;
				document.body.appendChild(form);
				form.submit();
			};
			if (window.animateModalOpen) {
				animateModalOpen('#deleteModal');
			} else {
				document.getElementById('deleteModal').style.display = 'block';
			}
		}
		
		function closeDeleteModal() {
			if (window.animateModalClose) {
				animateModalClose('#deleteModal');
			} else {
				document.getElementById('deleteModal').style.display = 'none';
			}
		}
		
		// Confirmation modal functions
		function showConfirmModal(action, details) {
			const isEdit = action === 'update';
			document.getElementById('confirmModalTitle').textContent = isEdit ? 'Confirm Update' : 'Confirm Create';
			document.getElementById('confirmModalMessage').textContent = isEdit 
				? 'Are you sure you want to update this user?' 
				: 'Are you sure you want to create this user?';
			
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
		
		// Track which fields have been touched/blurred
		const touchedUserFields = new Set();
		const duplicateCheckTimeouts = {}; // Store timeouts for duplicate checks
		
		// Set up real-time validation listeners
		const userFields = ['full_name', 'email', 'phone', 'username', 'password', 'confirm_password', 'role', 'user_type'];
		userFields.forEach(fieldId => {
			const field = document.getElementById(fieldId);
			if (field) {
				field.addEventListener('input', function() {
					updateUserSubmitButtonState();
				});
				field.addEventListener('change', function() {
					updateUserSubmitButtonState();
				});
				field.addEventListener('blur', function() {
					// Mark field as touched
					touchedUserFields.add(fieldId);
					const validationErrors = validateUserForm();
					// Only display error for this specific field
					const fieldError = {};
					if (validationErrors[fieldId]) {
						fieldError[fieldId] = validationErrors[fieldId];
					}
					displayUserValidationError(fieldError, fieldId);
					
					// For duplicate-checkable fields, check for duplicates if valid
					if ((fieldId === 'email' || fieldId === 'username' || fieldId === 'phone') && 
					    !validationErrors[fieldId]) {
						// Debounce duplicate check
						if (duplicateCheckTimeouts[fieldId]) {
							clearTimeout(duplicateCheckTimeouts[fieldId]);
						}
						duplicateCheckTimeouts[fieldId] = setTimeout(() => {
							checkDuplicateUserField(fieldId);
						}, 500);
					}
					
					updateUserSubmitButtonState();
				});
				field.addEventListener('focus', function() {
					// Clear error for this field when focused
					clearUserValidationErrorForField(fieldId);
				});
			}
		});
		
		// Special handling for phone field - numbers only, max 11 digits
		const phoneField = document.getElementById('phone');
		if (phoneField) {
			phoneField.addEventListener('input', function(e) {
				// Remove any non-digit characters
				let value = this.value.replace(/[^0-9]/g, '');
				// Limit to 11 digits
				if (value.length > 11) {
					value = value.slice(0, 11);
				}
				this.value = value;
			});
			
			// Prevent non-numeric characters
			phoneField.addEventListener('keypress', function(e) {
				if (!/[0-9]/.test(e.key)) {
					e.preventDefault();
				}
			});
		}
		
		// Check for duplicate email, username, or phone
		function checkDuplicateUser() {
			const email = document.getElementById('email').value.trim();
			const username = document.getElementById('username').value.trim();
			const phone = document.getElementById('phone').value.trim();
			const userId = document.getElementById('userId').value;
			
			// Only check if required fields are filled
			if (email === '' || username === '') {
				return Promise.resolve(false);
			}
			
			const params = new URLSearchParams();
			if (email) params.append('email', email);
			if (username) params.append('username', username);
			if (phone) params.append('phone', phone);
			if (userId) params.append('user_id', userId);
			
			return fetch('{{ route('api.check-duplicate-user') }}?' + params.toString())
				.then(response => response.json())
				.then(data => {
					if (data.isDuplicate) {
						const duplicateErrors = {};
						
						// Build error messages for duplicates
						if (data.duplicates.email) {
							duplicateErrors['email'] = 'Information already existed (email)';
						}
						if (data.duplicates.username) {
							duplicateErrors['username'] = 'Information already existed (username)';
						}
						if (data.duplicates.phone) {
							duplicateErrors['phone'] = 'Information already existed (phone number)';
						}
						
						// Display the duplicate errors
						displayUserValidationErrors(duplicateErrors);
						
						return true; // Duplicate found
					}
					return false; // No duplicate
				})
				.catch(error => {
					console.error('Error checking duplicates:', error);
					return false;
				});
		}
		
		// Check for duplicate in a specific field only
		function checkDuplicateUserField(fieldId) {
			const email = fieldId === 'email' ? document.getElementById('email').value.trim() : '';
			const username = fieldId === 'username' ? document.getElementById('username').value.trim() : '';
			const phone = fieldId === 'phone' ? document.getElementById('phone').value.trim() : '';
			const userId = document.getElementById('userId').value;
			
			// Skip if field is empty or invalid
			if (!email && !username && !phone) {
				return;
			}
			
			const params = new URLSearchParams();
			if (email) params.append('email', email);
			if (username) params.append('username', username);
			if (phone) params.append('phone', phone);
			if (userId) params.append('user_id', userId);
			
			fetch('{{ route('api.check-duplicate-user') }}?' + params.toString())
				.then(response => response.json())
				.then(data => {
					if (data.isDuplicate && data.duplicates[fieldId]) {
						const errorMessage = fieldId === 'phone' 
							? 'Information already existed including the phone number'
							: (fieldId === 'email' ? 'Information already existed (email)' : 'Information already existed (username)');
						
						displayUserValidationError({[fieldId]: errorMessage}, fieldId);
					} else {
						// Clear error if no duplicate
						clearUserValidationErrorForField(fieldId);
					}
					updateUserSubmitButtonState();
				})
				.catch(error => console.error('Error checking duplicate:', error));
		}
		
		// Intercept form submission
		document.getElementById('userForm').addEventListener('submit', function(e) {
			e.preventDefault();
			
			// Validate before showing confirmation
			const validationErrors = validateUserForm();
			if (Object.keys(validationErrors).length > 0) {
				// Mark all fields as touched and display all errors on submit
				userFields.forEach(fieldId => touchedUserFields.add(fieldId));
				displayUserValidationErrors(validationErrors);
				updateUserSubmitButtonState();
				return;
			}
			
			clearUserValidationErrors();
			
			// Check for duplicates
			checkDuplicateUser().then(hasDuplicate => {
				if (hasDuplicate) {
					updateUserSubmitButtonState();
					return;
				}
				
				const action = document.getElementById('formAction').value;
				const fullName = document.getElementById('full_name').value;
				const email = document.getElementById('email').value;
				const username = document.getElementById('username').value;
				const role = document.getElementById('role').value;
				const userType = document.getElementById('user_type').value;
				const isActive = document.getElementById('is_active') ? document.getElementById('is_active').value : '1';
				
				let details = '<div><strong>Full Name:</strong> ' + fullName + '</div>';
				details += '<div><strong>Email:</strong> ' + email + '</div>';
				details += '<div><strong>Username:</strong> ' + username + '</div>';
				details += '<div><strong>Role:</strong> ' + role + '</div>';
				details += '<div><strong>User Type:</strong> ' + userType + '</div>';
				if (action === 'update') {
					details += '<div><strong>Status:</strong> ' + (isActive === '1' ? 'Active' : 'Disabled') + '</div>';
				}
				
				showConfirmModal(action, details);
				
				// Store form reference for submission after confirmation
				window.pendingUserForm = this;
			});
		});
		
		// Handle confirmation
		document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
			if (window.pendingUserForm) {
				hideConfirmModal();
				closeModal();
				window.pendingUserForm.submit();
				window.pendingUserForm = null;
			}
		});
		
		// Close modals when clicking outside
		window.onclick = function(event) {
			const userModal = document.getElementById('userModal');
			const deleteModal = document.getElementById('deleteModal');
			const confirmModal = document.getElementById('confirmModal');
			if (event.target === userModal) {
				closeModal();
			}
			if (event.target === deleteModal) {
				closeDeleteModal();
			}
			if (event.target === confirmModal) {
				hideConfirmModal();
			}
		}
		
		// No automatic modal opening - only open when user clicks edit button
		
		// ============ VALIDATION FUNCTIONS ============
		function validateUserForm() {
			const errors = {};
			
			const fullName = document.getElementById('full_name').value.trim();
			const email = document.getElementById('email').value.trim();
			const phone = document.getElementById('phone').value.trim();
			const username = document.getElementById('username').value.trim();
			const password = document.getElementById('password').value.trim();
			const confirmPassword = document.getElementById('confirm_password').value.trim();
			const role = document.getElementById('role').value.trim();
			const userType = document.getElementById('user_type').value.trim();
			const isEditMode = document.getElementById('formAction').value === 'update';
			
			// Full name validation
			if (fullName === '') {
				errors['full_name'] = 'Full name is required.';
			} else if (fullName.length > 120) {
				errors['full_name'] = 'Full name must be less than 120 characters.';
			}
			
			// Email validation
			if (email === '') {
				errors['email'] = 'Email is required.';
			} else if (!isValidEmail(email)) {
				errors['email'] = 'Valid email address is required.';
			} else if (email.length > 160) {
				errors['email'] = 'Email must be less than 160 characters.';
			}
			
			// Phone validation
			if (phone !== '' && !/^[0-9]{11}$/.test(phone)) {
				errors['phone'] = 'Phone number must be exactly 11 digits.';
			}
			
			// Username validation
			if (username === '') {
				errors['username'] = 'Username is required.';
			} else if (username.length < 3) {
				errors['username'] = 'Username must be at least 3 characters.';
			} else if (username.length > 60) {
				errors['username'] = 'Username must be less than 60 characters.';
			} else if (!/^[a-zA-Z0-9_]+$/.test(username)) {
				errors['username'] = 'Username can only contain letters, numbers, and underscores.';
			}
			
			// Password validation
			if (isEditMode) {
				// In edit mode, password is optional unless user fills it
				if (password !== '') {
					if (password.length < 6) {
						errors['password'] = 'Password must be at least 6 characters.';
					} else if (password.length > 128) {
						errors['password'] = 'Password must be less than 128 characters.';
					}
					
					if (confirmPassword === '') {
						errors['confirm_password'] = 'Please confirm your password.';
					} else if (password !== confirmPassword) {
						errors['confirm_password'] = 'Passwords do not match.';
					}
				}
			} else {
				// In create mode, password is required
				if (password === '') {
					errors['password'] = 'Password is required.';
				} else if (password.length < 6) {
					errors['password'] = 'Password must be at least 6 characters.';
				} else if (password.length > 128) {
					errors['password'] = 'Password must be less than 128 characters.';
				}
				
				if (confirmPassword === '') {
					errors['confirm_password'] = 'Please confirm your password.';
				} else if (password !== confirmPassword) {
					errors['confirm_password'] = 'Passwords do not match.';
				}
			}
			
			// Role validation
			if (!['admin', 'staff', 'customer'].includes(role)) {
				errors['role'] = 'Invalid role selected.';
			}
			
			// User type validation
			if (!['Regular', 'Student', 'Senior Citizen/PWD'].includes(userType)) {
				errors['user_type'] = 'Invalid user type selected.';
			}
			
			return errors;
		}
		
		function isValidEmail(email) {
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			return emailRegex.test(email);
		}
		
		function displayUserValidationErrors(errors) {
			// Clear previous errors first
			clearUserValidationErrors();
			
			for (let fieldId in errors) {
				const field = document.getElementById(fieldId);
				if (field) {
					const errorElement = document.createElement('span');
					errorElement.className = 'error user-error-' + fieldId;
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
		
		function clearUserValidationErrors() {
			// Remove all error messages
			const fieldIds = ['full_name', 'email', 'phone', 'username', 'password', 'confirm_password', 'role', 'user_type'];
			document.querySelectorAll('.user-error-' + fieldIds.join(', .user-error-')).forEach(el => el.remove());
			
			// Reset field styling
			fieldIds.forEach(fieldId => {
				const field = document.getElementById(fieldId);
				if (field) {
					field.style.borderColor = 'rgba(255, 255, 255, 0.08)';
					field.style.background = 'rgba(255, 255, 255, 0.04)';
				}
			});
		}
		
		// Display error for a specific field only
		function displayUserValidationError(errors, fieldId) {
			// Clear previous error for this field
			clearUserValidationErrorForField(fieldId);
			
			if (errors[fieldId]) {
				const field = document.getElementById(fieldId);
				if (field) {
					const errorElement = document.createElement('span');
					errorElement.className = 'error user-error-' + fieldId;
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
		
		// Clear error for a specific field
		function clearUserValidationErrorForField(fieldId) {
			document.querySelectorAll('.user-error-' + fieldId).forEach(el => el.remove());
			
			const field = document.getElementById(fieldId);
			if (field) {
				field.style.borderColor = 'rgba(255, 255, 255, 0.08)';
				field.style.background = 'rgba(255, 255, 255, 0.04)';
			}
		}
		
		function updateUserSubmitButtonState() {
			const validationErrors = validateUserForm();
			const submitBtn = document.getElementById('submitBtn');
			
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
		
		// Password toggle functionality
		var passwordToggleInitialized = false;
		var passwordToggleClickHandler = null;
		var passwordToggleUpdateHandler = null;
		var confirmPasswordToggleClickHandler = null;
		var confirmPasswordToggleUpdateHandler = null;
		
		function setupPasswordToggle() {
			var passwordInput = document.getElementById('password');
			var passwordToggle = document.getElementById('passwordToggle');
			var confirmPasswordInput = document.getElementById('confirm_password');
			var confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
			
			if (!passwordInput || !passwordToggle) return;
			
			// Remove old event listeners if they exist
			if (passwordToggleClickHandler) {
				passwordToggle.removeEventListener('click', passwordToggleClickHandler);
			}
			if (passwordToggleUpdateHandler) {
				passwordInput.removeEventListener('input', passwordToggleUpdateHandler);
				passwordInput.removeEventListener('blur', passwordToggleUpdateHandler);
				passwordInput.removeEventListener('focus', passwordToggleUpdateHandler);
				passwordInput.removeEventListener('change', passwordToggleUpdateHandler);
			}
			if (confirmPasswordToggleClickHandler && confirmPasswordToggle) {
				confirmPasswordToggle.removeEventListener('click', confirmPasswordToggleClickHandler);
			}
			if (confirmPasswordToggleUpdateHandler && confirmPasswordInput) {
				confirmPasswordInput.removeEventListener('input', confirmPasswordToggleUpdateHandler);
				confirmPasswordInput.removeEventListener('blur', confirmPasswordToggleUpdateHandler);
				confirmPasswordInput.removeEventListener('focus', confirmPasswordToggleUpdateHandler);
				confirmPasswordInput.removeEventListener('change', confirmPasswordToggleUpdateHandler);
			}
			
			// Ensure positioning and transform are set immediately
			passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
			passwordToggle.style.setProperty('position', 'absolute', 'important');
			passwordToggle.style.setProperty('right', '12px', 'important');
			passwordToggle.style.setProperty('top', '50%', 'important');
			
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
			
			// Store handler reference
			passwordToggleUpdateHandler = updateToggle;
			
			// Set up click handler for toggle
			passwordToggleClickHandler = function (e) {
				e.preventDefault();
				e.stopPropagation();
				var isPassword = passwordInput.getAttribute('type') === 'password';
				passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
				passwordToggle.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
				passwordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
				passwordInput.focus();
			};
			
			passwordToggle.addEventListener('click', passwordToggleClickHandler);
			
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
			
			// Setup confirm password toggle if it exists
			if (confirmPasswordInput && confirmPasswordToggle) {
				function updateConfirmToggle() {
					var hasValue = confirmPasswordInput.value && confirmPasswordInput.value.length > 0;
					if (hasValue) {
						confirmPasswordToggle.classList.add('show');
						confirmPasswordToggle.style.setProperty('display', 'inline-flex', 'important');
						confirmPasswordToggle.style.setProperty('visibility', 'visible', 'important');
						confirmPasswordToggle.style.setProperty('opacity', '1', 'important');
						confirmPasswordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
					} else {
						confirmPasswordToggle.classList.remove('show');
						confirmPasswordToggle.style.setProperty('display', 'none', 'important');
						confirmPasswordToggle.style.setProperty('visibility', 'hidden', 'important');
						confirmPasswordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
					}
				}
				
				confirmPasswordToggleUpdateHandler = updateConfirmToggle;
				
				confirmPasswordToggleClickHandler = function (e) {
					e.preventDefault();
					e.stopPropagation();
					var isPassword = confirmPasswordInput.getAttribute('type') === 'password';
					confirmPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
					confirmPasswordToggle.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
					confirmPasswordToggle.style.setProperty('transform', 'translateY(-50%)', 'important');
					confirmPasswordInput.focus();
				};
				
				confirmPasswordToggle.addEventListener('click', confirmPasswordToggleClickHandler);
				confirmPasswordInput.addEventListener('input', updateConfirmToggle);
				confirmPasswordInput.addEventListener('blur', updateConfirmToggle);
				confirmPasswordInput.addEventListener('focus', updateConfirmToggle);
				confirmPasswordInput.addEventListener('change', updateConfirmToggle);
				confirmPasswordInput.addEventListener('paste', function() {
					setTimeout(updateConfirmToggle, 10);
				});
				confirmPasswordInput.addEventListener('keydown', function() {
					setTimeout(updateConfirmToggle, 0);
				});
				updateConfirmToggle();
				setTimeout(updateConfirmToggle, 100);
			}
			
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
			
			passwordToggleInitialized = true;
		}
		
		// Also setup on page load in case modal is already open
		document.addEventListener('DOMContentLoaded', function() {
			setTimeout(setupPasswordToggle, 500);
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



