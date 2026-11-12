<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    private const PER_PAGE = 50;

    public function index(Request $request): View
    {
        $user = $request->user();
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));

        $payments = array_map(function (array $row): array {
            if ($row['booking_status'] === 'cancelled' && in_array($row['status'], ['paid', 'pending'], true)) {
                $row['status'] = 'refunded';
            }

            return $row;
        }, $this->rows("SELECT p.*,
                b.booking_code, b.passenger_name, b.status AS booking_status,
                CONCAT(r.origin, ' → ', r.destination) AS route_name,
                DATE_FORMAT(t.departure_time, '%Y-%m-%d %H:%i') AS departure_time,
                DATE_FORMAT(p.created_at, '%Y-%m-%d %H:%i') AS payment_date,
                DATE_FORMAT(p.paid_at, '%Y-%m-%d %H:%i') AS paid_date
            FROM payments p
            JOIN bookings b ON b.id = p.booking_id
            JOIN trips t ON t.id = b.trip_id
            JOIN routes r ON r.id = t.route_id
            WHERE b.user_id = ?
            ORDER BY p.created_at DESC", [$user->id]));

        $filtered = array_values(array_filter($payments, fn (array $p): bool => ($status === '' || $p['status'] === $status)
            && ($search === '' || stripos((string) json_encode($p), $search) !== false)));

        $page = max(1, $request->integer('page', 1));
        $total = count($filtered);

        return view('customer.transactions', [
            'username' => $user->username,
            'full_name' => $user->full_name ?: $user->username,
            'filter_status' => $status,
            'search_term' => $search,
            'page' => $page,
            'total' => $total,
            'total_pages' => max(1, (int) ceil($total / self::PER_PAGE)),
            'slice' => array_slice($filtered, ($page - 1) * self::PER_PAGE, self::PER_PAGE),
        ]);
    }

    /**
     * Delete one of the user's payment records, or all of them.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $ownPayments = Payment::whereHas('booking', fn ($query) => $query->where('user_id', $user->id));

        if ($request->has('delete_transaction')) {
            $paymentId = $request->integer('payment_id');

            if ((clone $ownPayments)->whereKey($paymentId)->delete() > 0) {
                ActivityLogger::log('customer_payment_delete', ['payment_id' => $paymentId, 'user_id' => $user->id]);

                return redirect()->route('customer.transactions', ['deleted' => 1]);
            }
        }

        if ($request->input('action') === 'delete_all_transactions') {
            $count = $ownPayments->delete();
            ActivityLogger::log('customer_payment_delete_all', ['user_id' => $user->id, 'count' => $count]);

            return redirect()->route('customer.transactions', ['deleted_all' => 1]);
        }

        return redirect()->route('customer.transactions');
    }
}
