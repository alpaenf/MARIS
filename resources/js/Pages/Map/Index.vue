<template>
  <AuthenticatedLayout>
    <template #header>
      <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Peta</p>
        <h2 class="text-base font-semibold text-on-surface leading-tight">Coastal Map</h2>
      </div>
    </template>
    <div class="space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 class="text-xl font-bold text-on-surface">Coastal Map</h1>
          <p class="mt-1 text-sm text-on-surface-variant">Layer prioritas, risiko iklim, dan potensi karbon.</p>
        </div>
        <div class="flex items-center gap-3 text-xs text-slate-500">
          <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-emerald-700">Live</span>
          {{ analyses.length > 0 ? 'Database Terkoneksi' : 'Data demonstrasi' }}
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
        <aside class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <div>
            <h2 class="text-sm font-semibold text-slate-900">Map Layers</h2>
            <p class="mt-2 text-xs text-slate-500">Aktifkan layer sesuai kebutuhan.</p>
          </div>
          <div class="space-y-3 text-sm text-slate-600">
            <label class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
              <span>Priority Layer</span>
              <input v-model="layers.priority" type="checkbox" class="h-4 w-4 rounded border-slate-300" />
            </label>
            <label class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
              <span>Climate Layer</span>
              <input v-model="layers.climate" type="checkbox" class="h-4 w-4 rounded border-slate-300" />
            </label>
            <label class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-3 py-2">
              <span>Carbon Layer</span>
              <input v-model="layers.carbon" type="checkbox" class="h-4 w-4 rounded border-slate-300" />
            </label>
          </div>
          <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3 text-xs text-slate-500">
            Skor warna: merah (prioritas tinggi), biru (risiko iklim), hijau (potensi karbon).
          </div>
        </aside>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <CoastalMap :active-layers="layers" :analyses="analyses" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CoastalMap from "@/Components/Maps/CoastalMap.vue";

const props = defineProps({
  analyses: {
    type: Array,
    default: () => [],
  },
});

const layers = reactive({
  priority: true,
  climate: true,
  carbon: true,
});
</script>
