<?php

namespace App\Http\Controllers;

use App\Models\SiteJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SiteJobController extends Controller
{
    public function edit(SiteJob $job)
    {
        Gate::authorize('update', $job);

        return Inertia::render('Jobs/Edit', [
            'job' => $job,
            'technicians' => User::where('role', 'technician')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, SiteJob $job)
    {
        Gate::authorize('update', $job);

        $job->update($request->validate([
            'title' => ['required', 'string', 'max:120'],
            'scheduled_on' => ['required', 'date'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'technician_id' => ['required', Rule::exists('users', 'id')->where('role', 'technician')],
        ]));

        return redirect('/dashboard');
    }
}
