<script setup>
/**
 * Bloc « Mécanique de classe » — signature hors voies (ex. Pile/Face du Féca).
 *
 * @props {string} html - Contenu TipTap / HTML sanitizé côté lecteur
 */
import { computed } from "vue";
import RichTextReadonlyView from "@/Pages/Molecules/data-display/RichTextReadonlyView.vue";

const props = defineProps({
    html: {
        type: String,
        default: "",
    },
});

const hasContent = computed(() => {
    const raw = String(props.html || "").trim();
    if (!raw) return false;
    const text = raw
        .replace(/<[^>]+>/g, " ")
        .replace(/&nbsp;/gi, " ")
        .replace(/\s+/g, " ")
        .trim();
    return text.length > 0;
});
</script>

<template>
    <section
        v-if="hasContent"
        class="breed-mechanics-display rounded-box border border-accent-500/35 bg-accent-500/8 p-4 space-y-3 shadow-sm backdrop-blur-[1px]"
        role="region"
        aria-label="Mécanique de classe"
        data-cy="breed-mechanics"
    >
        <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-accent-300">
                Mécanique de classe
            </h3>
            <p class="text-xs text-accent-400/90">
                Signature hors voies — pilote les sorts et le jeu de la classe.
            </p>
        </div>
        <RichTextReadonlyView
            :html="html"
            :enable-rich-references="true"
            class="breed-mechanics-display__body text-base-content/95"
        />
    </section>
</template>
