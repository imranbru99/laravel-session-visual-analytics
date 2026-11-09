@extends('session-visual-analytics::layout', ['title' => 'Sessions', 'brand' => $brand])

@section('content')
<div class="topbar">
    <div>
        <h1>Active Sessions</h1>
        <p>Search, filter, and inspect live session health across your Laravel app.</p>
    </div>
</div>

<form method="get" class="filters">
    <a href="{{ route('session-vista.sessions') }}" class="{{ $status === '' ? 'active' : '' }}">All</a>
    <a href="{{ route('session-vista.sessions', ['status' => 'online']) }}" class="{{ $status === 'online' ? 'active' : '' }}">Online</a>
    <a href="{{ route('session-vista.sessions', ['status' => 'idle']) }}" class="{{ $status === 'idle' ? 'active' : '' }}">Idle</a>
    <a href="{{ route('session-vista.sessions', ['status' => 'expired']) }}" class="{{ $status === 'expired' ? 'active' : '' }}">Expired</a>
    <input class="search" type="search" name="q" value="{{ $q }}" placeholder="Search email, IP, path, country…">
    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
    <button class="btn" type="submit">Search</button>
</form>

<div class="card">
    <table>
        <thead>
        <tr>
            <th>User</th>
            <th>Device</th>
            <th>Browser</th>
            <th>Location</th>
            <th>Started</th>
            <th>Duration</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sessions as $session)
            <tr>
                <td>
                    <strong>{{ $session->user_name ?: 'Guest' }}</strong><br>
                    <span style="color:var(--muted);font-size:.8rem">{{ $session->user_email ?: $session->ip_address }}</span><br>
                    <span style="color:var(--muted);font-size:.8rem">{{ $session->current_path }}</span>
                </td>
                <td>{{ ucfirst($session->device_type ?: 'unknown') }}</td>
                <td>{{ $session->browser }} · {{ $session->platform }}</td>
                <td>{{ collect([$session->city, $session->country])->filter()->implode(', ') ?: '—' }}</td>
                <td>{{ optional($session->first_seen_at)->diffForHumans() }}</td>
                <td>{{ $analytics->formatDuration($session->durationSeconds()) }}</td>
                <td><span class="pill {{ $session->status }}">{{ ucfirst($session->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="7" style="color:var(--muted)">No sessions match this filter.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:16px">{{ $sessions->links() }}</div>
</div>
@endsection
