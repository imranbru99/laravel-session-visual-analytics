<!DOCTYPE html>
<html>
<head>
    <title>{{ $pageTitle }}</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:nth-child(odd) {
            background-color: #eef;
        }
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>{{ $pageTitle }}</h1>

    <form action="{{ route('sessions.deleteAll') }}" method="POST">
        @csrf
        @method('DELETE')
        <button class="delete-btn">Delete All Sessions</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Sr.</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Payload</th>
                <th>Last Activity</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sessions as $session)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $session->ip_address }}</td>
                    <td>{{ $session->user_agent }}</td>
                    <td>{{ $session->payload ? json_encode($session->payload) : 'N/A' }}</td>
                    <td>{{ $session->last_activity }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No sessions available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $sessions->links() }}
    <div class="footer">
        <p>Developed by <strong><a href="https://www.facebook.com/imranbru99/" target="_blank">Imran Ahmed</a></strong> from <a href="https://www.blogcutter.com" target="_blank">Blog Cutter</a>.</p>
    </div>
    
</body>
</html>
