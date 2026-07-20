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
        'hero_slot',
        'global_custom_css',
        'meta_data',
        'theme',
        'status',
        'created_by'
    ];

    /** Lima slot kipas hero, terurut kiri→kanan. */
    public const HERO_SLOTS = ['left-outer', 'left-inner', 'center', 'right-inner', 'right-outer'];

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

    /** Template hero terurut posisi kipas kiri→kanan; hanya yang published. */
    public static function heroFan()
    {
        $slots = array_flip(self::HERO_SLOTS);

        return self::where('status', 'published')
            ->whereIn('hero_slot', self::HERO_SLOTS)
            ->get()
            ->sortBy(fn ($t) => $slots[$t->hero_slot] ?? 99)
            ->values();
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
