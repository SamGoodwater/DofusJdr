<?php

declare(strict_types=1);

namespace App\Support\Entity;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Redirection après PATCH d’entité (page vs modal d’édition).
 *
 * `redirect_after_update=stay` → `back()` (modal EntitySurfaceHost / rester sur la page courante).
 */
final class EntityUpdateRedirect
{
    /**
     * @param  callable(): RedirectResponse  $defaultRedirect  Si `redirect_after_update` absent / inconnu
     * @param  (callable(): RedirectResponse)|null  $editRedirect
     * @param  (callable(): RedirectResponse)|null  $indexRedirect
     * @param  (callable(): RedirectResponse)|null  $showRedirect
     */
    public static function respond(
        Request $request,
        string $successMessage,
        callable $defaultRedirect,
        ?callable $editRedirect = null,
        ?callable $indexRedirect = null,
        ?callable $showRedirect = null,
    ): RedirectResponse {
        $redirectAfter = (string) $request->input('redirect_after_update', '');

        $target = match ($redirectAfter) {
            'stay' => fn (): RedirectResponse => back(),
            'edit' => $editRedirect ?? $defaultRedirect,
            'index' => $indexRedirect ?? $defaultRedirect,
            'show' => $showRedirect ?? $defaultRedirect,
            default => $defaultRedirect,
        };

        return $target()->with('success', $successMessage);
    }
}
