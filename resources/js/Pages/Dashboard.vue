<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import KpiCard from '@/Components/KPI/KpiCard.vue';
import TrendChartCard from '@/Components/Charts/TrendChartCard.vue';
import InsightCard from '@/Components/Cards/InsightCard.vue';
import SectionCard from '@/Components/Cards/SectionCard.vue';

const kpis = [
    {
        label: 'Average Risk Score',
        value: 64,
        unit: '/100',
        trend: 'Naik 6% dari minggu lalu',
        detail: '7 wilayah utama Pantura',
        tone: 'teal',
    },
    {
        label: 'Total Carbon Potential',
        value: '1.24M',
        unit: 'tCO2e',
        trend: 'Naik 12% dari baseline',
        detail: 'Estimasi restorasi 2024-2026',
        tone: 'emerald',
    },
    {
        label: 'High Priority Regions',
        value: 9,
        unit: 'kawasan',
        trend: 'Tetap stabil',
        detail: 'Tingkat abrasi tinggi',
        tone: 'sky',
    },
];

const recentAnalysis = [
    { region: 'Demak - Bedono', risk: 82, date: new Date(Date.now() - 86400000 * 0).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) },
    { region: 'Pekalongan - Tirto', risk: 71, date: new Date(Date.now() - 86400000 * 1).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) },
    { region: 'Semarang - Sayung', risk: 69, date: new Date(Date.now() - 86400000 * 1).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) },
    { region: 'Batang - Subah', risk: 62, date: new Date(Date.now() - 86400000 * 2).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) },
];

const priorityRegions = [
    { name: 'Demak', note: 'Abrasi 7.2 km per tahun' },
    { name: 'Pekalongan', note: 'Kepadatan penduduk tinggi' },
    { name: 'Jepara', note: 'Kehilangan mangrove 21%' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-on-surface-variant">Ringkasan</p>
                    <h2 class="text-base font-semibold text-on-surface leading-tight">Dashboard</h2>
                </div>
                <Link
                    href="/analysis/create"
                    class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-medium text-on-primary shadow-sm hover:bg-primary-container transition-colors"
                >
                    Mulai Analisis
                </Link>
            </div>
        </template>

        <div class="space-y-6 fade-in-up">
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <KpiCard
                    v-for="(kpi, index) in kpis"
                    :key="index"
                    :label="kpi.label"
                    :value="kpi.value"
                    :unit="kpi.unit"
                    :trend="kpi.trend"
                    :detail="kpi.detail"
                    :tone="kpi.tone"
                >
                    <template #icon>
                        <svg
                            v-if="index === 0"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 14l3-3 4 4 5-6" />
                        </svg>
                        <svg
                            v-else-if="index === 1"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0z" />
                        </svg>
                    </template>
                </KpiCard>
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                <TrendChartCard
                    title="Climate Trend"
                    subtitle="Perubahan risiko abrasi dan banjir"
                    left-label="Awal Bulan Ini"
                    right-label="Hari Ini"
                />
                <TrendChartCard
                    title="Carbon Trend"
                    subtitle="Estimasi potensi karbon tersimpan"
                    left-label="Awal Bulan Ini"
                    right-label="Hari Ini"
                />
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                <SectionCard title="Recent Analysis" subtitle="Aktivitas terbaru tim analisis" action="Lihat semua">
                    <div class="overflow-hidden rounded-xl border border-outline-variant">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-surface-container text-xs uppercase tracking-widest text-on-surface-variant">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Region</th>
                                    <th class="px-4 py-3 font-semibold">Risk Score</th>
                                    <th class="px-4 py-3 font-semibold">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant bg-surface-container-lowest">
                                <tr
                                    v-for="(item, index) in recentAnalysis"
                                    :key="index"
                                    class="hover:bg-surface-container-low transition-colors"
                                >
                                    <td class="px-4 py-3 text-on-surface font-medium">{{ item.region }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="item.risk >= 75
                                                ? 'bg-error/10 text-error border border-error/20'
                                                : item.risk >= 60
                                                    ? 'bg-tertiary/10 text-tertiary border border-tertiary/20'
                                                    : 'bg-secondary/10 text-secondary border border-secondary/20'"
                                        >{{ item.risk }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-on-surface-variant">{{ item.date }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </SectionCard>

                <div class="space-y-4">
                    <InsightCard
                        title="AI Insight"
                        badge="Gemini"
                        summary="Pola abrasi meningkat di segmen pesisir dengan curah hujan tinggi dan tutupan mangrove rendah."
                        :bullets="[
                            'Prioritaskan restorasi di Demak dan Sayung.',
                            'Naikkan kepadatan tanaman 20-30% untuk penguatan garis pantai.',
                            'Rekomendasi monitoring setiap 6 minggu.'
                        ]"
                    />
                    <SectionCard title="Priority Regions" subtitle="Daerah dengan prioritas restorasi tinggi">
                        <div class="space-y-3">
                            <div
                                v-for="(region, index) in priorityRegions"
                                :key="index"
                                class="flex items-center gap-3 rounded-xl border border-outline-variant
                                       bg-surface-container-low px-4 py-3
                                       hover:bg-surface-container transition-colors"
                            >
                                <span
                                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center
                                           rounded-full bg-error/10 text-error"
                                >
                                    <span class="material-symbols-outlined text-sm"
                                          style="font-variation-settings: 'FILL' 1">warning</span>
                                </span>
                                <div>
                                    <div class="text-sm font-semibold text-on-surface">{{ region.name }}</div>
                                    <div class="text-xs text-on-surface-variant">{{ region.note }}</div>
                                </div>
                            </div>
                        </div>
                    </SectionCard>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
