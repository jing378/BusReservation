<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bookings moved out of the live table by cleanup, kept for revenue history.
 */
class BookingArchive extends Model
{
    protected $table = 'bookings_archive';

    public $timestamps = false;

    protected $guarded = ['id'];
}
