<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationSection extends Model
{
    protected $fillable = [
        'page_id',
        'template_id',
        'parent_id',
        'section_type',
        'order_index',
        'props',
        'custom_css',
        'is_visible'
    ];

    protected $casts = [
        'props' => 'array',
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
