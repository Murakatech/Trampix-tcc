<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Freelancer;
use Illuminate\Auth\Access\HandlesAuthorization;

class FreelancerPolicy
{
    use HandlesAuthorization;

    /**
     * Grant all abilities to admins before other checks.
     */
    public function before(User $user, string $ability)
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the freelancer.
     */
    public function view(?User $user, Freelancer $freelancer): bool
    {
        // Public view allowed when active; owner can always view.
        if ($freelancer->is_active) {
            return true;
        }
        return $user && $freelancer->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the freelancer.
     */
    public function update(User $user, Freelancer $freelancer): bool
    {
        return $freelancer->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the freelancer.
     */
    public function delete(User $user, Freelancer $freelancer): bool
    {
        return $freelancer->user_id === $user->id;
    }
}