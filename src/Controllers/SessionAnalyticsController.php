<?php

namespace BlogCutter\LaravelSessionVisualAnalytics\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SessionAnalyticsController extends Controller
{
    public function sessions()
    {

        $pageTitle = 'Laravel Sessions';
        $sessions = DB::table('sessions')
            ->orderBy('last_activity', 'desc')
            ->paginate(10);

        // Check if there are no sessions
    if ($sessions->isEmpty()) {
        return view('admin.sessions', [
            'pageTitle' => $pageTitle,
            'sessions' => null, // No sessions
            'message' => 'No sessions available.'
        ]);
    }

    foreach ($sessions as $session) {
        // Decode the payload
        $decodedPayload = base64_decode($session->payload);

        if ($decodedPayload === false) {
            // If Base64 decode fails, set payload to null
            $session->payload = null;
            continue; // Skip to the next session if decoding fails
        }

        // Try unserializing the decoded payload
        $unserializedData = @unserialize($decodedPayload);

        if ($unserializedData !== false || $decodedPayload == 'b:0;') {
            // If unserialize was successful, store it
            $session->payload = $unserializedData;
        } else {
            // If unserialize failed, attempt JSON decode
            $jsonData = json_decode($decodedPayload, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                // If JSON decode was successful, store it
                $session->payload = $jsonData;
            } else {
                // If both unserialize and JSON decode fail, set payload to null
                $session->payload = null;
            }
        }

        // Extract the URL from the payload (if exists)
        if ($session->payload && isset($session->payload['_previous']['url'])) {
            $session->extracted_url = $session->payload['_previous']['url'];
        } else {
            $session->extracted_url = null;
        }

        $session->last_activity = Carbon::createFromTimestamp($session->last_activity, 'UTC')
                                         ->setTimezone(config('app.timezone'));
        $session->formatted_last_activity = $session->last_activity->format('Y-m-d H:i:s A');
    }

        return view('laravel-session-visual-analytics::sessions', compact('sessions', 'pageTitle'));
    }

    public function deleteAll()
    {
        DB::table('sessions')->truncate();

        return back()->with('success', 'All session records have been deleted.');
    }
}
