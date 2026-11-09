<?php

namespace Imranbru99\SessionVisualAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Imranbru99\SessionVisualAnalytics\Models\TrackedSession;
use Imranbru99\SessionVisualAnalytics\Services\SessionAnalyticsService;

class DashboardController extends Controller
{
    public function __construct(
        protected SessionAnalyticsService $analytics
    ) {}

    public function index()
    {
        $metrics = $this->analytics->overviewMetrics();
        $chart = $this->analytics->chartSeries((int) config('session-visual-analytics.dashboard.chart_hours', 24));
        $devices = $this->analytics->deviceMix();
        $browsers = $this->analytics->browserMix();
        $topPages = $this->analytics->topPages((int) config('session-visual-analytics.dashboard.top_pages_limit', 10));
        $countries = $this->analytics->topCountries();
        $sessions = $this->analytics->recentSessions((int) config('session-visual-analytics.dashboard.active_users_limit', 12));

        return view('session-visual-analytics::dashboard', [
            'brand' => config('session-visual-analytics.name', 'SessionVista'),
            'metrics' => $metrics,
            'chart' => $chart,
            'devices' => $devices,
            'browsers' => $browsers,
            'topPages' => $topPages,
            'countries' => $countries,
            'sessions' => $sessions,
            'avgDuration' => $this->analytics->formatDuration((int) ($metrics['avg_duration_seconds'] ?? 0)),
            'refreshSeconds' => (int) config('session-visual-analytics.dashboard.refresh_seconds', 30),
        ]);
    }

    public function sessions(Request $request)
    {
        $status = $request->string('status')->toString();

        $query = TrackedSession::query()->latest('last_seen_at');
        if (in_array($status, ['online', 'idle', 'expired'], true)) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('user_email', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('current_path', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        $sessions = $query->paginate(20)->withQueryString();

        return view('session-visual-analytics::sessions', [
            'brand' => config('session-visual-analytics.name', 'SessionVista'),
            'sessions' => $sessions,
            'status' => $status,
            'q' => $search ?? '',
            'analytics' => $this->analytics,
        ]);
    }

    public function devices()
    {
        return view('session-visual-analytics::devices', [
            'brand' => config('session-visual-analytics.name', 'SessionVista'),
            'devices' => $this->analytics->deviceMix(),
            'browsers' => $this->analytics->browserMix(),
            'countries' => $this->analytics->topCountries(12),
            'metrics' => $this->analytics->overviewMetrics(),
        ]);
    }

    public function apiOverview()
    {
        return response()->json([
            'metrics' => $this->analytics->overviewMetrics(),
            'chart' => $this->analytics->chartSeries((int) config('session-visual-analytics.dashboard.chart_hours', 24)),
            'devices' => $this->analytics->deviceMix(),
            'generated_at' => now()->toIso8601String(),
        ]);
    }
}
