<template>
  <AuthenticatedLayout>
    <template #header>
      <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Hasil Analisis</p>
        <h2 class="text-base font-semibold text-on-surface leading-tight">Climate Risk Report · IPCC AR6</h2>
      </div>
    </template>

    <div class="space-y-6">

      <!-- ── HEADER ── -->
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h1 class="text-xl font-bold text-on-surface">{{ analysis.region_name }}</h1>
          <p class="mt-0.5 text-sm text-on-surface-variant">{{ analysis.province }} · Analisis #{{ analysis.id }}</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span class="rounded-full border border-primary/25 bg-primary/8 px-3 py-0.5 text-[10px] font-semibold text-primary">
              {{ analysis.dominant_species ?? 'Spesies tidak diketahui' }}
            </span>
            <span class="rounded-full border border-outline-variant bg-surface-container px-3 py-0.5 text-[10px] font-semibold text-on-surface-variant uppercase">
              Substrat: {{ analysis.soil_type ?? '-' }}
            </span>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a :href="`/reports/analysis/${analysis.id}/export`"
            class="rounded-xl border border-outline-variant px-4 py-2 text-xs font-medium text-on-surface-variant hover:bg-surface-container transition-all flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm leading-none">picture_as_pdf</span>
            Export PDF
          </a>
          <span class="rounded-full px-3 py-1.5 text-xs font-bold border"
            :class="priorityClass">
            Prioritas {{ analysis.restoration_priority ?? '-' }}
          </span>
        </div>
      </div>

      <!-- ── ROW 1: CORE KPI ── -->
      <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-4 shadow-card">
          <div class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Climate Risk</div>
          <div class="mt-2 text-3xl font-extrabold" :class="riskColor">{{ analysis.climate_risk_score ?? '-' }}</div>
          <div class="mt-1 text-[10px] text-on-surface-variant">IPCC AR6 · ∛(H×E×V)</div>
        </div>
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-4 shadow-card">
          <div class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">MCVI</div>
          <div class="mt-2 text-3xl font-extrabold text-primary">{{ analysis.result_payload?.mcvi ?? '-' }}</div>
          <div class="mt-1 text-[10px] text-on-surface-variant">MARIS Vulnerability Index</div>
        </div>
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-4 shadow-card">
          <div class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">MRPS</div>
          <div class="mt-2 text-3xl font-extrabold text-secondary">{{ analysis.result_payload?.mrps ?? '-' }}</div>
          <div class="mt-1 text-[10px] text-on-surface-variant">Restoration Priority Score</div>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 shadow-card">
          <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-700">Blue Carbon</div>
          <div class="mt-2 text-3xl font-extrabold text-emerald-700">{{ Number(analysis.carbon_potential).toFixed(0) }}</div>
          <div class="mt-1 text-[10px] text-emerald-600">ton C · Tier-2 Accounting</div>
        </div>
      </section>

      <!-- ── ROW 2: IPCC AR6 Pillars + Carbon Breakdown ── -->
      <section class="grid gap-4 lg:grid-cols-2">

        <!-- Radar Chart: IPCC AR6 Pillars -->
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
          <div class="mb-4">
            <h3 class="text-sm font-bold text-on-surface">IPCC AR6 Risk Pillars</h3>
            <p class="text-[10px] text-on-surface-variant">Hazard × Exposure × Vulnerability · Risk = ∛(H×E×V)</p>
          </div>
          <!-- SVG Radar Chart (canvas-free, pure SVG) -->
          <div class="flex justify-center">
            <svg :viewBox="`0 0 220 220`" class="h-52 w-52">
              <!-- Background rings -->
              <polygon v-for="r in [60,45,30,15]" :key="r"
                :points="hexPoints(110, 110, r)"
                fill="none" stroke="#e5e7eb" stroke-width="1"/>
              <!-- Labels -->
              <text x="110" y="18" text-anchor="middle" class="fill-on-surface-variant" font-size="9" font-weight="600">HAZARD</text>
              <text x="205" y="75" text-anchor="start" class="fill-on-surface-variant" font-size="9" font-weight="600">EXPOSURE</text>
              <text x="205" y="160" text-anchor="start" class="fill-on-surface-variant" font-size="9" font-weight="600">VULN.</text>
              <!-- Data polygon -->
              <polygon :points="radarPoints" fill="rgba(16,185,129,0.15)" stroke="#10b981" stroke-width="2"/>
              <!-- Data dots -->
              <circle v-for="(pt, i) in radarDots" :key="i" :cx="pt.x" :cy="pt.y" r="4" fill="#10b981"/>
              <!-- Axis lines -->
              <line x1="110" y1="110" :x2="radarDots[0]?.x" :y2="radarDots[0]?.y" stroke="#d1fae5" stroke-width="1"/>
              <line x1="110" y1="110" :x2="radarDots[1]?.x" :y2="radarDots[1]?.y" stroke="#d1fae5" stroke-width="1"/>
              <line x1="110" y1="110" :x2="radarDots[2]?.x" :y2="radarDots[2]?.y" stroke="#d1fae5" stroke-width="1"/>
            </svg>
          </div>
          <!-- Pillar Bars -->
          <div class="mt-2 space-y-2">
            <PillarBar label="H Hazard" :value="analysis.hazard_score" color="bg-orange-400"/>
            <PillarBar label="E Exposure" :value="analysis.exposure_score" color="bg-blue-400"/>
            <PillarBar label="V Vulnerability" :value="analysis.vulnerability_score" color="bg-rose-400"/>
          </div>
        </div>

        <!-- Carbon Breakdown -->
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-6 shadow-card">
          <div class="mb-4">
            <h3 class="text-sm font-bold text-on-surface">Blue Carbon Breakdown</h3>
            <p class="text-[10px] text-on-surface-variant">Tier-2 Accounting — AGB + BGB + SOC · Sumber: SNI 7724:2011</p>
          </div>
          <!-- Donut SVG -->
          <div class="flex justify-center">
            <svg viewBox="0 0 160 160" class="h-48 w-48">
              <circle cx="80" cy="80" r="55" fill="none" stroke="#d1fae5" stroke-width="28"/>
              <!-- AGB -->
              <circle cx="80" cy="80" r="55" fill="none" stroke="#10b981" stroke-width="28"
                :stroke-dasharray="`${agbArc} 345.6`" stroke-dashoffset="86.4"
                stroke-linecap="butt" transform="rotate(-90 80 80)"/>
              <!-- BGB -->
              <circle cx="80" cy="80" r="55" fill="none" stroke="#059669" stroke-width="28"
                :stroke-dasharray="`${bgbArc} 345.6`" :stroke-dashoffset="`${86.4 - agbArc}`"
                stroke-linecap="butt" transform="rotate(-90 80 80)"/>
              <!-- SOC -->
              <circle cx="80" cy="80" r="55" fill="none" stroke="#6ee7b7" stroke-width="28"
                :stroke-dasharray="`${socArc} 345.6`" :stroke-dashoffset="`${86.4 - agbArc - bgbArc}`"
                stroke-linecap="butt" transform="rotate(-90 80 80)"/>
              <!-- Center text -->
              <text x="80" y="76" text-anchor="middle" font-size="14" font-weight="800" fill="#047857">
                {{ Number(analysis.carbon_potential).toFixed(0) }}
              </text>
              <text x="80" y="90" text-anchor="middle" font-size="7" fill="#6ee7b7">ton C total</text>
            </svg>
          </div>
          <!-- Legend -->
          <div class="mt-2 space-y-2">
            <CarbonBar label="AGB (Above Ground)" :value="analysis.carbon_agb" :total="analysis.carbon_potential" color="bg-emerald-500"/>
            <CarbonBar label="BGB (Below Ground)" :value="analysis.carbon_bgb" :total="analysis.carbon_potential" color="bg-emerald-700"/>
            <CarbonBar label="SOC (Soil Organic C)" :value="analysis.carbon_soc" :total="analysis.carbon_potential" color="bg-emerald-300"/>
          </div>
          <div class="mt-4 rounded-xl border border-emerald-200 bg-white p-3 text-center">
            <div class="text-[10px] text-emerald-700 font-semibold">CO₂ Equivalent</div>
            <div class="text-lg font-extrabold text-emerald-900">
              {{ (Number(analysis.carbon_potential) * 3.67).toFixed(0) }} ton CO₂
            </div>
            <div class="text-[10px] text-emerald-600">Faktor konversi: C × 3.67 (IPCC 2006)</div>
          </div>
        </div>
      </section>

      <!-- ── ROW 3: DPSIR Framework ── -->
      <section v-if="analysis.dpsir_payload" class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
        <div class="mb-5">
          <h3 class="text-sm font-bold text-on-surface">DPSIR Framework</h3>
          <p class="text-[10px] text-on-surface-variant">Driving Forces → Pressure → State → Impact → Response (OECD/EEA Methodology)</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-5">
          <DpsirCard v-for="(section, key) in dpsirData" :key="key"
            :label="section.label"
            :items="section.indicators ?? section.actions ?? []"
            :color="dpsirColors[key]"/>
        </div>
      </section>

      <!-- ── ROW 4: AI Explanation ── -->
      <section class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-primary/20 bg-primary/5 p-6 shadow-card">
          <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-primary text-lg leading-none">neurology</span>
            <h3 class="text-sm font-bold text-on-surface">Penjelasan Ilmiah (XAI)</h3>
          </div>
          <p class="text-xs text-on-surface-variant leading-relaxed">
            {{ analysis.ai_explanation ?? 'Belum ada penjelasan ilmiah.' }}
          </p>
        </div>
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
          <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-secondary text-lg leading-none">eco</span>
            <h3 class="text-sm font-bold text-on-surface">Rekomendasi Restorasi</h3>
          </div>
          <p class="text-xs text-on-surface-variant leading-relaxed">
            {{ analysis.restoration_recommendation ?? 'Belum ada rekomendasi.' }}
          </p>
        </div>
      </section>

      <!-- ── ROW 5: Input Parameters Summary ── -->
      <section class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
        <h3 class="text-sm font-bold text-on-surface mb-4">Parameter Input</h3>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
          <InputParam label="Luas Area" :value="`${analysis.area_size} ha`"/>
          <InputParam label="Curah Hujan" :value="`${analysis.rainfall} mm/thn`"/>
          <InputParam label="Abrasi" :value="`${analysis.abrasion_level}/5`"/>
          <InputParam label="Banjir Rob" :value="`${analysis.flood_risk}/5`"/>
          <InputParam label="Mangrove Loss" :value="`${analysis.mangrove_loss}%`"/>
          <InputParam label="Kepadatan Pddk" :value="`${analysis.population_density} jiwa/km²`"/>
        </div>
      </section>

      <!-- Back link -->
      <div>
        <a :href="route('analysis.index')"
          class="inline-flex items-center gap-1.5 text-xs text-on-surface-variant hover:text-primary transition-colors">
          <span class="material-symbols-outlined text-sm leading-none">arrow_back</span>
          Kembali ke daftar analisis
        </a>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed, defineComponent, h } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  analysis: { type: Object, required: true },
});

