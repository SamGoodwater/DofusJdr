<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use App\Console\ArtisanExitCode;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * --overwrite sans -y en non-interactif doit échouer (pas d’écrasement silencieux).
 */
final class ConfirmsSeedOverwriteTest extends TestCase
{
    public function test_project_init_overwrite_without_yes_aborts_non_interactively(): void
    {
        $code = Artisan::call('project:init', [
            '--overwrite' => true,
            '--skip-migrate' => true,
            '--skip-seeders' => true,
            '--skip-scrapping' => true,
            '--skip-types' => true,
            '--skip-capabilities' => true,
            '--skip-specializations' => true,
            '--skip-notify' => true,
            '--skip-super-admin-prompt' => true,
            '--skip-downloads' => true,
            '--no-interaction' => true,
        ]);

        $this->assertSame(ArtisanExitCode::FAILURE, $code);
        $output = strtolower(Artisan::output());
        $this->assertTrue(
            str_contains($output, 'overwrite') || str_contains($output, 'annulé'),
            'Expected overwrite abort message, got: '.$output
        );
    }
}
