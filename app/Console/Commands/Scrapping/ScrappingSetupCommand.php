<?php

declare(strict_types=1);

namespace App\Console\Commands\Scrapping;

use App\Console\ArtisanExitCode;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\DofusdbCharacteristicIdSeeder;
use Database\Seeders\DofusdbEffectMappingSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\RegenerateCharacteristicNormsSeeder;
use Database\Seeders\ScrappingEntityMappingCharacteristicSeeder;
use Database\Seeders\ScrappingEntityMappingSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Database\Seeders\SpellEffectTypeSeeder;
use Database\Seeders\Type\TypeSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Bootstrap du socle scrapping (migrations + seeders essentiels).
 *
 * Cette commande initialise les données indispensables au pipeline:
 * caractéristiques, mappings DofusDB, mappings scrapping par entité.
 *
 * @example php artisan scrapping:setup
 * @example php artisan scrapping:setup --fresh
 * @example php artisan scrapping:setup --skip-migrate
 */
class ScrappingSetupCommand extends Command
{
    protected $signature = 'scrapping:setup
                            {--fresh : Exécute migrate:fresh --force avant les seeders}
                            {--skip-migrate : Ne lance pas les migrations}';

    protected $description = 'Initialise le socle scrapping (migrations + caractéristiques + mappings)';

    /** @var list<class-string> */
    private const SEEDERS = [
        TypeSeeder::class,
        CharacteristicSeeder::class,
        CreatureCharacteristicSeeder::class,
        ObjectCharacteristicSeeder::class,
        DofusdbCharacteristicIdSeeder::class,
        SpellCharacteristicSeeder::class,
        RegenerateCharacteristicNormsSeeder::class,
        SpellEffectTypeSeeder::class,
        DofusdbEffectMappingSeeder::class,
        ScrappingEntityMappingSeeder::class,
        ScrappingEntityMappingCharacteristicSeeder::class,
    ];

    public function handle(): int
    {
        $this->info('Bootstrap scrapping: démarrage');

        if (! (bool) $this->option('skip-migrate')) {
            $migrationCommand = (bool) $this->option('fresh') ? 'migrate:fresh' : 'migrate';
            $this->info("Exécution: php artisan {$migrationCommand} --force");
            $code = Artisan::call($migrationCommand, ['--force' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->error("Échec de {$migrationCommand}.");

                return ArtisanExitCode::FAILURE;
            }
        } else {
            $this->warn('Migrations ignorées (--skip-migrate).');
        }

        foreach (self::SEEDERS as $seederClass) {
            $this->info("Seeding: {$seederClass}");
            $code = Artisan::call('db:seed', [
                '--class' => $seederClass,
                '--force' => true,
            ]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->error("Échec du seeder {$seederClass}.");

                return ArtisanExitCode::FAILURE;
            }
        }

        $this->info('Bootstrap scrapping: terminé.');

        return ArtisanExitCode::SUCCESS;
    }
}
