<script setup>
/**
 * SectionRenderer Organism
 * 
 * @description
 * Composant organisme pour rendre dynamiquement une section selon son type.
 * - Gère le header réutilisable (SectionHeader)
 * - Bascule entre mode lecture et écriture
 * - Charge dynamiquement les templates read/edit
 * - Gère les actions : copier lien, basculer mode, paramètres
 * 
 * @props {Object} section - Données de la section
 * @props {Object|null} user - Utilisateur connecté (optionnel)
 * 
 * @example
 * <SectionRenderer :section="section" :user="user" />
 */
import { computed, ref, watch, shallowRef, nextTick, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import SectionHeader from '@/Pages/Molecules/section/SectionHeader.vue';
import SectionContentSkeleton from '@/Pages/Molecules/section/SectionContentSkeleton.vue';
import SectionParamsModal from './modals/SectionParamsModal.vue';
import { useSectionMode } from './composables/useSectionMode';
import { useSectionSave } from './composables/useSectionSave';
import { useTemplateRegistry } from './composables/useTemplateRegistry';
import { useSectionAPI } from './composables/useSectionAPI';
import { useSectionUI } from './composables/useSectionUI';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { isSectionContentDeferred } from '@/Utils/section/sectionUpdatePayload';

const props = defineProps({
    section: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value === 'object' && 'id' in value && ('template' in value || 'type' in value);
        }
    },
    user: {
        type: Object,
        default: null
    },
    autoEdit: {
        type: Boolean,
        default: false
    },
    /**
     * Masque les titres génériques (Texte, Contenu…) hors édition — fiches immersives.
     */
    immersive: {
        type: Boolean,
        default: false,
    },
});

/** Titres CMS « méta » à ne pas afficher en lecture immersive. */
const GENERIC_SECTION_TITLES = new Set([
    "texte",
    "text",
    "section",
    "sections",
    "contenu",
    "content",
    "description",
    "body",
    "sans titre",
]);

/** Section locale (peut recevoir le HTML différé via API). */
const hydratedSection = ref({ ...props.section });
watch(
    () => props.section,
    (s) => {
        const incomingDeferred = isSectionContentDeferred(s);
        const alreadyHydrated =
            hydratedSection.value?.id === s?.id && !isSectionContentDeferred(hydratedSection.value);
        if (incomingDeferred && alreadyHydrated) {
            hydratedSection.value = {
                ...s,
                data: hydratedSection.value.data,
                settings: hydratedSection.value.settings ?? s.settings,
                files: hydratedSection.value.files ?? s.files,
                content_deferred: false,
            };
            return;
        }
        hydratedSection.value = { ...s };
    },
    { deep: true },
);

const contentLoading = ref(false);

/**
 * Charge data/settings si le payload Inertia a différé le HTML.
 *
 * @returns {Promise<void>}
 */
async function ensureSectionContent() {
    const current = hydratedSection.value;
    const deferred = isSectionContentDeferred(current);
    if (!deferred || !current?.id || contentLoading.value) {
        return;
    }
    contentLoading.value = true;
    try {
        const { data } = await axios.get(route('api.cms.sections.content', current.id), {
            headers: { Accept: 'application/json' },
        });
        const body = { ...(data?.data ?? {}) };
        delete body.content_deferred;
        hydratedSection.value = {
            ...current,
            data: body,
            settings: data?.settings ?? current.settings,
            files: data?.files ?? current.files,
            content_deferred: false,
        };
    } catch (err) {
        console.error('[SectionRenderer] chargement contenu différé échoué', err);
    } finally {
        contentLoading.value = false;
    }
}

onMounted(() => {
    void ensureSectionContent();
});

// Utiliser le composable UI unifié
const { 
    sectionModel, 
    canEdit,
    templateInfo,
    stateInfo,
    uiData 
} = useSectionUI(() => hydratedSection.value);

// États
const isHovered = ref(false);
const paramsModalOpen = ref(false);
const templateComponent = shallowRef(null);
const isLoadingTemplate = ref(false);

