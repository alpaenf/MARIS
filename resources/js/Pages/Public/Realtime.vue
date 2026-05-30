<template>
  <PublicLayout>
    <div class="space-y-8">

      <!-- ══ Page Header (persis pola admin) ════════════════════════════ -->
      <div class="border-b border-outline-variant bg-surface px-6 py-5">
        <div class="mx-auto max-w-7xl flex flex-wrap items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <a href="/" class="inline-flex items-center gap-1 text-[10px] font-semibold text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-sm leading-none">arrow_back</span>
                Beranda
              </a>
              <span class="text-outline text-xs">/</span>
              <span class="text-[10px] font-semibold text-primary">Realtime</span>
            </div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-primary">Monitor Publik</p>
            <h1 class="text-2xl font-bold text-on-surface">Dashboard Realtime Pantura</h1>
          </div>
          <div class="flex items-center gap-3">
            <!-- Live indicator -->
            <div class="flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3 py-1.5">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-60"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
              </span>
              <span class="text-[10px] font-bold text-primary uppercase tracking-wide">Live</span>
            </div>
            <!-- Status -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest px-4 py-2 shadow-card">
              <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Update Terakhir</div>
              <div class="mt-0.5 text-xs font-semibold text-on-surface">{{ status?.time ? formatTime(status.time) : 'Belum ada data' }}</div>
              <span class="text-[9px] font-semibold"
                :class="status?.state === 'ok' ? 'text-emerald-600' : 'text-amber-600'">
                {{ status?.state ?? 'Idle' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- ══ Main Content ══════════════════════════════════════════════ -->
      <div class="mx-auto max-w-7xl px-6 space-y-8 pb-12">

        <!-- ── Filter Bar ─────────────────────────────────────────────── -->
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card">
          <div class="text-[10px] font-bold uppercase tracking-[0.25em] text-on-surface-variant mb-4">Filter Data</div>
          <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold text-on-surface whitespace-nowrap">Sumber Data</label>
              <select v-model="localFilters.source" @change="applyFilters"
                class="rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs text-on-surface focus:outline-none focus:border-primary">
                <option v-for="s in sources" :key="s" :value="s">{{ s }}</option>
              </select>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold text-on-surface whitespace-nowrap">Rentang Jam</label>
              <div class="flex rounded-xl border border-outline-variant overflow-hidden">
                <button v-for="h in hourOptions" :key="h"
                  @click="localFilters.hours = h; applyFilters()"
                  :class="[
                    'px-3 py-2 text-[11px] font-semibold transition-colors',
                    localFilters.hours === h
                      ? 'bg-primary text-on-primary'
                      : 'text-on-surface-variant hover:bg-surface-container bg-surface-container-low'
                  ]">{{ h }}j</button>
              </div>
            </div>
            <div class="ml-auto text-[11px] text-on-surface-variant font-medium">
              <span class="font-bold text-primary">{{ compareData.length }}</span> wilayah terpantau
            </div>
          </div>
        </div>

        <!-- ── 1. Kartu 4 Wilayah Side-by-Side ─────────────────────── -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-sm font-bold text-on-surface">Snapshot Terkini per Wilayah</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">Klik kartu untuk melihat tren historis</p>
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="compareData.length === 0"
            class="rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-lowest p-10 text-center">
            <span class="material-symbols-outlined text-3xl text-outline mb-2 block">sensors_off</span>
            <div class="text-sm font-semibold text-on-surface-variant">Belum ada snapshot</div>
            <p class="text-xs text-on-surface-variant mt-1">
              Jalankan: <code class="bg-surface-container px-1.5 py-0.5 rounded font-mono">php artisan bmkg:ingest-hourly</code>
            </p>
          </div>

          <!-- Cards grid -->
          <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <button
              v-for="item in compareData"
              :key="item.region"
              @click="selectRegion(item.region)"
              class="group rounded-2xl border-2 p-5 text-left transition-all duration-200 shadow-card w-full"
              :class="localFilters.region === item.region
                ? 'border-primary bg-primary/5'
                : 'border-outline-variant bg-surface-container-lowest hover:border-primary/30 hover:shadow-md'"
            >
              <!-- Header -->
              <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0 pr-2">
                  <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Jawa Tengah</div>
                  <div class="mt-0.5 text-sm font-bold text-on-surface truncate">{{ item.region }}</div>
                </div>
                <span class="flex-shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold border"
                  :class="riskBadgeClass(item.hazard)">
                  {{ riskLabel(item.hazard) }}
                </span>
              </div>

              <!-- Hazard score big -->
              <div class="mb-3">
                <div class="text-[9px] font-bold uppercase tracking-[0.2em] text-on-surface-variant mb-1">Hazard Score</div>
                <div class="flex items-end gap-1">
                  <span class="text-3xl font-black leading-none" :class="hazardColorClass(item.hazard)">
                    {{ formatVal(item.hazard) }}
                  </span>
                  <span class="text-[11px] text-on-surface-variant mb-0.5">/100</span>
                </div>
                <div class="mt-2 h-1.5 rounded-full bg-surface-container overflow-hidden">
                  <div class="h-full rounded-full transition-all duration-700"
                    :class="hazardBarClass(item.hazard)"
                    :style="{ width: (item.hazard ?? 0) + '%' }"></div>
                </div>
              </div>

              <!-- Sub indices -->
              <div class="grid grid-cols-3 gap-1.5">
                <div class="rounded-xl bg-surface-container p-2 text-center">
                  <div class="text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">MCVI</div>
                  <div class="mt-0.5 text-xs font-bold text-on-surface">{{ formatVal(item.mcvi) }}</div>
                </div>
                <div class="rounded-xl bg-surface-container p-2 text-center">
                  <div class="text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">MRPS</div>
                  <div class="mt-0.5 text-xs font-bold text-on-surface">{{ formatVal(item.mrps) }}</div>
                </div>
                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-2 text-center">
                  <div class="text-[9px] font-bold uppercase tracking-wide text-emerald-600">C ton</div>
                  <div class="mt-0.5 text-xs font-bold text-emerald-700">{{ formatVal(item.carbon) }}</div>
                </div>
              </div>

              <!-- Timestamp -->
              <div class="mt-3 text-[10px] text-on-surface-variant">
                {{ item.time ? formatTime(item.time) : 'Data belum tersedia' }}
              </div>

              <!-- Selected marker -->
              <div v-if="localFilters.region === item.region"
                class="mt-3 flex items-center gap-1 text-[10px] font-semibold text-primary">
                <span class="material-symbols-outlined text-sm leading-none">show_chart</span>
                Tren dipilih
              </div>
            </button>
          </div>
        </div>

        <!-- ── 2. Tabel Ranking ─────────────────────────────────────── -->
        <div v-if="compareData.length > 0" class="rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-card">
          <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary text-xl leading-none">leaderboard</span>
            <div>
              <div class="text-sm font-bold text-on-surface">Ranking Prioritas Restorasi</div>
              <div class="text-[11px] text-on-surface-variant">Urut Hazard Score tertinggi → urgensi intervensi mangrove</div>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
              <thead class="bg-surface-container font-bold text-on-surface-variant uppercase tracking-wider">
                <tr>
                  <th class="px-5 py-3">Rank</th>
                  <th class="px-5 py-3">Wilayah</th>
                  <th class="px-5 py-3">Hazard</th>
                  <th class="px-5 py-3">MCVI</th>
                  <th class="px-5 py-3">MRPS</th>
                  <th class="px-5 py-3">Carbon (ton)</th>
                  <th class="px-5 py-3">Prioritas</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant">
                <tr v-for="(item, idx) in rankedData" :key="item.region"
                  class="hover:bg-surface-container-low transition-colors cursor-pointer"
                  :class="localFilters.region === item.region ? 'bg-primary/5' : ''"
                  @click="selectRegion(item.region)">
                  <td class="px-5 py-3">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black"
                      :class="idx === 0 ? 'bg-error/10 text-error' : idx === 1 ? 'bg-amber-100 text-amber-700' : idx === 2 ? 'bg-yellow-100 text-yellow-700' : 'bg-surface-container text-on-surface-variant'">
                      {{ idx + 1 }}
                    </span>
                  </td>
                  <td class="px-5 py-3 font-semibold text-on-surface">{{ item.region }}</td>
                  <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                      <span class="font-bold" :class="hazardColorClass(item.hazard)">{{ formatVal(item.hazard) }}</span>
                      <div class="w-16 h-1.5 rounded-full bg-surface-container overflow-hidden">
                        <div class="h-full rounded-full" :class="hazardBarClass(item.hazard)"
                          :style="{ width: (item.hazard ?? 0) + '%' }"></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-3 text-on-surface-variant font-medium">{{ formatVal(item.mcvi) }}</td>
                  <td class="px-5 py-3 text-on-surface-variant font-medium">{{ formatVal(item.mrps) }}</td>
                  <td class="px-5 py-3 font-medium text-emerald-600">{{ formatVal(item.carbon) }}</td>
                  <td class="px-5 py-3">
                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-semibold border"
                      :class="riskBadgeClass(item.hazard)">
                      {{ riskLabel(item.hazard) }}
                    </span>
                  </td>
                </tr>
                <tr v-if="rankedData.every(d => d.hazard === null)">
                  <td colspan="7" class="px-5 py-6 text-center text-[11px] text-on-surface-variant">
                    Semua wilayah belum memiliki data. Jalankan ingest terlebih dahulu.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ── 3. Perbandingan Visual Indeks (Bar) ─────────────────── -->
        <div v-if="compareData.length > 0" class="grid gap-4 md:grid-cols-3">
          <div v-for="metric in comparisonMetrics" :key="metric.key"
            class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card">
            <div class="text-[10px] font-bold uppercase tracking-[0.25em] text-on-surface-variant mb-0.5">{{ metric.label }}</div>
            <div class="text-[11px] text-on-surface-variant mb-4">{{ metric.desc }}</div>
            <div class="space-y-3">
              <div v-for="item in compareData" :key="item.region">
                <div class="flex items-center justify-between text-[11px] mb-1">
                  <span :class="localFilters.region === item.region ? 'font-bold text-primary' : 'text-on-surface-variant'">
                    {{ item.region }}
                  </span>
                  <span class="font-bold" :class="localFilters.region === item.region ? 'text-primary' : 'text-on-surface'">
                    {{ formatVal(item[metric.key]) }}
                  </span>
                </div>
                <div class="h-2 rounded-full bg-surface-container overflow-hidden">
                  <div class="h-full rounded-full transition-all duration-700"
                    :class="metric.barClass"
                    :style="{ width: normalizeValue(item[metric.key], metric.key) + '%' }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── 4. Tren Historis Wilayah Terpilih ──────────────────── -->
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-card">
          <div class="flex flex-wrap items-center justify-between gap-4 px-5 pt-5 pb-4 border-b border-outline-variant">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-xl leading-none">show_chart</span>
              <div>
                <div class="text-sm font-bold text-on-surface">
                  Tren Historis — {{ localFilters.region || '(pilih wilayah di atas)' }}
                </div>
                <div class="text-[11px] text-on-surface-variant">{{ localFilters.hours }} jam terakhir</div>
              </div>
            </div>
            <span class="text-[10px] text-on-surface-variant">{{ series.length }} titik data</span>
          </div>

          <div class="p-5 space-y-5">
            <!-- Chart 2x2 grid -->
            <div class="grid gap-4 lg:grid-cols-2">
              <div v-for="chart in trendCharts" :key="chart.key"
                class="rounded-2xl border border-outline-variant bg-surface p-4">
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="inline-block h-2.5 w-2.5 rounded-full" :class="chart.dotClass"></span>
                    <span class="text-xs font-bold text-on-surface">{{ chart.label }}</span>
                  </div>
                  <div class="text-right">
                    <div class="text-[9px] text-on-surface-variant uppercase tracking-wide">Terbaru</div>
                    <div class="text-sm font-black" :class="chart.textClass">
                      {{ formatVal(series.length ? series[series.length - 1][chart.key] : null) }}
                    </div>
                  </div>
                </div>
                <svg class="w-full" height="72" viewBox="0 0 320 72" preserveAspectRatio="none">
                  <defs>
                    <linearGradient :id="`g-${chart.key}`" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" :stop-color="chart.gradColor" stop-opacity="0.25"/>
                      <stop offset="100%" :stop-color="chart.gradColor" stop-opacity="0"/>
                    </linearGradient>
                  </defs>
                  <path v-if="chart.values.length > 1"
                    :d="buildAreaPath(chart.values)"
                    :fill="`url(#g-${chart.key})`"/>
                  <path v-if="chart.values.length > 1"
                    :d="buildLinePath(chart.values)"
                    fill="none"
                    :stroke="chart.gradColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>
                  <text v-if="chart.values.length === 0"
                    x="160" y="40" text-anchor="middle"
                    fill="#94a3b8" font-size="11">Belum ada data historis</text>
                </svg>
              </div>
            </div>

            <!-- Log table -->
            <div class="overflow-hidden rounded-2xl border border-outline-variant">
              <table class="min-w-full text-left text-xs">
                <thead class="bg-surface-container font-bold text-on-surface-variant uppercase tracking-wider">
                  <tr>
                    <th class="px-4 py-2.5">Waktu</th>
                    <th class="px-4 py-2.5">Hazard</th>
                    <th class="px-4 py-2.5">MCVI</th>
                    <th class="px-4 py-2.5">MRPS</th>
                    <th class="px-4 py-2.5">Carbon (ton)</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                  <tr v-for="row in series.slice().reverse().slice(0, 6)" :key="row.time"
                    class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-2.5 text-on-surface-variant font-mono text-[11px]">{{ formatTime(row.time) }}</td>
                    <td class="px-4 py-2.5 font-semibold" :class="hazardColorClass(row.hazard)">{{ formatVal(row.hazard) }}</td>
                    <td class="px-4 py-2.5 text-on-surface-variant">{{ formatVal(row.mcvi) }}</td>
                    <td class="px-4 py-2.5 text-on-surface-variant">{{ formatVal(row.mrps) }}</td>
                    <td class="px-4 py-2.5 text-emerald-600">{{ formatVal(row.carbon) }}</td>
                  </tr>
                  <tr v-if="series.length === 0">
                    <td colspan="5" class="px-4 py-6 text-center text-[11px] text-on-surface-variant">
                      Tidak ada data dalam {{ localFilters.hours }} jam terakhir untuk wilayah ini.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div><!-- /max-w-7xl -->
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  filters:     { type: Object, default: () => ({ source: '', region: '', hours: 24 }) },
  sources:     { type: Array,  default: () => [] },
  regions:     { type: Array,  default: () => [] },
  compareData: { type: Array,  default: () => [] },
  series:      { type: Array,  default: () => [] },
  status:      { type: Object, default: null },
});

