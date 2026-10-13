<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Les ressources sortent du système de caractéristiques (entité pivot object).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('characteristic_object')->where('entity', 'resource')->delete();
    }

    public function down(): void
    {
        // Irréversible : les surcharges resource doivent être reseedées depuis JSON si besoin.
    }
};
