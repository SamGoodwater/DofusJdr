<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class prepareCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:prepare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Préparer le projet : mettre à jour le framework, faire la migration, regènéèrer auto-load, géénéré les fichiers   ide-helper:models, generate, eloquent et meta';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Préparation du projet');

        $this->info('Mise à jour des dépendances avec Composer');
        exec('composer update');

        $this->info('Génération des fichiers ide-helper:models');
        $this->call('ide-helper:models');

        $this->info('Génération des fichiers ide-helper:generate');
        $this->call('ide-helper:generate');

        $this->info('Génération des fichiers ide-helper:eloquent');
        $this->call('ide-helper:eloquent');

        $this->info('Génération des fichiers ide-helper:meta');
        $this->call('ide-helper:meta');

        $this->info('Regénération de l\'autoloader de Composer');
        exec('composer dump-autoload');

        $this->info('Installation des dépendances npm');
        exec('npm install');

        $this->info('Exécution des migrations de la base de données');
        $this->call('migrate');

        $this->info('Optimisation du framework');
        $this->call('optimize');
    }
}
