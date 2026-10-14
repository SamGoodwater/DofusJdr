<script setup>
defineOptions({ inheritAttrs: false });

/**
 * Modal Molecule (DaisyUI + Custom Utility, dialog native)
 *
 * @description
 * Molécule Modal stylée DaisyUI, conforme Atomic Design et KrosmozJDR, version dialog native (accessible).
 * - Utilise <dialog class="modal"> pour l'accessibilité (`showModal` → **top layer** : pile native
 *   du navigateur pour plusieurs modals ouverts ; le dernier ouvert est au-dessus).
 * - Tooltips / menus téléportés : préférer un Teleport **dans** ce `dialog` si le trigger est
 *   dans le modal (voir {@link resolveTooltipTeleportTarget}), sinon ils restent derrière la top layer.
 * - Gère le drag & drop via le header
 * - Gère le redimensionnement (si activé)
 * - Gère la fermeture via Échap et clic sur overlay (configurable)
 * - Supporte plusieurs variants (glass, dash, outline, soft, ghost)
 * - Placement flexible (format Y-X : "middle-center", "top-start", etc.)
 * - Slots : header (titre), default (contenu principal), actions (boutons), backdrop (optionnel)
 *
 * @see https://daisyui.com/components/modal/
 *
 * @example
 * <Modal :open="showModal" size="lg" variant="glass" placement="middle-center" @close="showModal = false">
 *   <template #header><h3 class="text-lg font-bold">Titre</h3></template>
 *   <p>Contenu…</p>
 *   <template #actions><Btn @click="showModal = false">Fermer</Btn></template>
 * </Modal>
 *
 * @props {Boolean} open - Ouvre le modal à sa création (défaut: false)
 * @props {String} size - Taille du modal ('xs', 'sm', 'md', 'lg', 'xl', 'full', 'auto'), défaut 'auto'
 * @props {Boolean} closeOnOutsideClick - Permet de fermer en cliquant en dehors (défaut: true)
 * @props {Boolean} closeOnEsc - Active la fermeture via Échap (défaut: false)
 * @props {Boolean} closeOnButton - Affiche une icône de fermeture en haut à droite (défaut: true)
 * @props {String} color - Couleur du modal ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'), défaut ''
 * @props {String} variant - Style visuel ('glass', 'dash', 'outline', 'soft', 'ghost'), défaut 'glass'
 * @props {Boolean} overlay - Affiche l'arrière-plan (défaut: true)
 * @props {Boolean} resizable - Permet de redimensionner le modal (défaut: false)
 * @props {Boolean} draggable - Permet de déplacer le modal via son header (défaut: true)
 * @props {String} animation - Type d'animation ('none', 'fade', 'zoom', 'slide'), défaut 'fade'
 * @props {String} placement - Placement du modal (format Y-X, ex: 'middle-center'), défaut 'middle-center'
 * @slot header - Titre ou header custom (utilisé pour le drag)
 * @slot default - Contenu principal du modal
 * @slot actions - Boutons d'action (dans .modal-action)
 * @slot backdrop - Contenu custom du backdrop (optionnel)
 */
import { ref, watch, onMounted, onBeforeUnmount, computed, nextTick } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import interact from 'interactjs';
import { colorList } from '@/Pages/Atoms/atomMap';

const emit = defineEmits(['close', 'open']);

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    open: { 
        type: Boolean, 
        default: false 
    },
    size: {
        type: String,
        default: 'auto',
        validator: v => ['xs', 'sm', 'md', 'lg', 'xl', 'wide', 'full', 'auto'].includes(v),
    },
    closeOnOutsideClick: {
        type: Boolean,
        default: true,
    },
    closeOnEsc: {
        type: Boolean,
        default: false,
    },
    closeOnButton: {
        type: Boolean,
        default: true,
    },
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    variant: {
        type: String,
        default: 'glass',
        validator: v => ['glass', 'dash', 'outline', 'soft', 'ghost'].includes(v),
    },
    overlay: {
        type: Boolean,
        default: true,
    },
    resizable: {
        type: Boolean,
        default: false,
    },
    draggable: {
        type: Boolean,
        default: true,
    },
    animation: {
        type: String,
        default: 'fade',
        validator: v => ['none', 'fade', 'zoom', 'slide'].includes(v),
    },
    placement: {
        type: String,
        default: 'middle-center',
        validator: v => {
            // Format Y-X : "top-center", "middle-start", etc.
            const parts = v.split('-');
            if (parts.length !== 2) return false;
            const [y, x] = parts;
            return ['top', 'middle', 'bottom'].includes(y) && 
                   ['start', 'center', 'end'].includes(x);
        },
    },
    id: { 
        type: String, 
        default: '' 
    },
});

