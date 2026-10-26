<?php

namespace App\Http\Controllers;

use App\Models\SiteJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SiteJobController extends Controller
{
    public function edit(SiteJob $job)
    {
        Gate::authorize('update', $job);

        return Inertia::render('Jobs/Edit', ['job' => $job]);
    }

    public function update(Request $request, SiteJob $job)
    {
        Gate::authorize('update', $job);

        $job->update($request->validate([
            'title' => ['required', 'string', 'max:120'],
            'scheduled_on' => ['required', 'date'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'technician_id' => ['required', 'exists:users,id'],
        ]));

        return redirect('/dashboard');
    }
}
