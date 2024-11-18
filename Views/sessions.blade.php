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

    <form action="{{ route('session.analytics.deleteAll') }}" method="POST" style="display: flex; justify-content: center; align-items: center; margin: 0;">
        @csrf
        @method('DELETE')
        <button class="delete-btn" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #ff4d4d; color: white; border: none; border-radius: 5px;">
            Delete All Sessions
        </button>
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
                    <td>
                        <details>
                            <summary>{{ getInfoCountry($session->ip_address) }}</summary>
                            <p> {{ $session->ip_address }}</p>
                        </details>
                    </td>
                    <td>
                        <details>
                            <summary>{{ parseBrowser($session->user_agent) }}</summary>
                            <p>{{ $session->user_agent }}</p>
                        </details>
                    </td>
                    <td>
                        @if ($session->extracted_url)
                        <details>
                           <summary>{{ parse_url($session->extracted_url, PHP_URL_HOST) }}</summary>
                           <p>{{ $session->extracted_url }}</p> <!-- Full URL shown here -->
                       </details>
                       @else
                           <span>No URL available</span>
                       @endif
                    </td>
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
    <div class="footer" style="text-align: center; padding: 20px; background-color: #f1f1f1; margin-top: 50px;">
        <p style="margin: 0;">Developed by <strong><a href="https://www.facebook.com/imranbru99/" target="_blank" style="text-decoration: none; color: #007bff;">Imran Ahmed</a></strong> from <a href="https://www.blogcutter.com" target="_blank" style="text-decoration: none; color: #007bff;">Blog Cutter</a>.</p>
    </div>

</body>
</html>
