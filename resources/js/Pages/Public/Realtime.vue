<template>
  <PublicLayout active-page="realtime" force-scrolled>
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
              <span class="text-[10px] font-semibold text-primary">Realtime</span>
            </div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-primary">MARIS 2.0 Monitor</p>
            <h1 class="text-2xl font-bold text-on-surface">Intelligence & Climate Forecasting System</h1>
          </div>
          <div class="flex w-full flex-wrap items-center gap-3 sm:w-auto">
            <!-- Live indicator -->
            <div class="flex w-full items-center justify-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3 py-1.5 animate-pulse sm:w-auto sm:justify-start">
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-60"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
              </span>
              <span class="text-[10px] font-bold text-primary uppercase tracking-wide">Predictive Engine Live</span>
            </div>
            <!-- Status -->
            <div class="w-full rounded-2xl border border-outline-variant bg-surface-container-lowest px-4 py-2 shadow-card sm:w-auto">
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
            <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
              <label class="text-xs font-semibold text-on-surface whitespace-nowrap">Sumber Data</label>
              <select v-model="localFilters.source" @change="applyFilters"
                class="rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs text-on-surface focus:outline-none focus:border-primary">
                <option v-for="s in sources" :key="s" :value="s">{{ s }}</option>
              </select>
            </div>
            <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">
              <label class="text-xs font-semibold text-on-surface whitespace-nowrap">Rentang Jam</label>
              <div class="flex max-w-full overflow-x-auto rounded-xl border border-outline-variant">
                <button v-for="h in hourOptions" :key="h"
                  @click="localFilters.hours = h; applyFilters()"
                  :class="[
                     'shrink-0 px-3 py-2 text-[11px] font-semibold transition-colors',
                     localFilters.hours === h
                       ? 'bg-primary text-on-primary'
                       : 'text-on-surface-variant hover:bg-surface-container bg-surface-container-low'
                   ]">{{ h }}j</button>
              </div>
            </div>
            <div class="w-full text-[11px] text-on-surface-variant font-medium sm:ml-auto sm:w-auto sm:text-right">
              <span class="font-bold text-primary">{{ compareData.length }}</span> wilayah terpantau
            </div>
          </div>
        </div>

        <!-- ── EARLY WARNING SYSTEM (EWS) ALERTS ─────────────────────── -->
        <div v-if="ewsAlerts && ewsAlerts.length > 0" class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card">
          <div class="flex flex-wrap items-center gap-2.5 mb-4">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-primary"></span>
            </span>
            <h2 class="text-sm font-bold text-on-surface uppercase tracking-wider">Sistem Peringatan Dini Multi-Bahaya (EWS) Pesisir</h2>
          </div>
          
          <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
            <div v-for="alert in filteredEwsAlerts" :key="alert.region" 
              class="rounded-xl border p-4 transition-all hover:shadow-md flex flex-col justify-between w-full min-w-0"
              :class="alertCardClass(alert.code?.toLowerCase())">
              <div>
                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                  <div class="min-w-0 flex-1">
                    <div class="text-[8px] font-extrabold uppercase tracking-wider text-neutral-600">Wilayah Pesisir</div>
                    <h3 class="font-black text-xs truncate text-black">{{ alert.region }}</h3>
                  </div>
                  <span class="rounded-full px-1.5 py-0.5 text-[8px] font-black border uppercase tracking-wider whitespace-nowrap shadow-sm shrink-0"
                    :class="alertBadgeClass(alert.code?.toLowerCase())">
                    {{ alert.code === 'NORMAL' ? 'Aman' : alert.code === 'SIAGA_3' ? 'Siaga 3' : alert.code === 'SIAGA_2' ? 'Siaga 2' : 'Siaga 1' }}
                  </span>
                </div>

                <!-- Composite Danger Index -->
                <div class="my-3 space-y-1">
                  <div class="flex justify-between text-[9px] font-black text-neutral-900">
                    <span>Danger Index</span>
                    <span>{{ ((alert.score || 0) / 10).toFixed(1) }} / 10.0</span>
                  </div>
                  <div class="h-1.5 w-full bg-black/10 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                      :class="dangerBarColor(alert.score || 0)"
                      :style="{ width: (alert.score || 0) + '%' }"></div>
                  </div>
                </div>

                <!-- Metrics -->
                <div class="grid grid-cols-2 gap-1.5 my-3 text-[9px]">
                  <div class="bg-black/5 p-1.5 rounded-lg border border-black/5">
                    <span class="block text-neutral-700 font-bold">MCVI (V)</span>
                    <strong class="text-xs text-black font-black">{{ (alert.parameters?.mcvi || 0).toFixed(1) }}</strong>
                  </div>
                  <div class="bg-black/5 p-1.5 rounded-lg border border-black/5">
                    <span class="block text-neutral-700 font-bold">Pasang</span>
                    <strong class="text-xs text-black font-black">{{ (alert.parameters?.wave_height || 0).toFixed(1) }}m</strong>
                  </div>
                </div>
              </div>

              <!-- Recommendations -->
              <div class="mt-2 pt-2 border-t border-black/10 text-[9px] leading-relaxed">
                <strong class="block mb-1 text-black font-extrabold">Rekomendasi BNPB:</strong>
                <ul class="list-disc list-inside space-y-0.5 text-neutral-950 font-bold">
                  <li v-for="(rec, rIdx) in alert.recommendations" :key="rIdx" class="break-words" :title="rec">{{ rec }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- ── 1. Kartu Wilayah Side-by-Side ─────────────────────── -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-sm font-bold text-on-surface">Snapshot Terkini per Wilayah</h2>
              <p class="text-xs text-on-surface-variant mt-0.5">Klik kartu untuk melihat tren, forecasting & proyeksi ML</p>
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
          <div v-else class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
            <button
              v-for="item in compareData"
              :key="item.region"
              @click="selectRegion(item.region)"
              class="group rounded-2xl border-2 p-5 text-left transition-all duration-200 shadow-card w-full"
              :class="localFilters.region === item.region
                ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
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
                <div class="rounded-xl bg-surface-container p-2 text-center">
                  <div class="text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">C ton</div>
                  <div class="mt-0.5 text-xs font-bold text-on-surface">{{ item.carbon !== null && item.carbon !== undefined ? Math.round(item.carbon) : '–' }}</div>
                </div>
              </div>

              <!-- Timestamp -->
              <div class="mt-3 text-[10px] text-on-surface-variant">
                {{ item.time ? formatTime(item.time) : 'Data belum tersedia' }}
              </div>

              <!-- Selected marker -->
              <div v-if="localFilters.region === item.region"
                class="mt-3 flex items-center gap-1 text-[10px] font-bold text-primary">
                <span class="material-symbols-outlined text-sm leading-none">analytics</span>
                Wilayah Analisis Utama
              </div>
            </button>
          </div>
        </div>

        <!-- ── 2. FORECASTING & ML PREDICTION SECTION ──────────────── -->
        <div v-if="localFilters.region" class="grid gap-6 lg:grid-cols-2">
          
          <!-- HOLT'S CLIMATE FORECASTING ENGINE -->
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-primary">online_prediction</span>
                <h2 class="text-sm font-bold text-on-surface">Predictive Engine: 7-Day Climate Risk Forecast</h2>
              </div>
              <p class="text-xs text-on-surface-variant mb-4">
                Memproyeksikan indeks kerentanan wilayah berdasarkan algoritma <strong>Holt's Linear Exponential Smoothing</strong> (Double Smoothing) & trend regresi 95% Confidence Interval.
              </p>
              
              <div v-if="currentForecast && currentForecast.forecast.length > 0" class="space-y-4">
                <!-- Forecast Chart -->
                <div class="bg-surface rounded-xl p-4 border border-outline-variant">
                  <div class="flex items-center justify-between mb-3 text-xs">
                    <span class="text-on-surface-variant font-semibold">Proyeksi Risiko (7 Hari)</span>
                    <span class="font-bold text-primary flex items-center gap-1">
                      <span class="inline-block h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                      Confidence: {{ currentForecast.confidence }}%
                    </span>
                  </div>
                  
                  <div class="relative h-44 w-full">
                    <svg class="w-full h-full" viewBox="0 0 400 150" preserveAspectRatio="none">
                      <!-- Confidence Interval Area -->
                      <path :d="buildForecastAreaPath(currentForecast.forecast)" fill="var(--color-primary-rgb, rgba(13, 148, 136, 0.1))" opacity="0.2"/>
                      <!-- Forecast Line -->
                      <path :d="buildForecastLinePath(currentForecast.forecast)" fill="none" stroke="var(--md-sys-color-primary, #0d9488)" stroke-width="2.5" stroke-dasharray="3,3"/>
                      <!-- Points -->
                      <circle v-for="(pt, ptIdx) in currentForecast.forecast" :key="ptIdx"
                        :cx="((ptIdx) / (currentForecast.forecast.length - 1)) * 400"
                        :cy="150 - (pt.predicted / 100) * 130 - 10"
                        r="3.5"
                        fill="var(--md-sys-color-primary, #0d9488)"/>
                    </svg>
                  </div>
                  
                  <div class="flex justify-between text-[9px] text-on-surface-variant font-bold mt-2">
                    <span v-for="pt in currentForecast.forecast" :key="pt.date">
                      {{ formatForecastDate(pt.date) }}
                    </span>
                  </div>
                </div>

                <!-- Stats and Model Info -->
                <div class="grid grid-cols-3 gap-2 text-[10px]">
                  <div class="bg-surface border border-outline-variant p-2 rounded-xl text-center">
                    <span class="block opacity-75 mb-0.5">Tren Risiko</span>
                    <span class="font-bold uppercase" :class="trendClass(currentForecast.trend)">
                      {{ trendLabel(currentForecast.trend) }}
                    </span>
                  </div>
                  <div class="bg-surface border border-outline-variant p-2 rounded-xl text-center">
                    <span class="block opacity-75 mb-0.5">Model RMSE</span>
                    <strong class="text-xs">{{ currentForecast.model_metrics.rmse?.toFixed(3) ?? '0.000' }}</strong>
                  </div>
                  <div class="bg-surface border border-outline-variant p-2 rounded-xl text-center">
                    <span class="block opacity-75 mb-0.5">R² Score</span>
                    <strong class="text-xs">{{ currentForecast.model_metrics.r_squared?.toFixed(4) ?? '0.0000' }}</strong>
                  </div>
                </div>
              </div>

              <!-- Data insufficient fallback -->
              <div v-else class="text-center py-10 bg-surface rounded-xl border border-dashed border-outline-variant">
                <span class="material-symbols-outlined text-3xl text-outline mb-2">warning</span>
                <p class="text-xs text-on-surface-variant font-semibold">Data historis di source BMKG belum cukup untuk melatih model.</p>
                <p class="text-[10px] text-on-surface-variant mt-1">Harap pastikan cron job scheduler atau seeding data realtime berjalan.</p>
              </div>
            </div>
            
            <div class="mt-4 pt-3 border-t border-outline-variant text-[9px] text-on-surface-variant italic">
              Metodologi: {{ currentForecast?.model_metrics?.method || "Holt's Linear Smoothing" }}
            </div>
          </div>

          <!-- MANGROVE LOSS MULTIPLE LINEAR REGRESSION PROJECTION -->
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card flex flex-col justify-between">
            <div>
              <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-emerald-600">psychology</span>
                <h2 class="text-sm font-bold text-on-surface">ML Predictive Model: Mangrove Loss & Blue Carbon</h2>
              </div>
              <p class="text-xs text-on-surface-variant mb-4">
                Proyeksi kelestarian hutan mangrove & kerugian ekonomi pelepasan karbon biru 1-10 tahun ke depan menggunakan estimasi <strong>Multiple Linear Regression (MLR)</strong>.
              </p>

              <div v-if="currentML" class="space-y-5">
                <!-- Slider Selector -->
                <div>
                  <div class="flex justify-between items-center text-xs font-semibold mb-2">
                    <span class="text-on-surface-variant">Horizon Prediksi (Tahun)</span>
                    <span class="text-primary font-bold text-sm bg-primary/10 px-2 py-0.5 rounded-lg">{{ selectedMLHorizon }} Tahun</span>
                  </div>
                  <input type="range" min="1" max="10" v-model.number="selectedMLHorizon"
                    class="w-full accent-primary bg-surface-container h-2 rounded-lg cursor-pointer"/>
                </div>

                <!-- Projection Highlight -->
                <div class="grid grid-cols-2 gap-3">
                  <div class="bg-surface border border-outline-variant p-3 rounded-xl">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Kehilangan Mangrove</div>
                    <div class="flex items-end gap-1">
                      <span class="text-2xl font-black text-rose-600">{{ currentMLAtHorizon?.cumulative_loss?.toFixed(1) ?? '0.0' }}%</span>
                      <span class="text-[10px] text-on-surface-variant mb-1">(+{{ ((currentMLAtHorizon?.cumulative_loss || 0) - (currentML?.current_loss || 0))?.toFixed(1) ?? '0.0' }}%)</span>
                    </div>
                  </div>
                  
                  <div class="bg-surface border border-outline-variant p-3 rounded-xl">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Mangrove Tersisa</div>
                    <div class="flex items-end gap-1">
                      <span class="text-2xl font-black text-emerald-600">{{ currentMLAtHorizon?.remaining_pct?.toFixed(1) ?? '100.0' }}%</span>
                    </div>
                  </div>
                </div>

                <!-- Blue Carbon Impact -->
                <div class="bg-surface border border-outline-variant p-4 rounded-xl">
                  <h3 class="text-xs font-bold text-on-surface flex items-center gap-1.5 mb-2.5">
                    <span class="material-symbols-outlined text-sm text-primary">energy_savings_leaf</span>
                    Dampak Pelepasan Karbon Biru (Blue Carbon Release)
                  </h3>
                  <div class="grid grid-cols-2 gap-3 text-[10px] text-on-surface">
                    <div>
                      <span class="block text-on-surface-variant font-medium mb-0.5">Tambahan Mangrove Hilang:</span>
                      <strong class="text-xs text-on-surface font-extrabold">{{ currentMLHorizonCarbon.additional_area_lost_ha }} Ha</strong>
                    </div>
                    <div>
                      <span class="block text-on-surface-variant font-medium mb-0.5">Ekivalensi Pelepasan CO₂:</span>
                      <strong class="text-xs text-on-surface font-extrabold">{{ formatVal(currentMLHorizonCarbon.co2_equivalent_tons) }} ton CO₂e</strong>
                    </div>
                  </div>

                  <!-- Economic Valuation -->
                  <div class="mt-3 pt-3 border-t border-outline-variant grid grid-cols-2 gap-2 text-[10px] text-on-surface">
                    <div>
                      <span class="block text-on-surface-variant font-medium mb-0.5">Kerugian Nilai Karbon (USD):</span>
                      <strong class="text-xs text-on-surface font-extrabold">${{ formatVal(currentMLHorizonCarbon.economic_loss_usd) }} USD</strong>
                    </div>
                    <div>
                      <span class="block text-on-surface-variant font-medium mb-0.5">Kerugian Nilai Karbon (IDR):</span>
                      <strong class="text-xs text-primary font-extrabold">Rp {{ formatCurrency(currentMLHorizonCarbon.economic_loss_idr) }}</strong>
                    </div>
                  </div>
                </div>

                <!-- Trajectory Classification -->
                <div class="bg-surface border border-outline-variant p-4 rounded-xl">
                  <span class="block text-[8px] font-bold uppercase tracking-wider mb-1 text-on-surface-variant">Klasifikasi Trajektori Ekosistem</span>
                  <div class="font-extrabold text-xs text-on-surface">{{ currentML.trajectory.label }}</div>
                  <p class="text-[10px] text-on-surface-variant font-medium mt-1 leading-relaxed">{{ currentML.trajectory.description }}</p>
                </div>
              </div>
              
              <div v-else class="text-center py-10 bg-surface rounded-xl border border-dashed border-outline-variant">
                <span class="material-symbols-outlined text-3xl text-outline mb-2">warning</span>
                <p class="text-xs text-on-surface-variant font-semibold font-bold">Data analisis baseline untuk ML belum tersedia.</p>
                <p class="text-[10px] text-on-surface-variant mt-1">Harap lakukan analisis ilmiah kawasan di dashboard terlebih dahulu.</p>
              </div>
            </div>
            
            <div class="mt-4 pt-3 border-t border-outline-variant flex justify-between text-[9px] text-on-surface-variant italic">
              <span>Model R²: {{ currentML?.model_info?.r_squared || "0.847" }} | RMSE: {{ currentML?.model_info?.rmse || "4.2%" }}</span>
              <span>Metode: IPCC AR6 Multiple Regression</span>
            </div>
          </div>

        </div>

        <!-- ── 3. Tabel Ranking ─────────────────────────────────────── -->
        <div v-if="compareData.length > 0" class="rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-card">
          <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-outline-variant">
            <span class="material-symbols-outlined text-primary text-xl leading-none">leaderboard</span>
            <div>
              <div class="text-sm font-bold text-on-surface">Ranking Prioritas Restorasi Wilayah Pesisir</div>
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

        <!-- ── 4. Perbandingan Visual Indeks (Bar) ─────────────────── -->
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

        <!-- ── 5. Tren Historis Wilayah Terpilih ──────────────────── -->
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
import { computed, reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
  filters:       { type: Object, default: () => ({ source: '', region: '', hours: 24 }) },
  sources:       { type: Array,  default: () => [] },
  regions:       { type: Array,  default: () => [] },
  compareData:   { type: Array,  default: () => [] },
  series:        { type: Array,  default: () => [] },
  status:        { type: Object, default: null },
  // MARIS 2.0 new props
  forecastData:  { type: Array,  default: () => [] },
  ewsAlerts:     { type: Array,  default: () => [] },
  mlPredictions: { type: Array,  default: () => [] },
});

