<template>
  <PublicLayout>
    <div class="space-y-8">

      <!-- ══ Page Header ════════════════════════════ -->
      <div class="border-b border-outline-variant bg-surface px-4 py-5 sm:px-6">
        <div class="mx-auto max-w-7xl flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <a href="/" class="inline-flex items-center gap-1 text-[10px] font-semibold text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-sm leading-none">arrow_back</span>
                Beranda
              </a>
              <span class="text-outline text-xs">/</span>
              <span class="text-[10px] font-semibold text-primary">Bandingkan</span>
            </div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-primary">Analisis Spasial Komparatif</p>
            <h1 class="text-2xl font-bold text-on-surface">Bandingkan Wilayah Pesisir</h1>
          </div>
          <div class="flex w-full items-center gap-3 sm:w-auto">
            <span class="text-[11px] text-on-surface-variant font-medium sm:text-right">
              Bandingkan metrik risiko iklim, kerentanan pesisir (MCVI), ketahanan ekosistem (MCRI), dan valuasi karbon biru (BCEVI).
            </span>
          </div>
        </div>
      </div>

      <!-- ══ Main Content ══════════════════════════════════════════════ -->
      <div class="mx-auto max-w-7xl px-4 sm:px-6 space-y-8 pb-12">

        <!-- Region Selection Toggles -->
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
          <h2 class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">checklist</span>
            Pilih Wilayah untuk Dibandingkan
          </h2>
          <div class="flex flex-wrap gap-2">
            <button v-for="analysis in analyses" :key="analysis.id"
              @click="toggleRegion(analysis.region_name)"
              class="rounded-full border px-4 py-2 text-xs font-bold transition-all duration-200 flex items-center gap-1.5"
              :class="selectedRegions.includes(analysis.region_name)
                ? 'bg-primary text-on-primary border-primary shadow-btn'
                : 'bg-surface border-outline-variant text-on-surface hover:bg-surface-container-low'">
              <span class="material-symbols-outlined text-xs leading-none">
                {{ selectedRegions.includes(analysis.region_name) ? 'check_circle' : 'circle' }}
              </span>
              {{ analysis.region_name }}
            </button>
          </div>
          <div class="mt-3 text-[10px] text-on-surface-variant">
            Pilih minimal 2 wilayah untuk membandingkan secara komparatif.
          </div>
        </div>

        <!-- If insufficient selection -->
        <div v-if="selectedRegions.length < 2" 
          class="rounded-2xl border-2 border-dashed border-outline-variant bg-surface-container-lowest p-12 text-center">
          <span class="material-symbols-outlined text-4xl text-outline mb-3">compare</span>
          <h3 class="text-sm font-bold text-on-surface mb-1">Silakan Pilih Wilayah Pesisir</h3>
          <p class="text-xs text-on-surface-variant max-w-md mx-auto">
            Gunakan panel di atas untuk memilih dua atau lebih wilayah pesisir Jawa Tengah untuk menampilkan visualisasi grafik perbandingan komparatif multi-parameter.
          </p>
        </div>

        <div v-else class="grid gap-6 lg:grid-cols-3">

          <!-- 1. Interactive SVG Polar Radar Chart -->
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card lg:col-span-1 flex flex-col justify-between">
            <div>
              <h2 class="text-sm font-bold text-on-surface mb-1 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-primary">radar</span>
                Grafik Polar Komparatif
              </h2>
              <p class="text-[11px] text-on-surface-variant mb-6">
                Menyajikan peta kekuatan relatif 6 parameter penentu risiko & ketahanan pesisir.
              </p>

              <!-- Custom SVG Polar Chart -->
              <div class="relative flex justify-center py-4 bg-surface rounded-2xl border border-outline-variant">
                <svg width="220" height="220" viewBox="0 0 220 220" class="overflow-visible">
                  <!-- Concentric circles for guide -->
                  <circle cx="110" cy="110" r="30" fill="none" stroke="currentColor" stroke-width="0.5" class="text-outline/20" stroke-dasharray="2,2"/>
                  <circle cx="110" cy="110" r="60" fill="none" stroke="currentColor" stroke-width="0.5" class="text-outline/25" stroke-dasharray="2,2"/>
                  <circle cx="110" cy="110" r="90" fill="none" stroke="currentColor" stroke-width="0.5" class="text-outline/35"/>
                  
                  <!-- Axes -->
                  <line v-for="(axis, idx) in radarAxes" :key="idx"
                    x1="110" y1="110"
                    :x2="110 + 90 * Math.cos(axis.angle)"
                    :y2="110 + 90 * Math.sin(axis.angle)"
                    stroke="currentColor" stroke-width="0.5" class="text-outline/30"/>

                  <!-- Axis Labels -->
                  <text v-for="(axis, idx) in radarAxes" :key="`lbl-${idx}`"
                    :x="110 + 105 * Math.cos(axis.angle)"
                    :y="110 + 105 * Math.sin(axis.angle) + 4"
                    font-size="8" font-weight="bold" fill="currentColor" class="text-on-surface-variant text-center"
                    text-anchor="middle">
                    {{ axis.label }}
                  </text>

                  <!-- Polygons for selected regions -->
                  <polygon v-for="(regionData, rIdx) in selectedRegionRadarData" :key="`poly-${rIdx}`"
                    :points="regionData.points"
                    fill="none"
                    :stroke="getRegionColor(rIdx)"
                    stroke-width="2"
                    stroke-linejoin="round"
                    class="opacity-95"/>
                </svg>
              </div>

              <!-- Color Legends -->
              <div class="mt-6 space-y-2">
                <div v-for="(regionName, idx) in selectedRegions" :key="idx" class="flex items-center gap-2">
                  <span class="inline-block h-3 w-3 rounded-full" :style="{ backgroundColor: getRegionColor(idx) }"></span>
                  <span class="text-xs font-bold text-on-surface">{{ regionName }}</span>
                </div>
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-outline-variant text-[9px] text-on-surface-variant italic">
              Metrik dinormalisasi ke skala 0-100 untuk perbandingan visual seimbang.
            </div>
          </div>

          <!-- 2. Perbandingan Side-by-Side Matrix Table -->
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card lg:col-span-2 space-y-4">
            <h2 class="text-sm font-bold text-on-surface flex items-center gap-1.5">
              <span class="material-symbols-outlined text-primary">tune</span>
              Matriks Kuantitatif Multi-Parameter
            </h2>

            <div class="overflow-x-auto rounded-2xl border border-outline-variant">
              <table class="min-w-[680px] text-left text-xs">
                <thead class="bg-surface-container font-bold text-on-surface-variant uppercase tracking-wider">
                  <tr>
                    <th class="px-4 py-3">Metrik Indikator</th>
                    <th v-for="(regionName, idx) in selectedRegions" :key="idx" 
                      class="px-4 py-3 border-l border-outline-variant whitespace-normal">
                      <div class="flex items-center gap-1.5">
                        <span class="inline-block h-2 w-2 rounded-full" :style="{ backgroundColor: getRegionColor(idx) }"></span>
                        <span class="break-words">{{ regionName }}</span>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                  <!-- Hazard -->
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-on-surface flex flex-col">
                      <span>Hazard Score</span>
                      <span class="text-[9px] text-on-surface-variant font-normal">Tekanan abrasi & risiko banjir</span>
                    </td>
                    <td v-for="data in selectedRegionFullData" :key="data.region_name" class="px-4 py-3 border-l border-outline-variant font-bold">
                      {{ formatFixed(data.hazard_score, 1) }} / 100
                    </td>
                  </tr>
                  <!-- MCVI -->
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-on-surface flex flex-col">
                      <span>Vulnerability (MCVI)</span>
                      <span class="text-[9px] text-on-surface-variant font-normal">Kerentanan pesisir</span>
                    </td>
                    <td v-for="data in selectedRegionFullData" :key="data.region_name" class="px-4 py-3 border-l border-outline-variant font-bold text-amber-600">
                      {{ formatFixed(data.mcvi, 1) }}
                    </td>
                  </tr>
                  <!-- MRPS -->
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-on-surface flex flex-col">
                      <span>Restoration Priority (MRPS)</span>
                      <span class="text-[9px] text-on-surface-variant font-normal">Skor prioritas restorasi</span>
                    </td>
                    <td v-for="data in selectedRegionFullData" :key="data.region_name" class="px-4 py-3 border-l border-outline-variant font-bold text-primary">
                      {{ formatFixed(data.mrps, 1) }}
                    </td>
                  </tr>
                  <!-- MCRI -->
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-on-surface flex flex-col">
                      <span>Ecosystem Resilience (MCRI)</span>
                      <span class="text-[9px] text-on-surface-variant font-normal">Ketahanan mangrove pesisir</span>
                    </td>
                    <td v-for="data in selectedRegionFullData" :key="data.region_name" class="px-4 py-3 border-l border-outline-variant font-bold text-emerald-600">
                      {{ formatFixed(data.result_payload?.mcri, 2, '0.45') }}
                    </td>
                  </tr>
                  <!-- Blue Carbon Stock -->
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-on-surface flex flex-col">
                      <span>Potensi Karbon Biru</span>
                      <span class="text-[9px] text-on-surface-variant font-normal">Total simpanan karbon</span>
                    </td>
                    <td v-for="data in selectedRegionFullData" :key="data.region_name" class="px-4 py-3 border-l border-outline-variant font-bold text-emerald-700">
                      {{ formatVal(data.carbon_potential) }} ton C
                    </td>
                  </tr>
                  <!-- BCEVI (Economic Value) -->
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-on-surface flex flex-col">
                      <span>Valuasi Ekonomi (BCEVI)</span>
                      <span class="text-[9px] text-on-surface-variant font-normal">Blue Carbon Economic Valuation</span>
                    </td>
                    <td v-for="data in selectedRegionFullData" :key="data.region_name" class="px-4 py-3 border-l border-outline-variant font-bold text-teal-600">
                      Rp {{ formatCurrency(data.result_payload?.bcevi?.total_economic_value_idr || data.carbon_potential * 3.67 * 12 * 15000) }}
                    </td>
                  </tr>
                  <!-- Species Dominant -->
                  <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-4 py-3 font-semibold text-on-surface flex flex-col">
                      <span>Spesies Dominan</span>
                      <span class="text-[9px] text-on-surface-variant font-normal">Rehabilitasi optimal</span>
                    </td>
                    <td v-for="data in selectedRegionFullData" :key="data.region_name" class="px-4 py-3 border-l border-outline-variant italic break-words">
                      {{ data.dominant_species }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Scientific Analysis Narrative block -->
            <div class="bg-primary/5 rounded-xl border border-primary/20 p-4">
              <h3 class="text-xs font-bold text-primary flex items-center gap-1.5 mb-1.5">
                <span class="material-symbols-outlined text-sm">lightbulb</span>
                Rekomendasi Strategis Komparatif
              </h3>
              <p class="text-[11px] text-on-surface-variant leading-relaxed">
                Berdasarkan analisis komparatif, wilayah <strong class="text-on-surface">{{ highestPriorityRegion }}</strong> memiliki nilai prioritas restorasi tertinggi. Program rehabilitasi direkomendasikan difokuskan menggunakan spesies <strong class="italic text-on-surface">{{ highestPrioritySpecies }}</strong> dengan peningkatan kapasitas ketahanan melalui penanaman sabuk hijau mangrove selebar 50-100 meter.
              </p>
            </div>
          </div>

        </div>

      </div><!-- /max-w-7xl -->
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  analyses: { type: Array, default: () => [] }
});

