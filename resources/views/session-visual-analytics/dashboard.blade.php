@extends('session-visual-analytics::layout', ['title' => 'Live Session Analytics', 'brand' => $brand])

@section('content')
<div class="topbar">
    <div>
        <h1>Live Session Analytics</h1>
        <p>Real-time visibility into active users, duration, devices, and engagement.</p>
    </div>
    <div class="chip"><span class="dot"></span> Live · auto-refresh {{ $refreshSeconds }}s</div>
</div>

<div class="grid kpis">
    <div class="card kpi">
        <div class="label">Active Sessions</div>
        <div class="value">{{ number_format($metrics['active_sessions']) }}</div>
        <div class="hint">{{ number_format($metrics['online_sessions']) }} online now</div>
    </div>
    <div class="card kpi">
        <div class="label">Sessions Today</div>
        <div class="value">{{ number_format($metrics['sessions_today']) }}</div>
        <div class="hint">First-seen today</div>
    </div>
    <div class="card kpi">
        <div class="label">Avg Duration</div>
        <div class="value">{{ $avgDuration }}</div>
        <div class="hint">Across tracked sessions</div>
    </div>
    <div class="card kpi">
        <div class="label">Bounce Rate</div>
        <div class="value">{{ $metrics['bounce_rate'] }}%</div>
        <div class="hint">Single page-view sessions</div>
    </div>
</div>

<div class="grid panels" style="margin-top:18px">
    <div class="card">
        <h2 class="section-title">Sessions · last 24 hours</h2>
        <canvas id="sessionChart" height="120"></canvas>
    </div>
    <div class="card">
        <h2 class="section-title">Active users</h2>
        <table>
            <thead>
            <tr><th>User</th><th>Device</th><th>Status</th></tr>
            </thead>
            <tbody>
            @forelse($sessions as $session)
                <tr>
                    <td>
                        <strong>{{ $session->user_name ?: 'Guest' }}</strong><br>
                        <span style="color:var(--muted);font-size:.8rem">{{ $session->current_path }}</span>
                    </td>
                    <td>{{ ucfirst($session->device_type ?: 'unknown') }}</td>
                    <td><span class="pill {{ $session->status }}">{{ ucfirst($session->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3" style="color:var(--muted)">No sessions tracked yet. Browse your app to populate the dashboard.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid panels" style="margin-top:18px">
    <div class="card">
        <h2 class="section-title">Top pages</h2>
        <table>
            <thead><tr><th>Path</th><th>Views</th></tr></thead>
            <tbody>
            @forelse($topPages as $page)
                <tr><td>{{ $page['path'] }}</td><td>{{ number_format($page['views']) }}</td></tr>
            @empty
                <tr><td colspan="2" style="color:var(--muted)">No page views yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card">
        <h2 class="section-title">Device mix</h2>
        @php $deviceTotal = max(1, array_sum($devices)); @endphp
        <div class="bars">
            @foreach(['desktop','mobile','tablet','bot','unknown'] as $type)
                @php $count = (int) ($devices[$type] ?? 0); $pct = round(($count / $deviceTotal) * 100); @endphp
                <div class="bar-row">
                    <div>{{ ucfirst($type) }}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:{{ $pct }}%"></div></div>
                    <div>{{ $pct }}%</div>
                </div>
            @endforeach
        </div>
        <h2 class="section-title" style="margin-top:24px">Top countries</h2>
        <table>
            <tbody>
            @forelse($countries as $country => $total)
                <tr><td>{{ $country }}</td><td>{{ number_format($total) }}</td></tr>
            @empty
                <tr><td colspan="2" style="color:var(--muted)">Enable geo lookup to populate countries.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('sessionChart');
    if (!ctx || !window.Chart) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chart['labels']),
            datasets: [{
                label: 'Page views',
                data: @json($chart['values']),
                borderColor: '#0f8f8a',
                backgroundColor: 'rgba(15,143,138,.12)',
                fill: true,
                tension: .35,
                pointRadius: 0,
                borderWidth: 2.5
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: 'rgba(215,224,236,.8)' } }
            }
        }
    });
    setTimeout(() => location.reload(), {{ (int) $refreshSeconds * 1000 }});
});
</script>
@endpush
