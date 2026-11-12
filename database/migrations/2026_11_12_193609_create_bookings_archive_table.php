<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Holds bookings removed by cleanup so revenue and history reports stay accurate.
     */
    public function up(): void
    {
        Schema::create('bookings_archive', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->unsignedInteger('trip_id')->index();
            $table->string('passenger_name', 120);
            $table->string('passenger_phone', 30)->nullable();
            $table->unsignedInteger('seat_number');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'refunded', 'completed']);
            $table->decimal('amount', 10, 2);
            $table->string('booking_code', 20)->index();
            $table->timestamp('booked_at')->nullable();
            $table->timestamp('archived_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings_archive');
    }
};
