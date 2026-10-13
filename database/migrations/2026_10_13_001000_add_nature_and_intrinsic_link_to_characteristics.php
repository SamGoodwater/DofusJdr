<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nature (rôle) et lien vers l'intrinsèque créature modifiée (bonus/malus, buff/débuff).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characteristics', function (Blueprint $table) {
            $table->string('nature', 32)->nullable()->after('group')
                ->comment('descriptif|intrinsic|bonus_malus|buff_debuff|action_value');
            $table->foreignId('intrinsic_characteristic_id')->nullable()->after('linked_to_characteristic_id')
                ->constrained('characteristics')->nullOnDelete()
                ->comment('Intrinsèque créature modifiée (bonus_malus / buff_debuff)');
        });
    }

    public function down(): void
    {
        Schema::table('characteristics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('intrinsic_characteristic_id');
            $table->dropColumn('nature');
        });
    }
};