// ── Color helpers ──────────────────────────────────────────────
const riskScore = computed(() => Number(props.analysis.climate_risk_score ?? 0));

const riskColor = computed(() => {
  if (riskScore.value >= 70) return 'text-error';
  if (riskScore.value >= 50) return 'text-orange-500';
  return 'text-emerald-600';
});

const priorityClass = computed(() => {
  const p = props.analysis.restoration_priority;
  if (p === 'Tinggi') return 'bg-error/10 text-error border-error/20';
  if (p === 'Sedang') return 'bg-orange-50 text-orange-700 border-orange-200';
  return 'bg-emerald-50 text-emerald-700 border-emerald-200';
});

// ── IPCC Radar Chart ──────────────────────────────────────────
const hexPoints = (cx, cy, r) => {
  const angles = [270, 30, 150]; // 3 axis: top, bottom-right, bottom-left
  return angles.map(a => {
    const rad = (a * Math.PI) / 180;
    return `${cx + r * Math.cos(rad)},${cy + r * Math.sin(rad)}`;
  }).join(' ');
};

const radarDots = computed(() => {
  const cx = 110, cy = 110, maxR = 60;
  const vals = [
    Number(props.analysis.hazard_score ?? 0) / 100,
    Number(props.analysis.exposure_score ?? 0) / 100,
    Number(props.analysis.vulnerability_score ?? 0) / 100,
  ];
  const angles = [270, 30, 150];
  return angles.map((a, i) => {
    const r = vals[i] * maxR;
    const rad = (a * Math.PI) / 180;
    return { x: cx + r * Math.cos(rad), y: cy + r * Math.sin(rad) };
  });
});

