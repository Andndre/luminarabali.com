<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignPreset extends Model
{
    protected $fillable = [
        'name',
        'category',
        'section_type',
        'props',
        'created_by',
    ];

    protected $casts = [
        'props' => 'array',
    ];
}
