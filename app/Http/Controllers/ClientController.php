<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    // The props decide what the browser receives: internal notes are sent to managers only.
    public function show(Request $request, Client $client)
    {
        // Technicians open the clients they have a job with.
        abort_unless($request->user()->isManager() || $client->siteJobs()->where('technician_id', $request->user()->id)->exists(), 403);

        $fields = ['id', 'name', 'contact_email'];
        if ($request->user()->isManager()) {
            $fields[] = 'internal_notes';
        }

        return Inertia::render('Clients/Show', ['client' => [
            ...$client->only($fields),
            'site_jobs' => $client->siteJobs()->orderBy('scheduled_on')->get(['id', 'title', 'scheduled_on']),
        ]]);
    }
}
