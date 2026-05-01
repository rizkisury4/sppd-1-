<?php

namespace App\Policies;

use App\Models\Sppd\SppdRequest;
use App\Models\User;

class SppdRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SppdRequest $sppd): bool
    {
        return in_array($user->role, ['admin','manager','finance','direksi'], true) || $sppd->pegawai_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, SppdRequest $sppd): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, SppdRequest $sppd): bool
    {
        return $user->role === 'admin';
    }

    public function approve(User $user, SppdRequest $sppd): bool
    {
        return in_array($user->role, ['manager','direksi'], true);
    }
}
