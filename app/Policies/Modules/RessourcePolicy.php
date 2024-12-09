<?php

namespace App\Policies\Modules;

use App\Models\Modules\Ressource;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RessourcePolicy
{
    public function before(User $user): ?bool
    {
        if ($user->role === User::ROLES['super_admin']) {
            return true;
        }
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ressource $ressource): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->verifyRole(User::ROLES['game_master']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ressource $ressource): bool
    {
        if ($ressource->created_by === $user->id) {
            return $user->verifyRole(User::ROLES['game_master']);
        } else {
            return $user->verifyRole(User::ROLES['contributor']);
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ressource $ressource): bool
    {
        if ($ressource->created_by === $user->id) {
            return $user->verifyRole(User::ROLES['game_master']);
        } else {
            return $user->verifyRole(User::ROLES['contributor']);
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ressource $ressource): bool
    {
        return $user->verifyRole(User::ROLES['admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ressource $ressource): bool
    {
        return $user->verifyRole(User::ROLES['admin']);
    }
}