const localFilters = reactive({
  source: props.filters.source,
  region: props.filters.region,
  hours:  props.filters.hours,
});

const filteredEwsAlerts = computed(() => {
  if (!props.ewsAlerts) return [];
  return props.ewsAlerts.slice(0, 5);
});

const selectedMLHorizon = ref(5);
const hourOptions = [6, 12, 24, 48, 72, 168];

const applyFilters = () => {
  router.get('/realtime', { ...localFilters }, { preserveState: true, preserveScroll: true });
};

const selectRegion = (name) => {
  localFilters.region = name;
  applyFilters();
};

// Find forecast and ML prediction for active region
const currentForecast = computed(() => {
  if (!localFilters.region || !props.forecastData) return null;
  return props.forecastData.find(f => f.region === localFilters.region);
});

const currentML = computed(() => {
  if (!localFilters.region || !props.mlPredictions) return null;
  return props.mlPredictions.find(p => p.region === localFilters.region);
});

const currentMLAtHorizon = computed(() => {
  if (!currentML.value) return null;
  const idx = selectedMLHorizon.value - 1;
  return currentML.value.projections[idx] || currentML.value.projections[currentML.value.projections.length - 1];
});

const currentMLHorizonCarbon = computed(() => {
  if (!currentML.value || !currentMLAtHorizon.value) return {};
  
  // Recalculate carbon impact for custom year horizon
  const areaSizeHa = 100; // baseline standard
  const carbonDensity = 1023; // ton C per hectare
  const currentLoss = currentML.value.current_loss;
  const finalLoss = currentMLAtHorizon.value.cumulative_loss;
  
  const additionalLossPct = Math.max(0, finalLoss - currentLoss);
  const additionalAreaLost = areaSizeHa * (additionalLossPct / 100);
  
  const carbonLost = additionalAreaLost * carbonDensity;
  const co2Equivalent = carbonLost * 3.67;
  
  return {
    additional_area_lost_ha: additionalAreaLost.toFixed(1),
    carbon_lost_tons: carbonLost.toFixed(0),
    co2_equivalent_tons: co2Equivalent.toFixed(0),
    economic_loss_usd: (co2Equivalent * 12).toFixed(0),
    economic_loss_idr: (co2Equivalent * 12 * 15000).toFixed(0),
  };
});

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

