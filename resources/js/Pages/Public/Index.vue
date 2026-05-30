<template>
  <PublicLayout>
    <!-- ── NAVBAR ── -->
    <header class="sticky top-0 z-30 pt-4">
      <div class="mx-auto flex h-16 max-w-6xl items-center px-6">
        <div 
          class="flex w-full items-center justify-between gap-4 rounded-full border px-4 py-2 transition-all duration-300"
          :class="isScrolled 
            ? 'border-outline-variant bg-white/90 shadow-card backdrop-blur-md' 
            : 'border-white/10 bg-white/5 backdrop-blur-md shadow-none'"
        >
          <!-- Logo -->
          <div class="flex items-center gap-3">
            <img src="/logo.png" alt="MARIS Logo" class="h-10 w-10 object-contain" />
            <div class="hidden sm:block">
              <div 
                class="text-base font-bold transition-colors duration-300"
                :class="isScrolled ? 'text-on-surface' : 'text-white'"
              >MARIS</div>
              <div 
                class="text-xs transition-colors duration-300"
                :class="isScrolled ? 'text-on-surface-variant' : 'text-white/70'"
              >Mangrove Risk Intelligence System</div>
            </div>
          </div>

          <!-- Nav Links -->
          <nav class="hidden text-sm md:flex">
            <a
              v-for="item in navItems"
              :key="item.id"
              :href="item.href"
              class="rounded-full px-4 py-2 font-medium transition-all duration-300"
              :class="
                activeSection === item.id
                  ? (isScrolled ? 'bg-primary text-on-primary shadow-btn' : 'bg-white text-teal-950 shadow-btn')
                  : (isScrolled ? 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' : 'text-white/80 hover:bg-white/10 hover:text-white')
              "
            >
              {{ item.label }}
            </a>
          </nav>

          <!-- Auth Buttons -->
          <div class="flex items-center gap-2 text-sm">
            <a
              class="rounded-full border px-4 py-2 font-medium transition-all duration-300"
              :class="isScrolled 
                ? 'border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface' 
                : 'border-white/30 text-white hover:bg-white/10'"
              href="/login"
            >
              Masuk
            </a>
            <a
              class="rounded-full px-4 py-2 font-semibold shadow-btn transition-all duration-300 active:scale-95"
              :class="isScrolled 
                ? 'bg-primary text-on-primary hover:bg-primary-container' 
                : 'bg-white text-teal-950 hover:bg-white/90'"
              href="/register"
            >
              Daftar
            </a>
          </div>
        </div>
      </div>
    </header>

    <main>
      <!-- ── 1. HERO ── -->
      <section id="home" class="relative -mt-24 flex min-h-[680px] items-center justify-center overflow-hidden pt-24">
        <div class="absolute inset-0">
          <img
            class="h-full w-full object-cover"
            alt="Hutan mangrove di pesisir Jawa Tengah"
            src="/herobanner.gif"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-primary/80 via-primary/50 to-primary/10"></div>
        </div>

        <div class="relative z-10 mx-auto flex max-w-5xl flex-col items-center px-6 text-center text-on-primary">
          <span class="mb-6 inline-flex items-center gap-2 rounded-full border border-on-primary/30 bg-on-primary/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em]">
            SDG 13: Climate Action
          </span>
          <h1 class="text-shadow-soft text-3xl font-bold leading-tight sm:text-5xl lg:text-6xl">
            AI-Driven Mangrove Restoration
            <span class="block text-primary-fixed-dim">untuk Ketahanan Pesisir</span>
          </h1>
          <p class="mt-6 max-w-2xl text-base text-on-primary/80 sm:text-lg leading-relaxed">
            Menguatkan Pantura Jawa Tengah lewat analisis risiko iklim IPCC AR6, simulasi karbon Tier-2,
            dan rekomendasi kebijakan berbasis AI.
          </p>
          <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a
              class="group inline-flex items-center gap-2 rounded-full bg-on-primary px-7 py-3.5 text-sm font-semibold text-primary shadow-btn transition-all hover:bg-surface-container active:scale-95"
              href="/dashboard"
            >
              Explore Impact
              <span class="material-symbols-outlined text-base leading-none transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
            <a
              class="inline-flex items-center gap-2 rounded-full border border-on-primary/40 bg-on-primary/10 px-7 py-3.5 text-sm font-semibold text-on-primary transition-all hover:bg-on-primary/20"
              href="#about"
            >
              Latar Belakang
            </a>
          </div>
        </div>
      </section>

      <!-- ── 2. LATAR BELAKANG & KRISIS (Kondisi Terkini) ── -->
      <section id="about" class="mx-auto max-w-6xl px-6 py-24 scroll-reveal space-y-16" data-animate>
        <!-- Latar Belakang Text -->
        <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-primary">Latar Belakang</p>
            <h2 class="mt-2 text-2xl font-bold text-on-surface sm:text-3xl">Mengapa MARIS Lahir?</h2>
            <p class="mt-4 text-sm text-on-surface-variant leading-relaxed">
              Kawasan pesisir Pantai Utara (Pantura) Jawa Tengah, khususnya Demak dan Pekalongan, mengalami degradasi ekologi yang sangat kritis akibat abrasi ekstrem, penurunan permukaan tanah (land subsidence), dan banjir rob harian.
            </p>
            <p class="mt-3 text-sm text-on-surface-variant leading-relaxed">
              Mangrove merupakan perisai alamiah terbaik. Namun, restorasi manual sering kali gagal karena kurangnya data parameter lingkungan yang presisi. MARIS dirancang untuk menjembatani kesenjangan ini dengan mengintegrasikan data lapangan menggunakan model AI dan sistem pakar untuk merekomendasikan spesies penanaman yang paling adaptif.
            </p>
          </div>
          <div class="space-y-4 rounded-3xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
            <h3 class="text-sm font-bold text-on-surface flex items-center gap-1.5 mb-2">
              <span class="material-symbols-outlined text-primary text-base">eco</span> Fokus Utama Penelitian MARIS
            </h3>
            <div class="space-y-4">
              <div class="border-l-4 border-primary pl-4">
                <div class="text-xs font-bold text-on-surface">SDG 13 & 14: Penanganan Perubahan Iklim</div>
                <p class="text-[11px] text-on-surface-variant mt-1">Mengakselerasi restorasi hutan mangrove pesisir sebagai benteng tangguh mitigasi bencana rob.</p>
              </div>
              <div class="border-l-4 border-secondary pl-4">
                <div class="text-xs font-bold text-on-surface">Blue Carbon Sequestration (Tier-2)</div>
                <p class="text-[11px] text-on-surface-variant mt-1">Mengukur kontribusi riil penyerapan emisi karbon oleh ekosistem mangrove di Pantura secara ilmiah.</p>
              </div>
              <div class="border-l-4 border-tertiary pl-4">
                <div class="text-xs font-bold text-on-surface">Data-Driven Policy Brief</div>
                <p class="text-[11px] text-on-surface-variant mt-1">Menyajikan dokumen rekomendasi kebijakan (policy brief) otomatis yang disokong data konkret.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Crisis Numbers -->
        <div class="border-t border-outline-variant pt-16">
          <div class="text-center mb-10">
            <span class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Urgensi Lapangan</span>
            <h3 class="text-xl font-bold text-on-surface mt-1">Krisis Pesisir Pantura Jawa Tengah</h3>
          </div>
          <div class="grid gap-5 md:grid-cols-3">
            <!-- Stat 1 -->
            <div class="relative overflow-hidden rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card md:col-span-2">
              <div class="pointer-events-none absolute -right-4 -top-4 text-7xl font-black text-error/8 select-none">%</div>
              <div class="relative">
                <div class="text-4xl font-bold text-error">5,500+</div>
                <div class="mt-2 text-base font-semibold text-on-surface">Hektare Lahan Pesisir Tenggelam</div>
                <p class="mt-2 text-sm text-on-surface-variant">
                  Sejak tahun 2000, abrasi ekstrem dan genangan rob telah menenggelamkan ribuan hektare daratan dan memutus ekonomi tambak.
                </p>
              </div>
            </div>
            <!-- Stat 2 -->
            <div class="flex flex-col justify-between rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-error/10 text-error text-xl font-bold">!</div>
              <div>
                <div class="text-2xl font-bold text-on-surface">Harian</div>
                <div class="mt-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Genangan ROB</div>
                <p class="mt-2 text-sm text-on-surface-variant">Banjir pasang laut masuk ke jalanan dan rumah, melumpuhkan infrastruktur vital warga.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── 3. ALUR KERJA (WORKFLOW) ── -->
      <section id="workflow" class="border-y border-outline-variant bg-surface-container-low py-24 scroll-reveal" data-animate>
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto mb-16 max-w-3xl text-center">
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-primary">Sistem Operasional</p>
            <h2 class="mt-2 text-2xl font-bold text-on-surface sm:text-3xl">Alur Kerja Sistem MARIS</h2>
            <p class="mt-3 text-sm text-on-surface-variant sm:text-base leading-relaxed">
              Bagaimana data lapangan diproses secara ilmiah hingga menghasilkan dokumen kebijakan yang siap aksi.
            </p>
          </div>

          <div class="relative">
            <div class="workflow-line pointer-events-none absolute left-6 right-6 top-9 hidden h-px bg-gradient-to-r from-transparent via-outline-variant to-transparent lg:block"></div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
              <!-- Step 1 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary font-black text-sm">
                    01
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 1</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Input Survei / Dataset</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Analis memasukkan data survei pesisir (abrasi, rob, kerapatan) atau memilih dataset Pantura yang tersedia.
                  </p>
                </div>
              </div>

              <!-- Step 2 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary/10 text-secondary font-black text-sm">
                    02
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 2</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Kalkulasi IPCC AR6</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Server lokal menghitung skor Hazard, Exposure, Vulnerability, MCVI, MRPS, dan karbon Tier-2 secara deterministik.
                  </p>
                </div>
              </div>

              <!-- Step 3 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-tertiary/10 text-tertiary font-black text-sm">
                    03
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 3</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Narasi XAI (Gemini AI)</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Gemini XAI Layer menganalisis angka kalkulasi untuk menyusun narasi penjelasan ilmiah dan rekomendasi kebijakan terstruktur.
                  </p>
                </div>
              </div>

              <!-- Step 4 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary font-black text-sm">
                    04
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 4</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Visualisasi & Laporan</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Laporan disajikan dalam bentuk peta interaktif spasial dan siap diunduh sebagai dokumen PDF Policy Brief.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── 4. REALTIME MONITORING ── -->
      <section id="realtime" class="mx-auto max-w-6xl px-6 py-24 scroll-reveal space-y-14" data-animate>
        <div class="mx-auto max-w-3xl text-center">
          <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-primary">Realtime Monitor</p>
          <h2 class="mt-2 text-2xl font-bold text-on-surface sm:text-3xl">Pemantauan Per Jam, Transparan untuk Publik</h2>
          <p class="mt-3 text-sm text-on-surface-variant sm:text-base leading-relaxed">
            Data cuaca, gelombang, dan pasang surut disinkronkan otomatis setiap jam. Publik dapat melihat
            ringkasan pembaruan dan status sumber data secara terbuka.
          </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
          <div class="rounded-3xl border border-outline-variant bg-surface-container-lowest p-8 shadow-card">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Status Sinkron</div>
                <div class="mt-2 text-xl font-bold text-on-surface">Siklus Update: Per Jam</div>
              </div>
              <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                <span class="h-2 w-2 rounded-full bg-primary"></span>
                Live
              </span>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
              <div class="rounded-2xl border border-outline-variant bg-white p-4">
                <div class="text-xs font-semibold text-on-surface">Cuaca</div>
                <div class="mt-2 text-2xl font-bold text-primary">+1 jam</div>
                <div class="mt-1 text-[10px] text-on-surface-variant">Pembaruan terakhir</div>
              </div>
              <div class="rounded-2xl border border-outline-variant bg-white p-4">
                <div class="text-xs font-semibold text-on-surface">Gelombang</div>
                <div class="mt-2 text-2xl font-bold text-primary">+1 jam</div>
                <div class="mt-1 text-[10px] text-on-surface-variant">Pembaruan terakhir</div>
              </div>
              <div class="rounded-2xl border border-outline-variant bg-white p-4">
                <div class="text-xs font-semibold text-on-surface">Pasang Surut</div>
                <div class="mt-2 text-2xl font-bold text-primary">+1 jam</div>
                <div class="mt-1 text-[10px] text-on-surface-variant">Pembaruan terakhir</div>
              </div>
            </div>
          </div>

          <div class="rounded-3xl border border-outline-variant bg-surface-container-lowest p-8 shadow-card">
            <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Sumber Data</div>
            <div class="mt-4 space-y-4">
              <div class="flex items-center justify-between rounded-2xl border border-outline-variant bg-white px-4 py-3">
                <div>
                  <div class="text-sm font-semibold text-on-surface">BMKG Prakiraan Cuaca</div>
                  <div class="text-[10px] text-on-surface-variant">Update 2x per hari, disinkron per jam</div>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2 py-1 text-[10px] font-semibold text-primary">
                  OK
                </span>
              </div>
              <div class="flex items-center justify-between rounded-2xl border border-outline-variant bg-white px-4 py-3">
                <div>
                  <div class="text-sm font-semibold text-on-surface">StormGlass Ocean Data</div>
                  <div class="text-[10px] text-on-surface-variant">Gelombang & pasang surut Pantura</div>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-secondary/10 px-2 py-1 text-[10px] font-semibold text-secondary">
                  Live
                </span>
              </div>
              <div class="rounded-2xl border border-outline-variant bg-surface-container-low p-4">
                <div class="text-xs font-semibold text-on-surface">Akses Publik</div>
                <p class="mt-2 text-[11px] text-on-surface-variant leading-relaxed">
                  Ringkasan real time tersedia di dashboard publik. Dataset mentah tetap tersimpan untuk audit dan validasi.
                </p>
                <a class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-primary" href="/realtime">
                  Lihat Dashboard Publik
                  <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── 5. PLATFORM & MODULES (Solusi) ── -->
      <section id="platform" class="mx-auto max-w-6xl px-6 py-24 scroll-reveal space-y-16" data-animate>
        <!-- Map integration -->
        <div class="mx-auto flex flex-col items-center gap-14 lg:flex-row">
          <div class="lg:w-1/2">
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Solusi Spasial</p>
            <h2 class="mt-2 text-2xl font-bold text-on-surface sm:text-3xl">Peta Pesisir & Pengambilan Keputusan</h2>
            <p class="mt-4 text-sm text-on-surface-variant sm:text-base leading-relaxed">
              Visualisasikan temuan ilmiah Anda secara langsung. MARIS menggabungkan skor kerentanan dan potensi blue carbon di atas peta interaktif.
            </p>
            <div class="mt-8 space-y-5">
              <div v-for="feat in features" :key="feat.code" class="flex gap-4">
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-primary/10 text-xs font-bold text-primary">
                  {{ feat.code }}
                </div>
                <div>
                  <div class="text-sm font-semibold text-on-surface">{{ feat.title }}</div>
                  <p class="mt-1 text-xs text-on-surface-variant">{{ feat.desc }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="lg:w-1/2 w-full">
            <div class="relative overflow-hidden rounded-2xl border border-outline-variant shadow-card p-4 bg-surface-container-lowest">
              <CoastalMap :active-layers="mapLayers" :analyses="analyses" />
            </div>
          </div>
        </div>

        <!-- Modules layout -->
        <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr] pt-12 border-t border-outline-variant">
          <!-- Light teal card: modules list -->
          <div class="rounded-3xl border border-primary/20 bg-primary/8 p-8 shadow-card">
            <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-primary">Platform Features</div>
            <h3 class="mt-3 text-2xl font-bold text-on-surface">Rangkaian Modul MARIS</h3>
            <p class="mt-3 text-sm text-on-surface-variant leading-relaxed">
              Dari analisis risiko iklim hingga simulasi blue carbon, semua terhubung dalam satu pusat keputusan.
            </p>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
              <div v-for="mod in modules" :key="mod.title" class="rounded-2xl border border-primary/15 bg-surface-container-lowest p-4 shadow-card">
                <div class="text-sm font-semibold text-on-surface">{{ mod.title }}</div>
                <div class="mt-2 text-xs text-on-surface-variant">{{ mod.desc }}</div>
              </div>
            </div>
          </div>

          <!-- Light card: preview map -->
          <div class="rounded-3xl border border-outline-variant bg-surface-container-lowest p-8 shadow-card">
            <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Preview Map</div>
            <div class="mt-4 rounded-2xl border border-outline-variant bg-gradient-to-br from-primary/5 via-surface-container to-secondary/5 p-5">
              <div class="flex items-center justify-between text-sm">
                <span class="font-semibold text-on-surface">Pantura Risk Layer</span>
                <span class="rounded-full bg-tertiary/10 px-3 py-1 text-xs font-semibold text-tertiary border border-tertiary/20">Active</span>
              </div>
              <div class="mt-5 space-y-3">
                <div
                  v-for="region in previewRegions"
                  :key="region.name"
                  class="flex items-center justify-between rounded-xl bg-surface-container-lowest border border-outline-variant px-4 py-3"
                >
                  <div>
                    <div class="text-[10px] font-semibold uppercase tracking-[0.2em] text-on-surface-variant">{{ region.label }}</div>
                    <div class="text-sm font-semibold text-on-surface">{{ region.name }}</div>
                  </div>
                  <span
                    class="rounded-full px-3 py-1 text-xs font-bold border"
                    :class="region.score >= 75
                      ? 'bg-error/10 text-error border-error/20'
                      : 'bg-tertiary/10 text-tertiary border-tertiary/20'"
                  >{{ region.score }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── 6. IMPACT STATS (Dampak) ── -->
      <section id="impact" class="border-t border-outline-variant bg-surface-container-lowest py-24 scroll-reveal" data-animate>
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto mb-12 max-w-2xl text-center">
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Hasil Nyata</p>
            <h2 class="mt-2 text-2xl font-bold text-on-surface sm:text-3xl">Dampak Terukur</h2>
            <p class="mt-3 text-sm text-on-surface-variant sm:text-base leading-relaxed">
              Monitoring dampak restorasi untuk memastikan ketahanan pesisir dan penyerapan karbon.
            </p>
          </div>
          <div class="grid gap-5 md:grid-cols-4">
            <!-- Carbon -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 text-center shadow-card hover:border-primary/30 hover:shadow-btn transition-all duration-300">
              <div class="text-3xl font-bold text-primary">{{ animatedStats.carbon || '0' }}</div>
              <div class="mt-2 text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Tons CO₂ Terserap</div>
            </div>
            <!-- Hectares -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 text-center shadow-card hover:border-primary/30 hover:shadow-btn transition-all duration-300">
              <div class="text-3xl font-bold text-primary">{{ animatedStats.hectares || '0' }}</div>
              <div class="mt-2 text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Hektare Restorasi</div>
            </div>
            <!-- Coastline -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 text-center shadow-card hover:border-primary/30 hover:shadow-btn transition-all duration-300">
              <div class="text-3xl font-bold text-primary">{{ animatedStats.coastline || '0' }}</div>
              <div class="mt-2 text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Garis Pesisir Terlindungi</div>
            </div>
            <!-- People -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 text-center shadow-card hover:border-primary/30 hover:shadow-btn transition-all duration-300">
              <div class="text-3xl font-bold text-primary">{{ animatedStats.people || '0' }}</div>
              <div class="mt-2 text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Warga Terdampak Terbantu</div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── 7. FAQ ── -->
      <section id="faq" class="border-t border-outline-variant bg-surface-container-low py-24 scroll-reveal" data-animate>
        <div class="mx-auto max-w-4xl px-6">
          <div class="text-center mb-12">
            <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">FAQ</p>
            <h2 class="mt-2 text-2xl font-bold text-on-surface sm:text-3xl">Pertanyaan Sering Diajukan</h2>
            <p class="mt-3 text-sm text-on-surface-variant">Penjelasan ringkas mengenai sistem operasional platform MARIS.</p>
          </div>

          <div class="space-y-3">
            <div 
              v-for="(faq, index) in faqList" 
              :key="index" 
              class="rounded-2xl border border-outline-variant bg-surface-container-lowest overflow-hidden transition-all duration-300 shadow-sm"
            >
              <button 
                @click="faq.open = !faq.open"
                class="w-full flex items-center justify-between p-5 text-left text-xs font-bold text-on-surface hover:bg-surface-container transition-colors"
              >
                <span>{{ faq.question }}</span>
                <span class="material-symbols-outlined text-sm text-outline transition-transform duration-300" :class="faq.open ? 'rotate-180' : ''">
                  expand_more
                </span>
              </button>
              <div v-show="faq.open" class="p-5 pt-0 border-t border-outline-variant text-[11px] text-on-surface-variant leading-relaxed bg-surface-container-lowest/50">
                {{ faq.answer }}
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── 8. CTA BANNER ── -->
      <section id="cta" class="border-y border-primary/15 bg-primary/8 py-16">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-8 px-6 text-center md:flex-row md:text-left">
          <div>
            <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-primary">Siap Aksi</div>
            <div class="mt-2 text-2xl font-bold text-on-surface">Bangun strategi restorasi yang presisi</div>
            <p class="mt-2 text-sm text-on-surface-variant">
              Gabungkan data, analitik, dan kolaborasi lintas sektor dalam satu platform.
            </p>
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <a
              class="rounded-full bg-primary px-7 py-3 text-sm font-semibold text-on-primary shadow-btn transition-all hover:bg-primary-container active:scale-95"
              href="/register"
            >
              Mulai Sekarang
            </a>
            <a
              class="rounded-full border border-primary/30 px-7 py-3 text-sm font-semibold text-primary transition-all hover:bg-primary/10"
              href="/login"
            >
              Masuk
            </a>
          </div>
        </div>
      </section>
    </main>

    <!-- ── FOOTER ── -->
    <footer class="border-t border-outline-variant bg-surface-container">
      <div class="mx-auto flex max-w-6xl flex-col gap-6 px-6 py-10 md:flex-row md:items-center md:justify-between">
        <div>
          <div class="flex items-center gap-2">
            <img src="/logo.png" alt="MARIS Logo" class="h-7 w-7 object-contain" />
            <div class="text-sm font-bold text-on-surface">MARIS</div>
          </div>
          <div class="mt-1 text-xs text-on-surface-variant">Mendukung SDG 13: Climate Action</div>
        </div>
        <nav class="flex flex-wrap gap-5 text-sm text-on-surface-variant">
          <a class="hover:text-primary transition-colors" href="#about">Latar</a>
          <a class="hover:text-primary transition-colors" href="#workflow">Alur</a>
          <a class="hover:text-primary transition-colors" href="#platform">Platform</a>
          <a class="hover:text-primary transition-colors" href="#impact">Dampak</a>
          <a class="hover:text-primary transition-colors" href="#cta">Mulai</a>
        </nav>
      </div>
    </footer>
  </PublicLayout>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, reactive } from "vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import CoastalMap from "@/Components/Maps/CoastalMap.vue";

const props = defineProps({
  analyses: {
    type: Array,
    default: () => [],
  },
});

const isScrolled = ref(false);
const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const mapLayers = reactive({
  priority: true,
  climate: true,
  carbon: true,
});

const navItems = [
  { id: "home",     label: "Beranda",    href: "#home"     },
  { id: "workflow", label: "Alur Kerja", href: "#workflow" },
  { id: "cta",      label: "Mulai",      href: "#cta"      },
];

const features = [
  { code: "IPCC", title: "IPCC AR6 Framework",           desc: "Kalkulasi objektif pilar Hazard, Exposure, dan Vulnerability." },
  { code: "BC",   title: "Blue Carbon Tier-2",           desc: "Estimasi karbon biomassa & tanah berbasis koefisien ilmiah." },
  { code: "XAI",  title: "Explainable AI (XAI)",         desc: "Gemini AI menerjemahkan angka rumit menjadi narasi kebijakan." },
];

const modules = [
  { title: "AI Restoration Center (IPCC AR6)", desc: "Kalkulasi risiko iklim H×E×V deterministik dengan penjelasan Gemini XAI." },
  { title: "Simulator Blue Carbon (Tier-2)",    desc: "Estimasi karbon (AGB, BGB, SOC) berbasis jenis substrat & spesies." },
  { title: "Peta Pesisir Interaktif",           desc: "Visualisasi spasial layer risiko, indeks MCVI, dan prioritas restorasi." },
  { title: "Report Studio (DPSIR)",             desc: "Penyusunan DPSIR policy brief dan ekspor PDF terstandardisasi." },
];

const previewRegions = [
  { label: "MCVI (Vulnerability)", name: "Demak - Sayung",     score: 82 },
  { label: "MRPS (Priority)",      name: "Pekalongan - Tirto", score: 71 },
];

// Reactive stats values that will animate from 0
const animatedStats = reactive({
  carbon: 0,
  hectares: 0,
  coastline: 0,
  people: 0
});

const faqList = ref([
  {
    question: "Bagaimana cara kerja modul kecerdasan buatan (Gemini AI) di MARIS?",
    answer: "Gemini AI di MARIS bertindak murni sebagai Explainable AI (XAI) Layer. Semua keputusan dan skor kuantitatif (IPCC Risk, MCVI, MRPS, Karbon) dihitung secara deterministik oleh server lokal. Gemini kemudian membaca angka tersebut untuk menyusun narasi penjelasan ilmiah dan rekomendasi kebijakan pesisir yang logis.",
    open: false
  },
  {
    question: "Apa fungsi dari Rule-Based Expert System (Sistem Pakar) di MARIS?",
    answer: "Rule-Based Expert System merupakan model cadangan cerdas (expert system offline) yang mengevaluasi parameter lingkungan pesisir berdasarkan basis aturan akademis (IF-THEN) yang disetujui ahli kelautan jika koneksi API Gemini terputus.",
    open: false
  },
  {
    question: "Bagaimana MARIS membersihkan dataset yang acak-acakan?",
    answer: "MARIS dilengkapi algoritma Fuzzy Matching berbasis Levenshtein Distance di sisi backend Laravel. Sistem secara otomatis mencocokkan kemiripan penulisan nama kolom yang tidak seragam (misal: 'nama daerah' -> 'region_name') dan menyaring data kosong secara dinamis.",
    open: false
  },
  {
    question: "Siapa saja pengguna platform MARIS?",
    answer: "MARIS dirancang untuk dua jenis pengguna utama: Analis (staf teknis kehutanan/lingkungan daerah yang melakukan survei lapangan) dan Administrator (pemilik sistem yang mengelola data master, wilayah, pengguna, dan kebijakan sistem).",
    open: false
  }
]);
const activeSection = ref("home");
let observer;

const animateValue = (targetKey, start, end, duration, suffix = "") => {
  let startTimestamp = null;
  const step = (timestamp) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    animatedStats[targetKey] = Math.floor(progress * (end - start) + start) + suffix;
    if (progress < 1) {
      window.requestAnimationFrame(step);
    }
  };
  window.requestAnimationFrame(step);
};

onMounted(() => {
  window.addEventListener("scroll", handleScroll);
  handleScroll();

  const revealTargets = document.querySelectorAll("[data-animate]");
  const sectionTargets = navItems
    .map((item) => document.getElementById(item.id))
    .filter(Boolean);

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          const sectionId = entry.target.getAttribute("id");
          if (sectionId) activeSection.value = sectionId;
          
          // Trigger numbers animation when the stats section is in view
          if (sectionId === "impact") {
            animateValue("carbon", 0, 12000, 1500, "+ Tons");
            animateValue("hectares", 0, 450, 1500, " Hektar");
            animateValue("coastline", 0, 15, 1500, " km");
            animateValue("people", 0, 5000, 1500, "+");
          }
        }
      });
    },
    { threshold: 0.2 }
  );

  revealTargets.forEach((t) => observer.observe(t));
  sectionTargets.forEach((t) => observer.observe(t));
});

onBeforeUnmount(() => {
  window.removeEventListener("scroll", handleScroll);
  if (observer) observer.disconnect();
});
</script>
