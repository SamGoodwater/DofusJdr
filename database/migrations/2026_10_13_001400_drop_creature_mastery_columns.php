<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Les maîtrises quittent la fiche créature (groupe Profil). Export rapport avant drop.
 */
return new class extends Migration
{
    /**
     * Anciennes colonnes françaises de maîtrise sur creatures.
     *
     * @var list<string>
     */
    private const SKILL_COLUMNS = [
        'acrobatie_mastery',
        'discretion_mastery',
        'escamotage_mastery',
        'athletisme_mastery',
        'intimidation_mastery',
        'dressage_mastery',
        'medecine_mastery',
        'nature_mastery',
        'perception_mastery',
        'perspicacite_mastery',
        'survie_mastery',
        'arcane_mastery',
        'histoire_mastery',
        'investigation_mastery',
        'religion_mastery',
        'supercherie_mastery',
        'representation_mastery',
        'persuasion_mastery',
        'artisanat_mastery',
        'herbaliste_mastery',
        'connaissance_creatures_mastery',
    ];

    /**
     * @var list<string>
     */
    private const SAVE_COLUMNS = [
        'save_vitality_mastery',
        'save_wisdom_mastery',
        'save_strength_mastery',
        'save_intelligence_mastery',
        'save_chance_mastery',
        'save_agility_mastery',
    ];

    public function up(): void
    {
        $columns = array_values(array_filter(
            [...self::SKILL_COLUMNS, ...self::SAVE_COLUMNS],
            static fn (string $c): bool => Schema::hasColumn('creatures', $c)
        ));

        if ($columns !== []) {
            $select = array_merge(['id', 'name'], $columns);
            $rows = DB::table('creatures')->select($select)->get();
            $reportDir = storage_path('app/reports');
            if (! is_dir($reportDir)) {
                mkdir($reportDir, 0755, true);
            }
            $path = $reportDir.'/creature-masteries-before-drop-'.date('Ymd-His').'.json';
            file_put_contents($path, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        Schema::table('creatures', function (Blueprint $table) use ($columns) {
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('creatures', function (Blueprint $table) {
            foreach (self::SKILL_COLUMNS as $column) {
                if (! Schema::hasColumn('creatures', $column)) {
                    $table->tinyInteger($column)->default(0);
                }
            }
            foreach (self::SAVE_COLUMNS as $column) {
                if (! Schema::hasColumn('creatures', $column)) {
                    $table->unsignedTinyInteger($column)->default(0);
                }
            }
        });
    }
};
