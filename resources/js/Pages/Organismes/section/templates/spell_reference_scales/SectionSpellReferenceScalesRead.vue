<script setup>
/**
 * Lecture publique d’une section Barèmes de sorts.
 *
 * @description
 * Intègre SpellReferenceScales et propose d’ouvrir le même outil en modal.
 *
 * @example
 * <SectionSpellReferenceScalesRead :section="section" :settings="{}" />
 */
import { ref } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import SpellReferenceScales from '@/Pages/Organismes/tools/SpellReferenceScales.vue';
import SpellReferenceScalesModal from '@/Pages/Organismes/tools/SpellReferenceScalesModal.vue';

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const modalOpen = ref(false);
</script>

<template>
    <div class="section-spell-reference-scales space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-base-content/70 leading-snug m-0">
                Barèmes indicatifs (base : normes des caractéristiques de sort).
                Aussi disponible dans <strong>Outils → Barèmes de sorts</strong>.
            </p>
            <Btn
                size="sm"
                color="primary"
                variant="ghost"
                @click="modalOpen = true"
            >
                <Icon source="fa-up-right-from-square" pack="solid" alt="" size="sm" />
                Ouvrir en modal
            </Btn>
        </div>

        <SpellReferenceScales :compact="Boolean(props.settings?.compact)" />

        <SpellReferenceScalesModal
            :open="modalOpen"
            @close="modalOpen = false"
        />
    </div>
</template>
