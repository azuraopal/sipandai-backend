<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OpdPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)//: bool
    {
        // return $user->role === UserRole::CITY_ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Opd $opd)//: bool
    {
        // return $user->role === UserRole::CITY_ADMIN;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)//: bool
    {
        // return $user->role === UserRole::CITY_ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Opd $opd)//: bool
    {
        // return $user->role === UserRole::CITY_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Opd $opd)//: bool
    {
        // return $user->role === UserRole::CITY_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Opd $opd)//: bool
    {
        // return $user->role === UserRole::CITY_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Opd $opd)//: bool
    {
        // return $user->role === UserRole::CITY_ADMIN;
    }
}