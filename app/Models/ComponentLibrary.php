<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComponentLibrary extends Model
{
    protected $table = 'component_library';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'thumbnail',
        'code',
        'variables',
        'type',
        'is_public',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