// Composables
// Utiliser directement props.section.id car le validator garantit qu'il existe
// On peut aussi utiliser sectionModel.value?.id comme fallback
const sectionId = computed(() => {
  // Priorité à props.section.id (toujours disponible grâce au validator)
  return props.section?.id || sectionModel.value?.id;
});

/** Slug CMS (ancre {@code #ssec-…} + références riches importées). */
const sectionWebSlug = computed(
  () => String(sectionModel.value?.slug || props.section?.slug || "").trim() || null,
);
const { isEditing, toggleEditMode, setEditMode } = useSectionMode(sectionId);
const { saveSectionImmediate } = useSectionSave();
const registry = useTemplateRegistry();
const { updateSection, deleteSection } = useSectionAPI();
const { copyToClipboard } = useCopyToClipboard();

const sectionTitle = computed(
    () => String(hydratedSection.value?.title || sectionModel.value?.title || "").trim(),
);

const isGenericSectionTitle = computed(() => {
    const t = sectionTitle.value.toLowerCase();
    return t === "" || GENERIC_SECTION_TITLES.has(t);
});

/** Afficher le libellé de titre dans le header. */
const showSectionTitle = computed(() => {
    if (isEditing.value) return true;
    if (!props.immersive) return true;
    return !isGenericSectionTitle.value;
});

/**
 * En lecture immersive, pas de bandeau header si titre générique et pas de droits d’édition.
 */
const showSectionHeader = computed(() => {
    if (!props.immersive) return true;
    if (isEditing.value || canEdit.value) return true;
    return showSectionTitle.value;
});

// Activer automatiquement le mode édition si autoEdit est true
watch(() => props.autoEdit, async (shouldEdit) => {
    if (shouldEdit && sectionId.value && canEdit.value) {
        await ensureSectionContent();
        if (isSectionContentDeferred(hydratedSection.value)) {
            return;
        }
        setEditMode(true);
    }
}, { immediate: true });

/**
 * Template de la section (utilise templateInfo du composable UI)
 */
const templateValue = computed(() => {
  const rawTemplate =
    props.section?.template
    ?? props.section?.type
    ?? templateInfo.value.value
    ?? 'text';

  // Supporte les cas enum/object ({ value: 'text' }) et force une string stable.
  if (rawTemplate && typeof rawTemplate === 'object' && 'value' in rawTemplate) {
    return String(rawTemplate.value || 'text');
  }
  return String(rawTemplate || 'text');
});
const isTemplateValid = computed(() => {
  return Boolean(templateValue.value) && registry.isValidTemplate(templateValue.value);
});

/**
 * Données de la section
 */
const sectionData = computed(() => {
  return hydratedSection.value?.data || {};
});

/**
 * Paramètres de la section
 */
const sectionSettings = computed(() => {
  return hydratedSection.value?.settings || {};
});

/**
 * Templates tableaux : largeur 100 % du cadre (évite le shrink-to-fit au chargement).
 * Les autres sections occupent 2/3 à partir de `md`, centrées par défaut.
 */
const isFullWidthSection = computed(() => templateValue.value.endsWith('_table'));

const sectionAlignSelfClass = computed(() => {
  if (isFullWidthSection.value) return 'self-stretch';
  const raw = String(sectionSettings.value?.layoutAlignSelf || 'center').toLowerCase().trim();
  if (raw === 'start' || raw === 'left') return 'self-start';
  if (raw === 'end' || raw === 'right') return 'self-end';
  return 'self-center';
});

const sectionWidthClass = computed(() =>
  isFullWidthSection.value
    ? 'w-full min-w-0 max-w-full'
    : 'w-full min-w-0 max-w-full md:w-2/3'
);

/**
 * Charge le composant template selon le mode (via registry)
 */
