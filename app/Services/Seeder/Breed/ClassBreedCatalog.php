<?php

declare(strict_types=1);

namespace App\Services\Seeder\Breed;

use App\Models\Entity\Breed;
use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue des fiches classes JDR versionnées (JSON `entities/breeds`).
 *
 * @example $catalog = ClassBreedCatalog::load(ClassBreedCatalog::sacrieurPath());
 */
final class ClassBreedCatalog
{
    public const SCHEMA_VERSION = '1';

    public const DESCRIPTION_MAX = 255;

    /**
     * Ordre d’import des 19 classes (ids Dofus 1–18 puis Forgelance 20), puis tout autre JSON du dossier.
     *
     * @var list<string>
     */
    public const PREFERRED_FILES = [
        'feca.json',
        'osamodas.json',
        'enutrof.json',
        'sram.json',
        'xelor.json',
        'ecaflip.json',
        'eniripsa.json',
        'iop.json',
        'cra.json',
        'sadida.json',
        'sacrieur.json',
        'pandawa.json',
        'roublard.json',
        'zobal.json',
        'steamer.json',
        'eliotrope.json',
        'huppermage.json',
        'ouginak.json',
        'forgelance.json',
    ];

    /**
     * @var list<string>
     */
    public const BASE_CLASS_NAMES = [
        'Féca',
        'Osamodas',
        'Enutrof',
        'Sram',
        'Xélor',
        'Ecaflip',
        'Eniripsa',
        'Iop',
        'Crâ',
        'Sadida',
        'Sacrieur',
        'Pandawa',
        'Roublard',
        'Zobal',
        'Steamer',
        'Eliotrope',
        'Huppermage',
        'Ouginak',
        'Forgelance',
    ];

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        private readonly array $payload,
        private readonly string $slug = '',
    ) {}

    /**
     * Slug dossier ASCII (`iop`, `cra`) — basename du JSON sans extension.
     */
    public function slug(): string
    {
        return $this->slug;
    }

    public static function directory(): string
    {
        return database_path('seeders/data/entities/breeds');
    }

    public static function path(string $basename): string
    {
        return self::directory().'/'.$basename;
    }

    public static function fecaPath(): string
    {
        return self::path('feca.json');
    }

    public static function iopPath(): string
    {
        return self::path('iop.json');
    }

    public static function sacrieurPath(): string
    {
        return self::path('sacrieur.json');
    }

    public static function pandawaPath(): string
    {
        return self::path('pandawa.json');
    }

    /**
     * @return list<self>
     */
    public static function loadAllInDirectory(?string $directory = null): array
    {
        $dir = $directory ?? self::directory();
        if (! is_dir($dir)) {
            return [];
        }

        $files = File::glob($dir.'/*.json') ?: [];
        sort($files);

        $byBasename = [];
        foreach ($files as $file) {
            $byBasename[basename($file)] = $file;
        }

        $ordered = [];
        foreach (self::PREFERRED_FILES as $preferred) {
            if (isset($byBasename[$preferred])) {
                $ordered[] = $byBasename[$preferred];
                unset($byBasename[$preferred]);
            }
        }
        foreach ($byBasename as $file) {
            $ordered[] = $file;
        }

        $catalogs = [];
        foreach ($ordered as $file) {
            $catalogs[] = self::load($file);
        }

        return $catalogs;
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::sacrieurPath();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue de classe introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);
        $slug = strtolower(pathinfo($file, PATHINFO_FILENAME));

        return new self($payload, $slug);
    }

    /**
     * @return array{
     *     name: string,
     *     dofusdb_id: string|null,
     *     official_id: string|null,
     *     description_fast: string|null,
     *     description: string|null,
     *     specificity: string|null,
     *     mechanics: string|null,
     *     dofus_version: string,
     *     state: string,
     *     read_level: int,
     *     write_level: int,
     *     element_orientations: array<string, string|null>
     * }|null
     */
    public function entry(): ?array
    {
        $name = trim((string) ($this->payload['name'] ?? ''));
        if ($name === '') {
            return null;
        }

        $state = trim((string) ($this->payload['state'] ?? Breed::STATE_DRAFT));
        $allowedStates = [
            Breed::STATE_RAW,
            Breed::STATE_DRAFT,
            Breed::STATE_AUTO,
            Breed::STATE_PLAYABLE,
            Breed::STATE_ARCHIVED,
        ];
        if (! in_array($state, $allowedStates, true)) {
            $state = Breed::STATE_DRAFT;
        }

        $orientations = [];
        $raw = $this->payload['element_orientations'] ?? [];
        if (is_array($raw)) {
            foreach ($raw as $element => $key) {
                $el = trim((string) $element);
                if ($el === '') {
                    continue;
                }
                $orientations[$el] = $this->nullableString($key);
            }
        }

        return [
            'name' => $name,
            'dofusdb_id' => $this->nullableString($this->payload['dofusdb_id'] ?? null),
            'official_id' => $this->nullableString($this->payload['official_id'] ?? null),
            'description_fast' => $this->truncate($this->nullableString($this->payload['description_fast'] ?? null)),
            'description' => $this->truncate($this->nullableString($this->payload['description'] ?? null)),
            'specificity' => $this->truncate($this->nullableString($this->payload['specificity'] ?? null)),
            'mechanics' => $this->nullableString($this->payload['mechanics'] ?? null),
            'dofus_version' => $this->nullableString($this->payload['dofus_version'] ?? null) ?? '3',
            'state' => $state,
            'read_level' => max(0, (int) ($this->payload['read_level'] ?? 0)),
            'write_level' => max(0, (int) ($this->payload['write_level'] ?? 3)),
            'element_orientations' => $orientations,
        ];
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value) && ! is_int($value)) {
            return null;
        }
        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function truncate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (mb_strlen($value) <= self::DESCRIPTION_MAX) {
            return $value;
        }

        return mb_substr($value, 0, self::DESCRIPTION_MAX);
    }
}
