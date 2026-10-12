<?php

declare(strict_types=1);

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute la section outil « Barèmes de sorts » sous Barèmes dégâts/soins (page 5.3).
 */
return new class extends Migration
{
    private const PAGE_SLUG = 'regles-5-3-tables-de-reference';

    private const SECTION_SLUG = 'regle-5-3-1-resistances-et-degats-baremes-outil';

    public function up(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('sections')) {
            return;
        }

        $page = Page::query()->where('slug', self::PAGE_SLUG)->first();
        if ($page === null) {
            return;
        }

        $existing = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', self::SECTION_SLUG)
            ->first();

        if ($existing !== null) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->fill([
                'title' => 'Outil barèmes & conversion',
                'template' => SectionType::SPELL_REFERENCE_SCALES->value,
                'type' => SectionType::SPELL_REFERENCE_SCALES->value,
                'settings' => array_merge(
                    is_array($existing->settings) ? $existing->settings : [],
                    ['compact' => false],
                ),
                'state' => Section::STATE_PLAYABLE,
            ]);
            $existing->save();

            return;
        }

        $after = Section::query()
            ->where('page_id', $page->id)
            ->where('slug', 'regle-5-3-1-resistances-et-degats-baremes-degatssoins')
            ->value('order');
        $insertOrder = is_int($after) ? $after + 1 : 2;

        Section::query()
            ->where('page_id', $page->id)
            ->where('order', '>=', $insertOrder)
            ->orderByDesc('order')
            ->get()
            ->each(function (Section $section): void {
                $section->order = $section->order + 1;
                $section->save();
            });

        $creatorId = User::query()->where('role', '>=', User::ROLE_ADMIN)->value('id');

        Section::query()->create([
            'page_id' => $page->id,
            'title' => 'Outil barèmes & conversion',
            'slug' => self::SECTION_SLUG,
            'order' => $insertOrder,
            'template' => SectionType::SPELL_REFERENCE_SCALES->value,
            'type' => SectionType::SPELL_REFERENCE_SCALES->value,
            'settings' => ['compact' => false],
            'data' => [],
            'params' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => $page->read_level ?? User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('sections')) {
            return;
        }

        $page = Page::query()->where('slug', self::PAGE_SLUG)->first();
        if ($page === null) {
            return;
        }

        Section::query()
            ->where('page_id', $page->id)
            ->where('slug', self::SECTION_SLUG)
            ->each(fn (Section $section) => $section->delete());
    }
};