const localFilters = reactive({
  source: props.filters.source,
  region: props.filters.region,
  hours:  props.filters.hours,
});

const hourOptions = [6, 12, 24, 48, 72, 168];

const applyFilters = () => {
  router.get('/realtime', { ...localFilters }, { preserveState: true, preserveScroll: true });
};

const selectRegion = (name) => {
  localFilters.region = name;
  applyFilters();
};

// Ranking urut hazard DESC
const rankedData = computed(() =>
  [...props.compareData].sort((a, b) => {
    if (a.hazard === null && b.hazard === null) return 0;
    if (a.hazard === null) return 1;
    if (b.hazard === null) return -1;
    return b.hazard - a.hazard;
  })
);

// Bar chart perbandingan indeks
const comparisonMetrics = [
  {
    key: 'hazard',
    label: 'Hazard Score',
    desc: 'Tekanan abrasi, banjir rob, curah hujan',
    barClass: 'bg-error/60',
  },
  {
    key: 'mcvi',
    label: 'MCVI',
    desc: 'Mangrove Coastal Vulnerability Index',
    barClass: 'bg-amber-400',
  },
  {
    key: 'mrps',
    label: 'MRPS',
    desc: 'Mangrove Restoration Priority Score',
    barClass: 'bg-primary/70',
  },
];

