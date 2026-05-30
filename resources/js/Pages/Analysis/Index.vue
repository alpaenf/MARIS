<template>
  <AuthenticatedLayout>
    <template #header>
      <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Analisis</p>
        <h2 class="text-base font-semibold text-on-surface leading-tight">Analysis</h2>
      </div>
    </template>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
        <h1 class="text-xl font-bold text-on-surface">Analysis</h1>
          <p class="mt-1 text-sm text-on-surface-variant">Riwayat analisis terbaru.</p>
        </div>
        <a
          href="/analysis/create"
          class="rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary-container transition-colors"
        >
          Buat Analisis
        </a>
      </div>

      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase tracking-[0.3em] text-slate-400">
            <tr>
              <th class="px-4 py-3">Region</th>
              <th class="px-4 py-3">Score</th>
              <th class="px-4 py-3">Priority</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in analyses" :key="item.id" class="hover:bg-slate-50">
              <td class="px-4 py-3 text-slate-700">
                <a :href="`/analysis/${item.id}`" class="font-semibold text-teal-700 hover:text-teal-600">
                  {{ item.region_name }}
                </a>
                <div class="text-xs text-slate-400">{{ item.province }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-medium text-rose-600">
                  {{ item.climate_risk_score ?? '-' }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ item.restoration_priority ?? '-' }}</td>
              <td class="px-4 py-3 text-slate-500">
                {{ formatDate(item.created_at) }}
              </td>
              <td class="px-4 py-3 text-right">
                <button
                  @click="deleteAnalysis(item.id)"
                  class="rounded-full p-1.5 text-error hover:bg-error/10 hover:scale-105 active:scale-95 transition-all inline-flex items-center justify-center"
                  title="Hapus Analisis"
                >
                  <span class="material-symbols-outlined text-base leading-none">delete</span>
                </button>
              </td>
            </tr>
            <tr v-if="analyses.length === 0">
              <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                Belum ada analisis. Mulai buat analisis baru.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
  analyses: {
    type: Array,
    default: () => [],
  },
});

const deleteAnalysis = (id) => {
  if (confirm("Apakah Anda yakin ingin menghapus analisis ini secara permanen?")) {
    router.delete(route('analysis.destroy', { id }));
  }
};

const formatDate = (value) => {
  if (!value) return "-";
  return new Date(value).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};
</script>
