<?php

namespace Imranbru99\SessionVisualAnalytics\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Imranbru99\SessionVisualAnalytics\Models\TrackedEvent;
use Imranbru99\SessionVisualAnalytics\Models\TrackedSession;

class SessionAnalyticsService
{
    public function __construct(
        protected UserAgentParser $parser
    ) {}

    public function track(Request $request): ?TrackedSession
    {
        if (! config('session-visual-analytics.track.enabled')) {
            return null;
        }

        $path = '/'.ltrim($request->path(), '/');
        foreach ((array) config('session-visual-analytics.track.exclude_paths', []) as $pattern) {
            if ($request->is($pattern)) {
                return null;
            }
        }

        $sessionKey = $request->session()->getId();
        $now = now();
        $ua = config('session-visual-analytics.track.store_user_agent') ? (string) $request->userAgent() : null;
        $parsed = $this->parser->parse($ua);
        $user = $request->user();

        $session = TrackedSession::query()->firstOrNew(['session_id' => $sessionKey]);

        if (! $session->exists) {
            $session->first_seen_at = $now;
            $session->landing_path = $path;
            $session->page_views = 0;
        }

        $session->fill([
            'user_id' => $user?->getAuthIdentifier(),
            'user_email' => $user?->email ?? $session->user_email,
            'user_name' => $user?->name ?? $session->user_name,
            'ip_address' => config('session-visual-analytics.track.store_ip') ? $request->ip() : null,
            'user_agent' => $ua,
            'device_type' => $parsed['device_type'],
            'browser' => $parsed['browser'],
            'platform' => $parsed['platform'],
            'status' => 'online',
            'current_path' => $path,
            'last_seen_at' => $now,
            'ended_at' => null,
        ]);

        if (config('session-visual-analytics.track.store_geo') && ! $session->country) {
            $geo = $this->resolveGeo($request);
            $session->country = $geo['country'] ?? null;
            $session->city = $geo['city'] ?? null;
        }

        $session->page_views = (int) $session->page_views + 1;
        $session->save();

        TrackedEvent::query()->create([
            'session_vista_session_id' => $session->id,
            'type' => 'pageview',
            'path' => $path,
            'method' => $request->method(),
            'status_code' => 200,
            'payload' => config('session-visual-analytics.track.store_payload') ? [
                'query' => $request->query(),
            ] : null,
            'occurred_at' => $now,
        ]);

        $this->refreshStatuses();

        return $session;
    }

    public function refreshStatuses(): void
    {
        $idleAfter = (int) config('session-visual-analytics.track.idle_after_minutes', 15);
        $expireAfter = (int) config('session-visual-analytics.track.expire_after_minutes', 120);
        $now = now();

        TrackedSession::query()
            ->whereIn('status', ['online', 'idle'])
            ->where('last_seen_at', '<', $now->copy()->subMinutes($expireAfter))
            ->update(['status' => 'expired', 'ended_at' => $now]);

        TrackedSession::query()
            ->where('status', 'online')
            ->where('last_seen_at', '<', $now->copy()->subMinutes($idleAfter))
            ->update(['status' => 'idle']);
    }

    public function overviewMetrics(): array
    {
        $this->refreshStatuses();

        $active = TrackedSession::query()->active()->count();
        $online = TrackedSession::query()->online()->count();
        $today = TrackedSession::query()->whereDate('first_seen_at', today())->count();

        $avgDuration = (int) TrackedSession::query()
            ->whereNotNull('first_seen_at')
            ->whereNotNull('last_seen_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, first_seen_at, last_seen_at)) as avg_seconds')
            ->value('avg_seconds');

        $bounce = TrackedSession::query()->where('page_views', '<=', 1)->count();
        $total = max(1, TrackedSession::query()->count());

        return [
            'active_sessions' => $active,
            'online_sessions' => $online,
            'sessions_today' => $today,
            'avg_duration_seconds' => $avgDuration,
            'bounce_rate' => round(($bounce / $total) * 100, 1),
            'unique_devices' => TrackedSession::query()->distinct('device_type')->count('device_type'),
        ];
    }

    public function chartSeries(int $hours = 24): array
    {
        $start = now()->subHours($hours)->startOfHour();
        $rows = TrackedEvent::query()
            ->where('type', 'pageview')
            ->where('occurred_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(occurred_at, '%Y-%m-%d %H:00:00') as bucket, COUNT(*) as total")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('total', 'bucket');

        $labels = [];
        $values = [];
        for ($i = 0; $i <= $hours; $i++) {
            $bucket = $start->copy()->addHours($i);
            $key = $bucket->format('Y-m-d H:00:00');
            $labels[] = $bucket->format('H:i');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return compact('labels', 'values');
    }

    public function deviceMix(): array
    {
        return TrackedSession::query()
            ->select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->pluck('total', 'device_type')
            ->toArray();
    }

    public function browserMix(): array
    {
        return TrackedSession::query()
            ->select('browser', DB::raw('COUNT(*) as total'))
            ->groupBy('browser')
            ->orderByDesc('total')
            ->pluck('total', 'browser')
            ->toArray();
    }

    public function topPages(int $limit = 10): array
    {
        return TrackedEvent::query()
            ->where('type', 'pageview')
            ->whereNotNull('path')
            ->select('path', DB::raw('COUNT(*) as total'))
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => ['path' => $row->path, 'views' => (int) $row->total])
            ->all();
    }

    public function topCountries(int $limit = 8): array
    {
        return TrackedSession::query()
            ->whereNotNull('country')
            ->select('country', DB::raw('COUNT(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('total', 'country')
            ->toArray();
    }

    public function recentSessions(int $limit = 25)
    {
        return TrackedSession::query()
            ->latest('last_seen_at')
            ->limit($limit)
            ->get();
    }

    public function formatDuration(int $seconds): string
    {
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;

        if ($m >= 60) {
            $h = intdiv($m, 60);
            $m = $m % 60;

            return sprintf('%dh %02dm', $h, $m);
        }

        return sprintf('%dm %02ds', $m, $s);
    }

    protected function resolveGeo(Request $request): array
    {
        $driver = config('session-visual-analytics.geo.driver', 'none');

        if ($driver !== 'http') {
            return ['country' => null, 'city' => null];
        }

        $urlTemplate = config('session-visual-analytics.geo.http_url');
        if (! $urlTemplate || ! $request->ip()) {
            return ['country' => null, 'city' => null];
        }

        try {
            $url = str_replace('{ip}', urlencode($request->ip()), $urlTemplate);
            $json = @file_get_contents($url);
            if (! $json) {
                return ['country' => null, 'city' => null];
            }
            $data = json_decode($json, true) ?: [];

            return [
                'country' => $data['country_name'] ?? $data['country'] ?? null,
                'city' => $data['city'] ?? null,
            ];
        } catch (\Throwable) {
            return ['country' => null, 'city' => null];
        }
    }
}
