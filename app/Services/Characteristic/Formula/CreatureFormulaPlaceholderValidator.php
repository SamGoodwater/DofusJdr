<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

use App\Models\Characteristic;
use App\Models\Entity\Creature;
use App\Services\Creature\Runtime\CreatureObjectBonusToCreatureVariables;
use Illuminate\Support\Facades\Schema;

/**
 * Vérifie que les placeholders `[id]` des définitions créature pointent vers des identifiants connus
 * (caractéristiques, colonnes créature, alias courts, bonus compétences français, `d` pour conversions).
 *
 * @example
 *   $v = app(CreatureFormulaPlaceholderValidator::class);
 *   $errors = $v->validateCreatureDefinitionsDirectory(database_path('seeders/data/characteristic-definitions/creature'));
 */
final class CreatureFormulaPlaceholderValidator
{
    public function __construct(
        private readonly FormulaResolutionService $formulas,
    ) {}

    /**
     * Construit l’ensemble des identifiants autorisés dans les crochets (hors fonctions).
     *
     * @return array<string, true>
     */
    public function buildAllowedPlaceholderSet(): array
    {
        /** @var array<string, true> $allowed */
        $allowed = [];

        foreach (Characteristic::query()->orderBy('id')->pluck('key') as $key) {
            if (! is_string($key) || $key === '') {
                continue;
            }
            $this->registerKeyAndShortAliases($allowed, $key);
        }

        $table = (new Creature)->getTable();
        if (Schema::hasTable($table)) {
            foreach (Schema::getColumnListing($table) as $col) {
                if ($col !== '') {
                    $allowed[$col] = true;
                }
            }
        }

        foreach (CreatureObjectBonusToCreatureVariables::frenchSkillBonusVariableNames() as $fr) {
            $allowed[$fr] = true;
        }

        /* Variable de conversion Dofus → Krosmoz (tables JSON, etc.) */
        $allowed['d'] = true;
        /* Valeur intrinsèque injectée par CreatureRuntimeStatsService (colonne, sinon default_value) */
        $allowed['base'] = true;

        return $allowed;
    }

    /**
     * @return list<array{file: string, characteristic: string, entity: string, field: string, unknown: string}>
     */
    public function validateCreatureDefinitionsDirectory(string $absoluteDirectoryPath): array
    {
        $allowed = $this->buildAllowedPlaceholderSet();
        $pattern = rtrim($absoluteDirectoryPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'*-creature-definition.json';
        $files = glob($pattern) ?: [];
        sort($files);

        $errors = [];
        foreach ($files as $file) {
            $errors = array_merge($errors, $this->validateCreatureDefinitionFile($file, $allowed));
        }

        return $errors;
    }

    /**
     * @param  array<string, true>|null  $allowed  Si null, reconstruit via BDD.
     * @return list<array{file: string, characteristic: string, entity: string, field: string, unknown: string}>
     */
    public function validateCreatureDefinitionFile(string $absolutePath, ?array $allowed = null): array
    {
        $allowed ??= $this->buildAllowedPlaceholderSet();
        $raw = @file_get_contents($absolutePath);
        if ($raw === false) {
            return [[
                'file' => $absolutePath,
                'characteristic' => '?',
                'entity' => '?',
                'field' => 'read',
                'unknown' => 'fichier illisible',
            ]];
        }

        try {
            /** @var mixed $data */
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [[
                'file' => $absolutePath,
                'characteristic' => '?',
                'entity' => '?',
                'field' => 'json',
                'unknown' => 'JSON invalide',
            ]];
        }

        if (! is_array($data)) {
            return [];
        }

        $charKey = is_string($data['characteristic']['key'] ?? null) ? $data['characteristic']['key'] : 'unknown';
        $entities = $data['entities'] ?? [];
        if (! is_array($entities)) {
            return [];
        }

        $errors = [];
        foreach ($entities as $entityName => $row) {
            if (! is_array($row)) {
                continue;
            }
            foreach (['formula', 'conversion_formula'] as $field) {
                $value = $row[$field] ?? null;
                if (! is_string($value) || trim($value) === '') {
                    continue;
                }
                $ids = $this->formulas->extractVariablePlaceholders($value);
                foreach ($ids as $id) {
                    if (! isset($allowed[$id])) {
                        $errors[] = [
                            'file' => $absolutePath,
                            'characteristic' => $charKey,
                            'entity' => is_string($entityName) ? $entityName : '?',
                            'field' => $field,
                            'unknown' => $id,
                        ];
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * @param  array<string, true>  $allowed
     */
    private function registerKeyAndShortAliases(array &$allowed, string $key): void
    {
        $allowed[$key] = true;
        foreach (['_creature', '_object', '_spell'] as $suffix) {
            $fullSuffix = $suffix;
            if (str_ends_with($key, $fullSuffix)) {
                $short = substr($key, 0, -strlen($fullSuffix));
                if ($short !== '') {
                    $allowed[$short] = true;
                }
                break;
            }
        }
    }
}
