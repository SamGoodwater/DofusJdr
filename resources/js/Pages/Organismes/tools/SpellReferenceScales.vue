<script setup>
/**
 * SpellReferenceScales Organism
 *
 * @description
 * Barèmes de sorts (dégâts, soin, bouclier, PV temp., vol de vie) par niveau :
 * basse / moyenne / haute, par sort et par tour, avec conversion en dés,
 * champ valeur → propositions, et ajusteur de conditions (repères indicatifs).
 *
 * @props {Boolean} compact - Densité réduite (section page)
 * @example
 * <SpellReferenceScales />
 */
import { ref } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { useSpellReferenceScales } from '@/Composables/tools/useSpellReferenceScales.js';

defineProps({
    compact: { type: Boolean, default: false },
});

const {
    loading,
    error,
    level,
    mod,
    conditions,
    activeConditionIndices,
    toggleCondition,
    clearConditions,
    powerOffset,
    levelOffset,
    effectiveLevel,
    resolvedRows,
    convertValue,
    convertSuggestions,
    description,
    MAX_LEVEL,
    MAX_ACTION_POINTS,
    DISPLAY_BANDS,
    SPELL_SCALE_FAMILIES,
    formatDiceSummary,
    normsByKey,
} = useSpellReferenceScales();

const showFullTable = ref(false);
const showRules = ref(false);

const MODULATION_TEXT_RULES = [
    'Zone / multicible : niveaux inférieurs proportionnellement à la taille de la zone.',
    'Effets annexes (buff, debuff, déplacement…) : valeur basse ou 1 à 4 niveaux en dessous selon la puissance (ex. mouvement léger → valeur basse ; ±PA/PM/portée → −2 à −4 niveaux).',
    'Effets sur la durée (poison, soin multi-tours) : le total sur la durée ≈ valeur haute du sort, réparti.',
    'Effet conditionnel / différé (piège, glyphe) : +1 niveau.',
    'Contrepartie pour le lanceur (PV, malus) : +1 à +2 niveaux.',
    'Vol de vie déjà dans sa propre grille (−1 à −2 niveaux implicites sur les dégâts purs).',
    'Tir ami (zone touchant les alliés) : +1 niveau ; contrainte de lancer (ligne seule, PO min) : +0 à +1.',
    'Critique : on double les dés (pas les fixes) de dégâts, soin, vol de vie, bouclier et PV temporaires. Les effets annexes ne changent pas, sauf sort centré sur l’effet (ex. retrait PA 2 → 3).',
    'Cumul : ne pas dépasser +4 niveaux au total ; plancher niveau 1 (valeur basse) ; au-delà de 20, valeur haute du 20 + ≈10 % par niveau d’écart.',
];
</script>

