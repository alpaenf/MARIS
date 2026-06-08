<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import KpiCard from '@/Components/KPI/KpiCard.vue';
import TrendChartCard from '@/Components/Charts/TrendChartCard.vue';
import InsightCard from '@/Components/Cards/InsightCard.vue';
import SectionCard from '@/Components/Cards/SectionCard.vue';

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({ avg_risk: 0, total_carbon: 0, high_priority: 0, total_analyses: 0 }),
  },
  recentAnalysis: {
    type: Array,
    default: () => [],
  },
  priorityRegions: {
    type: Array,
    default: () => [],
  },
  climateTrend: {
    type: Array,
    default: () => [],
  },
  carbonTrend: {
    type: Array,
    default: () => [],
  },
});

const kpis = computed(() => [
  {
    label: 'Average Risk Score',
    value: props.stats.avg_risk,
    unit: '/100',
    trend: 'Skor rata-rata risiko iklim',
    detail: `${props.stats.total_analyses} wilayah dianalisis`,
    tone: 'teal',
  },
  {
    label: 'Total Carbon Potential',
    value: props.stats.total_carbon.toLocaleString('id-ID'),
    unit: 'tCO₂e',
    trend: 'Total potensi penyerapan karbon',
    detail: 'Berdasarkan data restorasi aktif',
    tone: 'emerald',
  },
  {
    label: 'High Priority Regions',
    value: props.stats.high_priority,
    unit: 'kawasan',
    trend: 'Tingkat abrasi dan kerentanan kritis',
    detail: 'Memerlukan intervensi segera',
    tone: 'sky',
  },
]);
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between gap-4">
        <div>
          <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Ringkasan</p>
          <h2 class="text-2xl font-semibold text-slate-900">Dashboard</h2>
        </div>
        <Link
          href="/analysis/create"
          class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-500"
        >
          Mulai Analisis
        </Link>
      </div>
    </template>

    <div class="space-y-6 fade-in-up">
      <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <KpiCard
          v-for="(kpi, index) in kpis"
          :key="index"
          :label="kpi.label"
          :value="kpi.value"
          :unit="kpi.unit"
          :trend="kpi.trend"
          :detail="kpi.detail"
          :tone="kpi.tone"
        >
          <template #icon>
            <svg
              v-if="index === 0"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 14l3-3 4 4 5-6" />
            </svg>
            <svg
              v-else-if="index === 1"
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0z" />
            </svg>
          </template>
        </KpiCard>
      </section>

      <section class="grid gap-4 lg:grid-cols-2">
        <TrendChartCard
          title="Climate Trend"
          subtitle="Perubahan risiko abrasi dan banjir"
          left-label="Awal Bulan Ini"
          right-label="Hari Ini"
          :data-values="climateTrend"
        />
        <TrendChartCard
          title="Carbon Trend"
          subtitle="Estimasi potensi karbon tersimpan"
          left-label="Awal Bulan Ini"
          right-label="Hari Ini"
          :data-values="carbonTrend"
        />
      </section>

      <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <SectionCard title="Recent Analysis" subtitle="Aktivitas terbaru tim analisis" action="Lihat semua">
          <div class="overflow-hidden rounded-xl border border-slate-100">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-400">
                <tr>
                  <th class="px-4 py-3">Region</th>
                  <th class="px-4 py-3">Risk Score</th>
                  <th class="px-4 py-3">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <tr v-for="(item, index) in recentAnalysis" :key="index">
                  <td class="px-4 py-3 text-slate-700">
                    <a :href="`/analysis/${item.id}`" class="hover:underline text-teal-700 font-semibold">
                      {{ item.region }}
                    </a>
                  </td>
                  <td class="px-4 py-3">
                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-medium text-rose-600">
                      {{ item.risk }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-slate-500">{{ item.date }}</td>
                </tr>
                <tr v-if="recentAnalysis.length === 0">
                  <td colspan="3" class="px-4 py-6 text-center text-xs text-slate-500">
                    Belum ada riwayat analisis. Silakan buat analisis pertama Anda.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </SectionCard>

        <div class="space-y-4">
          <InsightCard
            title="AI Insight"
            badge="Gemini"
            summary="Pola abrasi meningkat di segmen pesisir dengan curah hujan tinggi dan tutupan mangrove rendah."
            :bullets="[
              'Prioritaskan restorasi di Demak dan Sayung.',
              'Naikkan kepadatan tanaman 20-30% untuk penguatan garis pantai.',
              'Rekomendasi monitoring setiap 6 minggu.'
            ]"
          />
          <SectionCard title="Priority Regions" subtitle="Daerah dengan prioritas restorasi tinggi">
            <div class="space-y-3">
              <div
                v-for="(region, index) in priorityRegions"
                :key="index"
                class="rounded-xl border border-slate-100 bg-white px-4 py-3"
              >
                <div class="text-sm font-semibold text-slate-900">{{ region.name }}</div>
                <div class="text-xs text-slate-500">{{ region.note }}</div>
              </div>
              <div v-if="priorityRegions.length === 0" class="text-center py-4 text-xs text-slate-500">
                Tidak ada wilayah prioritas tinggi terekam di database.
              </div>
            </div>
          </SectionCard>
        </div>
      </section>
    </div>
  </AuthenticatedLayout>
</template>
