<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesBookingForm;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\BookingService;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class BookingController extends Controller
{
    use HandlesBookingForm;

    public function __construct(protected BookingService $bookings, private MaintenanceService $maintenance) {}

    public function index(): View
    {
        $this->maintenance->runScheduled();

        return $this->render();
    }

    public function store(Request $request): View
    {
        $this->maintenance->runScheduled();

        if ($request->has('cancel_booking')) {
            return $this->cancel($request);
        }

        if ($request->has('delete_booking')) {
            return $this->delete($request);
        }

        return $this->create($request);
    }

    private function cancel(Request $request): View
    {
        $bookingId = $request->integer('booking_id');

        if ($bookingId <= 0) {
            return $this->render();
        }

        $result = $this->bookings->cancel($bookingId);

        if (! $result['success']) {
            return $this->render(errors: ['general' => $result['message']]);
        }

        $code = $result['booking']->booking_code;
        ActivityLogger::log('booking_cancel', ['action' => 'CANCEL', 'target_type' => 'booking', 'target_id' => $bookingId, 'booking_code' => $code]);
        ActivityLogger::log('admin_booking_cancel', [
            'by_admin' => $request->user()->id, 'booking_id' => $bookingId,
            'booking_code' => $code, 'refund_processed' => $result['refunded'],
        ]);

        return $this->render(ok: e($result['message']));
    }

    private function delete(Request $request): View
    {
        $booking = Booking::find($request->integer('booking_id'));

        if (! $booking) {
            return $this->render(errors: ['general' => 'Booking not found.']);
        }

        if ($booking->status !== 'completed') {
            return $this->render(errors: ['general' => 'Can only delete completed bookings.']);
        }

        try {
            DB::transaction(function () use ($booking) {
                $this->bookings->archive('id', [$booking->id]);
                $booking->delete();
            });
        } catch (Throwable $exception) {
            ActivityLogger::log('booking_delete_failed', ['error' => $exception->getMessage(), 'booking_id' => $booking->id]);

            return $this->render(errors: ['general' => 'Failed to delete booking: '.$exception->getMessage()]);
        }

        ActivityLogger::log('booking_delete', ['booking_id' => $booking->id, 'booking_code' => $booking->booking_code]);

        return $this->render(ok: 'Booking '.e($booking->booking_code).' has been deleted and archived.');
    }

    private function create(Request $request): View
    {
        $trips = $this->bookableTrips();
        $tripId = trim((string) $request->input('trip_id'));
        $seatNumber = trim((string) $request->input('seat_number'));
        $amount = trim((string) $request->input('amount'));
        $userIdInput = trim((string) $request->input('user_id'));

        $customer = ctype_digit($userIdInput) ? User::find((int) $userIdInput) : null;
        $passengerName = (string) ($customer?->full_name ?? '');
        $userType = $customer?->user_type ?: 'Regular';

        $errors = [];

        if (! ctype_digit($tripId)) {
            $errors['trip_id'] = 'Trip is required.';
        }

        if ($passengerName === '') {
            $errors['passenger_name'] = 'Please select a customer account or provide a passenger name.';
        }

        $errors += $this->validateBookingFields($tripId, $seatNumber, $amount, $trips, $userType);

        if ($errors) {
            return $this->render(errors: $errors, trips: $trips);
        }

        $result = $this->bookings->createBooking([
            'user_id' => $customer?->id,
            'trip_id' => (int) $tripId,
            'passenger_name' => $passengerName,
            'passenger_phone' => $customer?->phone ?: null,
            'seat_number' => (int) $seatNumber,
            'amount' => (float) $amount,
        ]);

        if (! $result['success']) {
            ActivityLogger::log('booking_create_failed', [
                'action' => 'CREATE', 'target_type' => 'booking', 'error' => $result['error'], 'status' => 'failed',
                'passenger_name' => $passengerName, 'trip_id' => $tripId,
            ]);

            return $this->render(errors: ['general' => $result['error']], trips: $trips);
        }

        ActivityLogger::log('booking_create', [
            'action' => 'CREATE', 'target_type' => 'booking', 'target_id' => $result['booking_id'], 'booking_code' => $result['booking_code'],
            'passenger_name' => $passengerName, 'seat_number' => $seatNumber, 'trip_id' => $tripId, 'amount' => $amount,
        ]);

        $trip = $this->findTrip($trips, $tripId);

        return $this->render(
            ok: 'Booking created: '.e($result['booking_code']),
            receipt: $trip ? $this->receipt($result['booking_code'], $trip, $passengerName, $seatNumber, $amount, $userType) : null,
        );
    }

    /**
     * @param  array<string, string>  $errors
     * @param  list<array<string, mixed>>|null  $trips
     * @param  array<string, mixed>|null  $receipt
     */
    private function render(string $ok = '', array $errors = [], ?array $trips = null, ?array $receipt = null): View
    {
        return view('admin.bookings', [
            'trips' => $trips ?? $this->bookableTrips(),
            'bookings' => $this->recentBookings(null),
            'ok' => $ok,
            'formErrors' => $errors,
            'receiptData' => $receipt,
        ]);
    }
}
