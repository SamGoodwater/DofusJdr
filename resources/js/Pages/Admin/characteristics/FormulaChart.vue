<script setup>
/**
 * Aperçu graphique d'une formule (points x/y) avec Chart.js.
 * Axe X linéaire (valeurs numériques, pas catégories) pour que min/max fonctionnent.
 */
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    points: {
        type: Array,
        default: () => [],
    },
    /** Hauteur minimale en px (le conteneur peut être plus grand via CSS). */
    height: { type: Number, default: 128 },
    /** Libellé axe X (ex. niveau, variable). */
    xLabel: { type: String, default: '' },
    /** Libellé axe Y (ex. résultat, valeur). */
    yLabel: { type: String, default: '' },
    /** Bornes des axes { xMin, xMax, yMin, yMax }. Si fourni, fixe l'échelle (marge ~5 % au min, ~15 % au max). Surchargé par axisBoundsOverride si fourni. */
    axisBounds: { type: Object, default: null },
    /** Surcharge manuelle des bornes (affichage uniquement). Peut être partielle. */
    axisBoundsOverride: { type: Object, default: null },
    /** Tension de la courbe (0 = segments droits, 0.4 = très lisse). */
    tension: { type: Number, default: 0.2 },
    /** Rayon des points (0 = masqués). */
    pointRadius: { type: Number, default: 2 },
    /** Rayon des points au survol. */
    pointHoverRadius: { type: Number, default: 4 },
    /** Épaisseur de la ligne en px. */
    borderWidth: { type: Number, default: 2 },
    /** Afficher la grille des axes. */
    showGrid: { type: Boolean, default: true },
});

const canvasRef = ref(null);
/** Message si le filtre des bornes vide la courbe. */
const filterEmpty = ref(false);
/** @type {import('chart.js').Chart | null} */
let chartInstance = null;

const hasPoints = computed(() => Array.isArray(props.points) && props.points.length > 1);

/** Étend les bornes : ~5 % en min, ~15 % en max pour que les extrêmes restent visibles. */
function withPadding(bounds) {
    if (!bounds) return null;
    const padMin = 0.05;
    const padMax = 0.15;
    let xMin = bounds.xMin;
    let xMax = bounds.xMax;
    let yMin = bounds.yMin;
    let yMax = bounds.yMax;
    if (Number.isFinite(xMin) && Number.isFinite(xMax)) {
        const range = Math.max(xMax - xMin, 1);
        xMin = xMin - range * padMin;
        xMax = xMax + range * padMax;
    }
    if (Number.isFinite(yMin) && Number.isFinite(yMax)) {
        const range = Math.max(yMax - yMin, 1);
        yMin = yMin - range * padMin;
        yMax = yMax + range * padMax;
    }
    return { xMin, xMax, yMin, yMax };
}

/**
 * Fusionne surcharge partielle + bornes auto (avec padding).
 * @returns {{ xMin: number|null, xMax: number|null, yMin: number|null, yMax: number|null }|null}
 */
function resolveEffectiveBounds() {
    const padded = withPadding(props.axisBounds);
    const o = props.axisBoundsOverride;
    const hasOverride = o
        && (Number.isFinite(o.xMin) || Number.isFinite(o.xMax) || Number.isFinite(o.yMin) || Number.isFinite(o.yMax));
    if (!hasOverride) {
        return padded;
    }
    return {
        xMin: Number.isFinite(o.xMin) ? o.xMin : (padded?.xMin ?? null),
        xMax: Number.isFinite(o.xMax) ? o.xMax : (padded?.xMax ?? null),
        yMin: Number.isFinite(o.yMin) ? o.yMin : (padded?.yMin ?? null),
        yMax: Number.isFinite(o.yMax) ? o.yMax : (padded?.yMax ?? null),
    };
}

function buildChart() {
    filterEmpty.value = false;
    if (!canvasRef.value || !hasPoints.value) return;
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }

    const effectiveBounds = resolveEffectiveBounds();
    let dataPoints = props.points
        .map((p) => ({ x: Number(p.x), y: Number(p.y) }))
        .filter((p) => Number.isFinite(p.x) && Number.isFinite(p.y));

    if (effectiveBounds && Number.isFinite(effectiveBounds.xMin) && Number.isFinite(effectiveBounds.xMax)) {
        const lo = Math.min(effectiveBounds.xMin, effectiveBounds.xMax);
        const hi = Math.max(effectiveBounds.xMin, effectiveBounds.xMax);
        dataPoints = dataPoints.filter((p) => p.x >= lo && p.x <= hi);
    }

    if (dataPoints.length < 2) {
        filterEmpty.value = true;
        return;
    }

    chartInstance = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Résultat',
                    data: dataPoints,
                    borderColor: 'var(--color, oklch(var(--p)))',
                    backgroundColor: 'var(--chart-fill, oklch(var(--p) / 0.1))',
                    fill: true,
                    tension: Math.max(0, Math.min(1, props.tension)),
                    pointRadius: Math.max(0, props.pointRadius),
                    pointHoverRadius: Math.max(0, props.pointHoverRadius),
                    borderWidth: Math.max(0, props.borderWidth),
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'nearest',
                axis: 'x',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: (items) => {
                            const x = items[0]?.parsed?.x;
                            return Number.isFinite(x) ? String(x) : '';
                        },
                        label: (ctx) => ` ${ctx.parsed.y}`,
                    },
                },
            },
            scales: {
                x: {
                    type: 'linear',
                    title: { display: !!props.xLabel, text: props.xLabel },
                    ticks: { maxTicksLimit: 8 },
                    grid: { display: props.showGrid, color: 'oklch(var(--bc) / 0.12)' },
                    ...(effectiveBounds && Number.isFinite(effectiveBounds.xMin) && Number.isFinite(effectiveBounds.xMax)
                        ? { min: Math.min(effectiveBounds.xMin, effectiveBounds.xMax), max: Math.max(effectiveBounds.xMin, effectiveBounds.xMax) }
                        : {}),
                },
                y: {
                    type: 'linear',
                    title: { display: !!props.yLabel, text: props.yLabel },
                    beginAtZero: false,
                    grid: { display: props.showGrid, color: 'oklch(var(--bc) / 0.12)' },
                    ...(effectiveBounds && Number.isFinite(effectiveBounds.yMin) && Number.isFinite(effectiveBounds.yMax)
                        ? { min: Math.min(effectiveBounds.yMin, effectiveBounds.yMax), max: Math.max(effectiveBounds.yMin, effectiveBounds.yMax) }
                        : {}),
                },
            },
        },
    });
}

function destroyChart() {
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
}

onMounted(() => {
    buildChart();
});

onBeforeUnmount(() => {
    destroyChart();
});

watch(
    () => [
        props.points,
        props.axisBounds,
        props.axisBoundsOverride,
        props.tension,
        props.pointRadius,
        props.pointHoverRadius,
        props.borderWidth,
        props.showGrid,
    ],
    () => {
        destroyChart();
        buildChart();
    },
    { deep: true }
);
</script>

<template>
    <div v-if="!hasPoints" class="flex h-full items-center justify-center text-xs text-base-content/50">
        Aucun point (vérifiez la formule et les limites).
    </div>
    <div v-else class="relative h-full w-full" :style="{ minHeight: `${height}px` }">
        <div
            v-if="filterEmpty"
            class="absolute inset-0 z-10 flex items-center justify-center text-xs text-base-content/50"
        >
            Aucun point dans les bornes affichage.
        </div>
        <canvas ref="canvasRef" :class="{ invisible: filterEmpty }" />
    </div>
</template>
