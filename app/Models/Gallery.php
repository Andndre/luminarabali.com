<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public const UNIT_PHOTOBOOTH = 'photobooth';
    public const UNIT_VISUAL = 'visual';
}