const dialogRef = ref(null);
const modalBoxRef = ref(null);
const headerRef = ref(null);
const resizeHandleRef = ref(null);

// État pour le drag & drop
const isDragging = ref(false);
const isResizing = ref(false);
const position = ref({ x: 0, y: 0 });

// Classes pour le placement (DaisyUI)
const placementClasses = computed(() => {
    const parts = props.placement.split('-');
    const [y, x] = parts;
    const classes = [];
    
    // Classes Y (vertical) - DaisyUI
    if (y === 'top') classes.push('modal-top');
    else if (y === 'middle') classes.push('modal-middle');
    else if (y === 'bottom') classes.push('modal-bottom');
    
    // Classes X (horizontal) - DaisyUI
    // Note: DaisyUI n'a pas de classe modal-center par défaut (c'est le comportement par défaut)
    if (x === 'start') classes.push('modal-start');
    else if (x === 'end') classes.push('modal-end');
    // 'center' est le comportement par défaut, pas besoin de classe
    
    return classes;
});

// Classes pour le variant
const variantClasses = computed(() => {
    const variants = {
        glass: 'box-glass-lg',
        dash: 'box-dash-lg',
        outline: 'box-outline-lg',
        soft: 'box-soft-lg',
        ghost: 'box-ghost-lg',
    };
    return variants[props.variant] || variants.glass;
});

// Classes pour la couleur
// Utilise la classe color-{name} qui définit la variable CSS --color pour influencer le background
const colorClasses = computed(() => {
    if (!props.color) return 'color-neutral bg-color-neutral light:bg-color-neutral-50';
    return `color-${props.color} bg-color-${props.color} light:bg-color-${props.color}-50`;
});

// Classes pour l'overlay (backdrop : derrière le modal-box, couvre toute la surface cliquable)
const overlayClasses = computed(() => {
    if (!props.overlay) return '';
    return `modal-backdrop absolute inset-0 z-10 ${variantClasses.value} bg-base-900/30`;
});

const moleculeClasses = computed(() =>
    mergeClasses(
        [
            'modal',
            'relative',
            props.open && 'modal-open',
            !props.overlay && 'no-overlay',
            ...placementClasses.value,
            props.class
        ],
        getCustomUtilityClasses(props)
    )
);

const attrs = computed(() => getCommonAttrs(props));

// Mapping des tailles avec valeurs personnalisées
const sizeClassMap = {
    xs: 'modal-size-xs',
    sm: 'modal-size-sm',
    md: 'modal-size-md',
    lg: 'modal-size-lg',
    xl: 'modal-size-xl',
    wide: 'modal-size-wide',
    full: 'w-full h-full max-w-full max-h-full',
    auto: '', // Pas de contrainte de taille
};

const modalBoxClasses = computed(() => {
    const base = [
        'modal-box',
        'relative',
        'z-20',
        'overflow-x-hidden',
        'overflow-y-auto',
        sizeClassMap[props.size] || '',
        variantClasses.value,
        colorClasses.value,
    ];
    
    // Classes pour le drag
    if (props.draggable) {
        base.push('cursor-move');
    }
    
    // Classes pour le resize
    if (props.resizable) {
        base.push('resize');
    }
    
    // Animation classes
    if (props.animation !== 'none') {
        const animationClassMap = {
            fade: 'transition-opacity duration-200 opacity-0 modal-fade',
            zoom: 'transition-transform duration-200 scale-95 modal-zoom',
            slide: 'transition-transform duration-200 translate-y-4 modal-slide',
        };
        base.push(animationClassMap[props.animation]);
        
        if (props.open) {
            const animationActiveClassMap = {
                fade: 'opacity-100',
                zoom: 'scale-100',
                slide: 'translate-y-0',
            };
            base.push(animationActiveClassMap[props.animation]);
        }
    }
    
    return mergeClasses(base);
});

