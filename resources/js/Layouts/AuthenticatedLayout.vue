<script setup>
import { computed, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const page     = usePage();
const currentUrl = computed(() => page.url);
const isActive   = (path) => currentUrl.value.startsWith(path);

const mobileOpen = ref(false);
const showHelpModal = ref(false);

const navItems = computed(() => {
    const items = [
        {
            label: 'Dashboard',
            href:  '/dashboard',
            icon:  'dashboard',
        },
    ];

    if (page.props.auth.user?.role === 'admin') {
        items.push({
            label: 'Admin Control',
            href:  '/admin',
            icon:  'admin_panel_settings',
        });
    }

    items.push(
        {
            label: 'AI Restoration',
            href:  '/analysis',
            icon:  'eco',
            fill:  true,
        },
        {
            label: 'Coastal Map',
            href:  '/map',
            icon:  'map',
        },
        {
            label: 'Simulator',
            href:  '/simulator',
            icon:  'analytics',
        },
        {
            label: 'Reports',
            href:  '/reports',
            icon:  'description',
        }
    );

    return items;
});
</script>

<template>
    <!-- ────────────────────────────────────────────────────────────────
         Root — full-viewport flex, overflow hidden so inner panes scroll
    ──────────────────────────────────────────────────────────────────── -->
    <div class="flex h-screen overflow-hidden bg-background text-on-surface antialiased">

        <!-- ══════════════════════════════════════════════════════════════
             SIDEBAR — desktop
        ═══════════════════════════════════════════════════════════════ -->
        <aside
            class="hidden md:flex h-screen w-64 flex-shrink-0 flex-col
                   border-r border-outline-variant bg-surface shadow-nav
                   fixed left-0 top-0 z-40 py-6"
        >
            <!-- Brand -->
            <div class="px-6 mb-8">
                <Link href="/dashboard" class="flex items-center gap-3 group">
                    <ApplicationLogo class="h-11 w-11 object-contain" />
                    <div>
                        <div class="text-base font-bold tracking-tight text-on-surface">MARIS</div>
                        <div class="text-xs text-on-surface-variant">Mangrove Risk Intelligence</div>
                    </div>
                </Link>
            </div>

            <!-- CTA -->
            <div class="px-6 mb-6">
                <Link
                    href="/analysis/create"
                    class="flex w-full items-center justify-center gap-2
                           rounded-xl bg-primary-container text-on-primary
                           py-3 text-sm font-semibold shadow-btn
                           hover:bg-secondary transition-all active:scale-[0.98]"
                >
                    <span class="material-symbols-outlined text-lg leading-none">add</span>
                    Analisis Baru
                </Link>
            </div>

            <!-- Nav Links -->
            <nav class="flex-1 space-y-0.5 px-4 overflow-y-auto custom-scrollbar">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium
                           transition-all duration-150"
                    :class="
                        isActive(item.href)
                            ? 'nav-active font-semibold'
                            : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'
                    "
                >
                    <span
                        class="material-symbols-outlined text-xl leading-none transition-all"
                        :style="isActive(item.href) && item.fill
                            ? 'font-variation-settings: \'FILL\' 1'
                            : ''"
                    >{{ item.icon }}</span>
                    <span>{{ item.label }}</span>
                    <span
                        v-if="isActive(item.href)"
                        class="ml-auto text-[10px] font-bold uppercase tracking-wider
                               text-primary bg-primary/10 px-2 py-0.5 rounded-full"
                    >Aktif</span>
                </Link>
            </nav>

            <!-- Footer Nav -->
            <div class="mt-4 border-t border-outline-variant pt-4 px-4 space-y-0.5">
                <Link
                    href="/profile"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm
                           text-on-surface-variant hover:bg-surface-container transition-all"
                >
                    <span class="material-symbols-outlined text-xl leading-none">settings</span>
                    Settings
                </Link>
                <a
                    href="#"
                    @click.prevent="showHelpModal = true"
                    class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm
                           text-on-surface-variant hover:bg-surface-container transition-all"
                >
                    <span class="material-symbols-outlined text-xl leading-none">help</span>
                    Help
                </a>
            </div>
        </aside>

        <!-- ══════════════════════════════════════════════════════════════
             MAIN CONTENT AREA (offset by sidebar width on md+)
        ═══════════════════════════════════════════════════════════════ -->
        <div class="md:ml-64 flex flex-1 flex-col h-screen w-full overflow-hidden">

            <!-- ── TOP APP BAR ── -->
            <header
                class="sticky top-0 z-30 flex h-[72px] w-full items-center justify-between
                       border-b border-outline-variant bg-surface px-6"
            >
                <!-- Left: mobile hamburger + search -->
                <div class="flex flex-1 items-center gap-4">
                    <!-- Mobile menu toggle -->
                    <button
                        class="md:hidden rounded-full p-2 text-on-surface-variant
                               hover:bg-surface-container-high transition-colors"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <span class="material-symbols-outlined">menu</span>
                    </button>

                    <!-- Search bar -->
                    <div class="relative hidden sm:block max-w-xs w-full">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2
                                   -translate-y-1/2 text-outline text-xl leading-none"
                        >search</span>
                        <input
                            type="text"
                            placeholder="Cari wilayah, koordinat..."
                            class="w-full rounded-full border border-outline-variant
                                   bg-surface-container-low py-2 pl-10 pr-4
                                   text-sm text-on-surface placeholder:text-outline
                                   focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary
                                   transition-shadow"
                        />
                    </div>
                </div>

                <!-- Right: action buttons only -->
                <div class="flex items-center gap-2">

                    <!-- Notification bell -->
                    <button
                        class="relative rounded-full p-2 text-on-surface-variant
                               hover:bg-surface-container-high transition-colors active:scale-95 duration-100"
                    >
                        <span class="material-symbols-outlined">notifications</span>
                        <span
                            class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full
                                   bg-error border-2 border-surface"
                        ></span>
                    </button>

                    <!-- User dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="inline-flex items-center gap-2 rounded-full
                                       border border-outline-variant bg-surface-container-low
                                       px-3 py-2 text-sm font-medium text-on-surface-variant
                                       shadow-sm hover:bg-surface-container transition-all"
                            >
                                <span class="hidden sm:inline text-sm">
                                    {{ $page.props.auth.user.name }}
                                </span>
                                <span
                                    class="inline-flex h-8 w-8 items-center justify-center
                                           rounded-full bg-primary text-xs font-bold text-on-primary"
                                >
                                    {{ $page.props.auth.user.name?.[0]?.toUpperCase() ?? 'U' }}
                                </span>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- ── PAGE CONTENT ── -->
            <main class="flex-1 overflow-y-auto custom-scrollbar bg-background">
                <!-- Page header bar (replaces the crammed top-bar title) -->
                <div
                    v-if="$slots.header"
                    class="border-b border-outline-variant bg-surface px-6 py-4"
                >
                    <slot name="header" />
                </div>

                <div class="mx-auto w-full max-w-7xl px-6 py-8 fade-in-up">
                    <slot />
                </div>
            </main>
        </div>

        <!-- ══════════════════════════════════════════════════════════════
             MOBILE SIDEBAR OVERLAY
        ═══════════════════════════════════════════════════════════════ -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="mobileOpen"
                class="fixed inset-0 z-50 md:hidden"
                @click="mobileOpen = false"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-inverse-surface/40 backdrop-blur-sm"></div>

                <!-- Drawer -->
                <aside
                    class="absolute left-0 top-0 h-full w-72 bg-surface flex flex-col py-6 shadow-xl"
                    @click.stop
                >
                    <!-- Brand -->
                    <div class="px-6 mb-8 flex items-center justify-between">
                        <Link href="/dashboard" class="flex items-center gap-3" @click="mobileOpen = false">
                            <ApplicationLogo class="h-10 w-10 object-contain" />
                            <div>
                                <div class="text-base font-bold text-on-surface">MARIS</div>
                                <div class="text-xs text-on-surface-variant">Mangrove Risk Intelligence</div>
                            </div>
                        </Link>
                        <button
                            class="rounded-full p-1.5 text-on-surface-variant hover:bg-surface-container"
                            @click="mobileOpen = false"
                        >
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                    </div>

                    <!-- CTA -->
                    <div class="px-6 mb-5">
                        <Link
                            href="/analysis/create"
                            class="flex w-full items-center justify-center gap-2
                                   rounded-xl bg-primary-container text-on-primary
                                   py-3 text-sm font-semibold"
                            @click="mobileOpen = false"
                        >
                            <span class="material-symbols-outlined text-lg leading-none">add</span>
                            Analisis Baru
                        </Link>
                    </div>

                    <!-- Nav -->
                    <nav class="flex-1 space-y-0.5 px-4 overflow-y-auto custom-scrollbar">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all"
                            :class="
                                isActive(item.href)
                                    ? 'nav-active font-semibold'
                                    : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'
                            "
                            @click="mobileOpen = false"
                        >
                            <span class="material-symbols-outlined text-xl leading-none">{{ item.icon }}</span>
                            {{ item.label }}
                        </Link>
                    </nav>

                    <!-- Footer -->
                    <div class="mt-4 border-t border-outline-variant pt-4 px-4 space-y-0.5">
                        <Link
                            href="/profile"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm
                                   text-on-surface-variant hover:bg-surface-container transition-all"
                            @click="mobileOpen = false"
                        >
                            <span class="material-symbols-outlined text-xl leading-none">settings</span>
                            Settings
                        </Link>
                        <a
                            href="#"
                            @click.prevent="showHelpModal = true; mobileOpen = false"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm
                                   text-on-surface-variant hover:bg-surface-container transition-all"
                        >
                            <span class="material-symbols-outlined text-xl leading-none">help</span>
                            Help
                        </a>
                    </div>
                </aside>
            </div>
        </Transition>

        <!-- ── POPUP MODAL: HELP CENTER ── -->
        <div v-if="showHelpModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <div class="w-full max-w-lg rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-xl space-y-4 max-h-[85vh] overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-between border-b pb-3 border-outline-variant">
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">help</span>
                <h3 class="text-sm font-bold text-on-surface">Pusat Bantuan MARIS</h3>
              </div>
              <button @click="showHelpModal = false" class="rounded-full p-1.5 text-on-surface-variant hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-base">close</span>
              </button>
            </div>

            <div class="space-y-4 text-xs text-on-surface-variant leading-relaxed">
              <div>
                <h4 class="font-bold text-on-surface text-xs mb-1">1. Bagaimana Cara Kerja Analisis?</h4>
                <p>MARIS menghitung tingkat risiko iklim pesisir menggunakan kerangka **IPCC AR6** dengan rumus: **Risk = ∛(Hazard × Exposure × Vulnerability)**.</p>
                <ul class="list-disc pl-4 mt-1 space-y-0.5">
                  <li>**Hazard (H):** Ancaman fisik berupa curah hujan, abrasi, dan rob.</li>
                  <li>**Exposure (E):** Tingkat keterpaparan berupa kepadatan penduduk.</li>
                  <li>**Vulnerability (V):** Kerentanan ekologi berupa persentase kerusakan mangrove.</li>
                </ul>
              </div>

              <div>
                <h4 class="font-bold text-on-surface text-xs mb-1">2. Indeks MCVI dan MRPS</h4>
                <p>**MCVI (MARIS Climate Vulnerability Index)** berbobot 45% Vulnerability, 30% Hazard, 25% Exposure untuk mengukur kerentanan wilayah.</p>
                <p class="mt-1">**MRPS (Restoration Priority Score)** menggabungkan 60% Skor Risiko & 40% Evaluasi Dampak Ekologis untuk mengurutkan wilayah prioritas utama restorasi.</p>
              </div>

              <div>
                <h4 class="font-bold text-on-surface text-xs mb-1">3. Mengapa Status Gemini AI Belum Diatur?</h4>
                <p>Jika badge menunjukkan **"Gemini Belum Diatur"**, Anda perlu mendaftarkan API Key Gemini Anda di dalam file **`.env`** proyek dengan variabel:</p>
                <pre class="bg-surface-container-low border border-outline-variant rounded-lg p-2 font-mono text-[10px] mt-1 overflow-x-auto text-on-surface">GEMINI_API_KEY=isi_api_key_anda</pre>
              </div>

              <div class="border-t border-outline-variant pt-3 text-[10px] text-outline text-center">
                Mangrove Risk Intelligence System v1.0 · Dukungan Teknis: admin@maris.id
              </div>
            </div>
          </div>
        </div>

    </div>
</template>
