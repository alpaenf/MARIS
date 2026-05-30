<template>
  <AuthenticatedLayout>
    <template #header>
      <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Simulasi</p>
        <h2 class="text-base font-semibold text-on-surface leading-tight">Blue Carbon Simulator</h2>
      </div>
    </template>
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-on-surface">Blue Carbon Simulator</h1>
          <p class="mt-1 text-sm text-on-surface-variant">Simulasikan dampak restorasi mangrove terhadap penyerapan karbon, pengurangan abrasi, dan estimasi nilai ekonomi.</p>
        </div>
        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
          Live Interactive
        </span>
      </div>

      <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <!-- Left Column: Sliders and Config -->
        <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-6">
          <h3 class="text-sm font-bold text-on-surface flex items-center gap-2">
            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            </svg>
            Konfigurasi Skenario Restorasi
          </h3>

          <!-- Slider 1: Luas Restorasi -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="text-xs font-semibold text-on-surface">Luas Area Restorasi</label>
              <span class="text-xs font-bold text-primary">{{ inputs.area }} ha</span>
            </div>
            <input
              v-model.number="inputs.area"
              type="range"
              min="1"
              max="1000"
              step="1"
              class="w-full h-2 bg-surface-container rounded-lg appearance-none cursor-pointer accent-primary"
            />
            <div class="flex justify-between text-[10px] text-outline">
              <span>1 ha</span>
              <span>1,000 ha</span>
            </div>
          </div>

          <!-- Slider 2: Kerapatan -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="text-xs font-semibold text-on-surface">Kerapatan Penanaman</label>
              <span class="text-xs font-bold text-primary">{{ inputs.density }} pohon/ha</span>
            </div>
            <input
              v-model.number="inputs.density"
              type="range"
              min="500"
              max="5000"
              step="100"
              class="w-full h-2 bg-surface-container rounded-lg appearance-none cursor-pointer accent-primary"
            />
            <div class="flex justify-between text-[10px] text-outline">
              <span>500 pohon/ha</span>
              <span>5,000 pohon/ha</span>
            </div>
          </div>

          <!-- Selection grid -->
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="text-xs font-semibold text-on-surface">Spesies Mangrove</label>
              <select
                v-model="inputs.species"
                class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-primary"
              >
                <option value="Rhizophora apiculata">Rhizophora apiculata (Bakau)</option>
                <option value="Avicennia marina">Avicennia marina (Api-api)</option>
                <option value="Sonneratia alba">Sonneratia alba (Pedada)</option>
                <option value="Bruguiera gymnorrhiza">Bruguiera gymnorrhiza (Tanjang)</option>
              </select>
            </div>
            <div>
              <label class="text-xs font-semibold text-on-surface">Tipe Restorasi</label>
              <select
                v-model="inputs.restorationType"
                class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-primary"
              >
                <option value="natural">Natural Regeneration (90% success)</option>
                <option value="assisted">Assisted Planting (105% success)</option>
                <option value="hybrid">Hybrid + Wave Break (115% success)</option>
              </select>
            </div>
          </div>

          <div class="rounded-xl border border-primary/20 bg-primary/5 p-4 text-[10px] text-on-surface-variant flex gap-2.5">
            <svg class="h-4 w-4 text-primary flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Perhitungan simulator menggunakan pendekatan **Tier-2 Blue Carbon IPCC Guidelines** dengan asumsi harga kredit karbon global sukarela sebesar **$12 / ton CO₂e** (Kurs Rp 15.000 / USD).</span>
          </div>
        </div>

        <!-- Right Column: Results & Graphs -->
        <div class="space-y-4">
          <!-- KPI: Carbon Absorption & Economic Valuation -->
          <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-6 shadow-card space-y-4">
            <div class="flex items-center justify-between border-b border-emerald-100 pb-3">
              <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Serapan Karbon & Nilai Valuasi</span>
                <h4 class="text-2xl font-black text-emerald-950 mt-1">{{ outputs.carbonAbsorption }} <span class="text-xs font-medium">tCO₂e / tahun</span></h4>
              </div>
              <div class="text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Estimasi Nilai Kredit</span>
                <h4 class="text-base font-bold text-emerald-900 mt-1">{{ outputs.carbonValue }}</h4>
              </div>
            </div>

            <!-- Dynamic progress bars -->
            <div class="space-y-3 text-xs">
              <!-- Bar 1: Abrasion Reduction -->
              <div class="space-y-1">
                <div class="flex justify-between font-semibold text-slate-800">
                  <span>Reduksi Risiko Abrasi Pesisir</span>
                  <span class="text-primary font-bold">{{ outputs.abrasionReduction }}%</span>
                </div>
                <div class="w-full bg-slate-200/60 rounded-full h-2">
                  <div class="bg-primary h-2 rounded-full transition-all duration-300" :style="{ width: outputs.abrasionReduction + '%' }"></div>
                </div>
              </div>

              <!-- Bar 2: Coastline Protected -->
              <div class="space-y-1">
                <div class="flex justify-between font-semibold text-slate-800">
                  <span>Garis Pantai yang Dilindungi</span>
                  <span class="text-tertiary font-bold">{{ outputs.protectedArea }} km</span>
                </div>
                <div class="w-full bg-slate-200/60 rounded-full h-2">
                  <div class="bg-tertiary h-2 rounded-full transition-all duration-300" :style="{ width: Math.min(100, (outputs.protectedArea / 50) * 100) + '%' }"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Impact Assessment & Guidance -->
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-3">
            <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Penilaian Dampak Ekologis (Impact Assessment)</span>
            <div class="flex items-center gap-3">
              <span class="rounded-full px-3 py-1 text-xs font-bold border"
                :class="outputs.impactAssessment === 'High Impact'
                  ? 'bg-emerald-100 text-emerald-800 border-emerald-200'
                  : (outputs.impactAssessment === 'Moderate' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-slate-100 text-slate-800 border-slate-200')">
                {{ outputs.impactAssessment }}
              </span>
              <p class="text-xs text-on-surface-variant leading-relaxed">
                {{ outputs.impactDesc }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed, reactive } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const inputs = reactive({
  area: 150,
  density: 2000,
  species: "Rhizophora apiculata",
  restorationType: "assisted",
});

const SPECIES_FACTORS = {
  "Rhizophora apiculata": 2.3,
  "Avicennia marina": 1.8,
  "Sonneratia alba": 2.5,
  "Bruguiera gymnorrhiza": 2.1,
};

const typeMultiplier = computed(() => {
  if (inputs.restorationType === "natural") return 0.90;
  if (inputs.restorationType === "hybrid") return 1.15;
  return 1.05;
});

const outputs = computed(() => {
  const speciesMultiplier = SPECIES_FACTORS[inputs.species] ?? 2.0;
  
  // Hitung perkiraan serapan karbon (tCO2e)
  const carbonAbsorption = Math.max(0, inputs.area * (inputs.density / 1500) * speciesMultiplier * typeMultiplier.value);
  
  // Estimasi reduksi abrasi (maksimal 95%)
  const abrasionReduction = Math.min(95, Math.round((inputs.area / 1000) * 40 + (inputs.density / 5000) * 35 * typeMultiplier.value));
  
  // Panjang pantai terlindungi (km)
  const protectedArea = +(inputs.area * 0.08 * typeMultiplier.value).toFixed(1);

  // Valuasi finansial
  const valueUsd = carbonAbsorption * 12; // $12 per ton
  const valueIdr = valueUsd * 15000;
  
  const carbonValue = valueIdr >= 1000000000 
    ? `Rp ${(valueIdr / 1000000000).toFixed(2)} Miliar` 
    : `Rp ${(valueIdr / 1000000).toFixed(0)} Juta`;

  let impactAssessment = "Low Impact";
  let impactDesc = "Skenario restorasi skala kecil. Manfaat mitigasi abrasi dan penyimpanan karbon masih terbatas.";

  if (carbonAbsorption > 1000 && abrasionReduction > 60) {
    impactAssessment = "High Impact";
    impactDesc = "Dampak restorasi sangat signifikan! Benteng pertahanan pesisir terbentuk kokoh dan memiliki nilai valuasi karbon biru tinggi.";
  } else if (carbonAbsorption >= 300) {
    impactAssessment = "Moderate";
    impactDesc = "Dampak menengah. Cukup stabil untuk mengurangi ancaman rob lokal dan menyerap karbon secara berkala.";
  }

  return {
    carbonAbsorption: carbonAbsorption.toLocaleString("id-ID", { maximumFractionDigits: 0 }),
    abrasionReduction,
    protectedArea,
    carbonValue,
    impactAssessment,
    impactDesc,
  };
});
</script>
