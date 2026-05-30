<template>
    <div
        class="rounded-2xl border border-outline-variant bg-surface-container-lowest
               shadow-card overflow-hidden"
    >
        <!-- Card Header -->
        <div
            class="flex items-center justify-between border-b border-outline-variant
                   bg-surface-bright px-6 py-4"
        >
            <div>
                <p class="text-sm font-semibold text-on-surface">{{ title }}</p>
                <p v-if="subtitle" class="text-xs text-on-surface-variant mt-0.5">{{ subtitle }}</p>
            </div>
            <button
                v-if="action"
                type="button"
                class="rounded-full border border-outline-variant bg-surface-container
                       px-3 py-1.5 text-xs font-medium text-on-surface-variant
                       hover:bg-surface-container-high hover:text-on-surface transition-colors"
            >
                {{ action }}
            </button>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <slot />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title:    { type: String, required: true },
    subtitle: { type: String, default: '' },
    action:   { type: String, default: '' },
});
</script>