// Fonction de fermeture
function closeModal() {
    emit('close');
    if (dialogRef.value && dialogRef.value.open) {
        dialogRef.value.close();
    }
}

// Initialisation d'interact.js pour le drag & drop et le resize
let dragInstance = null;
let resizeInstance = null;

function initInteract() {
    if (!modalBoxRef.value) return;
    
    // Réinitialiser la position quand le modal s'ouvre
    if (props.open) {
        position.value = { x: 0, y: 0 };
        if (modalBoxRef.value) {
            modalBoxRef.value.style.transform = 'translate(0px, 0px)';
            modalBoxRef.value.style.position = '';
            modalBoxRef.value.style.left = '';
            modalBoxRef.value.style.top = '';
            modalBoxRef.value.style.margin = '';
        }
    }
    
    // Configuration du drag (via le header, mais déplace le modal-box)
    if (props.draggable && headerRef.value && modalBoxRef.value) {
        dragInstance = interact(headerRef.value)
            .draggable({
                listeners: {
                    start() {
                        isDragging.value = true;
                        // S'assurer que le modal-box est en position fixed pour le drag
                        if (modalBoxRef.value) {
                            const rect = modalBoxRef.value.getBoundingClientRect();
                            modalBoxRef.value.style.position = 'fixed';
                            modalBoxRef.value.style.left = `${rect.left}px`;
                            modalBoxRef.value.style.top = `${rect.top}px`;
                            modalBoxRef.value.style.margin = '0';
                            position.value = { x: 0, y: 0 };
                        }
                    },
                    move(event) {
                        if (modalBoxRef.value) {
                            position.value.x += event.dx;
                            position.value.y += event.dy;
                            modalBoxRef.value.style.transform = `translate(${position.value.x}px, ${position.value.y}px)`;
                        }
                    },
                    end() {
                        isDragging.value = false;
                    }
                },
                // Limiter le drag à la fenêtre
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ]
            });
    }
    
    // Configuration du resize (sur le modal-box directement)
    if (props.resizable && modalBoxRef.value) {
        resizeInstance = interact(modalBoxRef.value)
            .resizable({
                edges: { right: true, bottom: true },
                listeners: {
                    start() {
                        isResizing.value = true;
                        // S'assurer que le modal-box est en position fixed pour le resize
                        if (modalBoxRef.value && modalBoxRef.value.style.position !== 'fixed') {
                            const rect = modalBoxRef.value.getBoundingClientRect();
                            modalBoxRef.value.style.position = 'fixed';
                            modalBoxRef.value.style.left = `${rect.left}px`;
                            modalBoxRef.value.style.top = `${rect.top}px`;
                            modalBoxRef.value.style.margin = '0';
                        }
                    },
                    move(event) {
                        if (modalBoxRef.value) {
                            const target = event.target;
                            let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.deltaRect.left;
                            let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.deltaRect.top;
                            
                            target.style.width = event.rect.width + 'px';
                            target.style.height = event.rect.height + 'px';
                            
                            target.setAttribute('data-x', x);
                            target.setAttribute('data-y', y);
                        }
                    },
                    end() {
                        isResizing.value = false;
                    }
                },
                modifiers: [
                    // Limiter la taille minimale
                    interact.modifiers.restrictSize({
                        min: { width: 200, height: 150 }
                    })
                ]
            });
    }
}

function destroyInteract() {
    if (dragInstance) {
        dragInstance.unset();
        dragInstance = null;
    }
    if (resizeInstance) {
        resizeInstance.unset();
        resizeInstance = null;
    }
    if (headerRef.value) {
        interact(headerRef.value).unset();
    }
    if (modalBoxRef.value) {
        interact(modalBoxRef.value).unset();
    }
}

// Gestion de la touche Échap
function handleKeyDown(e) {
    if (e.key === 'Escape' && props.closeOnEsc && props.open) {
        closeModal();
    }
}

