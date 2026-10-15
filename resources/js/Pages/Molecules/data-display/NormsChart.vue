<script setup>
/**
 * Graphique Chart.js des normes : 5 courbes (power levels) avec le level actif en ligne verticale.
 */
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue';
import {
    POWER_LEVELS,
    POWER_LABELS,
    POWER_COLORS,
    MAX_LEVEL,
} from '@/Utils/Characteristic/normsConstants';
import { formatSignedBudgetCell } from '@/Utils/Characteristic/signedNormsDisplay.js';

const props = defineProps({
    grid: { type: Object, required: true },
    effectivePowerIndex: { type: Number, default: 2 },
    selectedLevel: { type: Number, default: null },
    effectiveLevelIndex: { type: Number, default: null },
    /** Affiche les valeurs du tooltip en ±N (budget bonus/malus). */
    signedBudget: { type: Boolean, default: false },
});

const canvasRef = ref(null);
let chart = null;

const levels = Array.from({ length: MAX_LEVEL }, (_, i) => i + 1);

const datasets = computed(() =>
    POWER_LEVELS.map((pl, idx) => ({
        label: POWER_LABELS[pl],
        data: (props.grid?.[pl] || []).map((v, i) => ({ x: i + 1, y: v })),
        borderColor: POWER_COLORS[pl],
        backgroundColor: POWER_COLORS[pl] + '33',
        borderWidth: idx === props.effectivePowerIndex ? 3 : 1.5,
        borderDash: idx === props.effectivePowerIndex ? [] : [4, 4],
        pointRadius: 0,
        tension: 0.3,
        fill: false,
    }))
);

/**
 * Plugin inline : dessine une ligne verticale au level sélectionné.
 */
const verticalLinePlugin = {
    id: 'normsVerticalLine',
    afterDraw(chartInstance) {
        const lvl = props.effectiveLevelIndex;
        if (lvl === null) return;
        const xScale = chartInstance.scales.x;
        const yScale = chartInstance.scales.y;
        if (!xScale || !yScale) return;
        const x = xScale.getPixelForValue(lvl + 1);
        const ctx = chartInstance.ctx;
        ctx.save();
        ctx.beginPath();
        ctx.setLineDash([6, 3]);
        ctx.strokeStyle = 'rgba(99, 102, 241, 0.6)';
        ctx.lineWidth = 2;
        ctx.moveTo(x, yScale.top);
        ctx.lineTo(x, yScale.bottom);
        ctx.stroke();
        ctx.restore();
    },
};

async function createChart() {
    if (!canvasRef.value) return;

    const { Chart, registerables } = await import('chart.js');
    Chart.register(...registerables);

    if (chart) chart.destroy();

    chart = new Chart(canvasRef.value, {
        type: 'line',
        data: {
            labels: levels,
            datasets: datasets.value,
        },
        plugins: [verticalLinePlugin],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: {
                    title: { display: true, text: 'Niveau' },
                    ticks: { stepSize: 1 },
                },
                y: {
                    title: { display: true, text: props.signedBudget ? 'Budget (±)' : 'Valeur' },
                    beginAtZero: true,
                },
            },
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            const raw = ctx.parsed?.y;
                            const shown = formatSignedBudgetCell(raw, props.signedBudget);
                            return `${ctx.dataset.label} : ${shown}`;
                        },
                    },
                },
            },
        },
    });
}

function updateChart() {
    if (!chart) return;
    chart.data.datasets = datasets.value;
    if (chart.options?.scales?.y?.title) {
        chart.options.scales.y.title.text = props.signedBudget ? 'Budget (±)' : 'Valeur';
    }
    chart.update();
}

watch([datasets, () => props.effectiveLevelIndex, () => props.signedBudget], updateChart, { deep: true });
onMounted(createChart);
onBeforeUnmount(() => { chart?.destroy(); chart = null; });
</script>

<template>
    <div class="relative h-64 min-h-64">
        <canvas ref="canvasRef" />
    </div>
</template>
