<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\District;
use App\Models\User;

class DistrictPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user)//: bool
    {
        // if ($user->role->value === UserRole::CITY_ADMIN->value) {
        //     return true;
        // }
        // return true;
    }

    public function view(User $user, District $district)//: bool
    {
        // if ($user->role->value === UserRole::CITY_ADMIN->value) {
        //     return true;
        // }
        // return false;
    }

    public function create(User $user): bool
    {
        if ($user->role->value === UserRole::CITY_ADMIN->value) {
            return true;
        }
        return false;
    }

    public function update(User $user, District $district)//: bool
    {
        // if ($user->role->value === UserRole::CITY_ADMIN->value) {
        //     return true;
        // }
        // return false;
    }

    public function destroy(User $user, District $district)//: bool
    {
        // if ($user->role->value === UserRole::CITY_ADMIN->value) {
        //     return true;
        // }
        // return false;
    }
}