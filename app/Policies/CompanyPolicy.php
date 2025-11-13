<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
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
     * Determine whether the user can view the company.
     */
    public function view(?User $user, Company $company): bool
    {
        // Public view allowed when active; owner can always view.
        if ($company->is_active) {
            return true;
        }

        return $user && $company->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        return $company->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        return $company->user_id === $user->id;
    }
}
