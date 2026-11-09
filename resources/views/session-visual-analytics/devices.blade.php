@extends('session-visual-analytics::layout', ['title' => 'Devices & Geography', 'brand' => $brand])

@section('content')
<div class="topbar">
    <div>
        <h1>Devices & Geography</h1>
        <p>Understand where sessions come from and which clients your users prefer.</p>
    </div>
</div>

@php
    $deviceTotal = max(1, array_sum($devices));
    $browserTotal = max(1, array_sum($browsers));
@endphp

<div class="grid panels">
    <div class="card">
        <h2 class="section-title">Device breakdown</h2>
        <div class="bars">
            @foreach($devices as $type => $count)
                @php $pct = round(($count / $deviceTotal) * 100); @endphp
                <div class="bar-row">
                    <div>{{ ucfirst($type ?: 'unknown') }}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:{{ $pct }}%"></div></div>
                    <div>{{ $pct }}%</div>
                </div>
            @endforeach
        </div>
        <h2 class="section-title" style="margin-top:28px">Browser share</h2>
        <div class="bars">
            @foreach($browsers as $browser => $count)
                @php $pct = round(($count / $browserTotal) * 100); @endphp
                <div class="bar-row">
                    <div>{{ $browser }}</div>
                    <div class="bar-track"><div class="bar-fill" style="width:{{ $pct }}%"></div></div>
                    <div>{{ $pct }}%</div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="card">
        <h2 class="section-title">Top countries</h2>
        <table>
            <thead><tr><th>Country</th><th>Sessions</th></tr></thead>
            <tbody>
            @forelse($countries as $country => $total)
                <tr><td>{{ $country }}</td><td>{{ number_format($total) }}</td></tr>
            @empty
                <tr><td colspan="2" style="color:var(--muted)">No geography data yet.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="grid kpis" style="margin-top:22px;grid-template-columns:1fr 1fr">
            <div class="card kpi" style="box-shadow:none">
                <div class="label">Active</div>
                <div class="value">{{ number_format($metrics['active_sessions']) }}</div>
            </div>
            <div class="card kpi" style="box-shadow:none">
                <div class="label">Bounce</div>
                <div class="value">{{ $metrics['bounce_rate'] }}%</div>
            </div>
        </div>
    </div>
</div>
@endsection
