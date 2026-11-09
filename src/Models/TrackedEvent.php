<?php

namespace Imranbru99\SessionVisualAnalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackedEvent extends Model
{
    protected $table = 'session_vista_events';

    protected $fillable = [
        'session_vista_session_id',
        'type',
        'path',
        'method',
        'status_code',
        'duration_ms',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'status_code' => 'integer',
        'duration_ms' => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrackedSession::class, 'session_vista_session_id');
    }
}
