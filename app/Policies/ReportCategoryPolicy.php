<?php
namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ReportCategory;
use App\Models\User;

class ReportCategoryPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return null;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::CITY_ADMIN;
    }

    public function update(User $user, ReportCategory $reportCategory): bool
    {
        return $user->role === UserRole::CITY_ADMIN;
    }

    public function destroy(User $user, ReportCategory $reportCategory): bool
    {
        return false;
    }
}