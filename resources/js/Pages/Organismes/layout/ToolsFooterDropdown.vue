<script setup>
/**
 * ToolsFooterDropdown Organism
 *
 * @description
 * Dropdown Outils dans le footer de l'Aside.
 * - Trigger stylé comme un DockItem (icône + label)
 * - Contenu : liste d'outils (Lanceur de dés, etc.)
 * - Au clic sur un outil : ferme le dropdown et ouvre le modal correspondant
 *
 * @props {String} icon - Icône FontAwesome (ex: fa-dice)
 * @props {String} pack - Pack FontAwesome (solid, regular, etc.)
 * @props {String} label - Label du trigger (ex: Outils)
 * @props {String} tooltip - Contenu du tooltip (optionnel)
 */
import { ref } from 'vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import DiceRollerModal from '@/Pages/Organismes/tools/DiceRollerModal.vue';
import SpellReferenceScalesModal from '@/Pages/Organismes/tools/SpellReferenceScalesModal.vue';

const props = defineProps({
    icon: { type: String, default: 'fa-dice' },
    pack: { type: String, default: 'solid' },
    label: { type: String, default: 'Outils' },
    tooltip: { type: String, default: 'Outils KrosmozJDR' },
});

const diceModalOpen = ref(false);
const scalesModalOpen = ref(false);

function openDiceModal() {
    diceModalOpen.value = true;
}

function closeDiceModal() {
    diceModalOpen.value = false;
}

function openScalesModal() {
    scalesModalOpen.value = true;
}

function closeScalesModal() {
    scalesModalOpen.value = false;
}

const tools = [
    {
        id: 'dice',
        label: 'Lanceur de dés',
        icon: 'fa-dice-d20',
        pack: 'solid',
        action: openDiceModal,
    },
    {
        id: 'spell-scales',
        label: 'Barèmes',
        icon: 'fa-table',
        pack: 'solid',
        action: openScalesModal,
    },
];
</script>

<template>
    <li class="dock-item">
        <Dropdown
            block
            placement="top-start"
            variant="glass"
            :aria-label="label"
        >
            <template #trigger="triggerA11y">
                <Tooltip :content="tooltip" placement="right">
                    <button
                        type="button"
                        class="dock-item__trigger"
                        v-bind="triggerA11y"
                    >
                        <span class="dock-item__icon">
                            <Icon
                                :source="icon"
                                :pack="pack"
                                alt=""
                                size="md"
                            />
                        </span>
                        <span class="dock-label">{{ label }}</span>
                    </button>
                </Tooltip>
            </template>
            <template #content>
                <ul
                    class="menu menu-sm rounded-box w-52 p-2 box-glass-sm border-glass-sm"
                    role="menu"
                >
                    <li
                        v-for="tool in tools"
                        :key="tool.id"
                        role="menuitem"
                    >
                        <button
                            type="button"
                            class="flex items-center gap-2"
                            @click="tool.action"
                        >
                            <Icon
                                :source="tool.icon"
                                :pack="tool.pack"
                                alt=""
                                size="sm"
                            />
                            {{ tool.label }}
                        </button>
                    </li>
                </ul>
            </template>
        </Dropdown>

        <DiceRollerModal
            :open="diceModalOpen"
            @close="closeDiceModal"
        />
        <SpellReferenceScalesModal
            :open="scalesModalOpen"
            @close="closeScalesModal"
        />
    </li>
</template>
