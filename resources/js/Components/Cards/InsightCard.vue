<template>
    <div
        class="relative overflow-hidden rounded-2xl border border-primary/20
               bg-primary/5 p-6"
    >
        <!-- Decorative background pulse -->
        <div
            class="pointer-events-none absolute -right-10 -top-10 h-36 w-36
                   rounded-full bg-primary/10 blur-3xl"
        ></div>

        <!-- Header -->
        <div class="relative flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl leading-none"
                      style="font-variation-settings: 'FILL' 1">psychiatry</span>
                <div class="text-sm font-semibold text-primary">{{ title }}</div>
            </div>
            <span
                class="rounded-full bg-primary text-on-primary
                       px-3 py-1 text-xs font-semibold"
            >{{ badge }}</span>
        </div>

        <!-- Summary -->
        <p class="relative text-sm text-on-surface leading-relaxed">{{ summary }}</p>

        <!-- Bullets -->
        <ul v-if="bullets.length" class="relative mt-4 space-y-2">
            <li
                v-for="(item, index) in bullets"
                :key="index"
                class="flex items-start gap-2 text-xs text-on-surface-variant"
            >
                <span
                    class="material-symbols-outlined text-tertiary mt-0.5"
                    style="font-size: 14px; font-variation-settings: 'FILL' 1"
                >check_circle</span>
                <span>{{ item }}</span>
            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps({
    title:   { type: String, required: true },
    badge:   { type: String, default: 'Gemini AI' },
    summary: { type: String, default: '' },
    bullets: { type: Array,  default: () => [] },
});
</script>
