<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TransactionController extends Controller
{
    private const PER_PAGE = 50;

    public function index(Request $request): View
    {
        return $this->render($request);
    }

    public function store(Request $request): View
    {
        if ($request->input('action') !== 'delete_all_transactions') {
            return $this->render($request);
        }

        try {
            $count = Payment::query()->delete();
            ActivityLogger::log('payments_delete_all', ['count' => $count]);
        } catch (Throwable $exception) {
            return $this->render($request, deleteError: 'Error: '.$exception->getMessage());
        }

        return $this->render($request, deleted: true);
    }

    private function render(Request $request, bool $deleted = false, ?string $deleteError = null): View
    {
        $status = trim((string) $request->query('status', ''));
        $method = trim((string) $request->query('method', ''));
        $search = trim((string) $request->query('search', ''));

        $conditions = [];
        $bindings = [];

        if (in_array($status, ['pending', 'paid', 'failed', 'refunded'], true)) {
            $conditions[] = 'p.status = ?';
            $bindings[] = $status;
        }

        if (in_array($method, ['cash', 'card', 'gcash', 'paypal', 'other'], true)) {
            $conditions[] = 'p.method = ?';
            $bindings[] = $method;
        }

        $where = $conditions ? 'WHERE '.implode(' AND ', $conditions) : '';

        $payments = $this->rows("SELECT p.id AS payment_id,
                p.booking_id,
                p.amount,
                p.method,
                CASE WHEN b.status IN ('cancelled', 'refunded') THEN 'refunded' ELSE p.status END AS payment_status,
                p.reference,
                DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i') AS payment_created,
                DATE_FORMAT(p.paid_at, '%Y-%m-%d %H:%i') AS paid_date,
                b.booking_code,
                b.passenger_name,
                b.status AS booking_status,
                b.seat_number,
                u.username,
                CONCAT(r.origin, ' → ', r.destination) AS route_name,
                DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS departure_time
            FROM payments p
            JOIN bookings b ON b.id = p.booking_id
            JOIN trips t ON t.id = b.trip_id
            JOIN routes r ON r.id = t.route_id
            LEFT JOIN users u ON u.id = b.user_id
            {$where}
            ORDER BY p.created_at DESC", $bindings);

        if ($search !== '') {
            $needle = strtolower($search);
            $payments = array_values(array_filter($payments, fn (array $p): bool => str_contains(
                strtolower($p['booking_code'].' '.$p['passenger_name'].' '.$p['username'].' '.$p['route_name']),
                $needle
            )));
        }

        $summary = [
            'total_revenue' => 0.0, 'total_refunded' => 0.0, 'total_transactions' => count($payments),
            'paid_count' => 0, 'pending_count' => 0, 'refunded_count' => 0, 'failed_count' => 0,
        ];

        foreach ($payments as $payment) {
            $paymentStatus = $payment['payment_status'];

            if ($paymentStatus === 'paid') {
                $summary['total_revenue'] += (float) $payment['amount'];
            } elseif ($paymentStatus === 'refunded') {
                $summary['total_refunded'] += (float) $payment['amount'];
            }

            if (isset($summary[$paymentStatus.'_count'])) {
                $summary[$paymentStatus.'_count']++;
            }
        }

        $page = max(1, $request->integer('page', 1));
        $total = count($payments);

        return view('admin.transactions', [
            'username' => $request->user()->username,
            'deleted' => $deleted,
            'delete_error' => $deleteError,
            'filter_status' => $status,
            'filter_method' => $method,
            'search_term' => $search,
            'page' => $page,
            'total' => $total,
            'total_pages' => max(1, (int) ceil($total / self::PER_PAGE)),
            'slice' => array_slice($payments, ($page - 1) * self::PER_PAGE, self::PER_PAGE),
            'summary' => $summary,
            'payment_history' => $payments,
        ]);
    }
}
