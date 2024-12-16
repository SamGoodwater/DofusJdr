<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class loadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lancer les servers de développement php et node';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $runNpmServer = '-w load sp -V cmd /c "cd ' . getcwd() . ' && npm run dev';
        $runPhpServer = '-w load sp -V php artisan serve';

        $this->info('Optimisation de Laravel');
        $this->call('optimize');

        $this->info('Lancement du serveur PHP');
        $this->info($runPhpServer);
        exec('wt ' . $runPhpServer);

        $this->info('Lancement du serveur Node');
        $this->info($runNpmServer);
        exec('wt ' . $runNpmServer);
    }
}
