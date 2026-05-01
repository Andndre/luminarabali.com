<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleCalendarEvent extends Model
{
    protected $table = 'google_calendar_events';

    protected $fillable = [
        'booking_id',
        'google_event_id',
        'calendar_id',
        'event_summary',
        'event_start',
        'event_end',
        'html_link',
        'status',
    ];

    protected $casts = [
        'event_start' => 'datetime',
        'event_end' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}