const selectedRegions = ref([]);

// Predefined modern harmonic color palette for compared regions
const regionColors = ['#0d9488', '#2563eb', '#d97706', '#dc2626', '#7c3aed'];

const getRegionColor = (idx) => regionColors[idx % regionColors.length];

// Toggle region in selection list
const toggleRegion = (name) => {
  if (selectedRegions.value.includes(name)) {
    selectedRegions.value = selectedRegions.value.filter(n => n !== name);
  } else {
    if (selectedRegions.value.length < 5) {
      selectedRegions.value.push(name);
    }
  }
};

// Initialize with first 2 regions if available
if (props.analyses.length >= 2) {
  selectedRegions.value = [props.analyses[0].region_name, props.analyses[1].region_name];
}

// Full data of selected regions
const selectedRegionFullData = computed(() => {
  return props.analyses.filter(a => selectedRegions.value.includes(a.region_name));
});

// Highest restoration priority calculation
const highestPriorityRegion = computed(() => {
  if (selectedRegionFullData.value.length === 0) return '–';
  const sorted = [...selectedRegionFullData.value].sort((a, b) => b.mrps - a.mrps);
  return sorted[0]?.region_name || '–';
});

const highestPrioritySpecies = computed(() => {
  if (selectedRegionFullData.value.length === 0) return '–';
  const sorted = [...selectedRegionFullData.value].sort((a, b) => b.mrps - a.mrps);
  return sorted[0]?.dominant_species || '–';
});