// Gestion du clic sur l'overlay (dialog reçoit le clic quand on clique le backdrop)
function handleBackdropClick(e) {
    if (isResizing.value || isDragging.value) return;
    if (props.closeOnOutsideClick && e.target === e.currentTarget) {
        closeModal();
    }
}

// Watch pour l'ouverture/fermeture
watch(() => props.open, async (val) => {
    if (dialogRef.value) {
        if (val && !dialogRef.value.open) {
            dialogRef.value.showModal();
            emit('open');
            // Réinitialiser la position et initialiser interact après l'ouverture
            await nextTick();
            position.value = { x: 0, y: 0 };
            if (modalBoxRef.value) {
                modalBoxRef.value.style.transform = 'translate(0px, 0px)';
                modalBoxRef.value.style.position = '';
                modalBoxRef.value.style.left = '';
                modalBoxRef.value.style.top = '';
                modalBoxRef.value.style.margin = '';
            }
            initInteract();
        } else if (!val && dialogRef.value.open) {
            dialogRef.value.close();
            destroyInteract();
        }
    }
});

onMounted(async () => {
    if (props.open && dialogRef.value && !dialogRef.value.open) {
        dialogRef.value.showModal();
    }
    
    if (dialogRef.value) {
        dialogRef.value.addEventListener('close', closeModal);
        if (props.closeOnEsc) {
            dialogRef.value.addEventListener('cancel', closeModal);
        }
    }
    
    // Ajouter le listener pour Échap
    if (props.closeOnEsc) {
        document.addEventListener('keydown', handleKeyDown);
    }
    
    // Initialiser interact.js après le montage
    await nextTick();
    if (props.open) {
        initInteract();
    }
});

