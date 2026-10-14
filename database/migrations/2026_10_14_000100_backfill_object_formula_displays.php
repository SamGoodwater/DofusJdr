<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Rend les formules objet affichables par le futur tooltip commun.
 *
 * Les tables par niveau sont déjà lisibles par CharacteristicFormulaRichText ;
 * une formule numérique telle que `0` reste identique à sa valeur réelle.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('characteristic_object')
            ->whereNotNull('formula')
            ->where('formula', '!=', '')
            ->where(function ($query): void {
                $query->whereNull('formula_display')->orWhere('formula_display', '');
            })
            ->update(['formula_display' => DB::raw('formula')]);

        Cache::forget('characteristics:frontend:v7');
    }

    public function down(): void
    {
        // Migration de données non destructive : impossible de distinguer après coup
        // une valeur ajoutée ici d'une valeur identique déjà saisie volontairement.
        Cache::forget('characteristics:frontend:v7');
    }
};
