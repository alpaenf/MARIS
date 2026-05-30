<template>
  <AuthenticatedLayout>
    <template #header>
      <div>
        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Analisis</p>
        <h2 class="text-base font-semibold text-on-surface leading-tight">AI Restoration Center</h2>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Page Header -->
      <div class="flex items-start justify-between gap-4">
        <div>
          <h1 class="text-xl font-bold text-on-surface">AI Restoration Center</h1>
          <p class="mt-1 text-sm text-on-surface-variant">
            Sistem kalkulasi berbasis <strong>IPCC AR6</strong> · Blue Carbon Tier-2 · DPSIR Framework
          </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <span class="rounded-full border px-3 py-1 text-[10px] font-semibold uppercase tracking-wide flex items-center gap-1.5"
            :class="aiAvailable ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700'">
            <template v-if="aiAvailable">
              <svg class="h-3 w-3 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
              </svg>
              <span>Gemini XAI Aktif</span>
            </template>
            <template v-else>
              <svg class="h-3 w-3 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
              </svg>
              <span>Gemini Belum Diatur</span>
            </template>
          </span>
        </div>
      </div>

      <!-- IPCC AR6 Methodology Badge -->
      <div class="rounded-2xl border border-primary/20 bg-primary/5 p-4">
        <div class="flex flex-wrap items-center gap-3">
          <span class="text-xs font-bold text-primary">Metodologi Terstandar:</span>
          <span v-for="badge in methodologyBadges" :key="badge"
            class="rounded-full border border-primary/20 bg-surface px-3 py-1 text-[10px] font-semibold text-primary">
            {{ badge }}
          </span>
        </div>
      </div>

      <div v-if="!props.regionsReady" class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
        <div class="flex items-start gap-3">
          <span class="material-symbols-outlined text-amber-600">warning</span>
          <div>
            <div class="text-sm font-semibold text-amber-800">Dataset wilayah belum disiapkan admin</div>
            <p class="mt-1 text-xs text-amber-700">
              Admin harus menambahkan wilayah terlebih dahulu di dashboard admin sebelum analisis dapat dibuat.
            </p>
          </div>
        </div>
      </div>

      <!-- Preset Datasets -->
      <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card">
        <div class="flex items-center gap-2 mb-1">
          <svg class="h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
          </svg>
          <h3 class="text-xs font-bold text-on-surface">Pilih dari Dataset Pesisir Pantura</h3>
        </div>
        <p class="text-[11px] text-on-surface-variant">Data aktual wilayah terdampak abrasi — otomatis mengisi semua parameter.</p>
        <div class="mt-3 flex flex-wrap gap-2">
          <button v-for="preset in props.presetDatasets" :key="preset.region_name"
            type="button"
            @click="applyPreset(preset)"
            class="rounded-xl border border-outline-variant bg-surface-container-lowest px-3 py-2 text-xs font-semibold text-on-surface hover:bg-primary/10 hover:border-primary transition-all text-left">
            <div>{{ preset.region_name }}</div>
            <div class="text-[10px] text-outline font-normal mt-0.5">
              {{ preset.area_size }} ha · Loss {{ preset.mangrove_loss }}% · {{ preset.dominant_species.split(' ')[0] }}
            </div>
          </button>
          <div v-if="props.presetDatasets.length === 0" class="text-[11px] text-on-surface-variant">
            Belum ada dataset dari admin. Tambahkan wilayah di dashboard admin terlebih dahulu.
          </div>
        </div>
      </div>

      <!-- Main Form with Tabs -->
      <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">

        <!-- ══ Left: Input Parameters with Tab Navigation ══ -->
        <div class="space-y-4">
          <!-- Tab Buttons -->
          <div class="flex border-b border-outline-variant">
            <button type="button" 
              @click="activeTab = 'general'"
              class="px-4 py-2 text-xs font-bold border-b-2 transition-all flex items-center gap-2"
              :class="activeTab === 'general' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface'">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              Informasi Wilayah & Karbon
            </button>
            <button type="button" 
              @click="activeTab = 'risk'"
              class="px-4 py-2 text-xs font-bold border-b-2 transition-all flex items-center gap-2"
              :class="activeTab === 'risk' ? 'border-primary text-primary' : 'border-transparent text-on-surface-variant hover:text-on-surface'">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Parameter Risiko IPCC AR6
            </button>
          </div>

          <!-- Tab Content: General & Carbon -->
          <div v-show="activeTab === 'general'" class="space-y-4">
            <!-- Section 1: Identifikasi Wilayah -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
              <div class="flex items-center gap-2 mb-4">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-primary text-on-primary text-[10px] font-bold">1</span>
                <h3 class="text-sm font-bold text-on-surface">Identifikasi Wilayah</h3>
              </div>
              <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                  <label class="text-xs font-semibold text-on-surface">Nama Wilayah / Desa</label>
                  <input v-model="form.region_name" type="text"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-primary"
                    placeholder="Demak - Bedono" />
                  <p v-if="form.errors.region_name" class="mt-1 text-[10px] text-error">{{ form.errors.region_name }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-on-surface">Provinsi</label>
                  <input v-model="form.province" type="text"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-primary"
                    placeholder="Jawa Tengah" />
                  <p v-if="form.errors.province" class="mt-1 text-[10px] text-error">{{ form.errors.province }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-on-surface">Luas Area Mangrove (ha)</label>
                  <input v-model.number="form.area_size" type="number" min="0.1" step="0.01"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-primary"
                    placeholder="145.2" />
                  <p v-if="form.errors.area_size" class="mt-1 text-[10px] text-error">{{ form.errors.area_size }}</p>
                </div>
              </div>
            </div>

            <!-- Section 5: Blue Carbon Parameters -->
            <div class="rounded-2xl border border-emerald-200/60 bg-emerald-50/40 p-6 shadow-card">
              <div class="flex items-center gap-2 mb-1">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-600 text-white text-[10px] font-bold">C</span>
                <h3 class="text-sm font-bold text-on-surface">Blue Carbon Parameters</h3>
                <span class="text-[10px] text-on-surface-variant">— Tier-2 Accounting</span>
              </div>
              <p class="mb-4 text-[10px] text-on-surface-variant">
                Koefisien AGB, BGB, SOC otomatis diambil dari basis data ilmiah berdasarkan spesies dan substrat.
              </p>
              <div class="grid gap-4 sm:grid-cols-2">
                <div>
                  <label class="text-xs font-semibold text-on-surface">Spesies Mangrove Dominan</label>
                  <p class="text-[10px] text-on-surface-variant">Sumber: Survei lapangan / KLHK</p>
                  <select v-model="form.dominant_species"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-white px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-emerald-500">
                    <option value="" disabled>Pilih spesies...</option>
                    <option v-for="sp in availableSpecies.filter(s => s !== 'default')" :key="sp" :value="sp">
                      {{ sp }}
                    </option>
                  </select>
                  <p v-if="form.errors.dominant_species" class="mt-1 text-[10px] text-error">{{ form.errors.dominant_species }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-on-surface">Tipe Substrat Tanah</label>
                  <p class="text-[10px] text-on-surface-variant">Menentukan nilai SOC (Soil Organic Carbon)</p>
                  <select v-model="form.soil_type"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-white px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-emerald-500">
                    <option value="mineral">Mineral (lempung/pasir)</option>
                    <option value="organik">Organik (gambut/lumpur kaya bahan organik)</option>
                  </select>
                  <p v-if="form.errors.soil_type" class="mt-1 text-[10px] text-error">{{ form.errors.soil_type }}</p>
                </div>
              </div>
              <!-- Carbon Preview -->
              <div v-if="carbonPreview" class="mt-4 rounded-xl border border-emerald-200 bg-white p-4 grid grid-cols-4 gap-3 text-center">
                <div>
                  <div class="text-[10px] text-on-surface-variant font-semibold uppercase tracking-wide">AGB</div>
                  <div class="text-sm font-bold text-emerald-700">{{ carbonPreview.agb }} t C/ha</div>
                </div>
                <div>
                  <div class="text-[10px] text-on-surface-variant font-semibold uppercase tracking-wide">BGB</div>
                  <div class="text-sm font-bold text-emerald-700">{{ carbonPreview.bgb }} t C/ha</div>
                </div>
                <div>
                  <div class="text-[10px] text-on-surface-variant font-semibold uppercase tracking-wide">SOC</div>
                  <div class="text-sm font-bold text-emerald-700">{{ carbonPreview.soc }} t C/ha</div>
                </div>
                <div>
                  <div class="text-[10px] text-on-surface-variant font-semibold uppercase tracking-wide">Total/ha</div>
                  <div class="text-sm font-bold text-emerald-900">{{ carbonPreview.total }} t C/ha</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab Content: Risk Parameters -->
          <div v-show="activeTab === 'risk'" class="space-y-4">
            <!-- Section 2: Parameter Hazard (IPCC AR6) -->
            <div class="rounded-2xl border border-orange-200/60 bg-orange-50/40 p-6 shadow-card">
              <div class="flex items-center gap-2 mb-1">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-orange-500 text-white text-[10px] font-bold">H</span>
                <h3 class="text-sm font-bold text-on-surface">Hazard Index</h3>
                <span class="text-[10px] text-on-surface-variant">— IPCC AR6 Pillar</span>
              </div>
              <p class="mb-4 text-[10px] text-on-surface-variant">Intensitas ancaman fisik iklim (abrasi, banjir, curah hujan anomali)</p>
              <div class="grid gap-4 sm:grid-cols-3">
                <div>
                  <label class="text-xs font-semibold text-on-surface">Curah Hujan (mm/tahun)</label>
                  <p class="text-[10px] text-on-surface-variant">BMKG / Data BPS</p>
                  <input v-model.number="form.rainfall" type="number" min="0" max="10000"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-white px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-orange-400"
                    placeholder="2200" />
                  <p v-if="form.errors.rainfall" class="mt-1 text-[10px] text-error">{{ form.errors.rainfall }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-on-surface">Tingkat Abrasi (0–5)</label>
                  <p class="text-[10px] text-on-surface-variant">0=Tidak ada · 5=Kritis</p>
                  <input v-model.number="form.abrasion_level" type="number" min="0" max="5"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-white px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-orange-400"
                    placeholder="4" />
                  <p v-if="form.errors.abrasion_level" class="mt-1 text-[10px] text-error">{{ form.errors.abrasion_level }}</p>
                </div>
                <div>
                  <label class="text-xs font-semibold text-on-surface">Banjir Rob (0–5)</label>
                  <p class="text-[10px] text-on-surface-variant">InaRISK BNPB</p>
                  <input v-model.number="form.flood_risk" type="number" min="0" max="5"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-white px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-orange-400"
                    placeholder="4" />
                  <p v-if="form.errors.flood_risk" class="mt-1 text-[10px] text-error">{{ form.errors.flood_risk }}</p>
                </div>
              </div>
            </div>

            <!-- Section 3: Parameter Exposure (IPCC AR6) -->
            <div class="rounded-2xl border border-blue-200/60 bg-blue-50/40 p-6 shadow-card">
              <div class="flex items-center gap-2 mb-1">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white text-[10px] font-bold">E</span>
                <h3 class="text-sm font-bold text-on-surface">Exposure Index</h3>
                <span class="text-[10px] text-on-surface-variant">— IPCC AR6 Pillar</span>
              </div>
              <p class="mb-4 text-[10px] text-on-surface-variant">Keterpaparan ekosistem dan komunitas terhadap ancaman pesisir</p>
              <div class="grid gap-4 sm:grid-cols-2">
                <div>
                  <label class="text-xs font-semibold text-on-surface">Kepadatan Penduduk (jiwa/km²)</label>
                  <p class="text-[10px] text-on-surface-variant">Sumber: BPS Kabupaten</p>
                  <input v-model.number="form.population_density" type="number" min="0"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-white px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-blue-400"
                    placeholder="3200" />
                  <p v-if="form.errors.population_density" class="mt-1 text-[10px] text-error">{{ form.errors.population_density }}</p>
                </div>
              </div>
            </div>

            <!-- Section 4: Parameter Vulnerability (IPCC AR6) -->
            <div class="rounded-2xl border border-rose-200/60 bg-rose-50/40 p-6 shadow-card">
              <div class="flex items-center gap-2 mb-1">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-white text-[10px] font-bold">V</span>
                <h3 class="text-sm font-bold text-on-surface">Vulnerability Index</h3>
                <span class="text-[10px] text-on-surface-variant">— IPCC AR6 Pillar</span>
              </div>
              <p class="mb-4 text-[10px] text-on-surface-variant">Kondisi ekologis dan kapasitas adaptasi ekosistem mangrove</p>
              <div class="grid gap-4 sm:grid-cols-2">
                <div>
                  <label class="text-xs font-semibold text-on-surface">Kehilangan Tutupan Mangrove (%)</label>
                  <p class="text-[10px] text-on-surface-variant">Global Mangrove Watch / KLHK</p>
                  <input v-model.number="form.mangrove_loss" type="number" min="0" max="100"
                    class="mt-1.5 w-full rounded-xl border border-outline-variant bg-white px-4 py-2.5 text-xs text-on-surface focus:outline-none focus:border-rose-400"
                    placeholder="45" />
                  <p v-if="form.errors.mangrove_loss" class="mt-1 text-[10px] text-error">{{ form.errors.mangrove_loss }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ══ Right: Settings + Submit ══ -->
        <div class="space-y-4">
          <!-- AI Engine Selector -->
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card">
            <div class="flex items-center gap-2 mb-1.5">
              <svg class="h-4 w-4 text-on-surface" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.297 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <h3 class="text-xs font-bold text-on-surface">Mode Kalkulasi</h3>
            </div>
            <p class="text-[10px] text-on-surface-variant leading-relaxed">
              Indeks IPCC, MCVI, MRPS, dan Carbon selalu dihitung deterministik.<br />
              AI berperan sebagai <strong>Explainable Layer</strong> (narasi & rekomendasi kebijakan).
            </p>
            <div class="mt-4 space-y-2 text-xs">
              <label class="flex items-start gap-2 cursor-pointer">
                <input type="radio" value="gemini" v-model="form.ai_engine" class="mt-0.5 h-4 w-4 text-primary" />
                <div>
                  <div class="font-semibold text-on-surface">Gemini XAI</div>
                  <div class="text-[10px] text-on-surface-variant">Narasi ilmiah & rekomendasi berbasis LLM</div>
                </div>
              </label>
              <label class="flex items-start gap-2 cursor-pointer">
                <input type="radio" value="rule_based" v-model="form.ai_engine" class="mt-0.5 h-4 w-4 text-primary" />
                <div>
                  <div class="font-semibold text-on-surface">Rule-Based Expert System</div>
                  <div class="text-[10px] text-on-surface-variant">Deterministic fallback — offline ready</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Formula Preview Card -->
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card">
            <div class="flex items-center gap-2 mb-3">
              <svg class="h-4 w-4 text-on-surface" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
              </svg>
              <h3 class="text-xs font-bold text-on-surface">Formula Aktif</h3>
            </div>
            <div class="space-y-2 text-[10px] text-on-surface-variant font-mono">
              <div class="rounded-lg bg-surface-container p-2">
                <span class="text-primary font-bold">Risk</span> = ∛(H × E × V)
              </div>
              <div class="rounded-lg bg-surface-container p-2">
                <span class="text-primary font-bold">MCVI</span> = 0.30H + 0.25E + 0.45V
              </div>
              <div class="rounded-lg bg-surface-container p-2">
                <span class="text-primary font-bold">MRPS</span> = 0.60×Risk + 0.40×CEL
              </div>
              <div class="rounded-lg bg-surface-container p-2">
                <span class="text-primary font-bold">Carbon</span> = AGB + BGB + SOC (Tier-2)
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button type="submit"
            class="w-full rounded-full bg-primary px-4 py-3.5 text-xs font-bold text-on-primary shadow-btn hover:bg-primary-container active:scale-95 transition-all disabled:opacity-60"
            :disabled="form.processing || !props.regionsReady">
            <span v-if="!form.processing" class="flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-base leading-none">analytics</span>
              Generate Analisis IPCC AR6
            </span>
            <span v-else>Memproses Kalkulasi...</span>
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  aiAvailable:      { type: Boolean, default: false },
  availableSpecies: { type: Array,   default: () => [] },
  regionsReady:     { type: Boolean, default: true },
  presetDatasets:   { type: Array,   default: () => [] },
});

const activeTab = ref('general');

const methodologyBadges = [
  'IPCC AR6 WG2',
  'Blue Carbon Initiative Tier-2',
  'DPSIR Framework (OECD/EEA)',
  'IUCN NbS Standard',
  'SDG 13 · 14 · 15',
];

// Koefisien spesies (mirror dari backend — hanya untuk preview client-side)
const SPECIES_COEFFICIENTS = {
  'Rhizophora apiculata':  { agb: 192.5, bgb: 96.3,  soc_mineral: 255.0, soc_organik: 340.0 },
  'Rhizophora mucronata':  { agb: 178.0, bgb: 89.0,  soc_mineral: 255.0, soc_organik: 340.0 },
  'Avicennia marina':      { agb: 143.2, bgb: 71.6,  soc_mineral: 265.0, soc_organik: 350.0 },
  'Sonneratia alba':       { agb: 215.8, bgb: 107.9, soc_mineral: 230.0, soc_organik: 310.0 },
  'Bruguiera gymnorrhiza': { agb: 203.4, bgb: 101.7, soc_mineral: 248.0, soc_organik: 325.0 },
};

const form = useForm({
  region_name:       '',
  province:          '',
  area_size:         '',
  rainfall:          '',
  abrasion_level:    '',
  flood_risk:        '',
  mangrove_loss:     '',
  population_density: '',
  dominant_species:  '',
  soil_type:         'mineral',
  ai_engine:         'gemini',
});

// Real-time carbon coefficient preview
const carbonPreview = computed(() => {
  if (!form.dominant_species || !form.soil_type) return null;
  const coeff = SPECIES_COEFFICIENTS[form.dominant_species];
  if (!coeff) return null;
  const socKey = 'soc_' + form.soil_type;
  return {
    agb:   coeff.agb,
    bgb:   coeff.bgb,
    soc:   coeff[socKey],
    total: +(coeff.agb + coeff.bgb + coeff[socKey]).toFixed(1),
  };
});

const applyPreset = (preset) => {
  Object.assign(form, preset);
};

const submit = () => {
  if (!props.regionsReady) return;
  form.post(route('analysis.store'));
};
</script>
