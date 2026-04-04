<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'title',
        'url',
        'thumbnail',
        'icon',
        'order',
        'is_active',
        'is_pinned',
        'business_unit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
        'order' => 'integer',
    ];
}