const radarPoints = computed(() =>
  radarDots.value.map(d => `${d.x},${d.y}`).join(' ')
);

// ── Blue Carbon Donut Arcs ────────────────────────────────────
const totalCarbon = computed(() => Number(props.analysis.carbon_potential ?? 1));
const agbArc = computed(() => (Number(props.analysis.carbon_agb ?? 0) / totalCarbon.value) * 345.6);
const bgbArc = computed(() => (Number(props.analysis.carbon_bgb ?? 0) / totalCarbon.value) * 345.6);
const socArc = computed(() => (Number(props.analysis.carbon_soc ?? 0) / totalCarbon.value) * 345.6);

// ── DPSIR ────────────────────────────────────────────────────
const dpsirData = computed(() => props.analysis.dpsir_payload ?? {});

const dpsirColors = {
  driving_forces: 'border-purple-200 bg-purple-50/50',
  pressures:      'border-orange-200 bg-orange-50/50',
  state:          'border-blue-200 bg-blue-50/50',
  impacts:        'border-rose-200 bg-rose-50/50',
  responses:      'border-emerald-200 bg-emerald-50/50',
};

// ── Sub-components ────────────────────────────────────────────
const PillarBar = defineComponent({
  props: ['label', 'value', 'color'],
  setup(p) {
    return () => h('div', { class: 'flex items-center gap-2' }, [
      h('span', { class: 'text-[10px] font-semibold text-on-surface-variant w-24 flex-shrink-0' }, p.label),
      h('div', { class: 'flex-1 h-2 rounded-full bg-surface-container' }, [
        h('div', {
          class: `h-2 rounded-full ${p.color} transition-all`,
          style: { width: `${p.value ?? 0}%` }
        })
      ]),
      h('span', { class: 'text-[10px] font-bold text-on-surface w-8 text-right' }, `${p.value ?? 0}`),
    ]);
  }
});

