<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationPage extends Model
{
    protected $fillable = [
        'template_id',
        'title',
        'slug',
        'groom_name',
        'bride_name',
        'event_date',
        'published_status',
        'meta_data',
        'theme_overrides',
        'created_by',
        'owner_id'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'theme_overrides' => 'array',
        'event_date' => 'datetime'
    ];

    public function sections()
    {
        return $this->hasMany(InvitationSection::class, 'page_id');
    }

    public function template()
    {
        return $this->belongsTo(InvitationTemplate::class);
    }



    public function assets()
    {
        return $this->hasMany(InvitationAsset::class, 'page_id');
    }

    public function rsvpResponses()
    {
        return $this->hasMany(InvitationRsvpResponse::class, 'page_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
