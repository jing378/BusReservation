<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class BookingService
{
    /** Students, seniors and PWD get 20% off. */
    public const DISCOUNTED_USER_TYPES = ['Student', 'Senior Citizen/PWD'];

    public const DISCOUNT_PERCENT = 20;

    public function generateBookingCode(): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $code = 'BK-'.time().'-'.random_int(1000, 9999);

            if (! Booking::where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        return 'BK-'.time().'-'.Str::lower(Str::random(6));
    }

    /**
     * @return array{original_price: float, discounted_price: float, discount_percent: int, discount_amount: float}
     */
    public function calculateDiscount(float $basePrice, ?string $userType): array
    {
        $discountPercent = in_array($userType, self::DISCOUNTED_USER_TYPES, true) ? self::DISCOUNT_PERCENT : 0;
        $discountAmount = $basePrice * $discountPercent / 100;

        return [
            'original_price' => $basePrice,
            'discounted_price' => round($basePrice - $discountAmount, 2),
            'discount_percent' => $discountPercent,
            'discount_amount' => round($discountAmount, 2),
        ];
    }

    public function isSeatTaken(int $tripId, int $seatNumber): bool
    {
        return Booking::where('trip_id', $tripId)
            ->where('seat_number', $seatNumber)
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->exists();
    }

    /**
     * @return array{available: bool, error: string|null}
     */
    public function checkSeatAvailability(int $tripId, int $seatNumber): array
    {
        $trip = DB::table('trips as t')
            ->join('buses as b', 'b.id', '=', 't.bus_id')
            ->where('t.id', $tripId)
            ->where('t.status', 'scheduled')
            ->first(['t.id', 'b.capacity']);

        if (! $trip) {
            return ['available' => false, 'error' => 'Trip not found or not available for booking.'];
        }

        if ($seatNumber < 1 || $seatNumber > $trip->capacity) {
            return ['available' => false, 'error' => 'Seat number is out of range for this bus.'];
        }

        if ($this->isSeatTaken($tripId, $seatNumber)) {
            return ['available' => false, 'error' => 'Seat is already booked.'];
        }

        return ['available' => true, 'error' => null];
    }

    /**
     * Create a confirmed booking plus a matching paid payment record.
     *
     * @param  array{user_id: int|null, trip_id: int, passenger_name: string, passenger_phone?: string|null, seat_number: int, amount: float, payment_method?: string, payment_reference?: string|null}  $data
     * @return array{success: bool, booking_id: int|null, booking_code: string|null, error: string|null}
     */
    public function createBooking(array $data): array
    {
        $seatCheck = $this->checkSeatAvailability($data['trip_id'], $data['seat_number']);

        if (! $seatCheck['available']) {
            return ['success' => false, 'booking_id' => null, 'booking_code' => null, 'error' => $seatCheck['error']];
        }

        $bookingCode = $this->generateBookingCode();

        try {
            $booking = Booking::create([
                'user_id' => $data['user_id'] ?: null,
                'trip_id' => $data['trip_id'],
                'passenger_name' => $data['passenger_name'],
                'passenger_phone' => $data['passenger_phone'] ?? null,
                'seat_number' => $data['seat_number'],
                'status' => 'confirmed',
                'amount' => $data['amount'],
                'booking_code' => $bookingCode,
            ]);
        } catch (QueryException $exception) {
            $error = match ($exception->errorInfo[1] ?? null) {
                1062 => 'Information already existed.',
                1452 => 'Invalid trip or user reference.',
                default => 'Database error: '.($exception->errorInfo[2] ?? $exception->getMessage()),
            };

            return ['success' => false, 'booking_id' => null, 'booking_code' => null, 'error' => $error];
        }

        try {
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $data['amount'],
                'method' => $data['payment_method'] ?? 'cash',
                'status' => 'paid',
                'reference' => $data['payment_reference'] ?? null,
                'paid_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }

        return ['success' => true, 'booking_id' => $booking->id, 'booking_code' => $bookingCode, 'error' => null];
    }

    /**
     * Mark the payment of a cancelled booking as refunded. The booking itself stays "cancelled".
     *
     * @return array{success: bool, message: string}
     */
    public function processRefund(int $bookingId): array
    {
        $booking = Booking::find($bookingId);

        if (! $booking) {
            return ['success' => false, 'message' => 'Booking not found.'];
        }

        if ($booking->status !== 'cancelled') {
            return ['success' => false, 'message' => 'Only cancelled bookings can be refunded.'];
        }

        $booking->payments()->update(['status' => 'refunded', 'paid_at' => now()]);

        return ['success' => true, 'message' => 'Refund processed successfully for booking '.$booking->booking_code];
    }

    /**
     * Cancel an active booking whose trip has not departed yet, then refund it.
     *
     * @return array{success: bool, message: string, booking: Booking|null, refunded: bool}
     */
    public function cancel(int $bookingId, ?int $ownerId = null): array
    {
        $booking = Booking::with('trip')
            ->when($ownerId !== null, fn ($query) => $query->where('user_id', $ownerId))
            ->find($bookingId);

        if (! $booking) {
            $message = $ownerId !== null
                ? 'Booking not found or you do not have permission to cancel it.'
                : 'Booking not found.';

            return ['success' => false, 'message' => $message, 'booking' => null, 'refunded' => false];
        }

        if (! in_array($booking->status, Booking::ACTIVE_STATUSES, true) || $booking->trip->departure_time->isPast()) {
            return [
                'success' => false,
                'message' => 'Cannot cancel this booking. It may have already departed or been cancelled.',
                'booking' => $booking,
                'refunded' => false,
            ];
        }

        $booking->update(['status' => 'cancelled']);
        $refund = $this->processRefund($booking->id);

        $message = 'Booking '.$booking->booking_code.' has been cancelled. '
            .($refund['success'] ? 'Refund has been processed.' : 'Refund processing processed.');

        return ['success' => true, 'message' => $message, 'booking' => $booking, 'refunded' => $refund['success']];
    }

    /**
     * Copy bookings into bookings_archive. Call inside a transaction before deleting them.
     *
     * @param  'id'|'trip_id'|'status'  $column
     * @param  list<int|string>  $values
     */
    public function archive(string $column, array $values): int
    {
        if ($values === []) {
            return 0;
        }

        $columns = ['user_id', 'trip_id', 'passenger_name', 'passenger_phone', 'seat_number', 'status', 'amount', 'booking_code', 'booked_at'];

        return DB::table('bookings_archive')->insertUsing(
            $columns,
            DB::table('bookings')->select($columns)->whereIn($column, $values)
        );
    }
}
