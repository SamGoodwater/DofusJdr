<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Models\User;

/**
 * Niveaux d’accès (`read_level` / `write_level`) : mutation réservée aux admins (SEC-07).
 *
 * Utilisé par les Form Requests, les PATCH bulk, et le rétablissement
 * d’instantané DofusDB/IA (`EntityUpdateDiffService`).
 *
 * @example
 * $payload = AccessLevelMutationGuard::stripUnlessAdmin($request->user(), $validated);
 */
final class AccessLevelMutationGuard
{
    /** @var list<string> */
    public const KEYS = ['read_level', 'write_level'];

    /**
     * Un admin (rôle ≥ 4) peut poser les niveaux d’accès.
     */
    public static function userMayMutate(?User $user): bool
    {
        return $user?->isAdmin() === true;
    }

    /**
     * Retire read_level / write_level du payload si l’acteur n’est pas admin.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function stripUnlessAdmin(?User $user, array $payload): array
    {
        if (self::userMayMutate($user)) {
            return $payload;
        }

        foreach (self::KEYS as $key) {
            unset($payload[$key]);
        }

        return $payload;
    }
}
