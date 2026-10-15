<script setup>
/**
 * Panneau MJ d'une caractéristique : barème, calcul (fiche créature), conversion, propriétés.
 * L'outil Barème n'affiche pas l'onglet Calcul (pas de runtime, doublon du tableau).
 */
import { computed, inject, ref, toValue, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import NormsViewer from "@/Pages/Organismes/data-display/NormsViewer.vue";
import CharacteristicFormulaRichText from "@/Pages/Molecules/data-display/CharacteristicFormulaRichText.vue";
import CharacteristicDecompositionBody from "@/Pages/Molecules/data-display/CharacteristicDecompositionBody.vue";
import FormulaChart from "@/Pages/Admin/characteristics/FormulaChart.vue";
import { loadCharacteristicCard } from "@/Composables/characteristic/useCharacteristicCard";
import { buildFormulaTableView } from "@/Utils/characteristic/formulaConfig";
import { CHARACTERISTIC_RUNTIME_INJECT_KEY } from "@/Composables/entity/characteristicRuntimeContext";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import {
    resolveCreatureContextFromPageProps,
    resolveRuntimeModelForKey,
} from "@/Composables/characteristic/resolveRuntimeModelForKey";

const props = defineProps({
    characteristicKey: { type: String, required: true },
    /** Groupe API : creature | object | spell | consumable | profile */
    group: { type: String, default: "" },
    /** Entité pivot : * | monster | item | … */
    entity: { type: String, default: "*" },
    /** ViewModel runtime (créature en édition) pour l'onglet Calcul. */
    runtimeModel: { type: Object, default: null },
    /** Payload resolved-stats complet (prioritaire sur inject / page). */
    runtime: { type: Object, default: null },
    /** Valeur saisie à positionner dans le barème (optionnel). */
    highlightValue: { type: [Number, String, null], default: null },
    /** Onglets à afficher (défaut : tous disponibles). */
    tabs: {
        type: Array,
        default: () => ["bareme", "calcul", "conversion", "proprietes"],
    },
});

const page = usePage();
const injectedRuntime = inject(CHARACTERISTIC_RUNTIME_INJECT_KEY, null);
const pageCreatureContext = computed(() => resolveCreatureContextFromPageProps(page.props));

const fetchCreatureId = computed(() => {
    if (props.runtimeModel || props.runtime) return null;
    const injected = toValue(injectedRuntime);
    if (injected && typeof injected === "object") return null;
    if (pageCreatureContext.value?.runtime) return null;
    return pageCreatureContext.value?.creatureId ?? null;
});

const fetchEntityQuery = computed(
    () => pageCreatureContext.value?.entityQuery || "monster",
);

const { runtime: fetchedRuntime, loading: runtimeLoading } = useCreatureResolvedStats(
    fetchCreatureId,
    fetchEntityQuery,
);

const loading = ref(false);
const error = ref("");
const normsData = ref(null);
const card = ref(null);
const activeTab = ref("bareme");

const resolvedRuntimePayload = computed(() => {
    if (props.runtime && typeof props.runtime === "object") return props.runtime;
    const injected = toValue(injectedRuntime);
    if (injected && typeof injected === "object") return injected;
    if (pageCreatureContext.value?.runtime) return pageCreatureContext.value.runtime;
    return fetchedRuntime.value;
});

const effectiveRuntimeModel = computed(() => {
    if (props.runtimeModel && typeof props.runtimeModel === "object") {
        return props.runtimeModel;
    }
    const key = String(props.characteristicKey || "").trim();
    const baseFromCard = card.value
        ? {
              key,
              name: card.value.name || key,
              icon: card.value.icon || "",
              color: card.value.color || "",
              formulaDisplay: card.value.formula_display || "",
              formulaBdd: card.value.formula || "",
              helper: card.value.helper || "",
              descriptions: card.value.descriptions || "",
              limitMin: card.value.limit_min ?? null,
              limitMax: card.value.limit_max ?? null,
          }
        : null;
    return resolveRuntimeModelForKey(resolvedRuntimePayload.value, key, baseFromCard);
});

const availableTabs = computed(() => {
    const wanted = props.tabs;
    const tabs = [];
    if (wanted.includes("bareme")) {
        tabs.push({ id: "bareme", label: "Barème" });
    }
    if (wanted.includes("calcul")) {
        tabs.push({ id: "calcul", label: "Calcul" });
    }
    if (wanted.includes("conversion") && (card.value?.conversion_formula || card.value?.conversion_dofus_sample)) {
        tabs.push({ id: "conversion", label: "Conversion" });
    }
    if (wanted.includes("proprietes")) {
        tabs.push({ id: "proprietes", label: "Propriétés" });
    }
    return tabs;
});

watch(
    availableTabs,
    (tabs) => {
        if (!tabs.some((t) => t.id === activeTab.value)) {
            activeTab.value = tabs[0]?.id || "bareme";
        }
    },
    { immediate: true },
);

const title = computed(
    () => card.value?.name || normsData.value?.characteristic?.name || props.characteristicKey,
);

const subtitle = computed(() => {
    const labels = availableTabs.value.map((t) => t.label.toLowerCase());
    if (labels.length === 0) {
        return "Outils MJ.";
    }
    return `Outils MJ — ${labels.join(", ")}.`;
});

const formulaTable = computed(() => buildFormulaTableView(card.value?.formula || ""));

const conversionPoints = computed(() => {
    const samplesX = card.value?.conversion_dofus_sample;
    const samplesY = card.value?.conversion_krosmoz_sample;
    if (!Array.isArray(samplesX) || !Array.isArray(samplesY)) return [];
    const n = Math.min(samplesX.length, samplesY.length);
    const points = [];
    for (let i = 0; i < n; i++) {
        const x = Number(samplesX[i]);
        const y = Number(samplesY[i]);
        if (Number.isFinite(x) && Number.isFinite(y)) {
            points.push({ x, y });
        }
    }
    return points;
});

const propertyRows = computed(() => {
    const c = card.value;
    if (!c) return [];
    return [
        { label: "Clé", value: c.key },
        { label: "Groupe", value: c.group },
        { label: "Entité", value: c.entity },
        { label: "Nature", value: c.nature },
        { label: "Type", value: c.type },
        { label: "Unité", value: c.unit },
        { label: "Min", value: c.min },
        { label: "Max", value: c.max },
        { label: "Défaut", value: c.default_value },
        { label: "Limite absolue", value: c.limit_absolute ? "oui" : "non" },
        { label: "Formule affichée", value: c.formula_display },
        { label: "Formule réelle", value: c.formula },
        { label: "Forgemagie max", value: c.forgemagie_max },
        { label: "Prix / unité", value: c.base_price_per_unit },
        { label: "Prix rune", value: c.rune_price_per_unit },
    ].filter((row) => row.value !== null && row.value !== undefined && String(row.value).trim() !== "");
});

async function load() {
    const key = String(props.characteristicKey || "").trim();
    if (!key) {
        error.value = "Caractéristique inconnue.";
        return;
    }
    loading.value = true;
    error.value = "";
    try {
        const entity = props.entity || "*";
        const group = props.group || "";
        const normsUrl = route("api.characteristics.norms", { key, entity });
        const normsParams = {};
        if (group) normsParams.group = group;
        const [normsRes, cardRes] = await Promise.all([
            axios.get(normsUrl, { params: normsParams }),
            loadCharacteristicCard(key, { group: group || null, entity }),
        ]);
        normsData.value = normsRes.data;
        card.value = cardRes;
    } catch (e) {
        error.value = e?.response?.data?.error || "Impossible de charger la fiche MJ.";
        normsData.value = null;
        card.value = null;
    } finally {
        loading.value = false;
    }
}

watch(
    () => [props.characteristicKey, props.group, props.entity],
    () => {
        normsData.value = null;
        card.value = null;
        void load();
    },
    { immediate: true },
);

defineExpose({ reload: load });
</script>

<template>
    <div class="characteristic-gm-panel space-y-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h3 class="text-lg font-bold leading-tight">{{ title }}</h3>
                <p class="text-sm text-base-content/70">
                    {{ subtitle }}
                </p>
            </div>
            <div v-if="availableTabs.length > 1" class="tabs tabs-boxed tabs-sm">
                <button
                    v-for="tab in availableTabs"
                    :key="tab.id"
                    type="button"
                    class="tab"
                    :class="{ 'tab-active': activeTab === tab.id }"
                    @click="activeTab = tab.id"
                >
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="flex justify-center py-12">
            <span class="loading loading-spinner loading-md" />
        </div>

        <div v-else-if="error" class="alert alert-warning text-sm">
            <i class="fa-solid fa-triangle-exclamation" />
            <span>{{ error }}</span>
        </div>

        <template v-else>
            <div v-show="activeTab === 'bareme'">
                <div v-if="!normsData?.norms" class="alert alert-info text-sm">
                    <i class="fa-solid fa-info-circle" />
                    <span>Aucune norme définie pour cette caractéristique dans ce contexte.</span>
                </div>
                <NormsViewer
                    v-else
                    :grid="normsData.norms.grid"
                    :conditions="normsData.norms.conditions || []"
                    :description="normsData.norms.description || ''"
                    :min-limit="normsData.norms.limits?.min ?? null"
                    :max-limit="normsData.norms.limits?.max ?? null"
                    :characteristic-name="title"
                    :characteristic-color="card?.color || normsData.characteristic?.color || 'indigo'"
                    :available-characteristics="normsData.available_characteristics || {}"
                    :help-section-html="normsData.norms.help_section?.html || ''"
                    :help-section-title="normsData.norms.help_section?.title || ''"
                    :show-header="false"
                />
                <p
                    v-if="highlightValue !== null && highlightValue !== ''"
                    class="mt-2 text-xs text-base-content/70"
                >
                    Valeur saisie : <span class="font-semibold tabular-nums">{{ highlightValue }}</span>
                </p>
            </div>

            <div v-show="activeTab === 'calcul'" class="space-y-3">
                <div v-if="card?.formula" class="rounded-box border border-base-content/15 p-3">
                    <p class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-base-content/55">
                        Formule réelle
                    </p>
                    <CharacteristicFormulaRichText
                        v-if="formulaTable"
                        :formula="String(card.formula)"
                    />
                    <code v-else class="text-xs break-all">{{ card.formula }}</code>
                    <p v-if="card.formula_display" class="mt-2 text-xs text-base-content/70">
                        Affichage joueur · {{ card.formula_display }}
                    </p>
                </div>
                <div v-if="runtimeLoading" class="flex items-center gap-2 text-sm text-base-content/60">
                    <span class="loading loading-spinner loading-sm" />
                    Chargement de la décomposition…
                </div>
                <CharacteristicDecompositionBody
                    v-else-if="effectiveRuntimeModel"
                    :model="effectiveRuntimeModel"
                />
                <div v-else class="alert alert-info text-sm">
                    <i class="fa-solid fa-info-circle" />
                    <span>
                        Ouvre la fiche ou l’édition d’un monstre / PNJ pour afficher la décomposition
                        base / équipement / contexte / temporaire de cette caractéristique.
                    </span>
                </div>
            </div>

            <div v-show="activeTab === 'conversion'" class="space-y-3">
                <p v-if="card?.conversion_formula" class="text-xs">
                    <span class="text-base-content/55">Formule · </span>
                    <code class="break-all">{{ card.conversion_formula }}</code>
                </p>
                <FormulaChart
                    v-if="conversionPoints.length > 1"
                    :points="conversionPoints"
                    :height="220"
                    x-label="Dofus"
                    y-label="Krosmoz"
                />
                <div v-else class="alert alert-info text-sm">
                    <i class="fa-solid fa-info-circle" />
                    <span>Pas d'échantillon de conversion disponible pour cette caractéristique.</span>
                </div>
            </div>

            <div v-show="activeTab === 'proprietes'">
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div
                        v-for="row in propertyRows"
                        :key="row.label"
                        class="rounded-md border border-base-content/10 bg-base-200/40 px-2.5 py-2"
                    >
                        <dt class="text-[10px] font-semibold uppercase tracking-wider text-base-content/55">
                            {{ row.label }}
                        </dt>
                        <dd class="mt-0.5 break-words text-sm">{{ row.value }}</dd>
                    </div>
                </dl>
            </div>
        </template>
    </div>
</template>
