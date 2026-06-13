<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public const UNIT_PHOTOBOOTH = 'photobooth';
    public const UNIT_VISUAL = 'visual';

    public function prices()
    {
        return $this->hasMany(PackagePrice::class);
    }
}