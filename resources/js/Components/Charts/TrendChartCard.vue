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
            <svg viewBox="0 0 320 120" class="h-28 w-full">
                <defs>
                    <linearGradient id="trend" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#14b8a6" />
                        <stop offset="1" stop-color="#22c55e" />
                    </linearGradient>
                </defs>
                <path
                    d="M10 90 C 60 30, 120 110, 170 60 C 210 20, 260 70, 310 30"
                    fill="none"
                    stroke="url(#trend)"
                    stroke-width="4"
                    stroke-linecap="round"
                />
                <path
                    d="M10 90 C 60 30, 120 110, 170 60 C 210 20, 260 70, 310 30 L310 120 L10 120 Z"
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
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    leftLabel: { type: String, default: 'Senin' },
    rightLabel: { type: String, default: 'Minggu' },
});
</script>
