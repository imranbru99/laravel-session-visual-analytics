<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Overview' }} · {{ $brand ?? 'SessionVista' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" defer></script>
    <style>
        :root {
            --ink: #10233f;
            --muted: #5b6b82;
            --line: #d7e0ec;
            --bg: #eef3f8;
            --panel: rgba(255,255,255,.86);
            --teal: #0f8f8a;
            --teal-deep: #0b6e6a;
            --amber: #d97706;
            --rose: #e11d48;
            --ok: #059669;
            --shadow: 0 18px 50px rgba(16,35,63,.08);
            --radius: 22px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Manrope, system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 500px at 10% -10%, #d9f3f1 0%, transparent 55%),
                radial-gradient(900px 420px at 100% 0%, #dbe7ff 0%, transparent 50%),
                linear-gradient(180deg, #f7fafc 0%, var(--bg) 100%);
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        .shell { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        .sidebar {
            padding: 28px 20px;
            border-right: 1px solid rgba(215,224,236,.8);
            background: rgba(255,255,255,.55);
            backdrop-filter: blur(16px);
            position: sticky; top: 0; height: 100vh;
        }
        .brand {
            display: flex; align-items: center; gap: 12px;
            font-family: Sora, sans-serif; font-weight: 700; font-size: 1.05rem;
            margin-bottom: 34px; padding: 0 8px;
        }
        .mark {
            width: 38px; height: 38px; border-radius: 12px;
            background: linear-gradient(145deg, var(--teal), #1f4b7a);
            box-shadow: 0 10px 24px rgba(15,143,138,.35);
            position: relative;
        }
        .mark::after {
            content: ""; position: absolute; inset: 10px 8px 10px 8px;
            border: 2px solid #fff; border-radius: 999px; border-right-color: transparent;
            transform: rotate(-20deg);
        }
        .nav { display: grid; gap: 6px; }
        .nav a {
            padding: 12px 14px; border-radius: 14px; color: var(--muted);
            font-weight: 600; font-size: .95rem;
        }
        .nav a:hover, .nav a.active {
            background: #fff; color: var(--ink); box-shadow: var(--shadow);
        }
        .main { padding: 28px 32px 48px; }
        .topbar {
            display: flex; justify-content: space-between; align-items: end;
            gap: 16px; margin-bottom: 24px; flex-wrap: wrap;
        }
        .topbar h1 {
            margin: 0; font-family: Sora, sans-serif; font-size: clamp(1.5rem, 2vw, 2rem);
            letter-spacing: -.03em;
        }
        .topbar p { margin: 8px 0 0; color: var(--muted); }
        .chip {
            display: inline-flex; align-items: center; gap: 8px;
            background: #fff; border: 1px solid var(--line); border-radius: 999px;
            padding: 10px 14px; font-weight: 600; font-size: .85rem; color: var(--muted);
        }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--ok); box-shadow: 0 0 0 4px rgba(5,150,105,.15); }
        .grid { display: grid; gap: 18px; }
        .kpis { grid-template-columns: repeat(4, minmax(0,1fr)); }
        .card {
            background: var(--panel); border: 1px solid rgba(215,224,236,.9);
            border-radius: var(--radius); box-shadow: var(--shadow); padding: 20px;
            backdrop-filter: blur(10px);
        }
        .kpi .label { color: var(--muted); font-size: .84rem; font-weight: 600; }
        .kpi .value { font-family: Sora, sans-serif; font-size: 1.8rem; margin-top: 10px; letter-spacing: -.04em; }
        .kpi .hint { margin-top: 8px; font-size: .8rem; color: var(--teal-deep); font-weight: 700; }
        .panels { grid-template-columns: 1.6fr .9fr; }
        .section-title { margin: 0 0 16px; font-family: Sora, sans-serif; font-size: 1.05rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid var(--line); font-size: .9rem; }
        th { color: var(--muted); font-size: .75rem; text-transform: uppercase; letter-spacing: .06em; }
        .pill {
            display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px;
            font-size: .75rem; font-weight: 700;
        }
        .pill.online { background: #d1fae5; color: #047857; }
        .pill.idle { background: #fef3c7; color: #b45309; }
        .pill.expired { background: #e5e7eb; color: #4b5563; }
        .filters { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .filters a, .filters .btn {
            border: 1px solid var(--line); background: #fff; border-radius: 999px;
            padding: 8px 14px; font-weight: 700; font-size: .82rem; color: var(--muted);
        }
        .filters a.active { background: var(--ink); color: #fff; border-color: var(--ink); }
        .search {
            flex: 1; min-width: 220px; border: 1px solid var(--line); border-radius: 14px;
            padding: 11px 14px; font: inherit; background: #fff;
        }
        .bars { display: grid; gap: 14px; }
        .bar-row { display: grid; grid-template-columns: 90px 1fr 48px; gap: 10px; align-items: center; }
        .bar-track { height: 10px; background: #e8eef6; border-radius: 999px; overflow: hidden; }
        .bar-fill { height: 100%; background: linear-gradient(90deg, var(--teal), #1f4b7a); border-radius: 999px; }
        .footer-note { margin-top: 28px; color: var(--muted); font-size: .85rem; }
        @media (max-width: 1100px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: relative; height: auto; }
            .kpis, .panels { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 720px) {
            .main { padding: 20px 16px 40px; }
            .kpis, .panels { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand"><span class="mark"></span>{{ $brand ?? 'SessionVista' }}</div>
        <nav class="nav">
            <a href="{{ route('session-vista.dashboard') }}" class="{{ request()->routeIs('session-vista.dashboard') ? 'active' : '' }}">Overview</a>
            <a href="{{ route('session-vista.sessions') }}" class="{{ request()->routeIs('session-vista.sessions') ? 'active' : '' }}">Sessions</a>
            <a href="{{ route('session-vista.devices') }}" class="{{ request()->routeIs('session-vista.devices') ? 'active' : '' }}">Devices & Geo</a>
        </nav>
    </aside>
    <main class="main">
        @yield('content')
        <p class="footer-note">Built by <strong>Imran Ahmed</strong> · <a href="https://imrandev.bd/contact">Future development & support</a> · me@imrandev.bd · +880 1576-918420</p>
    </main>
</div>
@stack('scripts')
</body>
</html>
