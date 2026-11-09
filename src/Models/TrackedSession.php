<?php

namespace Imranbru99\SessionVisualAnalytics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackedSession extends Model
{
    protected $table = 'session_vista_sessions';

    protected $fillable = [
        'session_id',
        'user_id',
        'user_email',
        'user_name',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'country',
        'city',
        'status',
        'landing_path',
        'current_path',
        'page_views',
        'first_seen_at',
        'last_seen_at',
        'ended_at',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'ended_at' => 'datetime',
        'page_views' => 'integer',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(TrackedEvent::class, 'session_vista_session_id');
    }

    public function durationSeconds(): int
    {
        if (! $this->first_seen_at || ! $this->last_seen_at) {
            return 0;
        }

        return max(0, $this->last_seen_at->diffInSeconds($this->first_seen_at));
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['online', 'idle']);
    }
}
