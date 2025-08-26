<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ReportAssignment;
use App\Models\ReportUserAssignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportUserAssignmentPolicy
{   
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReportUserAssignment $reportUserAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $admin, User $officer): bool
    {
        if ($admin->role->value !== UserRole::OPD_ADMIN->value) {
            return false;
        }

        return $admin->opd_id === $officer->opd_id;
    }

    public function end(User $user, ReportUserAssignment $reportuserassignment)
    {
        return $user->id === $reportuserassignment->officer_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReportUserAssignment $reportUserAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReportUserAssignment $reportUserAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReportUserAssignment $reportUserAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReportUserAssignment $reportUserAssignment): bool
    {
        return false;
    }
}