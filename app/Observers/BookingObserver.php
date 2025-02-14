<?php

namespace App\Observers;

use App\Events\BookingCancelled;
use App\Events\BookingCreated;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        Log::info('New booking created', ['booking_id' => $booking->id]);
        event(new BookingCreated($booking));
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        if ($booking->wasChanged('status') && $booking->status === 'cancelled') {
            Log::info('Booking cancelled', ['booking_id' => $booking->id]);
            event(new BookingCancelled($booking));
        }
    }
}
