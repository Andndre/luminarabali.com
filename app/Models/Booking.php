<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Enums for Business Unit
    const UNIT_PHOTOBOOTH = 'photobooth';
    const UNIT_VISUAL = 'visual';

    protected $casts = [
        'event_date' => 'date',
        'price_total' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'duration_hours' => 'integer',
    ];

    // Enums for Status
    const STATUS_PENDING = 'PENDING';
    const STATUS_DP_BAYAR = 'DP_BAYAR';
    const STATUS_LUNAS = 'LUNAS';

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function googleCalendarEvent()
    {
        return $this->hasOne(GoogleCalendarEvent::class);
    }
}