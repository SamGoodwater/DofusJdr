<script setup>
/**
 * SectionHeader Molecule
 * 
 * @description
 * Header réutilisable pour toutes les sections.
 * - Titre modifiable en mode édition
 * - Icônes d'action au hover
 * - Gère les actions : copier lien, basculer mode, paramètres
 * 
 * @props {String} title - Titre de la section
 * @props {Boolean} isEditing - Mode édition actif
 * @props {Boolean} canEdit - Droits d'écriture
 * @props {Boolean} isHovered - Hover actif sur la section
 * 
 * @emits update:title - Mise à jour du titre
 * @emits toggle-edit - Basculer mode lecture/écriture
 * @emits open-params - Ouvrir modal paramètres
 * @emits copy-link - Copier le lien de la section
 * @emits request-delete - Demander la suppression de la section
 */
import { ref, watch } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  /** Affiche le libellé / input titre (false = actions seules, fiches immersives). */
  showTitle: {
    type: Boolean,
    default: true,
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  canEdit: {
    type: Boolean,
    default: false
  },
  canDelete: {
    type: Boolean,
    default: false
  },
  isHovered: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:title', 'toggle-edit', 'open-params', 'copy-link', 'request-delete']);

// Titre local pour l'input
const localTitle = ref(props.title);

// Synchroniser avec le prop title
watch(() => props.title, (newTitle) => {
  localTitle.value = newTitle;
});

/**
 * Gère la mise à jour du titre
 */
const handleTitleBlur = () => {
  if (localTitle.value !== props.title) {
    emit('update:title', localTitle.value);
  }
};

/**
 * Gère la touche Enter pour valider le titre
 */
const handleTitleKeydown = (event) => {
  if (event.key === 'Enter') {
    event.target.blur();
  }
};
</script>

<template>
  <div
    class="section-header flex items-center justify-between gap-4 py-3"
    :class="showTitle || isEditing ? 'border-b border-base-300/45' : 'justify-end border-b-0 py-1'"
  >
    <!-- Titre à gauche -->
    <div
      v-if="showTitle || isEditing"
      class="section-header__title flex min-w-0 flex-1 items-start gap-2"
    >
      <span class="mt-1.5 hidden h-8 w-1 shrink-0 rounded-full bg-primary/70 sm:block" aria-hidden="true" />
      <input
        v-if="isEditing"
        v-model="localTitle"
        @blur="handleTitleBlur"
        @keydown="handleTitleKeydown"
        type="text"
        class="input input-sm input-ghost w-full"
        placeholder="Titre de la section"
      />
      <h3 v-else class="truncate text-xl font-bold tracking-tight text-base-content md:text-2xl">
        {{ title || 'Sans titre' }}
      </h3>
    </div>
    
    <!-- Icônes à droite (hover) -->
    <div class="section-header__actions flex items-center gap-2" :class="{ 'section-header__actions--visible': isHovered }">
      <!-- Copier lien (toujours visible) -->
      <button
        @click="$emit('copy-link')"
        class="btn btn-ghost btn-sm btn-square"
        title="Copier le lien de la section"
        type="button"
      >
        <Icon source="fa-link" pack="solid" alt="Copier le lien" size="sm" />
      </button>
      
      <!-- Basculer mode (si droits d'écriture) -->
      <button
        v-if="canEdit"
        @click="$emit('toggle-edit')"
        class="btn btn-ghost btn-sm btn-square"
        :title="isEditing ? 'Passer en mode lecture' : 'Passer en mode édition'"
        type="button"
      >
        <Icon 
          :source="isEditing ? 'fa-eye' : 'fa-edit'" 
          pack="solid"
          :alt="isEditing ? 'Passer en mode lecture' : 'Passer en mode édition'"
          size="sm" 
        />
      </button>
      
      <!-- Paramètres (si droits d'écriture) -->
      <button
        v-if="canEdit"
        @click="$emit('open-params')"
        class="btn btn-ghost btn-sm btn-square"
        title="Paramètres de la section"
        type="button"
      >
        <Icon source="fa-gear" pack="solid" alt="Paramètres" size="sm" />
      </button>

      <!-- Suppression rapide (si droits) -->
      <button
        v-if="canDelete"
        @click="$emit('request-delete')"
        class="btn btn-ghost btn-sm btn-square text-error"
        title="Supprimer la section"
        type="button"
      >
        <Icon source="fa-trash-can" pack="solid" alt="Supprimer la section" size="sm" />
      </button>
    </div>
  </div>
</template>

<style scoped lang="scss">
.section-header {
  transition: all 0.2s ease;
  
  &__actions {
    // Styles pour les actions
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
    
    &--visible {
      opacity: 1;
      pointer-events: auto;
    }
  }
}

// Afficher les actions au hover du parent (géré par SectionRenderer)
// Utiliser aussi group-hover comme fallback
:deep(.group:hover) .section-header__actions {
  opacity: 1;
  pointer-events: auto;
}
</style>

