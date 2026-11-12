<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Records - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
    <style>
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .summary-card:hover {
            background: rgba(255,255,255,0.04);
            border-color: rgba(255,255,255,0.1);
        }

        .summary-label {
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .summary-value {
            color: #e5e7eb;
            font-size: 1.6rem;
            font-weight: 700;
        }

        .summary-value.accent {
            color: #3b82f6;
        }

        .summary-value.success {
            color: #22c55e;
        }

        .summary-value.warning {
            color: #eab308;
        }

        .summary-value.danger {
            color: #ef4444;
        }

        .search-sort {
            display: flex;
            gap: 8px;
            margin-bottom: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex: 1;
            min-width: 250px;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            color: #e5e7eb;
            font-size: 13px;
            flex: 1;
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
            white-space: nowrap;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.15);
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-group select {
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            color: #e5e7eb;
            font-size: 13px;
            cursor: pointer;
        }

        .filter-group select:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.15);
        }

        .filter-group select:focus {
            outline: none;
            border-color: #3b82f6;
            background: rgba(255,255,255,0.08);
        }

        .table-wrap {
            background: rgba(255,255,255,0.02);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: rgba(255,255,255,0.02);
            padding: 12px;
            font-size: 13px;
            font-weight: 500;
            text-align: left;
            color: #94a3b8;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .table td {
            padding: 12px;
            font-size: 13px;
            border-top: 1px solid rgba(255,255,255,0.05);
            color: #e5e7eb;
        }

        .table tbody tr:hover {
            background: rgba(255,255,255,0.02);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Delete Modal Styles */
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

        .status-paid {
            background: rgba(34,197,94,0.1);
            color: #22c55e;
        }

        .status-pending {
            background: rgba(234,179,8,0.1);
            color: #eab308;
        }

        .status-failed {
            background: rgba(239,68,68,0.1);
            color: #ef4444;
        }

        .status-refunded {
            background: rgba(251,146,60,0.1);
            color: #fb923c;
        }

        .amount {
            font-weight: 600;
            color: #3b82f6;
        }

        .amount.refunded {
            color: #fb923c;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            border: 1px solid rgba(239, 68, 68, 0.6);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* Delete Modal Styles - Removed */

        .method-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            color: #e5e7eb;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.1);
            color: #e5e7eb;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pagination a:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.2);
        }

        .pagination .active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .search-sort {
                flex-direction: column;
                align-items: stretch;
            }

            .search-form {
                min-width: 100%;
            }

            .filter-group {
                width: 100%;
            }

            .filter-group select {
                width: 100%;
            }

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px;
            }
        }

        @media (max-width: 576px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .table {
                font-size: 11px;
            }

            .table th,
            .table td {
                padding: 6px;
            }

            .status-badge {
                font-size: 0.65rem;
                padding: 2px 6px;
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
                <div class="welcome"><h2>Transaction Records</h2><p>All customer payment transactions and audit trail</p></div>
                <div class="actions">
                    <button type="button" class="btn btn-danger" onclick="showDeleteConfirmModal()">
                        <i class="fas fa-trash"></i> Delete All
                    </button>
                </div>
            </header>

            @if ($deleted)
            <div class="qa" style="justify-content:flex-start;color:#22c55e;margin:1rem 0"><i class="fas fa-check-circle"></i><span>All transactions deleted.</span></div>
            @elseif ($delete_error)
            <div class="qa" style="justify-content:flex-start;color:#ef4444;margin:1rem 0"><i class="fas fa-exclamation-circle"></i><span>{{ $delete_error }}</span></div>
            @endif

            <!-- Transactions Section -->
            <section class="panel">
                <h3><i class="fas fa-receipt"></i> All Transactions</h3>
                
                <!-- Search and Filter -->
                <div class="search-sort">
                    <form method="get" action="" class="search-form">
                        <input type="text" name="search" class="search-input" placeholder="Search booking code, passenger, username, route..." value="{{ $search_term }}">
                        <button type="submit" class="btn-secondary"><i class="fas fa-search"></i> Search</button>
                    </form>

                    <div class="filter-group">
                        <form method="get" action="" style="display: flex; gap: 6px; align-items: center;">
                            <input type="hidden" name="search" value="{{ $search_term }}">
                            <select name="status" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="paid" {!! $filter_status === 'paid' ? 'selected' : '' !!}>Paid</option>
                                <option value="pending" {!! $filter_status === 'pending' ? 'selected' : '' !!}>Pending</option>
                                <option value="failed" {!! $filter_status === 'failed' ? 'selected' : '' !!}>Failed</option>
                                <option value="refunded" {!! $filter_status === 'refunded' ? 'selected' : '' !!}>Refunded</option>
                            </select>
                        </form>
                    </div>

                    <div class="filter-group">
                        <form method="get" action="" style="display: flex; gap: 6px; align-items: center;">
                            <input type="hidden" name="search" value="{{ $search_term }}">
                            <input type="hidden" name="status" value="{{ $filter_status }}">
                            <select name="method" onchange="this.form.submit()">
                                <option value="">All Methods</option>
                                <option value="cash" {!! $filter_method === 'cash' ? 'selected' : '' !!}>Cash</option>
                                <option value="card" {!! $filter_method === 'card' ? 'selected' : '' !!}>Card</option>
                                <option value="gcash" {!! $filter_method === 'gcash' ? 'selected' : '' !!}>GCash</option>
                                <option value="paypal" {!! $filter_method === 'paypal' ? 'selected' : '' !!}>PayPal</option>
                                <option value="other" {!! $filter_method === 'other' ? 'selected' : '' !!}>Other</option>
                            </select>
                        </form>
                    </div>

                    @if ($filter_status || $filter_method || $search_term)
                    <a href="{{ route('admin.transactions') }}" class="btn-secondary"><i class="fas fa-times"></i> Clear</a>
                    @endif
                </div>

                <!-- Transaction Table -->
                @if (!$slice)
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Transactions Found</h3>
                    <p>{!! ($filter_status || $filter_method || $search_term) ? 'Try adjusting your filters.' : 'No payment transactions recorded yet.' !!}</p>
                </div>
                @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Booking Code</th>
                                <th>Customer</th>
                                <th>Passenger</th>
                                <th>Route</th>
                                <th>Departure</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slice as $payment)
                            <tr>
                                <td><strong>#{{ $payment['payment_id'] }}</strong></td>
                                <td><strong>{{ $payment['booking_code'] }}</strong></td>
                                <td>{!! $payment['username'] ? htmlspecialchars($payment['username']) : '<em>Guest</em>' !!}</td>
                                <td>{{ $payment['passenger_name'] }}</td>
                                <td>{{ $payment['route_name'] }}</td>
                                <td>{{ $payment['departure_time'] }}</td>
                                <td><span class="amount {!! $payment['payment_status'] === 'refunded' ? 'refunded' : '' !!}">₱{!! number_format((float)$payment['amount'], 2) !!}</span></td>
                                <td><span class="method-badge">{!! ucfirst($payment['method']) !!}</span></td>
                                <td><span class="status-badge status-{{ $payment['payment_status'] }}">{!! ucfirst($payment['payment_status']) !!}</span></td>
                                <td>{!! $payment['payment_status'] === 'pending' ? 'N/A' : htmlspecialchars($payment['paid_date']) !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($total_pages > 1)
                <div class="pagination">
                    @if ($page > 1)
                    <a href="?page=1{!! $filter_status ? '&status=' . urlencode($filter_status) : '' !!}{!! $filter_method ? '&method=' . urlencode($filter_method) : '' !!}{!! $search_term ? '&search=' . urlencode($search_term) : '' !!}"><i class="fas fa-angles-left"></i> First</a>
                    <a href="?page={!! $page - 1 !!}{!! $filter_status ? '&status=' . urlencode($filter_status) : '' !!}{!! $filter_method ? '&method=' . urlencode($filter_method) : '' !!}{!! $search_term ? '&search=' . urlencode($search_term) : '' !!}"><i class="fas fa-angle-left"></i> Previous</a>
                    @endif

                    @for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++)
                    <span class="{!! $i == $page ? 'active' : '' !!}">
                        <a href="?page={!! $i !!}{!! $filter_status ? '&status=' . urlencode($filter_status) : '' !!}{!! $filter_method ? '&method=' . urlencode($filter_method) : '' !!}{!! $search_term ? '&search=' . urlencode($search_term) : '' !!}">{!! $i !!}</a>
                    </span>
                    @endfor

                    @if ($page < $total_pages)
                    <a href="?page={!! $page + 1 !!}{!! $filter_status ? '&status=' . urlencode($filter_status) : '' !!}{!! $filter_method ? '&method=' . urlencode($filter_method) : '' !!}{!! $search_term ? '&search=' . urlencode($search_term) : '' !!}">Next <i class="fas fa-angle-right"></i></a>
                    <a href="?page={!! $total_pages !!}{!! $filter_status ? '&status=' . urlencode($filter_status) : '' !!}{!! $filter_method ? '&method=' . urlencode($filter_method) : '' !!}{!! $search_term ? '&search=' . urlencode($search_term) : '' !!}">Last <i class="fas fa-angles-right"></i></a>
                    @endif
                </div>
                @endif
                @endif
            </section>
        </main>
    </div>

    <!-- Delete All Transactions Modal -->
    <div id="deleteConfirmModal" class="delete-modal">
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <div class="delete-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="delete-modal-title">Delete All Transactions</h3>
            </div>
            <div class="delete-modal-body">
                <p>Are you sure you want to delete ALL transactions?</p>
                <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <div><strong>Action:</strong> Clear all payment transactions</div>
                    <div><strong>Impact:</strong> All transaction records will be permanently removed</div>
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
                    <input type="hidden" name="action" value="delete_all_transactions">
                    <button type="submit" class="delete-modal-btn delete-modal-btn-danger">
                        <i class="fas fa-trash"></i> Delete All
                    </button>
                </form>
            </div>
        </div>
    </div>

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

    <style>
        .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1002;
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease-out;
        }

        .confirm-modal.is-visible {
            opacity: 1;
            pointer-events: auto;
        }

        .confirm-modal.is-hiding {
            opacity: 0;
            pointer-events: none;
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
            z-index: 1003;
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
    </style>

    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script>
        // Delete all transactions modal
        const deleteConfirmModal = document.getElementById('deleteConfirmModal');

        function showDeleteConfirmModal() {
            if (window.animateModalOpen) {
                animateModalOpen(deleteConfirmModal);
            } else {
                deleteConfirmModal.style.display = 'flex';
            }
        }

        function hideDeleteConfirmModal() {
            if (window.animateModalClose) {
                animateModalClose(deleteConfirmModal);
            } else {
                deleteConfirmModal.style.display = 'none';
            }
        }

        // Close modal when clicking outside
        deleteConfirmModal.addEventListener('click', function(event) {
            if (event.target === deleteConfirmModal) {
                hideDeleteConfirmModal();
            }
        });
    </script>
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

        // Close modals when clicking outside
        logoutConfirmModal.addEventListener('click', function(event) {
            if (event.target === logoutConfirmModal) {
                hideLogoutConfirmModal();
            }
        });
    </script>
</body>
</html>