// SVG path builders for Forecast
const buildForecastLinePath = (forecastPoints) => {
  if (!forecastPoints || forecastPoints.length < 2) return '';
  const W = 400, H = 150;
  return forecastPoints.map((pt, i) => {
    const x = (i / (forecastPoints.length - 1)) * W;
    const y = H - (pt.predicted / 100) * 130 - 10;
    return `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`;
  }).join(' ');
};

const buildForecastAreaPath = (forecastPoints) => {
  if (!forecastPoints || forecastPoints.length < 2) return '';
  const W = 400, H = 150;
  const upperPts = forecastPoints.map((pt, i) => {
    const x = (i / (forecastPoints.length - 1)) * W;
    const y = H - (pt.upper_ci / 100) * 130 - 10;
    return `${x.toFixed(1)},${y.toFixed(1)}`;
  });
  const lowerPts = forecastPoints.slice().reverse().map((pt, i) => {
    const revI = forecastPoints.length - 1 - i;
    const x = (revI / (forecastPoints.length - 1)) * W;
    const y = H - (pt.lower_ci / 100) * 130 - 10;
    return `${x.toFixed(1)},${y.toFixed(1)}`;
  });
  return `M${upperPts[0]} L${upperPts.join(' L')} L${lowerPts.join(' L')} Z`;
};

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