const loadTemplateComponent = async () => {
  if (!templateValue.value) return;
  if (!isTemplateValid.value) {
    const available = (registry.templates?.value || []).map((t) => t.value).filter(Boolean);
    console.error('[SectionRenderer] Template invalide', {
      sectionId: sectionId.value,
      requestedTemplate: templateValue.value,
      availableTemplates: available,
    });
    templateComponent.value = null;
    return;
  }
  
  isLoadingTemplate.value = true;
  try {
    const mode = isEditing.value ? 'edit' : 'read';
    const component = await registry.loadComponent(templateValue.value, mode);
    templateComponent.value = component;
    
    // Si le composant n'est pas trouvé, logger l'erreur du registry
    if (!component && registry.lastError.value) {
      console.error('Registry error:', registry.lastError.value);
    }
  } catch (error) {
    console.error('Erreur lors du chargement du template:', error);
    templateComponent.value = null;
  } finally {
    isLoadingTemplate.value = false;
  }
};

// Charger le template initial
loadTemplateComponent();

// Recharger le template quand le mode change
watch(isEditing, async (newValue, oldValue) => {
  if (newValue !== oldValue) {
    if (newValue) {
      await ensureSectionContent();
      if (isSectionContentDeferred(hydratedSection.value)) {
        setEditMode(false);
        return;
      }
    }
    loadTemplateComponent();
  }
}, { immediate: false, flush: 'sync' });

// Également écouter les changements de sectionId pour recharger si nécessaire
watch(sectionId, () => {
  loadTemplateComponent();
}, { immediate: false });

// Recharger le template si le type change
watch(templateValue, (newValue, oldValue) => {
  // S'assurer que le type a vraiment changé
  if (newValue !== oldValue) {
    loadTemplateComponent();
  }
}, { immediate: false });

/**
 * Gère le basculement du mode édition avec rechargement forcé
 */
const handleToggleEdit = async () => {
  if (!isEditing.value) {
    await ensureSectionContent();
    if (isSectionContentDeferred(hydratedSection.value)) {
      console.error('[SectionRenderer] édition refusée : contenu différé non chargé', {
        sectionId: sectionId.value,
      });
      return;
    }
  }
  toggleEditMode();
  await nextTick();
  loadTemplateComponent();
};

/**
 * Gère la mise à jour du titre
 */
const handleTitleUpdate = (newTitle) => {
    const id = sectionId.value;
    
    if (!id) {
        console.error('SectionRenderer: Impossible de mettre à jour le titre, sectionId manquant', { 
            sectionId: id,
            sectionModel: sectionModel.value,
            propsSection: props.section,
            newTitle 
        });
        return;
    }
  
    saveSectionImmediate(id, {
        title: newTitle
    });
};

/**
 * Gère la copie du lien de la section
 */
const handleCopyLink = async () => {
  if (!sectionModel.value || !sectionModel.value.page) return;
  
  const pageSlug = sectionModel.value.page.slug || sectionModel.value.pageId;
  const sid = sectionModel.value.id ?? props.section?.id;
  if (!sid) return;
  const slug = sectionWebSlug.value;
  const hash = slug ? `ssec-${slug}` : `section-${sid}`;
  const url = `${window.location.origin}${route('pages.show', pageSlug)}#${hash}`;
  
  await copyToClipboard(url, 'Lien de la section copié !');
};

/**
 * Gère l'ouverture du modal de paramètres
 */
const handleOpenParamsModal = () => {
    paramsModalOpen.value = true;
};

/**
 * Gère la fermeture du modal de paramètres
 */
const handleCloseParamsModal = () => {
    paramsModalOpen.value = false;
};

/**
 * Gère la mise à jour des paramètres
 * 
 * @param {Object} updatedParams - Paramètres mis à jour (title, slug, order, read_level, write_level, state, settings)
 */
const handleParamsUpdated = async (updatedParams) => {
    const id = sectionId.value; // Utiliser le computed sectionId (avec fallback)
    
    if (!id) {
        console.error('SectionRenderer: Impossible de mettre à jour la section, ID manquant', { 
            sectionId: id,
            sectionModel: sectionModel.value,
            sectionModelId: sectionModel.value?.id,
            propsSection: props.section,
            propsSectionId: props.section?.id
        });
        return;
    }
    
    try {
        await updateSection(id, updatedParams, {
            onSuccess: () => {
                paramsModalOpen.value = false;
                router.reload({ only: ['page'] });
            }
        });
    } catch (errors) {
        console.error('Erreur lors de la mise à jour de la section:', errors);
    }
};

