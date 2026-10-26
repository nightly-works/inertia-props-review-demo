<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            // Shared props go into every page's HTML: only what every page needs.
            'auth' => ['user' => $request->user()?->only(['id', 'name', 'role'])],
            'team' => fn () => $request->user() ? User::orderBy('name')->get(['id', 'name']) : [],
        ];
    }
}
