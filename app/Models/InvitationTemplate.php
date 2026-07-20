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
        'price',
        'global_custom_css',
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
            'price' => 'integer',
        ];
    }

    public function priceLabel(): string
    {
        return $this->price === null
            ? 'Hubungi kami'
            : 'Rp' . number_format($this->price, 0, ',', '.');
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