/**
 * Gère la mise à jour des données depuis le template
 */
const handleDataUpdate = () => {
  // Les templates compatibles auto-save gèrent déjà la sauvegarde
  // Cette fonction est appelée pour informer le parent si nécessaire
};

/**
 * Suppression rapide d'une section depuis l'en-tête.
 */
const handleDeleteSection = async () => {
  const id = sectionId.value;
  if (!id || !canEdit.value) return;

  const label = sectionModel.value?.title || `Section #${id}`;
  const confirmed = window.confirm(`Supprimer "${label}" ? Cette action est irréversible.`);
  if (!confirmed) return;

  try {
    await deleteSection(id, {
      onSuccess: () => {
        router.reload({ only: ['page'] });
      },
    });
  } catch (errors) {
    console.error('Erreur lors de la suppression rapide de la section:', errors);
  }
};
</script>

<template>
    <div 
        :id="sectionId ? `section-${sectionId}` : undefined"
        class="section-renderer section-renderer-surface group relative rounded-2xl border border-base-300/40 bg-base-100/40 px-3 pb-4 pt-2 shadow-sm backdrop-blur-[1px] md:px-5 md:pb-6 md:pt-3" 
        :class="[uiData.containerClass, sectionAlignSelfClass, sectionWidthClass]"
        :data-section-id="sectionModel?.id" 
        :data-section-slug="sectionWebSlug || undefined"
        :data-section-template="templateValue"
        :data-section-state="stateInfo.value"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
    <span
        v-if="sectionWebSlug"
        :id="`ssec-${sectionWebSlug}`"
        class="section-scroll-anchor pointer-events-none absolute left-0 top-0 block h-px w-px -translate-y-20 opacity-0"
        aria-hidden="true"
    />
    <SectionHeader
      v-if="showSectionHeader"
      :title="sectionTitle"
      :show-title="showSectionTitle"
      :isEditing="isEditing"
      :canEdit="canEdit"
      :canDelete="canEdit"
      :isHovered="isHovered"
      @update:title="handleTitleUpdate"
      @toggle-edit="handleToggleEdit"
      @open-params="handleOpenParamsModal"
      @copy-link="handleCopyLink"
      @request-delete="handleDeleteSection"
    />
    
    <div class="section-renderer__body min-w-0 max-w-full overflow-x-auto">
    <!-- Contenu selon le mode -->
    <div v-if="isLoadingTemplate || contentLoading" class="section-loading">
      <SectionContentSkeleton
        :template="templateValue"
        :title="hydratedSection.title || sectionModel?.title || ''"
        :show-header="false"
      />
        </div>

        <component
      v-else-if="templateComponent"
            :is="templateComponent"
            :section="hydratedSection"
      :data="sectionData"
      :settings="sectionSettings"
      :editing="isEditing"
      @data-updated="handleDataUpdate"
        />

    <!-- Erreur si le template n'existe pas -->
        <div v-else class="section-error alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
        <h3 class="font-bold">Template non trouvé</h3>
                <p class="text-sm">
          Le template "{{ templateValue }}" n'est pas disponible.
                    Vérifie la valeur du template de la section et le registre des templates.
                </p>
                <p v-if="!isTemplateValid" class="text-xs opacity-80 mt-1">
                    Diagnostic: template inconnu du registre frontend.
                </p>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal de paramètres -->
    <SectionParamsModal
        v-if="sectionModel"
        :open="paramsModalOpen"
        :section-template="templateValue"
        :section="sectionModel"
        @close="handleCloseParamsModal"
        @validated="handleParamsUpdated"
        @deleted="() => router.reload({ only: ['page'] })"
    />
</template>

<style scoped lang="scss">
.section-renderer {
    position: relative;
  margin-bottom: 2rem;
  
  &:last-child {
    margin-bottom: 0;
  }
}

.section-loading {
    padding: 0.25rem 0 0.5rem;
    min-height: 6rem;
}

.section-error {
    margin: 1rem 0;
}
</style>
