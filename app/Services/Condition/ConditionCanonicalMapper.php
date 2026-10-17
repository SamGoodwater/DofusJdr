<?php

declare(strict_types=1);

namespace App\Services\Condition;

use App\Models\Entity\Condition;
use Illuminate\Support\Collection;

/**
 * Résout un état Dofus vers un état JDR canonique (`playable`).
 *
 * @example
 * $canonical = $mapper->resolve($rawPesanteur);
 * // Condition playable « Pesanteur », ou null si aucun équivalent de base
 */
final class ConditionCanonicalMapper
{
    /**
     * Alias normalisés → nom normalisé du canon (Pesanteur, Empoisonné, …).
     *
     * @var array<string, string>
     */
    private const ALIASES = [
        'pesanteur' => 'pesanteur',
        'gravity' => 'pesanteur',
        'immobilise' => 'pesanteur',
        'immobilisee' => 'pesanteur',
        'ancre' => 'pesanteur',
        'empoisonne' => 'empoisonne',
        'empoisonnee' => 'empoisonne',
        'poison' => 'empoisonne',
        'intoxique' => 'empoisonne',
        'etourdi' => 'etourdi',
        'etourdie' => 'etourdi',
        'stun' => 'etourdi',
        'stupefie' => 'etourdi',
        'assomme' => 'etourdi',
        'silencieux' => 'etourdi',
        'ralenti' => 'ralenti',
        'ralentie' => 'ralenti',
        'ralentissement' => 'ralenti',
        'affaibli' => 'affaibli',
        'affaiblie' => 'affaibli',
        'faiblesse' => 'affaibli',
        'affaiblissement' => 'affaibli',
    ];

    /** @var Collection<string, Condition>|null */
    private ?Collection $playableByNormalizedName = null;

    /**
     * @example
     * $mapper->resolve($condition)?->name;
     */
    public function resolve(Condition $source): ?Condition
    {
        if ($source->state === Condition::STATE_PLAYABLE) {
            return $source;
        }

        $byName = $this->resolveByName((string) $source->name);
        if ($byName !== null) {
            return $byName;
        }

        return $this->resolveByFlags($source);
    }

    /**
     * Normalise un libellé pour comparer les alias (minuscule, sans accents).
     *
     * @example
     * ConditionCanonicalMapper::normalizeName('Étourdi');
     * // 'etourdi'
     */
    public static function normalizeName(string $name): string
    {
        $s = mb_strtolower(trim($name));
        $s = strtr($s, [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n', 'ÿ' => 'y',
        ]);
        $s = preg_replace('/[^a-z0-9]+/', ' ', $s) ?? $s;

        return trim(preg_replace('/\s+/', ' ', $s) ?? $s);
    }

    public function forgetCachedPlayables(): void
    {
        $this->playableByNormalizedName = null;
    }

    private function resolveByName(string $name): ?Condition
    {
        $normalized = self::normalizeName($name);
        if ($normalized === '') {
            return null;
        }

        $playables = $this->playablesByNormalizedName();
        if ($playables->has($normalized)) {
            return $playables->get($normalized);
        }

        $canonKey = self::ALIASES[$normalized] ?? null;
        if ($canonKey !== null && $playables->has($canonKey)) {
            return $playables->get($canonKey);
        }

        foreach (self::ALIASES as $alias => $canonKey) {
            if ($alias === '' || ! preg_match('/\b'.preg_quote($alias, '/').'\b/u', $normalized)) {
                continue;
            }
            if ($playables->has($canonKey)) {
                return $playables->get($canonKey);
            }
        }

        return null;
    }

    private function resolveByFlags(Condition $source): ?Condition
    {
        $playables = $this->playablesByNormalizedName();
        if ((bool) $source->cant_be_moved || (bool) $source->cant_be_pushed || (bool) $source->cant_switch_position) {
            return $playables->get('pesanteur');
        }
        if ((bool) $source->prevents_spell_cast || (bool) $source->prevents_fight) {
            return $playables->get('etourdi');
        }

        return null;
    }

    /**
     * @return Collection<string, Condition>
     */
    private function playablesByNormalizedName(): Collection
    {
        if ($this->playableByNormalizedName instanceof Collection) {
            return $this->playableByNormalizedName;
        }

        $this->playableByNormalizedName = Condition::query()
            ->where('state', Condition::STATE_PLAYABLE)
            ->orderBy('id')
            ->get()
            ->keyBy(fn (Condition $condition): string => self::normalizeName((string) $condition->name));

        return $this->playableByNormalizedName;
    }
}
