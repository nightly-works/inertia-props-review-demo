<?php

namespace App\Http\Controllers;

use App\Models\SiteJob;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Dashboard', [
            'jobs' => SiteJob::with('client:id,name')
                ->when(! $user->isManager(), fn ($q) => $q->where('technician_id', $user->id))
                ->orderBy('scheduled_on')->get(),
            // Loaded only when a manager clicks "Payroll" (partial reload).
            'payroll' => Inertia::optional(fn () => User::orderBy('name')->get(['name', 'hourly_cost_cents'])),
        ]);
    }
}
