<?php

declare(strict_types=1);

use App\Support\Profile\ProfileMasteryColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Maîtrises de compétences (0–2) et de sauvegardes (0–1) sur classe, spécialisation et monstre.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['breeds', 'specializations', 'monsters'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                foreach (ProfileMasteryColumns::SKILL_COLUMNS as $column) {
                    if (! Schema::hasColumn($tableName, $column)) {
                        $table->unsignedTinyInteger($column)->default(0);
                    }
                }
                foreach (ProfileMasteryColumns::SAVE_COLUMNS as $column) {
                    if (! Schema::hasColumn($tableName, $column)) {
                        $table->unsignedTinyInteger($column)->default(0);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['breeds', 'specializations', 'monsters'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $drop = [];
                foreach (ProfileMasteryColumns::all() as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $drop[] = $column;
                    }
                }
                if ($drop !== []) {
                    $table->dropColumn($drop);
                }
            });
        }
    }
};
