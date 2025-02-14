<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all bookings for this resource
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if resource is available for the given time period
     */
    public function isAvailable($startTime, $endTime, $excludeBookingId = null)
    {
        // Convert input times to Carbon instances if they're not already
        $startTime = $startTime instanceof Carbon ? $startTime : Carbon::parse($startTime);
        $endTime = $endTime instanceof Carbon ? $endTime : Carbon::parse($endTime);

        $query = $this->bookings()
            ->where('status', 'confirmed')
            ->where(function ($query) use ($startTime, $endTime) {
                // Check for any overlapping bookings
                $query->where(function ($q) use ($startTime, $endTime) {
                    // Start time falls within an existing booking
                    $q->where('start_time', '<=', $startTime)
                        ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // End time falls within an existing booking
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New booking completely encompasses an existing booking
                    $q->where('start_time', '>=', $startTime)
                        ->where('end_time', '<=', $endTime);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }

    /**
     * Check if resource has any active bookings
     */
    public function hasActiveBookings(): bool
    {
        return $this->bookings()
            ->where('status', 'confirmed')
            ->where('end_time', '>', now())
            ->exists();
    }
}
