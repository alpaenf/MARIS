<template>
    <div
        class="relative overflow-hidden rounded-2xl border border-outline-variant
               bg-surface-container-lowest p-6 shadow-card
               hover:shadow-[0_8px_30px_rgba(15,23,42,0.08)] transition-shadow duration-300"
    >
        <!-- Decorative glow -->
        <div
            class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full blur-2xl opacity-60"
            :class="glowClass"
        ></div>

        <div class="relative flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-on-surface-variant">
                    {{ label }}
                </p>
                <div class="mt-2 flex items-end gap-1.5">
                    <p class="text-3xl font-bold text-on-surface leading-none">{{ value }}</p>
                    <span v-if="unit" class="mb-0.5 text-sm text-outline">{{ unit }}</span>
                </div>
                <p v-if="detail" class="mt-1.5 text-xs text-on-surface-variant">{{ detail }}</p>
            </div>

            <div
                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl"
                :class="iconBgClass"
            >
                <slot name="icon" />
            </div>
        </div>

        <p v-if="trend" :class="['mt-4 text-xs font-semibold flex items-center gap-1', trendClass]">
            <span class="material-symbols-outlined text-sm leading-none">
                {{ trend.toLowerCase().includes('naik') ? 'trending_up' : trend.toLowerCase().includes('turun') ? 'trending_down' : 'trending_flat' }}
            </span>
            {{ trend }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label:  { type: String,           required: true },
    value:  { type: [String, Number], required: true },
    unit:   { type: String,  default: '' },
    trend:  { type: String,  default: '' },
    detail: { type: String,  default: '' },
    tone:   { type: String,  default: 'teal' },
});

const iconBgClass = computed(() => {
    if (props.tone === 'emerald') return 'bg-tertiary/10 text-tertiary';
    if (props.tone === 'sky')     return 'bg-secondary/10 text-secondary';
    return 'bg-primary/10 text-primary';
});

const glowClass = computed(() => {
    if (props.tone === 'emerald') return 'bg-tertiary/20';
    if (props.tone === 'sky')     return 'bg-secondary/20';
    return 'bg-primary/15';
});

const trendClass = computed(() => {
    if (props.trend.toLowerCase().includes('turun')) return 'text-error';
    if (props.trend.toLowerCase().includes('naik'))  return 'text-tertiary';
    return 'text-outline';
});
</script>
