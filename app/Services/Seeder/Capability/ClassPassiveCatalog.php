<?php

declare(strict_types=1);

namespace App\Services\Seeder\Capability;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

/**
 * Catalogue des 19 passifs de classe JDR (JSON `entities/capabilities/class-passives.json`).
 *
 * @example $catalog = ClassPassiveCatalog::load();
 */
final class ClassPassiveCatalog
{
    public const SCHEMA_VERSION = '1';

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(private readonly array $payload) {}

    public static function path(): string
    {
        return database_path('seeders/data/entities/capabilities/class-passives.json');
    }

    /**
     * @throws JsonException
     */
    public static function load(?string $path = null): self
    {
        $file = $path ?? self::path();
        if (! File::isFile($file)) {
            throw new RuntimeException('Catalogue de passifs de classe introuvable : '.$file);
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);

        return new self($payload);
    }

    /**
     * @return list<array{
     *     breed: string,
     *     name: string,
     *     engine: string,
     *     is_magic: bool,
     *     description: string,
     *     effect: string,
     *     previous_name: string
     * }>
     */
    public function entries(): array
    {
        $raw = $this->payload['entries'] ?? [];
        if (! is_array($raw)) {
            return [];
        }

        $entries = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $breed = trim((string) ($row['breed'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));
            if ($breed === '' || $name === '') {
                continue;
            }
            $entries[] = [
                'breed' => $breed,
                'name' => $name,
                'engine' => (string) ($row['engine'] ?? ''),
                'is_magic' => (bool) ($row['is_magic'] ?? true),
                'description' => (string) ($row['description'] ?? ''),
                'effect' => (string) ($row['effect'] ?? ''),
                'previous_name' => trim((string) ($row['previous_name'] ?? '')),
            ];
        }

        return $entries;
    }
}