// Radar axes and angle mappings
const radarAxes = [
  { label: 'Hazard', key: 'hazard_score',  angle: (0 * Math.PI) / 3 },
  { label: 'MCVI',   key: 'mcvi',          angle: (1 * Math.PI) / 3 },
  { label: 'MRPS',   key: 'mrps',          angle: (2 * Math.PI) / 3 },
  { label: 'Resil',  key: 'mcri',          angle: (3 * Math.PI) / 3 },
  { label: 'Carbon', key: 'carbon',        angle: (4 * Math.PI) / 3 },
  { label: 'BCEVI',  key: 'bcevi',         angle: (5 * Math.PI) / 3 },
];

// Computed coordinates for radar polygons
const selectedRegionRadarData = computed(() => {
  const W = 220, H = 220;
  const cx = W / 2, cy = H / 2;
  const maxRadius = 90;

  return selectedRegionFullData.value.map(data => {
    // Normalization mapping for each of the 6 dimensions
    const points = radarAxes.map(axis => {
      let val = 0;
      if (axis.key === 'hazard_score') {
        val = data.hazard_score;
      } else if (axis.key === 'mcvi') {
        val = data.mcvi * 10; // MCVI is 0-10, scale to 100
      } else if (axis.key === 'mrps') {
        val = data.mrps; // MRPS is 0-100
      } else if (axis.key === 'mcri') {
        const mcri = data.result_payload?.mcri || 0.45;
        val = mcri * 100; // MCRI is 0-1, scale to 100
      } else if (axis.key === 'carbon') {
        // Max carbon relative in selected dataset
        const maxCarbon = Math.max(...props.analyses.map(a => a.carbon_potential), 1000);
        val = (data.carbon_potential / maxCarbon) * 100;
      } else if (axis.key === 'bcevi') {
        const bcevi = data.result_payload?.bcevi?.total_economic_value_idr || 0;
        const maxBcevi = Math.max(...props.analyses.map(a => a.result_payload?.bcevi?.total_economic_value_idr || 100000000), 100000000);
        val = maxBcevi > 0 ? (bcevi / maxBcevi) * 100 : 0;
      }

      val = Math.max(0, Math.min(100, val));
      const radius = (val / 100) * maxRadius;
      const x = cx + radius * Math.cos(axis.angle);
      const y = cy + radius * Math.sin(axis.angle);
      return `${x.toFixed(1)},${y.toFixed(1)}`;
    }).join(' ');

    return { region_name: data.region_name, points };
  });
});

// Helpers
const formatVal = (v) => {
  if (v === null || v === undefined) return '–';
  return typeof v === 'number' ? v.toLocaleString('id-ID', { maximumFractionDigits: 1 }) : v;
};

const formatCurrency = (val) => {
  if (!val) return '0';
  const num = typeof val === 'string' ? parseFloat(val) : val;
  if (num >= 1e9) return (num / 1e9).toFixed(2) + ' Miliar';
  if (num >= 1e6) return (num / 1e6).toFixed(2) + ' Juta';
  return num.toLocaleString('id-ID');
};

const formatFixed = (val, digits, fallback = '–') => {
  const num = typeof val === 'string' ? parseFloat(val) : val;
  if (Number.isFinite(num)) {
    return num.toFixed(digits);
  }
  return fallback;
};
</script>
