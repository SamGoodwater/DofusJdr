<?php

declare(strict_types=1);

namespace App\Services\Characteristics;

use App\Support\Characteristics\CharacteristicDefinitionJson;
use App\Support\Characteristics\CharacteristicDefinitionNaming;

/**
 * Parcourt les fichiers `stem-groupe-definition.json` et charge le contenu utile au seed.
 */
final class CharacteristicDefinitionReader
{
    /**
     * @return list<string> chemins absolus triés
     */
    public static function allDefinitionAbsolutePaths(): array
    {
        $paths = [];
        foreach (CharacteristicDefinitionNaming::groups() as $group) {
            $dir = base_path(CharacteristicDefinitionNaming::RELATIVE_ROOT.'/'.$group);
            if (! is_dir($dir)) {
                continue;
            }
            $glob = glob($dir.DIRECTORY_SEPARATOR.'*-definition.json') ?: [];
            foreach ($glob as $p) {
                if (is_file($p)) {
                    $paths[] = $p;
                }
            }
        }
        sort($paths);

        return $paths;
    }

    /**
     * Vérifie que le fichier est sous {@see CharacteristicDefinitionNaming::RELATIVE_ROOT} (contre les chemins arbitraires).
     */
    public static function assertPathUnderDefinitionsRoot(string $absolutePath): void
    {
        $realFile = realpath($absolutePath);
        $definitionsRoot = realpath(base_path(CharacteristicDefinitionNaming::RELATIVE_ROOT));
        if ($realFile === false || $definitionsRoot === false || ! is_file($realFile)) {
            throw new \InvalidArgumentException('Fichier définition introuvable ou inaccessible : '.$absolutePath);
        }
        $prefix = $definitionsRoot.DIRECTORY_SEPARATOR;
        if ($realFile !== $definitionsRoot && ! str_starts_with($realFile, $prefix)) {
            throw new \InvalidArgumentException(
                'Le fichier définition doit être sous '.CharacteristicDefinitionNaming::RELATIVE_ROOT.' : '.$absolutePath
            );
        }
    }

    /**
     * @return array{characteristic: array<string, mixed>, entities: array<string, array<string, mixed>>, relations: mixed}
     */
    public static function load(string $absolutePath): array
    {
        self::assertPathUnderDefinitionsRoot($absolutePath);
        $raw = CharacteristicDefinitionJson::decodeFile($absolutePath);
        $clean = CharacteristicDefinitionJson::stripUnderscoreKeys($raw);
        if (! is_array($clean)) {
            throw new \RuntimeException('Définition invalide : '.$absolutePath);
        }
        $characteristic = $clean['characteristic'] ?? null;
        $entities = $clean['entities'] ?? null;
        if (! is_array($characteristic) || ! isset($characteristic['key'])) {
            throw new \RuntimeException('Bloc characteristic.key manquant : '.$absolutePath);
        }
        if (! is_array($entities)) {
            $entities = [];
        }

        return [
            'characteristic' => $characteristic,
            'entities' => $entities,
            'relations' => $clean['relations'] ?? null,
        ];
    }
}
