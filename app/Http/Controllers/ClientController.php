<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function show(Client $client)
    {
        return Inertia::render('Clients/Show', ['client' => $client->load('siteJobs')]);
    }
}
