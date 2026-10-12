<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

/**
 * La purge d’import TOC ne doit supprimer que les sections texte orphelines.
 */
final class PagesImportRulesTocPurgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_purge_keeps_non_text_orphan_sections(): void
    {
        $page = Page::factory()->create([
            'slug' => 'test-purge-page',
            'read_level' => User::ROLE_GAME_MASTER,
        ]);

        $textOrphan = Section::factory()->create([
            'page_id' => $page->id,
            'slug' => 'orphan-text',
            'template' => SectionType::TEXT,
            'type' => SectionType::TEXT,
            'order' => 1,
        ]);

        $toolKept = Section::factory()->create([
            'page_id' => $page->id,
            'slug' => 'orphan-tool',
            'template' => SectionType::SPELL_REFERENCE_SCALES,
            'type' => SectionType::SPELL_REFERENCE_SCALES,
            'order' => 2,
        ]);

        $command = $this->app->make(\App\Console\Commands\Pages\PagesImportRulesTocCommand::class);
        $method = new ReflectionMethod($command, 'purgeOrphanSectionsOnPage');
        $method->setAccessible(true);
        $method->invoke($command, $page->id, ['kept-slug']);

        $this->assertSoftDeleted($textOrphan);
        $this->assertDatabaseHas('sections', [
            'id' => $toolKept->id,
            'deleted_at' => null,
        ]);
    }
}
