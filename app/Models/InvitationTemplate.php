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
        'html_content',
        'cover_content',
        'meta_data',
        'theme',
        'status',
        'created_by'
    ];

    protected function casts(): array
    {
        return [
            'meta_data' => 'array',
            'theme' => 'array',
        ];
    }

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
