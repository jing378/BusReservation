<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Concerns\HandlesBookingForm;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\BookingService;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    use HandlesBookingForm;

    public function __construct(protected BookingService $bookings, private MaintenanceService $maintenance) {}

    public function index(Request $request): View
    {
        $this->maintenance->runScheduled();

        return $this->render($request);
    }

    public function store(Request $request): View
    {
        $this->maintenance->runScheduled();

        return $request->has('cancel_booking') ? $this->cancel($request) : $this->create($request);
    }

    private function cancel(Request $request): View
    {
        $bookingId = $request->integer('booking_id');
        $userId = $request->user()->id;

        if ($bookingId <= 0) {
            return $this->render($request);
        }

        $result = $this->bookings->cancel($bookingId, $userId);

        if (! $result['success']) {
            return $this->render($request, errors: ['general' => $result['message']]);
        }

        ActivityLogger::log('customer_booking_cancel', [
            'by_user_id' => $userId, 'booking_id' => $bookingId,
            'booking_code' => $result['booking']->booking_code, 'refund_processed' => $result['refunded'],
        ]);

        return $this->render($request, ok: e($result['message']));
    }

    private function create(Request $request): View
    {
        $user = $request->user();
        $trips = $this->bookableTrips();
        $tripId = trim((string) $request->input('trip_id'));
        $seatNumber = trim((string) $request->input('seat_number'));
        $amount = trim((string) $request->input('amount'));
        $userType = $user->user_type ?: 'Regular';

        $errors = $this->validateBookingFields($tripId, $seatNumber, $amount, $trips, $userType);

        if (! $errors && trim((string) $user->full_name) === '') {
            $errors['general'] = 'Unable to retrieve your profile information. Please update your profile and try again.';
        }

        if ($errors) {
            return $this->render($request, errors: $errors, trips: $trips);
        }

        $result = $this->bookings->createBooking([
            'user_id' => $user->id,
            'trip_id' => (int) $tripId,
            'passenger_name' => $user->full_name,
            'passenger_phone' => $user->phone ?: null,
            'seat_number' => (int) $seatNumber,
            'amount' => (float) $amount,
        ]);

        if (! $result['success']) {
            ActivityLogger::log('customer_booking_create_failed', [
                'by_user_id' => $user->id, 'error' => $result['error'], 'trip_id' => $tripId, 'seat_number' => $seatNumber,
            ]);

            return $this->render($request, errors: ['general' => $result['error']], trips: $trips);
        }

        ActivityLogger::log('customer_booking_create', [
            'by_user_id' => $user->id, 'booking_id' => $result['booking_id'], 'booking_code' => $result['booking_code'],
            'trip_id' => $tripId, 'seat_number' => $seatNumber, 'amount' => $amount,
        ]);

        $trip = $this->findTrip($trips, $tripId);

        return $this->render(
            $request,
            ok: 'Booking created: '.e($result['booking_code']),
            receipt: $trip ? $this->receipt($result['booking_code'], $trip, $user->full_name, $seatNumber, $amount, $userType) : null,
        );
    }

    /**
     * Admins visiting this page see every booking, customers only their own.
     *
     * @param  array<string, string>  $errors
     * @param  list<array<string, mixed>>|null  $trips
     * @param  array<string, mixed>|null  $receipt
     */
    private function render(Request $request, string $ok = '', array $errors = [], ?array $trips = null, ?array $receipt = null): View
    {
        $user = $request->user();

        return view('customer.bookings', [
            'trips' => $trips ?? $this->bookableTrips(),
            'bookings' => $this->recentBookings($user->isAdmin() ? null : $user->id),
            'user_type' => $user->user_type ?: 'Regular',
            'ok' => $ok,
            'formErrors' => $errors,
            'receiptData' => $receipt,
        ]);
    }
}