// Normalize relative ke max di set
const normalizeValue = (val, key) => {
  if (val === null || val === undefined) return 0;
  const values = props.compareData.map(d => d[key]).filter(v => v !== null);
  const max = Math.max(...values, 1);
  return (val / max) * 100;
};

// Config tren chart
const trendCharts = computed(() => [
  {
    key: 'hazard',
    label: 'Hazard Score',
    values: props.series.map(r => r.hazard).filter(v => v !== null),
    gradColor: '#dc2626',
    dotClass: 'bg-red-500',
    textClass: 'text-red-600',
  },
  {
    key: 'mcvi',
    label: 'MCVI',
    values: props.series.map(r => r.mcvi).filter(v => v !== null),
    gradColor: '#d97706',
    dotClass: 'bg-amber-500',
    textClass: 'text-amber-600',
  },
  {
    key: 'mrps',
    label: 'MRPS',
    values: props.series.map(r => r.mrps).filter(v => v !== null),
    gradColor: '#0d9488',
    dotClass: 'bg-teal-500',
    textClass: 'text-teal-600',
  },
  {
    key: 'carbon',
    label: 'Carbon (ton)',
    values: props.series.map(r => r.carbon).filter(v => v !== null),
    gradColor: '#059669',
    dotClass: 'bg-emerald-500',
    textClass: 'text-emerald-600',
  },
]);

