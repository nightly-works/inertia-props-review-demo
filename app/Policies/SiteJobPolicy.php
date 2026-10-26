<?php

namespace App\Policies;

use App\Models\SiteJob;
use App\Models\User;

class SiteJobPolicy
{
    // Prices, dates and assignments are set by managers.
    public function update(User $user, SiteJob $job): bool
    {
        return $user->isManager();
    }
}
