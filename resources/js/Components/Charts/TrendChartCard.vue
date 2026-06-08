<template>
    <div class="glass-card rounded-2xl p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900">{{ title }}</p>
                <p class="text-xs text-slate-500">{{ subtitle }}</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-500">7 Hari</span>
        </div>
        <div class="mt-4">
            <svg viewBox="0 0 320 120" class="h-28 w-full preserve-3d">
                <defs>
                    <linearGradient id="trend" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#14b8a6" />
                        <stop offset="1" stop-color="#22c55e" />
                    </linearGradient>
                </defs>
                <path
                    :d="pathData.line"
                    fill="none"
                    stroke="url(#trend)"
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
                <path
                    :d="pathData.area"
                    fill="url(#trend)"
                    opacity="0.08"
                />
            </svg>
            <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                <span>{{ leftLabel }}</span>
                <span>{{ rightLabel }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    leftLabel: { type: String, default: 'Senin' },
    rightLabel: { type: String, default: 'Minggu' },
    dataValues: { type: Array, default: () => [] }
});

const pathData = computed(() => {
    const values = props.dataValues.length ? props.dataValues : [10, 20, 15, 30, 25, 40, 50];
    const maxVal = Math.max(...values, 1);
    const minVal = Math.min(...values, 0);
    const range = maxVal - minVal || 1;
    
    // SVG bounds
    const width = 310;
    const height = 100;
    const paddingX = 10;
    const paddingY = 10;

    const points = values.map((val, i) => {
        const x = paddingX + (i / (values.length - 1)) * (width - paddingX * 2);
        const y = paddingY + height - ((val - minVal) / range) * height;
        return { x, y };
    });

    if (points.length === 0) return { line: '', area: '' };

    let dLine = `M${points[0].x} ${points[0].y}`;
    for (let i = 1; i < points.length; i++) {
        // Simple cubic bezier smoothing
        const curr = points[i];
        const prev = points[i - 1];
        const cx1 = prev.x + (curr.x - prev.x) / 2;
        const cy1 = prev.y;
        const cx2 = prev.x + (curr.x - prev.x) / 2;
        const cy2 = curr.y;
        dLine += ` C ${cx1} ${cy1}, ${cx2} ${cy2}, ${curr.x} ${curr.y}`;
    }

    const dArea = `${dLine} L${points[points.length - 1].x} 120 L${points[0].x} 120 Z`;

    return { line: dLine, area: dArea };
});
</script>
