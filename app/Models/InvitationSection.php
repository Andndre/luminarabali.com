<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InvitationSection extends Model
{
    protected static function booted(): void
    {
        // Thumbnail varian dipakai HANYA oleh section ini (path deterministik
        // section-thumbs/{id}/), jadi ikut terhapus bersama section-nya.
        static::deleting(function (self $section) {
            Storage::disk('public')->deleteDirectory("section-thumbs/{$section->id}");
        });
    }

    protected $fillable = [
        'page_id',
        'template_id',
        'parent_id',
        'section_type',
        'order_index',
        'props',
        'variant_thumbnails',
        'custom_css',
        'is_visible'
    ];

    protected $casts = [
        'props' => 'array',
        'variant_thumbnails' => 'array',
        'is_visible' => 'boolean'
    ];

    public function page()
    {
        return $this->belongsTo(InvitationPage::class);
    }

    public function template()
    {
        return $this->belongsTo(InvitationTemplate::class);
    }

    public function parent()
    {
        return $this->belongsTo(InvitationSection::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(InvitationSection::class, 'parent_id');
    }
}
