<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Enums for Business Unit
    public const UNIT_PHOTOBOOTH = 'photobooth';
    public const UNIT_VISUAL = 'visual';

    protected $casts = [
        'event_date' => 'date',
        'price_total' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'duration_hours' => 'integer',
    ];

    // Enums for Status
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_DP_BAYAR = 'DP_BAYAR';
    public const STATUS_LUNAS = 'LUNAS';

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function googleCalendarEvent()
    {
        return $this->hasOne(GoogleCalendarEvent::class);
    }
}