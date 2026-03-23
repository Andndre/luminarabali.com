<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'description',
        'category',
        'global_custom_css',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function pages()
    {
        return $this->hasMany(InvitationPage::class);
    }

    public function sections()
    {
        return $this->hasMany(InvitationSection::class, 'template_id')->orderBy('order_index');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