const CarbonBar = defineComponent({
  props: ['label', 'value', 'total', 'color'],
  setup(p) {
    const pct = computed(() => ((Number(p.value ?? 0) / Number(p.total ?? 1)) * 100).toFixed(0));
    return () => h('div', { class: 'flex items-center gap-2' }, [
      h('div', { class: `h-2.5 w-2.5 rounded-full flex-shrink-0 ${p.color}` }),
      h('span', { class: 'text-[10px] text-on-surface-variant flex-1 truncate' }, p.label),
      h('span', { class: 'text-[10px] font-bold text-on-surface w-20 text-right' },
        `${Number(p.value ?? 0).toFixed(0)} t (${pct.value}%)`),
    ]);
  }
});

const DpsirCard = defineComponent({
  props: ['label', 'items', 'color'],
  setup(p) {
    return () => h('div', { class: `rounded-xl border p-3 ${p.color}` }, [
      h('div', { class: 'text-[10px] font-extrabold uppercase tracking-wide text-on-surface mb-2' }, p.label),
      h('ul', { class: 'space-y-1' },
        (p.items ?? []).map(item =>
          h('li', { class: 'text-[10px] text-on-surface-variant leading-relaxed' }, `• ${item}`)
        )
      )
    ]);
  }
});

const InputParam = defineComponent({
  props: ['label', 'value'],
  setup(p) {
    return () => h('div', { class: 'rounded-xl border border-outline-variant bg-surface-container p-3' }, [
      h('div', { class: 'text-[10px] text-on-surface-variant font-semibold uppercase tracking-wide' }, p.label),
      h('div', { class: 'mt-1 text-xs font-bold text-on-surface' }, p.value),
    ]);
  }
});
</script>