onBeforeUnmount(() => {
    // Nettoyer interact.js
    destroyInteract();
    
    if (dialogRef.value) {
        dialogRef.value.removeEventListener('close', closeModal);
        if (props.closeOnEsc) {
            dialogRef.value.removeEventListener('cancel', closeModal);
        }
    }
    
    // Nettoyer les listeners
    document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <Teleport to="body">
        <dialog 
            ref="dialogRef" 
            :id="id" 
            :class="moleculeClasses" 
            role="dialog"
            aria-modal="true"
            v-bind="attrs"
            @click="handleBackdropClick"
        >
            <div 
                ref="modalBoxRef"
                :class="modalBoxClasses"
            >
                <header 
                    v-if="$slots.header || closeOnButton" 
                    ref="headerRef"
                    class="mb-2 relative"
                    :class="{ 
                        'cursor-grab select-none': draggable && !isDragging,
                        'cursor-grabbing select-none': draggable && isDragging
                    }"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <slot name="header" />
                        </div>
                        <button
                            v-if="closeOnButton"
                            type="button"
                            class="btn btn-sm btn-circle btn-ghost absolute top-0 right-0 -mt-2 -mr-2 z-10 cursor-pointer"
                            @click="closeModal"
                            aria-label="Fermer le modal"
                        >
                            <Icon 
                                source="fa-xmark" 
                                pack="solid"
                                alt="Fermer"
                                size="sm"
                            />
                        </button>
                    </div>
                </header>
                
                <div class="cursor-default">
                    <slot />
                </div>
                
                <footer v-if="$slots.actions" class="modal-action mt-4">
                    <slot name="actions" />
                </footer>
                
                <!-- Handle de redimensionnement -->
                <div 
                    v-if="resizable"
                    ref="resizeHandleRef"
                    class="absolute bottom-0 right-0 w-4 h-4 cursor-se-resize bg-base-300/50 hover:bg-base-300"
                >
                    <div class="absolute bottom-1 right-1 w-2 h-2 border-r-2 border-b-2 border-base-content/50"></div>
                </div>
            </div>
            
            <form 
                v-if="overlay && closeOnOutsideClick"
                method="dialog"
                :class="overlayClasses"
            >
                <button type="submit" class="absolute inset-0 w-full h-full cursor-pointer" aria-label="Fermer">
                </button>
            </form>
            <form
                v-else-if="overlay"
                method="dialog"
                :class="overlayClasses"
            >
                <slot name="backdrop">
                    <button type="submit" class="sr-only" aria-label="Fermer le modal">Fermer</button>
                </slot>
            </form>
        </dialog>
    </Teleport>
</template>

<style scoped lang="scss">

.modal{
    // Animations
    &-fade {
        opacity: 0;
    }
    &-open &-fade {
        opacity: 1;
    }
    &-zoom {
        transform: scale(0.95);
    }
    &-open &-zoom {
        transform: scale(1);
    }
    &-slide {
        transform: translateY(1rem);
    }
    &-open &-slide {
        transform: translateY(0);
    }

}

/* Placement géré via flexbox sur le dialog */
dialog.modal {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    overflow-x: hidden;
    overflow-y: hidden;
    
    // Masquer le backdrop natif du dialog HTML5 quand overlay est false
    &::backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    // Classe pour masquer le backdrop quand overlay est false
    &.no-overlay::backdrop {
        display: none;
    }
    
    // Placement vertical (Y)
    &.modal-top {
        align-items: flex-start;
        padding-top: 2rem;
    }
    
    &.modal-middle {
        align-items: center;
    }
    
    &.modal-bottom {
        align-items: flex-end;
        padding-bottom: 2rem;
    }
    
    // Placement horizontal (X)
    &.modal-start {
        justify-content: flex-start;
        padding-left: 2rem;
    }
    
    &.modal-end {
        justify-content: flex-end;
        padding-right: 2rem;
    }
    
    // Combinaisons pour éviter les conflits
    &.modal-top.modal-start {
        align-items: flex-start;
        justify-content: flex-start;
        padding: 2rem;
    }
    
    &.modal-top.modal-end {
        align-items: flex-start;
        justify-content: flex-end;
        padding: 2rem;
    }
    
    &.modal-bottom.modal-start {
        align-items: flex-end;
        justify-content: flex-start;
        padding: 2rem;
    }
    
    &.modal-bottom.modal-end {
        align-items: flex-end;
        justify-content: flex-end;
        padding: 2rem;
    }
    
    // Center par défaut (pas besoin de classe)
    &:not(.modal-start):not(.modal-end) {
        justify-content: center;
    }
    
    &:not(.modal-top):not(.modal-bottom) {
        align-items: center;
    }
}

/* S'assurer que le contenu du modal a un curseur normal */
.modal-box > div:not(header):not(footer) {
    cursor: default;
}

/* Le header draggable a le curseur grab/grabbing */
.modal-box header.cursor-grab {
    cursor: grab;
}

.modal-box header.cursor-grab:active,
.modal-box header.cursor-grabbing {
    cursor: grabbing;
}

/* Le bouton de fermeture garde son curseur pointer */
.modal-box header button {
    cursor: pointer !important;
}

/* Tailles personnalisées pour les modals */
.modal-box {
    /* Le scroll doit rester dans la modal-box (jamais sur le <dialog>) */
    overscroll-behavior: contain;

    &.modal-size-xs {
        width: 20rem; /* 320px - inchangé */
        max-width: 20rem;
        max-height: calc(100vh - 4rem);
    }
    
    &.modal-size-sm {
        width: 24rem; /* 384px - inchangé */
        max-width: 24rem;
        max-height: calc(100vh - 4rem);
    }
    
    &.modal-size-md {
        width: 32rem; /* 512px - valeur de lg actuel */
        max-width: 32rem;
        max-height: calc(100vh - 4rem);
    }
    
    &.modal-size-lg {
        width: 40rem; /* 640px - un peu plus grand que xl actuel */
        max-width: 40rem;
        max-height: calc(100vh - 4rem);
    }
    
    &.modal-size-xl {
        width: 54rem; /* 864px - 50% plus grand que xl actuel (36rem * 1.5) */
        max-width: 54rem;
        max-height: calc(100vh - 4rem);
    }

    &.modal-size-wide {
        width: min(96rem, calc(100vw - 2rem));
        max-width: min(96rem, calc(100vw - 2rem));
        max-height: calc(100vh - 2rem);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    &.modal-size-wide > .cursor-default {
        flex: 1 1 auto;
        min-height: 0;
        overflow-x: hidden;
        overflow-y: auto;
    }

    &.modal-size-wide > .modal-action {
        flex-shrink: 0;
        margin-top: 0.75rem;
    }
}
</style>