<template>
    <div
        class="spell-ref-scales space-y-4"
        :class="{ 'text-sm': compact }"
    >
        <div
            class="alert alert-warning py-2 text-sm leading-snug"
            role="note"
        >
            <Icon source="fa-triangle-exclamation" pack="solid" alt="" size="sm" />
            <span>
                Ces valeurs sont des <strong>repères indicatifs</strong>, pas une règle absolue.
                À calibrer selon le sort, la table et le feeling.
            </span>
        </div>

        <div v-if="loading" class="flex items-center gap-2 text-base-content/70">
            <span class="loading loading-spinner loading-sm" />
            Chargement des barèmes…
        </div>

        <div v-else-if="error" class="alert alert-error text-sm">
            <Icon source="fa-circle-exclamation" pack="solid" alt="" size="sm" />
            <span>{{ error }}</span>
        </div>

        <template v-else>
            <p
                v-if="description"
                class="text-sm text-base-content/70 leading-snug"
            >
                {{ description }}
            </p>

            <div class="flex flex-wrap items-end gap-3">
                <label class="form-control w-28">
                    <span class="label-text text-xs">Niveau</span>
                    <input
                        v-model.number="level"
                        type="number"
                        min="1"
                        :max="MAX_LEVEL"
                        class="input input-bordered input-sm w-full"
                        aria-label="Niveau du personnage"
                    >
                </label>
                <div class="text-xs text-base-content/60 pb-1">
                    <span>Niveau effectif : <strong>{{ effectiveLevel }}</strong></span>
                    <span v-if="levelOffset" class="ml-1">({{ levelOffset > 0 ? '+' : '' }}{{ levelOffset }})</span>
                    <span class="mx-1">·</span>
                    <span>Mod. typique : <strong>+{{ mod }}</strong></span>
                    <span class="mx-1">·</span>
                    <span>PA/tour : <strong>{{ MAX_ACTION_POINTS[effectiveLevel - 1] }}</strong></span>
                    <span v-if="powerOffset" class="ml-1">· palier {{ powerOffset > 0 ? '+' : '' }}{{ powerOffset }}</span>
                </div>
            </div>

            <!-- Ajusteur -->
            <fieldset class="border border-base-content/10 rounded-box p-3 space-y-2">
                <legend class="px-1 text-xs font-semibold uppercase tracking-wide text-base-content/60">
                    Ajusteur (conditions)
                </legend>
                <div class="flex flex-wrap gap-2">
                    <label
                        v-for="(cond, idx) in conditions"
                        :key="idx"
                        class="label cursor-pointer gap-2 py-1 px-2 rounded-btn border border-base-content/10 hover:bg-base-content/5"
                        :class="{ 'bg-primary/10 border-primary/40': activeConditionIndices.has(idx) }"
                    >
                        <input
                            type="checkbox"
                            class="checkbox checkbox-xs"
                            :checked="activeConditionIndices.has(idx)"
                            @change="toggleCondition(idx)"
                        >
                        <span class="label-text text-xs leading-snug">
                            {{ cond.comment || `${cond.characteristic_key} ${cond.operator} ${cond.value}` }}
                        </span>
                    </label>
                </div>
                <Btn
                    v-if="activeConditionIndices.size"
                    size="xs"
                    color="neutral"
                    variant="ghost"
                    @click="clearConditions"
                >
                    Réinitialiser
                </Btn>
            </fieldset>

            <!-- Tableau niveau courant -->
            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr>
                            <th>Famille</th>
                            <th
                                v-for="band in DISPLAY_BANDS"
                                :key="band.power"
                                class="text-center"
                            >
                                {{ band.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template
                            v-for="row in resolvedRows"
                            :key="row.key"
                        >
                            <tr>
                                <td class="font-medium whitespace-nowrap">
                                    {{ row.label }}
                                    <span class="block text-[10px] font-normal text-base-content/50">par sort (3 PA)</span>
                                    <span
                                        v-if="row.formula"
                                        class="block text-[10px] font-mono font-normal text-base-content/40 max-w-[14rem] truncate"
                                        :title="row.formula"
                                    >{{ row.formula }}</span>
                                </td>
                                <td
                                    v-for="band in row.bands"
                                    :key="band.power + '-cast'"
                                    class="text-center align-top"
                                >
                                    <div class="font-semibold">{{ band.cast ?? '—' }}</div>
                                    <div class="text-[10px] text-base-content/55 leading-tight mt-0.5">{{ band.diceCast }}</div>
                                </td>
                            </tr>
                            <tr class="bg-base-200/40">
                                <td class="whitespace-nowrap text-base-content/70">
                                    {{ row.short }}
                                    <span class="block text-[10px]">par tour</span>
                                </td>
                                <td
                                    v-for="band in row.bands"
                                    :key="band.power + '-turn'"
                                    class="text-center align-top"
                                >
                                    <div class="font-semibold">{{ band.turn ?? '—' }}</div>
                                    <div class="text-[10px] text-base-content/55 leading-tight mt-0.5">{{ band.diceTurn }}</div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Conversion valeur → dés -->
            <div class="space-y-2 rounded-box border border-base-content/10 p-3">
                <h4 class="font-semibold text-sm flex items-center gap-2">
                    <Icon source="fa-dice" pack="solid" alt="" size="sm" />
                    Convertir une valeur en dés
                </h4>
                <p class="text-xs text-base-content/60 leading-snug">
                    Saisis une moyenne (mod déjà inclus ou hors mod). Dés classiques :
                    d2 (pièce), d4, d6, d8, d10, d12.
                    <em>Bas</em> = 1 dé (forte variance), <em>moy</em> = 2 dés, <em>haut</em> = 3+ dés.
                    En critique : dés doublés, fixes inchangés.
                </p>
                <InputField
                    v-model="convertValue"
                    label="Valeur moyenne"
                    default-label-position="top"
                    type="number"
                    placeholder="ex. 6"
                    class="w-40"
                    aria-label="Valeur à convertir en dés"
                />
                <div
                    v-if="convertSuggestions"
                    class="grid gap-2 sm:grid-cols-2"
                >
                    <div
                        v-for="(item, label) in {
                            'Bas (1 dé)': convertSuggestions.high,
                            'Moy (2 dés)': convertSuggestions.mid,
                            'Haut (3+ dés)': convertSuggestions.low,
                            'Mixte': convertSuggestions.mixed,
                            'Dés + fixe': convertSuggestions.withFixed,
                        }"
                        :key="label"
                        class="rounded-btn bg-base-200/60 px-3 py-2 text-sm"
                    >
                        <div class="text-[10px] uppercase tracking-wide text-base-content/50">{{ label }}</div>
                        <template v-if="item">
                            <div class="font-mono font-semibold">{{ item.formula }}</div>
                            <div class="text-xs text-base-content/60">
                                moy {{ item.average }} · crit {{ item.criticalFormula }} ({{ item.criticalAverage }})
                            </div>
                        </template>
                        <div
                            v-else
                            class="text-base-content/40"
                        >
                            —
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table complète repliable -->
            <details
                class="collapse collapse-arrow border border-base-content/10 rounded-box"
                :open="showFullTable"
                @toggle="showFullTable = $event.target.open"
            >
                <summary class="collapse-title text-sm font-semibold min-h-0 py-3">
                    Table complète (niveaux 1–20, dégâts par sort)
                </summary>
                <div class="collapse-content overflow-x-auto">
                    <table
                        v-if="normsByKey.dommages_spell?.norms?.grid"
                        class="table table-xs"
                    >
                        <thead>
                            <tr>
                                <th>Niv.</th>
                                <th
                                    v-for="band in DISPLAY_BANDS"
                                    :key="band.power"
                                >
                                    {{ band.short }}
                                </th>
                                <th>Équiv. dés (moy)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="lvl in MAX_LEVEL"
                                :key="lvl"
                            >
                                <td>{{ lvl }}</td>
                                <td
                                    v-for="band in DISPLAY_BANDS"
                                    :key="band.power"
                                >
                                    {{ normsByKey.dommages_spell.norms.grid[band.power]?.[lvl - 1] ?? '—' }}
                                </td>
                                <td class="text-[10px] text-base-content/60 whitespace-nowrap">
                                    {{ formatDiceSummary(normsByKey.dommages_spell.norms.grid.neutral?.[lvl - 1], { mod: Math.floor(lvl / 4) + 1 }) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-xs text-base-content/50 mt-2">
                        Familles : {{ SPELL_SCALE_FAMILIES.map((f) => f.label).join(', ') }}.
                        Soin / bouclier / PV temp. ≈ 80 % des dégâts ; vol de vie ≈ 50 %.
                    </p>
                </div>
            </details>

            <!-- Règles texte -->
            <details
                class="collapse collapse-arrow border border-base-content/10 rounded-box"
                :open="showRules"
                @toggle="showRules = $event.target.open"
            >
                <summary class="collapse-title text-sm font-semibold min-h-0 py-3">
                    Moduler un sort (repères hors cases à cocher)
                </summary>
                <div class="collapse-content">
                    <ul class="list-disc pl-5 space-y-1 text-xs text-base-content/80 leading-snug">
                        <li
                            v-for="(rule, i) in MODULATION_TEXT_RULES"
                            :key="i"
                        >
                            {{ rule }}
                        </li>
                    </ul>
                </div>
            </details>
        </template>
    </div>
</template>