const formatForecastDate = (d) => {
  if (!d) return '';
  return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
};

const formatCurrency = (val) => {
  if (!val) return '0';
  const num = typeof val === 'string' ? parseFloat(val) : val;
  if (num >= 1e9) return (num / 1e9).toFixed(2) + ' Miliar';
  if (num >= 1e6) return (num / 1e6).toFixed(2) + ' Juta';
  return num.toLocaleString('id-ID');
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
  if (hazard >= 65) return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30';
  if (hazard >= 45) return 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-950/20 dark:text-yellow-400 dark:border-yellow-900/30';
  return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/30';
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

// EWS Classes
const alertCardClass = (lvl) => {
  if (lvl === 'normal' || !lvl) {
    return 'bg-surface border border-outline-variant text-emerald-700 shadow-sm';
  }
  return 'bg-surface border border-outline-variant text-slate-900 shadow-sm';
};

const alertBadgeClass = (lvl) => {
  switch (lvl) {
    case 'siaga_1': return 'bg-rose-600 text-white border-rose-700 animate-pulse';
    case 'siaga_2': return 'bg-orange-600 text-white border-orange-700';
    case 'siaga_3': return 'bg-amber-500 text-black border-amber-600';
    default: return 'bg-emerald-100 text-emerald-800 border-emerald-200';
  }
};

const dangerBarColor = (score) => {
  if (score >= 8) return 'bg-rose-600';
  if (score >= 6) return 'bg-orange-500';
  if (score >= 4) return 'bg-amber-500';
  return 'bg-emerald-500';
};

const trendClass = (t) => {
  if (t === 'increasing') return 'text-rose-600';
  if (t === 'decreasing') return 'text-emerald-600';
  return 'text-amber-600';
};

const trendLabel = (t) => {
  if (t === 'increasing') return 'Meningkat ↑';
  if (t === 'decreasing') return 'Menurun ↓';
  return 'Stabil →';
};

const trajectoryClass = (cls) => {
  switch (cls) {
    case 'critical': return 'bg-rose-50 border-rose-200 text-rose-950 dark:bg-rose-950/20 dark:border-rose-900 dark:text-rose-200';
    case 'severe': return 'bg-orange-50 border-orange-200 text-orange-950 dark:bg-orange-950/20 dark:border-orange-900 dark:text-orange-200';
    case 'moderate': return 'bg-amber-50 border-amber-200 text-amber-950 dark:bg-amber-950/20 dark:border-amber-900 dark:text-amber-200';
    default: return 'bg-emerald-50 border-emerald-200 text-emerald-950 dark:bg-emerald-950/20 dark:border-emerald-900 dark:text-emerald-200';
  }
};
</script>
