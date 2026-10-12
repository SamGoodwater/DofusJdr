<?php

namespace App\Console\Commands\Pages;

use App\Console\ArtisanExitCode;
use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\PageService;
use App\Support\Cms\RulesHtmlSectionSplitter;
use App\Support\Cms\RulesImportSlugHelper;
use App\Support\Cms\RulesMarkdownSectionCompiler;
use App\Support\Cms\RulesTocPagePlacement;
use App\Support\Cms\RulesTocParser;
use App\Support\Cms\RulesTocSlugIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Importe la hiérarchie des règles depuis une table des matières Markdown.
 *
 * Mapping appliqué:
 * - Niveau 1 (##) => page parente
 * - Niveau 2 (###) => sous-page (enfant du niveau 1)
 * - Niveau 3 (liste - x.x.x) => section texte de la page niveau 2
 * - Chapitre 5 => menu Pour les MJ, lecture MJ ; autres chapitres => Règles, lecture invité
 */
class PagesImportRulesTocCommand extends Command
{
    protected $signature = 'pages:import-rules-toc
        {path? : Chemin du fichier TABLE_DES_MATIERES.md}
        {--dry-run : Affiche le plan sans écrire en base}
        {--force-content : Remplace le HTML des sections par celui généré depuis les .md (krefs, liens) ; sans ce flag, une section déjà remplie garde son ancien contenu}
        {--compile-downloads : Compile ensuite le livre de règles (PDF et ODT)}';

    protected $description = 'Crée/maj pages et sections depuis la table des matières des règles.';

    /**
     * Contenus HTML découpés par numéro TOC (ex: 3.2.2 => liste de blocs).
     *
     * @var array<string, list<array{title: string, html: string}>>
     */
    private array $sectionContentByNumber = [];

    private bool $forceContent = false;

    /** Nombre de sections existantes où le HTML issu des .md n’a pas été appliqué (contenu CMS déjà présent, sans --force-content). */
    private int $skippedExistingSectionBodyFromMarkdown = 0;

    private ?RulesTocSlugIndex $rulesTocSlugIndex = null;

    public function handle(): int
    {
        $path = (string) ($this->argument('path') ?: base_path('private/game/rules/TABLE_DES_MATIERES.md'));
        $dryRun = (bool) $this->option('dry-run');
        $this->forceContent = (bool) $this->option('force-content');

        if (! is_file($path)) {
            $this->error("Fichier introuvable: {$path}");

            return ArtisanExitCode::FAILURE;
        }

        $tree = RulesTocParser::parse($path);
        if (count($tree) === 0) {
            $this->warn('Aucune hiérarchie détectée dans la table des matières.');

            return ArtisanExitCode::SUCCESS;
        }

        $this->rulesTocSlugIndex = RulesTocSlugIndex::fromTree($tree);
        $rulesRootDirectory = dirname($path);
        $this->sectionContentByNumber = $this->buildSectionContentMap($rulesRootDirectory);
        $this->line(sprintf(
            'Contenus de sections détectés: %d',
            count($this->sectionContentByNumber)
        ));
        if ($this->forceContent) {
            $this->warn('Mode force-content: le contenu existant des sections sera écrasé.');
        } else {
            $this->comment(
                'Sans --force-content, les sections texte déjà remplies en base conservent leur HTML '
                .'(les changements dans les .md — y compris les [[kref:characteristic:…]] — ne sont appliqués au CMS '
                .'qu’avec --force-content).'
            );
        }

        if ($dryRun) {
            $this->info('Mode dry-run: aucun changement en base.');
            $this->printTreePreview($tree);

            return ArtisanExitCode::SUCCESS;
        }

        $creatorId = $this->resolveDefaultCreatorId();
        $this->skippedExistingSectionBodyFromMarkdown = 0;

        DB::beginTransaction();
        try {
            foreach ($tree as $level1) {
                $parent = $this->upsertLevel1Page($level1, $creatorId);

                foreach ($level1['children'] as $level2) {
                    $child = $this->upsertLevel2Page($level2, (int) $parent->id, $creatorId);

                    $expectedSectionSlugs = [];
                    $pageSectionOrder = 0;
                    foreach ($level2['sections'] as $level3) {
                        foreach ($this->upsertLevel3Sections($level3, (int) $child->id, $creatorId, $pageSectionOrder) as $slug) {
                            $expectedSectionSlugs[] = $slug;
                        }
                    }

                    $this->purgeOrphanSectionsOnPage((int) $child->id, $expectedSectionSlugs);
                }
            }

            $this->keepPlayerDownloadsInRulesMenu();
            $this->hideRetiredRulesAnnexes();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Import interrompu: '.$e->getMessage());

            return ArtisanExitCode::FAILURE;
        }

        PageService::clearMenuCache();
        $this->info('Import terminé avec succès.');
        if (! $this->forceContent && $this->skippedExistingSectionBodyFromMarkdown > 0) {
            $this->warn(sprintf(
                '%d section(s) existante(s) : le HTML des fichiers Markdown n’a pas remplacé le contenu déjà '
                .'enregistré. Pour appliquer les .md (références kref, etc.), relance : '
                .'php artisan pages:import-rules-toc --force-content',
                $this->skippedExistingSectionBodyFromMarkdown
            ));
        }

        if ((bool) $this->option('compile-downloads')) {
            $this->newLine();
            $this->info('Compilation du livre de règles (PDF / ODT)…');
            $compileCode = $this->call('rules:compile-downloads');
            if ($compileCode !== 0) {
                $this->warn('La compilation des téléchargements a échoué. Relance : php artisan rules:compile-downloads');
            }
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @param  array{number:string,title:string,menu_order:int,children:array<int, mixed>}  $level1
     */
    private function upsertLevel1Page(array $level1, ?int $creatorId): Page
    {
        $slug = RulesImportSlugHelper::buildPageSlug($level1['number'], $level1['title']);
        $placement = RulesTocPagePlacement::forNumber($level1['number']);

        return $this->upsertPageBySlug($slug, [
            'title' => $level1['title'],
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => $placement['read_level'],
            'write_level' => User::ROLE_ADMIN,
            'parent_id' => null,
            'menu_order' => $level1['menu_order'],
            'menu_group' => $placement['menu_group'],
            'created_by' => $creatorId,
        ]);
    }

    /**
     * @param  array{number:string,title:string,menu_order:int,sections:array<int, mixed>}  $level2
     */
    private function upsertLevel2Page(array $level2, int $parentId, ?int $creatorId): Page
    {
        $slug = RulesImportSlugHelper::buildPageSlug($level2['number'], $level2['title']);
        $placement = RulesTocPagePlacement::forNumber($level2['number']);

        return $this->upsertPageBySlug($slug, [
            'title' => $level2['title'],
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => $placement['read_level'],
            'write_level' => User::ROLE_ADMIN,
            'parent_id' => $parentId,
            'menu_order' => $level2['menu_order'],
            'menu_group' => $placement['menu_group'],
            'created_by' => $creatorId,
        ]);
    }

    /**
     * @param  array{number:string,title:string,order:int}  $level3
     * @return list<string> Slugs créés ou mis à jour
     */
    private function upsertLevel3Sections(array $level3, int $pageId, ?int $creatorId, int &$pageSectionOrder): array
    {
        $baseSlug = $this->buildSectionSlug($level3['number'], $level3['title']);
        $chunks = $this->resolveSectionChunks($level3['number'], $level3['title']);
        $slugs = [];

        foreach ($chunks as $index => $chunk) {
            $chunkTitle = trim($chunk['title']) !== '' ? trim($chunk['title']) : $level3['title'];
            $slug = count($chunks) === 1
                ? $baseSlug
                : ($index === 0 ? $baseSlug : $baseSlug.'-'.Str::slug($chunkTitle));
            $content = trim($chunk['html']);

            $this->upsertTextSectionBySlug(
                $pageId,
                $slug,
                $chunkTitle,
                $content !== '' ? $content : '<p>'.e($chunkTitle).'</p>',
                $pageSectionOrder,
                $creatorId,
                $level3['number'],
            );

            $slugs[] = $slug;
            $pageSectionOrder++;
        }

        return $slugs;
    }

    /**
     * @return list<array{title: string, html: string}>
     */
    private function resolveSectionChunks(string $number, string $fallbackTitle): array
    {
        if (! isset($this->sectionContentByNumber[$number])) {
            return [['title' => $fallbackTitle, 'html' => '<h3>'.e($fallbackTitle).'</h3>']];
        }

        $chunks = $this->sectionContentByNumber[$number];

        return $chunks !== [] ? $chunks : [['title' => $fallbackTitle, 'html' => '<h3>'.e($fallbackTitle).'</h3>']];
    }

    private function upsertTextSectionBySlug(
        int $pageId,
        string $slug,
        string $title,
        string $content,
        int $order,
        ?int $creatorId,
        string $level3Number,
    ): Section {
        $existing = Section::withTrashed()
            ->where('page_id', $pageId)
            ->where('slug', $slug)
            ->first();

        $textSettings = [
            'align' => 'left',
            'size' => 'md',
            'enableRichReferences' => true,
        ];

        $placement = RulesTocPagePlacement::forNumber($level3Number);
        $attributes = [
            'page_id' => $pageId,
            'title' => $title,
            'slug' => $slug,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => $textSettings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => $placement['read_level'],
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ];

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }

            $mergedSettings = array_merge(
                is_array($existing->settings) ? $existing->settings : [],
                $textSettings,
            );
            $attributes['settings'] = $mergedSettings;

            // Respecter un éventuel contenu édité à la main: on ne l'écrase pas.
            $existingData = is_array($existing->data) ? $existing->data : [];
            $existingParams = is_array($existing->params) ? $existing->params : [];
            $hasCustomDataContent = isset($existingData['content']) && trim((string) $existingData['content']) !== '';
            $hasCustomParamsContent = isset($existingParams['content']) && trim((string) $existingParams['content']) !== '';

            if ($this->forceContent) {
                $attributes['data'] = $this->replaceSectionContent($existingData, $content);
                $attributes['params'] = $this->replaceSectionContent($existingParams, $content);
            } else {
                $hasMarkdownForNumber = isset($this->sectionContentByNumber[$level3Number]);
                if ($hasMarkdownForNumber && ($hasCustomDataContent || $hasCustomParamsContent)) {
                    $this->skippedExistingSectionBodyFromMarkdown++;
                }
                $attributes['data'] = $hasCustomDataContent ? $existingData : ['content' => $content];
                $attributes['params'] = $hasCustomParamsContent ? $existingParams : ['content' => $content];
            }

            $existing->fill($attributes);
            $existing->save();

            return $existing;
        }

        $attributes['data'] = ['content' => $content];
        $attributes['params'] = ['content' => $content];

        return Section::create($attributes);
    }

    /**
     * @param  list<string>  $expectedSlugs
     */
    private function purgeOrphanSectionsOnPage(int $pageId, array $expectedSlugs): void
    {
        if ($expectedSlugs === []) {
            return;
        }

        // Ne purger que les sections texte orphelines : les templates outils
        // (barèmes, chartes, catalogues…) doivent survivre à la réimportation.
        Section::query()
            ->where('page_id', $pageId)
            ->whereNotIn('slug', $expectedSlugs)
            ->where(function ($query): void {
                $query->where('template', SectionType::TEXT->value)
                    ->orWhere('type', SectionType::TEXT->value);
            })
            ->each(fn (Section $section) => $section->delete());
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function replaceSectionContent(array $payload, string $content): array
    {
        $payload['content'] = $content;

        return $payload;
    }

    /**
     * Construit la map des contenus des sections à partir des fichiers markdown.
     *
     * @return array<string, string>
     */
    private function buildSectionContentMap(string $rulesRootDirectory): array
    {
        if (! is_dir($rulesRootDirectory)) {
            return [];
        }

        $rulesRootReal = realpath($rulesRootDirectory) ?: $rulesRootDirectory;

        $contentByNumber = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rulesRootDirectory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo->isFile() || strtolower((string) $fileInfo->getExtension()) !== 'md') {
                continue;
            }

            $path = (string) $fileInfo->getPathname();

            $basename = pathinfo($path, PATHINFO_BASENAME);
            if (in_array($basename, ['TABLE_DES_MATIERES.md', 'INDEX.md'], true)) {
                continue;
            }

            if (! preg_match('/^(\d+(?:\.\d+){1,2})-/u', $basename, $matches)) {
                continue;
            }

            $number = (string) $matches[1];
            $rawMarkdown = file_get_contents($path);
            if (! is_string($rawMarkdown) || trim($rawMarkdown) === '') {
                continue;
            }

            $html = RulesMarkdownSectionCompiler::markdownFileToHtml(
                $rawMarkdown,
                $path,
                $rulesRootReal,
                $this->rulesTocSlugIndex,
            );
            if ($html === '') {
                continue;
            }

            $chunks = RulesHtmlSectionSplitter::split($html);
            $contentByNumber[$number] = $chunks !== []
                ? $chunks
                : [['title' => '', 'html' => $html]];
        }

        return $contentByNumber;
    }

    /**
     * La page téléchargements reste dans Règles (hors chapitre 5 MJ).
     */
    private function keepPlayerDownloadsInRulesMenu(): void
    {
        $path = database_path('seeders/data/ressources-page.php');
        if (! is_file($path)) {
            return;
        }

        $config = require $path;
        if (! is_array($config) || ! isset($config['slug'])) {
            return;
        }

        $page = Page::query()->where('slug', (string) $config['slug'])->first();
        if ($page === null) {
            return;
        }

        $parentSlug = (string) ($config['parent_slug'] ?? '');
        $parent = $parentSlug !== ''
            ? Page::query()->where('slug', $parentSlug)->first()
            : null;

        $page->parent_id = $parent?->id;
        $page->menu_group = 'Règles';
        $page->read_level = User::ROLE_GUEST;
        $page->in_menu = true;
        $page->menu_order = $parent !== null
            ? (int) ($config['menu_order'] ?? 90)
            : (int) ($config['fallback_menu_order'] ?? $config['menu_order'] ?? 90);
        $page->save();
    }

    /**
     * Le changelog n’appartient plus au livre de règles.
     */
    private function hideRetiredRulesAnnexes(): void
    {
        Page::query()
            ->where('slug', 'like', 'regles-6-%')
            ->each(function (Page $page): void {
                $page->in_menu = false;
                $page->menu_group = null;
                $page->state = Page::STATE_ARCHIVED;
                $page->save();
            });
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function upsertPageBySlug(string $slug, array $attributes): Page
    {
        $page = Page::withTrashed()->where('slug', $slug)->first();

        if ($page) {
            if ($page->trashed()) {
                $page->restore();
            }
            $page->fill($attributes);
            $page->slug = $slug;
            $page->save();

            return $page;
        }

        $attributes['slug'] = $slug;

        return Page::create($attributes);
    }

    private function resolveDefaultCreatorId(): ?int
    {
        $systemUser = User::query()->where('email', User::SYSTEM_USER_EMAIL)->first();
        if ($systemUser) {
            return (int) $systemUser->id;
        }

        $superAdmin = User::query()->where('role', User::ROLE_SUPER_ADMIN)->orderBy('id')->first();
        if ($superAdmin) {
            return (int) $superAdmin->id;
        }

        $firstUser = User::query()->orderBy('id')->first();

        return $firstUser ? (int) $firstUser->id : null;
    }

    private function buildSectionSlug(string $number, string $title): string
    {
        return RulesImportSlugHelper::buildSectionSlug($number, $title);
    }

    /**
     * @param  array<int, array{number:string,title:string,menu_order:int,children:array<int, array{number:string,title:string,menu_order:int,sections:array<int, array{number:string,title:string,order:int}>}>}>  $tree
     */
    private function printTreePreview(array $tree): void
    {
        foreach ($tree as $l1) {
            $this->line("N1 {$l1['number']} - {$l1['title']}");
            foreach ($l1['children'] as $l2) {
                $this->line("  N2 {$l2['number']} - {$l2['title']}");
                foreach ($l2['sections'] as $l3) {
                    $this->line("    N3 {$l3['number']} - {$l3['title']}");
                }
            }
        }
    }
}
