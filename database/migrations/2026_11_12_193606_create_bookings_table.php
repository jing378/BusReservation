<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One record per reserved seat. The stored `active_seat_check` column is NULL for
     * inactive bookings, so the unique (trip_id, active_seat_check) index blocks double
     * booking while still letting cancelled seats be booked again.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('trip_id');
            $table->string('passenger_name', 120);
            $table->string('passenger_phone', 30)->nullable();
            $table->unsignedInteger('seat_number');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'refunded', 'completed'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('booking_code', 20)->unique();
            $table->timestamp('booked_at')->useCurrent();
            $table->integer('active_seat_check')->nullable()
                ->storedAs("CASE WHEN status IN ('pending', 'confirmed') THEN seat_number ELSE NULL END");

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('trip_id')->references('id')->on('trips')->restrictOnDelete()->cascadeOnUpdate();
            $table->unique(['trip_id', 'active_seat_check'], 'uq_trip_active_seat');
            $table->index(['trip_id', 'status']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::unprepared(<<<'SQL'
                CREATE TRIGGER trg_bookings_bi BEFORE INSERT ON bookings
                FOR EACH ROW
                BEGIN
                  DECLARE v_capacity INT UNSIGNED;
                  DECLARE v_status VARCHAR(20);

                  SELECT b.capacity, t.status INTO v_capacity, v_status
                  FROM trips t JOIN buses b ON b.id = t.bus_id
                  WHERE t.id = NEW.trip_id
                  LIMIT 1;

                  IF v_capacity IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid trip for booking.';
                  END IF;

                  IF NEW.seat_number < 1 OR NEW.seat_number > v_capacity THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Seat number out of range for bus capacity.';
                  END IF;

                  IF v_status IN ('cancelled','completed') THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot book on cancelled or completed trips.';
                  END IF;
                END
                SQL);

            DB::unprepared(<<<'SQL'
                CREATE TRIGGER trg_bookings_bu BEFORE UPDATE ON bookings
                FOR EACH ROW
                BEGIN
                  DECLARE v_capacity INT UNSIGNED;
                  DECLARE v_status VARCHAR(20);

                  SELECT b.capacity, t.status INTO v_capacity, v_status
                  FROM trips t JOIN buses b ON b.id = t.bus_id
                  WHERE t.id = COALESCE(NEW.trip_id, OLD.trip_id)
                  LIMIT 1;

                  IF v_capacity IS NULL THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid trip for booking update.';
                  END IF;

                  IF NEW.seat_number < 1 OR NEW.seat_number > v_capacity THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Seat number out of range for bus capacity.';
                  END IF;

                  IF v_status IN ('cancelled','completed') AND (NEW.seat_number != OLD.seat_number OR NEW.amount != OLD.amount) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot modify booking on cancelled or completed trips.';
                  END IF;
                END
                SQL);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS trg_bookings_bi');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_bookings_bu');
        }

        Schema::dropIfExists('bookings');
    }
};
