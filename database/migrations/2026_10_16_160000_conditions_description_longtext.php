<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Description d'état sans plafond : TEXT (64 Ko) devient LONGTEXT.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `conditions` MODIFY `description` LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `conditions` MODIFY `description` TEXT NULL');
    }
};
