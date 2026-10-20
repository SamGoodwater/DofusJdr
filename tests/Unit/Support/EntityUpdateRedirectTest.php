<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Entity\EntityUpdateRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * @covers \App\Support\Entity\EntityUpdateRedirect
 */
class EntityUpdateRedirectTest extends TestCase
{
    public function test_stay_returns_back(): void
    {
        $request = Request::create('/entities/capabilities/1', 'PATCH', [
            'redirect_after_update' => 'stay',
        ]);
        $request->headers->set('Referer', url('/entities/breeds/1'));
        $this->app->instance('request', $request);

        $response = EntityUpdateRedirect::respond(
            $request,
            'ok',
            fn (): RedirectResponse => redirect()->to('/default'),
            editRedirect: fn (): RedirectResponse => redirect()->to('/edit'),
        );

        $this->assertTrue($response->isRedirect());
        $location = (string) $response->headers->get('Location');
        $this->assertStringEndsWith('/entities/breeds/1', $location);
        $this->assertStringNotContainsString('/edit', $location);
        $this->assertStringNotContainsString('/default', $location);
        $this->assertSame('ok', session('success'));
    }

    public function test_edit_uses_edit_redirect(): void
    {
        $request = Request::create('/entities/capabilities/1', 'PATCH', [
            'redirect_after_update' => 'edit',
        ]);

        $response = EntityUpdateRedirect::respond(
            $request,
            'ok',
            fn (): RedirectResponse => redirect()->to('/default'),
            editRedirect: fn (): RedirectResponse => redirect()->to('/edit'),
        );

        $this->assertSame(url('/edit'), $response->headers->get('Location'));
    }

    public function test_missing_target_uses_default(): void
    {
        $request = Request::create('/entities/capabilities/1', 'PATCH', []);

        $response = EntityUpdateRedirect::respond(
            $request,
            'ok',
            fn (): RedirectResponse => redirect()->to('/default'),
            editRedirect: fn (): RedirectResponse => redirect()->to('/edit'),
        );

        $this->assertSame(url('/default'), $response->headers->get('Location'));
    }
}