// SVG path builders
const buildLinePath = (values) => {
  if (values.length < 2) return '';
  const W = 320, H = 72;
  const max = Math.max(...values);
  const min = Math.min(...values);
  const span = max - min || 1;
  return values.map((v, i) => {
    const x = (i / (values.length - 1)) * W;
    const y = H - ((v - min) / span) * (H - 10) - 5;
    return `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`;
  }).join(' ');
};

const buildAreaPath = (values) => {
  if (values.length < 2) return '';
  const W = 320, H = 72;
  const max = Math.max(...values);
  const min = Math.min(...values);
  const span = max - min || 1;
  const pts = values.map((v, i) => {
    const x = (i / (values.length - 1)) * W;
    const y = H - ((v - min) / span) * (H - 10) - 5;
    return `${x.toFixed(1)},${y.toFixed(1)}`;
  });
  return `M0,${H} L${pts.join(' L')} L${W},${H} Z`;
};

// Helpers
const formatVal = (v) => {
  if (v === null || v === undefined) return '–';
  return typeof v === 'number' ? v.toFixed(1) : v;
};

const formatTime = (t) => {
  if (!t) return '–';
  return new Date(t).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit'
  });
};

const riskLabel = (hazard) => {
  if (hazard === null || hazard === undefined) return 'N/A';
  if (hazard >= 80) return 'Kritis';
  if (hazard >= 65) return 'Tinggi';
  if (hazard >= 45) return 'Sedang';
  return 'Rendah';
};

const riskBadgeClass = (hazard) => {
  if (hazard === null || hazard === undefined) return 'bg-surface-container text-on-surface-variant border-outline-variant';
  if (hazard >= 80) return 'bg-error/10 text-error border-error/20';
  if (hazard >= 65) return 'bg-amber-50 text-amber-700 border-amber-200';
  if (hazard >= 45) return 'bg-yellow-50 text-yellow-700 border-yellow-200';
  return 'bg-emerald-50 text-emerald-700 border-emerald-200';
};

const hazardColorClass = (hazard) => {
  if (hazard === null || hazard === undefined) return 'text-on-surface-variant';
  if (hazard >= 80) return 'text-error';
  if (hazard >= 65) return 'text-amber-600';
  if (hazard >= 45) return 'text-yellow-600';
  return 'text-emerald-600';
};

const hazardBarClass = (hazard) => {
  if (hazard === null || hazard === undefined) return 'bg-outline-variant';
  if (hazard >= 80) return 'bg-error/70';
  if (hazard >= 65) return 'bg-amber-400';
  if (hazard >= 45) return 'bg-yellow-400';
  return 'bg-emerald-400';
};
</script>
