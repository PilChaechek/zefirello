<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'user_id',
        'path',
        'thumbnail_path',
        'original_name',
        'mime_type',
        'size',
    ];

    /**
     * Get the parent attachable model (task or note).
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the attachment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
