<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IconsJsonGenerator extends Command
{

    const PATH_DEST = 'storage/app/public/icons/icons.json';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:IconsGenerator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère un fichier JSON contenant les liens de toutes les images dans des dossiers spécifiques';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $directories = [
            'storage/app/public/icons/',
            'storage/app/public/icons/modules/',
            'storage/app/public/icons/modules/classes',
            'storage/app/public/icons/modules/dices',
            'storage/app/public/icons/modules/spell_zone',
            'storage/app/public/icons/modules/classe_orientations/',
        ];

        $imageLinks = [];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                $this->error('Le dossier ' . $directory . ' n\'existe pas.');
                continue;
            }
            $this->info('Recherche d\'images dans le dossier : ' . $directory);
            $files = $this->getAllFiles($directory);
            foreach ($files as $file) {
                if (file_exists($file)) {
                    $fileName = pathinfo($file, PATHINFO_FILENAME);
                    $relativePath = str_replace('storage/app/public/', 'storage/', $file);
                    $this->info('Image trouvée : ' . $file);
                    $directoryName = basename(rtrim($directory, '/'));
                    if (!isset($imageLinks[$directoryName])) {
                        $imageLinks[$directoryName] = [];
                    }
                    $imageLinks[$directoryName][$fileName] = $relativePath;
                    $this->info('Lien de l\'image : [' . $directoryName . "] [" . $fileName . '] => ' . $relativePath);
                } else {
                    $this->error('Image non trouvée : ' . $file);
                    continue;
                }
            }
        }

        // Vérifiez et créez le répertoire si nécessaire
        $directoryPath = dirname(self::PATH_DEST);
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $json = json_encode($imageLinks, JSON_PRETTY_PRINT); // ou JSON_UNESCAPED_UNICODE
        file_put_contents(self::PATH_DEST, $json);

        $this->info('Fichier JSON généré avec succès.');

        return 0;
    }

    /**
     * Get all files from a directory recursively.
     *
     * @param string $directory
     * @return array
     */
    private function getAllFiles($directory)
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }
}
