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

          <!-- Nav Links (desktop) -->
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

          <!-- Mobile menu toggle -->
          <button
            class="md:hidden inline-flex items-center justify-center rounded-full p-2 text-white/90 transition-colors"
            :class="isScrolled ? 'text-on-surface-variant hover:bg-surface-container-high' : 'hover:bg-white/10'"
            @click="mobileOpen = !mobileOpen"
            aria-label="Buka menu"
          >
            <span class="material-symbols-outlined">menu</span>
          </button>

          <!-- Auth Buttons (desktop) -->
          <div class="hidden md:flex items-center gap-2 text-sm">
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

    <!-- ── MOBILE MENU ── -->
    <Transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="mobileOpen" class="fixed inset-0 z-40 md:hidden" @click="mobileOpen = false">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div
          class="absolute left-4 right-4 top-20 rounded-3xl border border-outline-variant bg-surface p-4 shadow-card"
          @click.stop
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <img src="/logo.png" alt="MARIS Logo" class="h-8 w-8 object-contain" />
              <div class="text-sm font-bold text-on-surface">MARIS</div>
            </div>
            <button
              class="rounded-full p-1.5 text-on-surface-variant hover:bg-surface-container"
              @click="mobileOpen = false"
              aria-label="Tutup menu"
            >
              <span class="material-symbols-outlined text-lg">close</span>
            </button>
          </div>

          <nav class="mt-4 grid gap-1 text-sm">
            <a
              v-for="item in navItems"
              :key="item.id"
              :href="item.href"
              class="rounded-2xl px-4 py-3 font-semibold transition-colors"
              :class="activeSection === item.id
                ? 'bg-primary text-on-primary'
                : 'text-on-surface-variant hover:bg-surface-container'"
              @click="mobileOpen = false"
            >
              {{ item.label }}
            </a>
          </nav>

          <div class="mt-4 grid gap-2 text-sm">
            <a
              class="rounded-2xl border border-outline-variant px-4 py-3 text-center font-semibold text-on-surface-variant"
              href="/login"
              @click="mobileOpen = false"
            >
              Masuk
            </a>
            <a
              class="rounded-2xl bg-primary px-4 py-3 text-center font-semibold text-on-primary"
              href="/register"
              @click="mobileOpen = false"
            >
              Daftar
            </a>
          </div>
        </div>
      </div>
    </Transition>

    <main>
         <!-- ── 1. HERO ── -->
      <section id="home" class="relative -mt-24 flex min-h-[600px] items-center justify-center overflow-hidden pt-36 pb-12">
        <div class="absolute inset-0">
          <img
            class="h-full w-full object-cover"
            alt="Hutan mangrove di pesisir Jawa Tengah"
            src="/herobanner.gif"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/90 via-teal-900/70 to-emerald-950/90"></div>
        </div>

        <div class="relative z-10 mx-auto flex max-w-5xl flex-col items-center px-6 text-center text-white">
          <h1 class="text-shadow-soft text-4xl font-extrabold leading-tight sm:text-6xl lg:text-7xl tracking-tight">
            MARIS 2.0
            <span class="block text-emerald-300 font-medium text-2xl sm:text-3xl mt-3 tracking-wide">Mangrove Risk Intelligence & Climate Forecasting System</span>
          </h1>
          <p class="mt-6 max-w-3xl text-sm text-white/80 sm:text-base leading-relaxed">
            Sistem inteligensi mitigasi pesisir Pantura Jawa Tengah terintegrasi. Menggabungkan model prediksi 
            garis pantai AI, penaksir ketahanan mangrove ML, penaksir karbon Tier-2 IPCC, dan sistem pakar berbasis aturan.
          </p>
          <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <a
              class="group inline-flex items-center gap-2 rounded-full bg-emerald-500 px-8 py-4 text-xs font-black uppercase tracking-wider text-white shadow-lg transition-all hover:bg-emerald-600 active:scale-95 hover:shadow-emerald-500/20"
              href="/realtime"
            >
              Jelajahi Dashboard EWS
              <span class="material-symbols-outlined text-base leading-none transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
            <a
              class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-8 py-4 text-xs font-black uppercase tracking-wider text-white transition-all hover:bg-white/20 backdrop-blur-sm"
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
            <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-emerald-600">Latar Belakang & Krisis</p>
            <h2 class="mt-2 text-2xl font-extrabold text-on-surface sm:text-3xl">Mitigasi Abrasi Pantura Jawa Tengah</h2>
            <p class="mt-4 text-sm text-on-surface-variant leading-relaxed">
              Kawasan pesisir Pantai Utara (Pantura) Jawa Tengah mengalami degradasi ekologi yang sangat kritis akibat abrasi ekstrem, penurunan permukaan tanah (land subsidence), dan banjir rob harian. Demak (Sayung, Bedono) dan Pekalongan (Tirto) adalah salah satu wilayah dengan tingkat kerentanan tertinggi.
            </p>
            <p class="mt-3 text-sm text-on-surface-variant leading-relaxed">
              Mangrove merupakan perisai alamiah terbaik. Namun, restorasi manual sering kali gagal karena kurangnya presisi data parameter lingkungan dan ketidaksesuaian penanaman spesies. MARIS 2.0 hadir membawa <strong>5 Core AI & ML Engines</strong> untuk menyajikan kalkulasi kerentanan berbasis data satelit dan sensor realtime BMKG.
            </p>
          </div>
          <div class="space-y-4 rounded-3xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
            <h3 class="text-sm font-bold text-on-surface flex items-center gap-1.5 mb-2">
              <span class="material-symbols-outlined text-emerald-600 text-base">eco</span> Kerangka Ilmiah MARIS 2.0
            </h3>
            <div class="space-y-4">
              <div class="border-l-4 border-emerald-500 pl-4">
                <div class="text-xs font-bold text-on-surface">Mitigasi Perubahan Iklim (SDG 13)</div>
                <p class="text-[11px] text-on-surface-variant mt-1">Mengakselerasi restorasi hutan mangrove pesisir sebagai benteng tangguh mitigasi bencana rob.</p>
              </div>
              <div class="border-l-4 border-teal-500 pl-4">
                <div class="text-xs font-bold text-on-surface">Blue Carbon Sequestration (Tier-2 IPCC)</div>
                <p class="text-[11px] text-on-surface-variant mt-1">Mengukur kontribusi riil penyerapan emisi karbon oleh ekosistem mangrove di Pantura secara ilmiah menggunakan koefisien lokal.</p>
              </div>
              <div class="border-l-4 border-emerald-700 pl-4">
                <div class="text-xs font-bold text-on-surface">Aksi Kebijakan Terstandardisasi</div>
                <p class="text-[11px] text-on-surface-variant mt-1">Menyajikan dokumen rekomendasi kebijakan (policy brief) otomatis yang disokong data konkret.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 5 Core AI Engines Showcase -->
        <div class="border-t border-outline-variant pt-16">
          <div class="text-center mb-12">
            <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-emerald-600">Teknologi Mutakhir</span>
            <h3 class="text-2xl font-extrabold text-on-surface mt-1">5 Core AI & ML Engines MARIS 2.0</h3>
            <p class="mt-2 text-xs text-on-surface-variant max-w-xl mx-auto">Model kecerdasan buatan deterministik dan prediktif berstandar nasional untuk mitigasi pesisir.</p>
          </div>
          
          <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Core 1 -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card hover:-translate-y-1 transition-all duration-300">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 mb-4">
                <span class="material-symbols-outlined text-lg">timeline</span>
              </div>
              <h4 class="text-xs font-bold text-on-surface">AI Shoreline Forecasting</h4>
              <p class="mt-2 text-[10px] text-on-surface-variant leading-relaxed">Prediksi kemunduran garis pantai Pantura menggunakan Holt's Double Exponential Smoothing 7 hari ke depan.</p>
            </div>
            <!-- Core 2 -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card hover:-translate-y-1 transition-all duration-300">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 mb-4">
                <span class="material-symbols-outlined text-lg">psychology</span>
              </div>
              <h4 class="text-xs font-bold text-on-surface">AI Mangrove Survival</h4>
              <p class="mt-2 text-[10px] text-on-surface-variant leading-relaxed">Estimasi persentase kelangsungan hidup bibit mangrove berdasarkan substrat tanah, salinitas, dan tinggi pasang air laut.</p>
            </div>
            <!-- Core 3 -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card hover:-translate-y-1 transition-all duration-300">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800 mb-4">
                <span class="material-symbols-outlined text-lg">co2</span>
              </div>
              <h4 class="text-xs font-bold text-on-surface">AI Blue Carbon Forecast</h4>
              <p class="mt-2 text-[10px] text-on-surface-variant leading-relaxed">Model regresi linier memproyeksikan pelepasan emisi karbon biru akibat degradasi lahan pesisir hingga 10 tahun ke depan.</p>
            </div>
            <!-- Core 4 -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card hover:-translate-y-1 transition-all duration-300">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-800 mb-4">
                <span class="material-symbols-outlined text-lg">priority_high</span>
              </div>
              <h4 class="text-xs font-bold text-on-surface">AI Restoration Priority</h4>
              <p class="mt-2 text-[10px] text-on-surface-variant leading-relaxed">Mesin prioritas penentuan zonasi rehabilitasi pesisir berbasis kalkulasi indeks kerentanan iklim IPCC AR6.</p>
            </div>
            <!-- Core 5 -->
            <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card hover:-translate-y-1 transition-all duration-300">
              <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-900 mb-4">
                <span class="material-symbols-outlined text-lg">tsunami</span>
              </div>
              <h4 class="text-xs font-bold text-on-surface">Climate Impact Simulator</h4>
              <p class="mt-2 text-[10px] text-on-surface-variant leading-relaxed">Simulasi dinamis dampak kenaikan permukaan air laut terhadap luasan mangrove dan kerugian valuasi ekonomi di 5 daerah.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ── 3. ALUR KERJA (WORKFLOW) ── -->
      <section id="workflow" class="border-y border-outline-variant bg-surface-container-low py-24 scroll-reveal" data-animate>
        <div class="mx-auto max-w-6xl px-6">
          <div class="mx-auto mb-16 max-w-3xl text-center">
            <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-emerald-600">Sistem Operasional</p>
            <h2 class="mt-2 text-2xl font-bold text-on-surface sm:text-3xl">Alur Kerja Ilmiah MARIS 2.0</h2>
            <p class="mt-3 text-sm text-on-surface-variant sm:text-base leading-relaxed">
              Bagaimana data realtime diproses secara deterministik hingga menghasilkan dokumen kebijakan yang siap aksi.
            </p>
          </div>

          <div class="relative">
            <div class="workflow-line pointer-events-none absolute left-6 right-6 top-9 hidden h-px bg-gradient-to-r from-transparent via-outline-variant to-transparent lg:block"></div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
              <!-- Step 1 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 font-black text-sm">
                    01
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 1</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Ingest Data BMKG & IoT</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Sistem otomatis mengunggah data pasang surut, tinggi gelombang, dan curah hujan dari BMKG secara berkala.
                  </p>
                </div>
              </div>

              <!-- Step 2 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-700 font-black text-sm">
                    02
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 2</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Kalkulasi Indeks Kebencanaan</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Server menghitung indeks kerentanan pesisir (MCVI), indeks prioritas restorasi (MRPS), dan stok karbon biru Tier-2 secara objektif.
                  </p>
                </div>
              </div>

              <!-- Step 3 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-800 font-black text-sm">
                    03
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 3</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Explainable AI (XAI) Layer</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Gemini AI menganalisis hasil kalkulasi numerik dan menyusun policy brief serta penjelasan ilmiah terstandardisasi.
                  </p>
                </div>
              </div>

              <!-- Step 4 -->
              <div class="workflow-step relative rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card space-y-4">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-50 text-teal-800 font-black text-sm">
                    04
                  </div>
                  <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Langkah 4</div>
                </div>
                <div>
                  <h3 class="text-sm font-bold text-on-surface">Visualisasi & Aksi Restorasi</h3>
                  <p class="mt-2 text-xs text-on-surface-variant leading-relaxed">
                    Visualisasi terintegrasi disajikan kepada pemangku kebijakan untuk panduan aksi penanaman di lapangan.
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
const mobileOpen = ref(false);
const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const mapLayers = reactive({
  priority: true,
  climate: true,
  carbon: true,
});

const navItems = [
  { id: "home",        label: "Beranda",    href: "/"            },
  { id: "realtime",    label: "Realtime",   href: "/realtime"    },
  { id: "compare",     label: "Bandingkan", href: "/compare"     },
  { id: "methodology", label: "Metodologi", href: "/methodology" },
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
