<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationRsvpResponse extends Model
{
    protected $fillable = [
        'page_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'attendance_status',
        'number_of_guests',
        'message',
        'submitted_at',
        'is_hidden'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'number_of_guests' => 'integer',
        'is_hidden' => 'boolean'
    ];

    public function page()
    {
        return $this->belongsTo(InvitationPage::class);
    }
}
