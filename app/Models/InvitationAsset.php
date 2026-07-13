<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvitationAsset extends Model
{
    protected $fillable = [
        'page_id',
        'asset_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'dimensions',
        'alt_text',
        'uploaded_by',
        'visibility',
        'collection'
    ];

    protected $casts = [
        'dimensions' => 'array',
        'file_size' => 'integer'
    ];

    public function page()
    {
        return $this->belongsTo(InvitationPage::class);
    }
}
