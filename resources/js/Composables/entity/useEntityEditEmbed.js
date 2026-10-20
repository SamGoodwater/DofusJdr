/**
 * Contexte d’édition embarquée (modal EntitySurfaceHost).
 *
 * @description
 * Les pages `Edit.vue` et formulaires partagés lisent ce provide pour activer
 * `embeddedInModal` + `redirectAfterUpdate=stay` sans prop forcée partout.
 *
 * @example
 * const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);
 */
import { computed, inject } from "vue";

export const ENTITY_EDIT_EMBED_KEY = Symbol("entityEditEmbed");

/**
 * @param {{ embeddedInModal?: boolean, redirectAfterUpdate?: string|null }} props
 * @returns {{
 *   embed: null|{ embeddedInModal: boolean, onCancel?: Function, onSaved?: Function, onExpand?: Function },
 *   isEmbeddedInModal: import('vue').ComputedRef<boolean>,
 *   resolvedRedirectAfterUpdate: import('vue').ComputedRef<string|null|undefined>,
 * }}
 */
export function useEntityEditEmbed(props = {}) {
    const embed = inject(ENTITY_EDIT_EMBED_KEY, null);

    const isEmbeddedInModal = computed(() => {
        // Accès direct (pas `?.`) pour tracker la réactivité du provide EntitySurfaceHost.
        const fromEmbed = embed != null && Boolean(embed.embeddedInModal);
        return Boolean(props.embeddedInModal) || fromEmbed;
    });

    const resolvedRedirectAfterUpdate = computed(() => {
        // Modal EntitySurfaceHost : toujours rester (la page sous le modal ne doit pas naviguer).
        if (isEmbeddedInModal.value) {
            return "stay";
        }
        return props.redirectAfterUpdate ?? "edit";
    });

    return {
        embed,
        isEmbeddedInModal,
        resolvedRedirectAfterUpdate,
    };
}
