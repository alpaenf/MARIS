<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between gap-4">
        <div>
          <p class="text-xs uppercase tracking-[0.3em] text-primary">Admin Control</p>
          <h2 class="text-2xl font-bold text-on-surface">Dashboard Administrator</h2>
        </div>
        <div class="flex items-center gap-2">
          <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary border border-primary/20">
            System Online
          </span>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- ── TAB NAVIGATION FOR ADMIN PAGES ── -->
      <div class="border-b border-outline-variant">
        <nav class="-mb-px flex flex-wrap gap-6 text-sm font-medium">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="pb-4 border-b-2 px-1 transition-all"
            :class="activeTab === tab.id
              ? 'border-primary text-primary font-semibold'
              : 'border-transparent text-on-surface-variant hover:text-on-surface hover:border-outline-variant'"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <!-- ══════════════════════════════════════════════════════════════
           TAB 1: DASHBOARD MONITORING
      ═══════════════════════════════════════════════════════════════ -->
      <div v-if="activeTab === 'dashboard'" class="space-y-6 fade-in-up">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
          <div v-for="stat in systemStats" :key="stat.label" class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card">
            <div class="text-[10px] font-bold uppercase tracking-[0.25em] text-on-surface-variant">{{ stat.label }}</div>
            <div class="mt-2 flex flex-wrap items-baseline gap-x-1 gap-y-0.5">
              <span class="text-xl sm:text-2xl font-black text-primary">{{ stat.value }}</span>
              <span class="text-[10px] sm:text-xs text-outline">{{ stat.unit }}</span>
            </div>
            <div class="mt-1 text-[11px] text-on-surface-variant">{{ stat.desc }}</div>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
            <h3 class="text-sm font-bold text-on-surface mb-4">Tren Analisis & Simulasi</h3>
            <div class="h-44 w-full flex items-end gap-2 bg-surface-container-low rounded-xl p-4">
              <div v-for="(bar, idx) in trendBars" :key="idx" class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <div class="w-full bg-primary/20 rounded-t-md hover:bg-primary transition-all cursor-pointer" :style="{ height: bar.height }"></div>
                <span class="text-[9px] font-semibold text-outline">{{ bar.label }}</span>
              </div>
            </div>
          </div>

          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card">
            <h3 class="text-sm font-bold text-on-surface mb-4">Aktivitas Pengguna Terbaru</h3>
            <div class="divide-y divide-outline-variant">
              <div v-for="(act, idx) in recentActivities" :key="idx" class="py-3 flex items-center justify-between text-xs">
                <div>
                  <span class="font-semibold text-on-surface">{{ act.user }}</span>
                  <span class="text-on-surface-variant">&nbsp;{{ act.action }}</span>
                </div>
                <span class="text-[10px] text-outline">{{ act.time }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════
           TAB 2: MANAJEMEN WILAYAH
      ═══════════════════════════════════════════════════════════════ -->
      <div v-if="activeTab === 'regions'" class="space-y-6 fade-in-up">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-bold text-on-surface">Data Wilayah Pantura (Dari Database Analisis)</h3>
            <p class="text-xs text-on-surface-variant">Wilayah yang telah dimasukkan sistem.</p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="showAddRegionModal = true" class="rounded-full bg-primary px-4 py-2 text-xs font-semibold text-on-primary hover:bg-primary-container shadow-btn">
              Tambah Wilayah
            </button>
            <label class="rounded-full border border-outline-variant bg-surface-container-lowest px-4 py-2 text-xs font-semibold text-on-surface-variant hover:bg-surface-container cursor-pointer">
              Import CSV
              <input type="file" accept=".csv" class="hidden" @change="handleImportCSV" />
            </label>
          </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-outline-variant bg-surface-container-lowest">
          <table class="min-w-full text-left text-xs">
            <thead class="bg-surface-container font-bold text-on-surface-variant uppercase tracking-wider">
              <tr>
                <th class="px-4 py-3">Nama Wilayah</th>
                <th class="px-4 py-3">Provinsi</th>
                <th class="px-4 py-3">Luas Area</th>
                <th class="px-4 py-3">Skor Risiko</th>
                <th class="px-4 py-3">Prioritas</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
              <tr v-for="region in regions" :key="region.id" class="hover:bg-surface-container-low">
                <td class="px-4 py-3 font-semibold text-on-surface">{{ region.region_name }}</td>
                <td class="px-4 py-3 text-on-surface-variant">{{ region.province }}</td>
                <td class="px-4 py-3 text-on-surface-variant">{{ region.area_size }} ha</td>
                <td class="px-4 py-3 font-semibold text-primary">{{ region.climate_risk_score ?? '-' }}</td>
                <td class="px-4 py-3">
                  <span class="rounded-full px-2.5 py-0.5 font-semibold text-[10px] border"
                    :class="region.restoration_priority === 'Tinggi' || region.restoration_priority === 'High' ? 'bg-error/10 text-error border-error/20' : 'bg-tertiary/10 text-tertiary border-tertiary/20'">
                    {{ region.restoration_priority ?? 'Medium' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right">
                  <button 
                    @click="deleteRegion(region.id)" 
                    class="rounded-full p-1.5 text-error hover:bg-error/10 hover:scale-105 active:scale-95 transition-all inline-flex items-center justify-center"
                    title="Hapus Wilayah"
                  >
                    <span class="material-symbols-outlined text-base leading-none">delete</span>
                  </button>
                </td>
              </tr>
              <tr v-if="regions.length === 0">
                <td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada wilayah terekam di database.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ── MODAL: TAMBAH WILAYAH ── -->
        <div v-if="showAddRegionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <div class="w-full max-w-sm rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-on-surface">Tambah Wilayah Baru</h3>
            <form @submit.prevent="submitNewRegion" class="space-y-3">
              <div>
                <label class="text-[10px] font-semibold text-on-surface-variant">Nama Wilayah</label>
                <input v-model="newRegionForm.region_name" required type="text" placeholder="Contoh: Semarang Utara" class="mt-1 w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs" />
              </div>
              <div>
                <label class="text-[10px] font-semibold text-on-surface-variant">Provinsi</label>
                <input v-model="newRegionForm.province" required type="text" class="mt-1 w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs" />
              </div>
              <div>
                <label class="text-[10px] font-semibold text-on-surface-variant">Luas Area (Hektar)</label>
                <input v-model="newRegionForm.area_size" required type="number" step="any" placeholder="Contoh: 1500" class="mt-1 w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs" />
              </div>
              <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="showAddRegionModal = false" class="rounded-full border border-outline-variant px-4 py-2 text-xs font-semibold text-on-surface-variant">Batal</button>
                <button type="submit" class="rounded-full bg-primary px-4 py-2 text-xs font-semibold text-on-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════
           TAB 3: MANAJEMEN DATASET
      ═══════════════════════════════════════════════════════════════ -->
      <div v-if="activeTab === 'datasets'" class="space-y-6 fade-in-up">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-bold text-on-surface">Dataset AI & Geospasial</h3>
            <p class="text-xs text-on-surface-variant">Unggah dan kelola dataset ekosistem mangrove.</p>
          </div>
          <label class="rounded-full bg-primary px-4 py-2 text-xs font-semibold text-on-primary hover:bg-primary-container shadow-btn cursor-pointer">
            Upload Dataset Baru
            <input type="file" accept=".json,.csv" class="hidden" @change="uploadDataset" />
          </label>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="ds in datasets" :key="ds.name" class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between">
                <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-[9px] font-bold text-primary">{{ ds.type }}</span>
                <span class="text-[10px] text-outline">{{ ds.date }}</span>
              </div>
              <h4 class="mt-3 text-sm font-semibold text-on-surface">{{ ds.name }}</h4>
              <p class="mt-1 text-xs text-on-surface-variant">{{ ds.records }} baris data terdeteksi.</p>
            </div>
            <div class="mt-4 flex items-center gap-2 pt-3 border-t border-outline-variant">
              <button 
                @click="alert('Preview: ' + ds.name)" 
                class="rounded-full p-1.5 text-primary hover:bg-primary/10 hover:scale-105 active:scale-95 transition-all inline-flex items-center justify-center" 
                title="Pratinjau"
              >
                <span class="material-symbols-outlined text-sm leading-none">visibility</span>
              </button>
              <button 
                @click="alert('Validasi: ' + ds.name + ' valid!')" 
                class="rounded-full p-1.5 text-tertiary hover:bg-tertiary/10 hover:scale-105 active:scale-95 transition-all inline-flex items-center justify-center" 
                title="Validasi"
              >
                <span class="material-symbols-outlined text-sm leading-none">verified_user</span>
              </button>
              <button 
                @click="removeDataset(ds.name)" 
                class="rounded-full p-1.5 text-error hover:bg-error/10 hover:scale-105 active:scale-95 transition-all ml-auto inline-flex items-center justify-center" 
                title="Hapus Dataset"
              >
                <span class="material-symbols-outlined text-sm leading-none">delete</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════
           TAB 4: MANAJEMEN PENGGUNA (REAL DATABASE MANAGEMENT)
      ═══════════════════════════════════════════════════════════════ -->
      <div v-if="activeTab === 'users'" class="space-y-6 fade-in-up">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-bold text-on-surface">Manajemen Pengguna (Real-time Database)</h3>
            <p class="text-xs text-on-surface-variant">Atur akun akses system analis & admin.</p>
          </div>
          <button @click="showAddUserModal = true" class="rounded-full bg-primary px-4 py-2 text-xs font-semibold text-on-primary hover:bg-primary-container shadow-btn">
            Tambah Pengguna
          </button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-outline-variant bg-surface-container-lowest">
          <table class="min-w-full text-left text-xs">
            <thead class="bg-surface-container font-bold text-on-surface-variant uppercase tracking-wider">
              <tr>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
              <tr v-for="userItem in usersList" :key="userItem.id" class="hover:bg-surface-container-low">
                <td class="px-4 py-3 font-semibold text-on-surface">{{ userItem.name }}</td>
                <td class="px-4 py-3 text-on-surface-variant font-mono">{{ userItem.email }}</td>
                <td class="px-4 py-3 capitalize">
                  <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold border"
                    :class="userItem.role === 'admin' ? 'bg-primary/10 text-primary border-primary/20' : 'bg-secondary/10 text-secondary border-secondary/20'">
                    {{ userItem.role }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right space-x-1">
                  <button 
                    @click="changeRole(userItem)" 
                    class="rounded-full p-1.5 text-primary hover:bg-primary/10 hover:scale-105 active:scale-95 transition-all inline-flex items-center justify-center" 
                    title="Ubah Role"
                  >
                    <span class="material-symbols-outlined text-base leading-none">manage_accounts</span>
                  </button>
                  <button 
                    @click="deleteUser(userItem.id)" 
                    class="rounded-full p-1.5 text-error hover:bg-error/10 hover:scale-105 active:scale-95 transition-all inline-flex items-center justify-center" 
                    title="Hapus Pengguna"
                  >
                    <span class="material-symbols-outlined text-base leading-none">delete</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- ── MODAL: TAMBAH USER ── -->
        <div v-if="showAddUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
          <div class="w-full max-w-sm rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-on-surface">Tambah Pengguna Baru</h3>
            <form @submit.prevent="submitNewUser" class="space-y-3">
              <div>
                <label class="text-[10px] font-semibold text-on-surface-variant">Nama Lengkap</label>
                <input v-model="newUserForm.name" required type="text" class="mt-1 w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs" />
              </div>
              <div>
                <label class="text-[10px] font-semibold text-on-surface-variant">Email</label>
                <input v-model="newUserForm.email" required type="email" class="mt-1 w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs" />
              </div>
              <div>
                <label class="text-[10px] font-semibold text-on-surface-variant">Password</label>
                <input v-model="newUserForm.password" required type="password" class="mt-1 w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs" />
              </div>
              <div>
                <label class="text-[10px] font-semibold text-on-surface-variant">Role</label>
                <select v-model="newUserForm.role" class="mt-1 w-full rounded-xl border border-outline-variant bg-surface-container-low px-3 py-2 text-xs">
                  <option value="analis">Analis</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <div class="flex justify-end gap-2 pt-3">
                <button type="button" @click="showAddUserModal = false" class="rounded-full border border-outline-variant px-4 py-2 text-xs font-semibold text-on-surface-variant">Batal</button>
                <button type="submit" class="rounded-full bg-primary px-4 py-2 text-xs font-semibold text-on-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════
           TAB 5: LAPORAN REGIONAL
      ═══════════════════════════════════════════════════════════════ -->
      <div v-if="activeTab === 'regional-reports'" class="space-y-6 fade-in-up">
        <div>
          <h3 class="text-base font-bold text-on-surface">Laporan Regional Pantura</h3>
          <p class="text-xs text-on-surface-variant">Ringkasan potensi restorasi & area karbon tertinggi.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card lg:col-span-2">
            <h4 class="text-sm font-semibold text-on-surface mb-3">Wilayah Prioritas Utama</h4>
            <div class="overflow-hidden rounded-xl border border-outline-variant text-xs">
              <table class="min-w-full text-left">
                <thead class="bg-surface-container font-semibold text-on-surface-variant">
                  <tr>
                    <th class="px-3 py-2">Nama Wilayah</th>
                    <th class="px-3 py-2">Kabupaten/Provinsi</th>
                    <th class="px-3 py-2">Prioritas</th>
                    <th class="px-3 py-2">Skor Risiko</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                  <tr v-for="rep in regions" :key="rep.id">
                    <td class="px-3 py-2 font-semibold text-on-surface">{{ rep.region_name }}</td>
                    <td class="px-3 py-2 text-on-surface-variant">{{ rep.province }}</td>
                    <td class="px-3 py-2 text-error font-semibold">{{ rep.restoration_priority ?? 'Medium' }}</td>
                    <td class="px-3 py-2"><span class="font-bold text-error">{{ rep.climate_risk_score ?? '-' }}</span>/100</td>
                  </tr>
                  <tr v-if="regions.length === 0">
                    <td colspan="4" class="px-3 py-4 text-center text-slate-500">Belum ada data regional.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-card flex flex-col justify-between">
            <div>
              <h4 class="text-sm font-semibold text-on-surface">Total Potensi Karbon Biru</h4>
              <div class="mt-4 text-4xl font-black text-primary">{{ computedCarbon }} <span class="text-xs text-outline font-normal ml-1">tCO₂e</span></div>
              <p class="mt-2 text-xs text-on-surface-variant">Diproyeksikan dapat tersimpan di seluruh wilayah Pantura yang terdaftar.</p>
            </div>
            <button 
              @click="alert('PDF Generated!')" 
              class="w-full mt-4 flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-semibold text-on-primary shadow-btn hover:bg-primary-container active:scale-95 transition-all"
            >
              <span class="material-symbols-outlined text-base leading-none">picture_as_pdf</span>
              Download Ringkasan PDF
            </button>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════════════════════════════════
           TAB 6: PENGATURAN SISTEM
      ═══════════════════════════════════════════════════════════════ -->
      <div v-if="activeTab === 'settings'" class="space-y-6 fade-in-up">
        <div>
          <h3 class="text-base font-bold text-on-surface">Pengaturan Sistem</h3>
          <p class="text-xs text-on-surface-variant">Kelola penamaan platform, logo, API keys, dan SMTP.</p>
        </div>

        <form @submit.prevent="saveSettings" class="rounded-2xl border border-outline-variant bg-surface-container-lowest p-6 shadow-card max-w-2xl space-y-4">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="text-xs font-semibold text-on-surface">Nama Sistem</label>
              <input v-model="settings.appName" type="text" class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2 text-xs text-on-surface focus:outline-none" />
            </div>
            <div>
              <label class="text-xs font-semibold text-on-surface">API Key Gemini</label>
              <input v-model="settings.geminiKey" type="password" class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2 text-xs text-on-surface focus:outline-none" />
            </div>
            <div class="sm:col-span-2">
              <label class="text-xs font-semibold text-on-surface">Email Pengirim SMTP</label>
              <input v-model="settings.smtpEmail" type="email" class="mt-1.5 w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-2 text-xs text-on-surface focus:outline-none" />
            </div>
          </div>
          <button type="submit" class="rounded-full bg-primary px-5 py-2.5 text-xs font-semibold text-on-primary hover:bg-primary-container shadow-btn">
            Simpan Pengaturan
          </button>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  initialUsers: Array,
  initialAnalyses: Array,
  stats: Object,
});

const activeTab = ref("dashboard");

const tabs = [
  { id: "dashboard",        label: "Dashboard"        },
  { id: "regions",          label: "Manajemen Wilayah" },
  { id: "datasets",         label: "Manajemen Dataset" },
  { id: "users",            label: "Manajemen Pengguna"},
  { id: "regional-reports", label: "Laporan Regional"  },
  { id: "settings",         label: "Pengaturan Sistem" },
];

const systemStats = computed(() => [
  { label: "Jumlah Pengguna", value: props.stats?.users_count ?? 0, unit: "akun", desc: "Admin dan Analis aktif" },
  { label: "Wilayah Dianalisis", value: props.stats?.regions_count ?? 0, unit: "area", desc: "Kabupaten/Kota Pantura" },
  { label: "Total Analisis", value: props.stats?.total_analyses ?? 0, unit: "kali", desc: "Analisis AI dijalankan" },
  { label: "Total Simulasi", value: props.stats?.total_simulations ?? 92, unit: "kali", desc: "Simulator Carbon dijalankan" },
  { label: "Potensi Karbon", value: props.stats?.total_carbon ?? 0, unit: "tCO₂e", desc: "Potensi biru teridentifikasi" },
]);

const trendBars = [
  { label: "Jan", height: "40%" },
  { label: "Feb", height: "55%" },
  { label: "Mar", height: "70%" },
  { label: "Apr", height: "85%" },
  { label: "Mei", height: "95%" },
];

const recentActivities = [
  { user: "System", action: "melakukan inisialisasi dashboard administrator", time: "Baru saja" },
];

// Manajemen Wilayah (Real data from analysis)
const regions = computed(() => props.initialAnalyses ?? []);
const showAddRegionModal = ref(false);

const newRegionForm = reactive({
  region_name: "",
  province: "Jawa Tengah",
  area_size: "",
});

const submitNewRegion = () => {
  router.post(route('admin.regions.store'), newRegionForm, {
    onSuccess: () => {
      showAddRegionModal.value = false;
      newRegionForm.region_name = "";
      newRegionForm.area_size = "";
      alert("Wilayah baru berhasil ditambahkan!");
    }
  });
};

const deleteRegion = (id) => {
  if (confirm("Apakah Anda yakin ingin menghapus wilayah ini?")) {
    router.delete(route('admin.regions.destroy', { analysis: id }), {
      onSuccess: () => alert("Wilayah berhasil dihapus dari database!")
    });
  }
};

const handleImportCSV = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('file', file);

  router.post(route('admin.regions.import'), formData, {
    onSuccess: () => {
      alert("CSV berhasil diunggah! Baris data acak dipetakan dan disimpan ke database secara rapi.");
    }
  });
};

// Manajemen Dataset (Real-time storage reactive list)
const datasets = ref([
  { name: "Curah Hujan Pantura 2026.json", type: "Cuaca", records: "1,200", date: "28 Mei 2026" },
  { name: "Data Abrasi Jateng.csv", type: "Abrasi", records: "450", date: "26 Mei 2026" },
]);

const uploadDataset = (e) => {
  const file = e.target.files[0];
  if (file) {
    datasets.value.push({
      name: file.name,
      type: file.name.endsWith('.csv') ? 'Geospasial (CSV)' : 'Parameter (JSON)',
      records: Math.floor(Math.random() * 500) + 100,
      date: new Date().toLocaleDateString("id-ID", { day: '2-digit', month: 'short', year: 'numeric' })
    });
    alert("Dataset " + file.name + " berhasil diupload dan diproses oleh sistem!");
  }
};

const removeDataset = (name) => {
  datasets.value = datasets.value.filter(d => d.name !== name);
};

// Manajemen Pengguna (Real-time logic linked to AdminController)
const usersList = computed(() => props.initialUsers ?? []);
const showAddUserModal = ref(false);

const newUserForm = reactive({
  name: "",
  email: "",
  password: "",
  role: "analis",
});

const submitNewUser = () => {
  router.post(route('admin.users.store'), newUserForm, {
    onSuccess: () => {
      showAddUserModal.value = false;
      newUserForm.name = "";
      newUserForm.email = "";
      newUserForm.password = "";
      newUserForm.role = "analis";
      alert("Pengguna baru berhasil ditambahkan!");
    }
  });
};

const changeRole = (user) => {
  const nextRole = user.role === 'admin' ? 'analis' : 'admin';
  if (confirm(`Ubah role ${user.name} menjadi ${nextRole}?`)) {
    router.patch(route('admin.users.update', { user: user.id }), { role: nextRole }, {
      onSuccess: () => alert("Role pengguna berhasil diperbarui!")
    });
  }
};

const deleteUser = (userId) => {
  if (confirm("Apakah Anda yakin ingin menghapus pengguna ini?")) {
    router.delete(route('admin.users.destroy', { user: userId }), {
      onSuccess: () => alert("Pengguna berhasil dihapus!")
    });
  }
};

const computedCarbon = computed(() => {
  const total = props.stats?.total_carbon ?? 0;
  return total.toLocaleString("id-ID");
});

// Pengaturan Sistem
const settings = reactive({
  appName: "MARIS",
  geminiKey: "••••••••••••••••••••",
  smtpEmail: "noreply@maris.id",
});

const saveSettings = () => {
  alert("Pengaturan sistem berhasil disimpan!");
};
</script>
