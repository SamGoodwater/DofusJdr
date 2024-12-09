<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestGenerator extends Command
{
    protected $signature = 'generate:test {name}';
    protected $description = 'Générer un test PHPUnit pour un modèle donné';

    public function handle()
    {
        $name = $this->argument('name');
        $cheminFichierTest = base_path("tests/Feature/{$name}Test.php");

        if (File::exists($cheminFichierTest)) {
            $this->error("Le fichier de test existe déjà !");
            return;
        }

        $contenuTest = $this->generateTestContent($name);
        File::put($cheminFichierTest, $contenuTest);
        $this->info("Fichier de test créé avec succès : {$cheminFichierTest}");
    }

    protected function generateTestContent($name)
    {
        return "<?php

            namespace Tests\\Feature;

            use Tests\\TestCase;

            class {$name}Test extends TestCase
            {
                public function test_example()
                {
                    \$this->assertTrue(true);
                }
            }";
    }
}
