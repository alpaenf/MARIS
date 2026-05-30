<template>
  <AuthenticatedLayout>
    <template #header>
      <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Laporan</p>
        <h2 class="text-base font-semibold text-on-surface leading-tight">Reports</h2>
      </div>
    </template>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-on-surface">Reports</h1>
          <p class="mt-1 text-sm text-on-surface-variant">Export laporan analisis dalam format PDF.</p>
        </div>
      </div>

      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase tracking-[0.3em] text-slate-400">
            <tr>
              <th class="px-4 py-3">Region</th>
              <th class="px-4 py-3">Score</th>
              <th class="px-4 py-3">Priority</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in analyses" :key="item.id" class="hover:bg-slate-50">
              <td class="px-4 py-3 text-slate-700">
                <div class="font-semibold">{{ item.region_name }}</div>
                <div class="text-xs text-slate-400">{{ item.province }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-medium text-rose-600">
                  {{ item.climate_risk_score ?? '-' }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ item.restoration_priority ?? '-' }}</td>
              <td class="px-4 py-3">
                <a
                  :href="`/reports/analysis/${item.id}/export`"
                  class="rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-600 hover:text-teal-700"
                >
                  Export PDF
                </a>
              </td>
            </tr>
            <tr v-if="analyses.length === 0">
              <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
                Belum ada analisis untuk diekspor.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

defineProps({
  analyses: {
    type: Array,
    default: () => [],
  },
});
</script>
