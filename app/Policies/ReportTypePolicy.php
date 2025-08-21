<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ReportType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportTypePolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return null;
    }

    public function create(User $user): bool
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return false;
    }

    public function update(User $user, ReportType $reportType): bool
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return false;
    }

    public function destroy(User $user, ReportType $reportType): bool
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return false;
    }
}
