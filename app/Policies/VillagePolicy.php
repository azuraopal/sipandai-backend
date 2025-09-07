<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Village;
use App\Enums\UserRole;

class VillagePolicy
{
    public function before(User $user, string $ability)//: bool|null
    {
        // if ($user->role === UserRole::CITY_ADMIN) {
        //     return true;
        // }
        // return null;
    }

    public function viewAny(User $user)//: bool
    {
        // return true;
    }

    public function view(User $user, Village $village)//: bool
    {
        // return true;
    }

    public function create(User $user, Village $village)//: bool
    {
        // return in_array($user->role, [
        //     UserRole::CITY_ADMIN,
        //     UserRole::DISTRICT_ADMIN
        // ]);
    }

    public function update(User $user, Village $village)//: bool
    {
        // return in_array($user->role, [
        //     UserRole::CITY_ADMIN,
        //     UserRole::DISTRICT_ADMIN
        // ]);
    }

    public function destroy(User $user, Village $village)//: bool
    {
        // return in_array($user->role, [
        //     UserRole::CITY_ADMIN,
        //     UserRole::DISTRICT_ADMIN
        // ]);
    }
}