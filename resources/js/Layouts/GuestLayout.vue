<script setup>
import { Link } from '@inertiajs/vue3';

const features = [
    { icon: 'eco',       title: 'AI Restoration Center', desc: 'Rekomendasi restorasi berbasis Gemini AI & IPCC AR6' },
    { icon: 'map',       title: 'Peta Interaktif Pesisir', desc: 'Visualisasi geospasial real-time seluruh Indonesia' },
    { icon: 'analytics', title: 'Blue Carbon Simulator', desc: 'Estimasi AGB, BGB & SOC secara Tier-2 terstandar' },
];
</script>

<template>
    <div class="h-screen overflow-hidden bg-white flex">

        <!-- ══ LEFT PANEL — Hero Brand ══════════════════════════════ -->
        <div class="hidden lg:flex flex-col justify-between w-[440px] flex-shrink-0 relative overflow-hidden bg-white lg:ml-10">

            <!-- Top: Brand -->
            <div class="relative z-10 px-10 pt-10">
                <div class="flex items-center gap-3">
                    <img src="/logo.png" alt="MARIS Logo" class="h-11 w-11 object-contain drop-shadow-lg" />
                    <div>
                        <div class="text-base font-bold text-slate-800 tracking-tight">MARIS</div>
                        <div class="text-[10px] text-slate-500">Mangrove Risk Intelligence System</div>
                    </div>
                </div>
            </div>

            <!-- Center: Main Copy -->
            <div class="relative z-10 px-10">
                <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-700 mb-3">Masuk ke Platform</p>
                <h1 class="text-3xl font-bold text-slate-900 leading-tight">
                    Kelola risiko pesisir dengan analitik yang presisi.
                </h1>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                    Akses dashboard terpadu, simulasi blue carbon, dan rekomendasi restorasi berbasis AI.
                </p>

                <!-- Stats -->
                <div class="mt-8 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-emerald-200/70 bg-white/70 p-3">
                        <div class="text-xl font-extrabold text-emerald-700">3.3M</div>
                        <div class="mt-0.5 text-[10px] text-slate-500">Ha Termonitor</div>
                    </div>
                    <div class="rounded-2xl border border-emerald-200/70 bg-white/70 p-3">
                        <div class="text-xl font-extrabold text-emerald-700">97%</div>
                        <div class="mt-0.5 text-[10px] text-slate-500">Akurasi Model</div>
                    </div>
                    <div class="rounded-2xl border border-emerald-200/70 bg-white/70 p-3">
                        <div class="text-xl font-extrabold text-emerald-700">514</div>
                        <div class="mt-0.5 text-[10px] text-slate-500">Kab/Kota</div>
                    </div>
                </div>
            </div>

            <!-- Bottom: Feature highlights -->
            <div class="relative z-10 px-10 pb-10 space-y-2">
                <div v-for="feat in features" :key="feat.title"
                    class="flex items-center gap-3 rounded-xl border border-emerald-200/70 bg-white/70 px-4 py-3">
                    <span class="material-symbols-outlined text-emerald-700 text-lg leading-none">{{ feat.icon }}</span>
                    <div>
                        <div class="text-xs font-semibold text-slate-800">{{ feat.title }}</div>
                        <div class="text-[10px] text-slate-500">{{ feat.desc }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ RIGHT PANEL — Form ════════════════════════════════════ -->
        <div class="flex flex-1 items-center justify-center overflow-y-auto px-6 py-8 bg-transparent">
            <div class="w-full max-w-[400px]">

                <!-- Mobile brand header -->
                <div class="flex items-center justify-between mb-6 lg:hidden">
                    <Link href="/" class="flex items-center gap-2">
                        <img src="/logo.png" alt="MARIS Logo" class="h-8 w-8 object-contain" />
                        <span class="text-sm font-bold text-slate-800">MARIS</span>
                    </Link>
                </div>

                <!-- Back link (desktop) -->
                <div class="hidden lg:flex items-center justify-between mb-5">
                    <Link href="/" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-teal-700 transition-colors">
                        <span class="material-symbols-outlined text-sm leading-none">arrow_back</span>
                        Kembali ke beranda
                    </Link>
                    <span class="text-[10px] uppercase tracking-[0.3em] text-slate-400">MARIS</span>
                </div>

                <!-- Form Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl">
                    <slot />
                </div>

                <p class="mt-4 text-center text-[10px] text-slate-400">
                    Dengan masuk, Anda menyetujui kebijakan privasi MARIS.
                </p>
            </div>
        </div>
    </div>
</template>

