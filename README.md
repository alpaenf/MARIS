# MARIS: Mangrove Risk Intelligence System
### AI-Driven Decision Support System untuk Mitigasi Abrasi dan Konservasi Blue Carbon di Pesisir Utara Jawa Tengah

---

## 1. Pendahuluan

### 1.1 Latar Belakang Krisis Ekologi Pesisir
Kawasan Pantai Utara (Pantura) Jawa Tengah saat ini menghadapi ancaman eksistensial berupa degradasi lingkungan pesisir yang masif. Fenomena perubahan iklim global yang ditandai dengan kenaikan permukaan air laut (*sea level rise*) berpadu dengan faktor lokal berupa penurunan permukaan tanah yang ekstrem (*land subsidence*) akibat eksploitasi air tanah dan beban infrastruktur perkotaan. Wilayah kritis seperti Kabupaten Demak (khususnya Kecamatan Sayung), Kendal (Kaliwungu), Kota Tegal (Tegal Barat), dan Kabupaten Brebes mengalami laju abrasi pantai hingga belasan meter per tahun, menenggelamkan ribuan hektare lahan produktif, tambak budidaya, serta pemukiman warga secara permanen.

Hutan mangrove secara ilmiah diakui sebagai solusi berbasis alam (*Nature-based Solutions* / NbS) terbaik untuk meredam energi gelombang, mengikat sedimen, dan melindungi kawasan daratan dari erosi. Namun, sebagian besar program restorasi mangrove konvensional mengalami tingkat kegagalan yang tinggi (mencapai 60–80%). Kegagalan tersebut disebabkan oleh kurangnya parameter data lingkungan yang presisi saat perencanaan, pemilihan spesies mangrove yang tidak adaptif terhadap tipe substrat/tanah setempat, serta tidak adanya alat bantu pengambilan keputusan (*decision support system*) yang mengintegrasikan data lapangan dengan proyeksi risiko iklim.

### 1.2 Kehadiran MARIS (Mangrove Risk Intelligence System)
MARIS dikembangkan sebagai platform kecerdasan buatan (*AI-driven platform*) untuk menjembatani kesenjangan data ekologis pesisir. MARIS mengintegrasikan data lapangan deterministik, proyeksi ilmiah kerentanan iklim berbasis kerangka kerja **IPCC AR6**, dan perhitungan potensi penyimpanan karbon biru (*Blue Carbon Tier-2 Accounting*) ke dalam visualisasi spasial peta interaktif 3D serta sistem penyusunan dokumen rekomendasi kebijakan publik otomatis (*Policy Brief Studio*).

Melalui integrasi data cuaca realtime dari **Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)** serta parameter oseanografi dari satelit **StormGlass**, MARIS menyediakan transparansi data spasial bagi analis lingkungan, pengambil kebijakan (pemerintah daerah), akademisi, dan publik secara luas guna mewujudkan aksi iklim yang adaptif.

### 1.3 Kontribusi Terhadap Tujuan Pembangunan Berkelanjutan (SDGs)
Sistem ini dirancang untuk berkontribusi langsung pada pencapaian agenda global PBB:
* **SDG 13 (Climate Action)**: Mengembangkan ketahanan dan kapasitas adaptasi masyarakat pesisir terhadap bahaya perubahan iklim dan bencana pasang air laut (rob).
* **SDG 14 (Life Below Water)**: Melindungi dan merehabilitasi ekosistem laut dan pesisir secara berkelanjutan guna menghindari dampak buruk degradasi habitat.
* **SDG 15 (Life on Land)**: Menghentikan laju hilangnya keanekaragaman hayati melalui pengelolaan dan restorasi hutan pesisir yang terdegradasi.

---

## 2. Tinjauan Pustaka

### 2.1 IPCC AR6 Climate Risk Framework
Kerangka kerja penilaian risiko iklim dalam MARIS mengadopsi standar metodologi **IPCC AR6 (Intergovernmental Panel on Climate Change Sixth Assessment Report)**. Risiko iklim diartikan sebagai fungsi interaksi dari tiga pilar utama:
1. **Hazard (Bahaya)**: Peristiwa fisik atau tren iklim yang berpotensi menyebabkan kerusakan (misalnya: tingkat abrasi pesisir, banjir rob, dan anomali curah hujan).
2. **Exposure (Keterpaparan)**: Kehadiran manusia, mata pencaharian, spesies ekosistem, atau aset sosial-ekonomi di lokasi yang rentan terkena bahaya (misalnya: kepadatan penduduk pesisir dan luas kawasan ekologis).
3. **Vulnerability (Kerentanan)**: Kecenderungan atau kepekaan ekosistem terhadap kerusakan serta kurangnya kapasitas untuk beradaptasi dan memulihkan diri (misalnya: laju degradasi tutupan mangrove dan penalti luasan minimum buffer).

### 2.2 Blue Carbon Tier-2 Carbon Accounting
Mangrove merupakan salah satu ekosistem dengan kapasitas sekuestrasi karbon tertinggi di bumi, yang dikenal sebagai **Karbon Biru (*Blue Carbon*)**. Perhitungan kapasitas karbon dalam MARIS menggunakan metodologi **Tier-2** yang ditetapkan oleh *The Blue Carbon Initiative (IUCN/IOC/Conservation International, 2013)*. 

Metodologi Tier-2 menggabungkan luasan area mangrove spesifik dengan koefisien emisi/cadangan karbon berbasis spesies dominan lokal serta tipe substrat tanah (*mineral* vs *organik*) yang merujuk pada literatur bereputasi (seperti *Murdiyarso et al., 2015* dan *SNI 7724:2011*). Cadangan karbon dihitung pada tiga kompartemen utama:
* **Aboveground Biomass (AGB)**: Karbon vegetasi di atas permukaan tanah (batang, daun, dahan).
* **Belowground Biomass (BGB)**: Karbon sistem perakaran mangrove di bawah tanah.
* **Soil Organic Carbon (SOC)**: Karbon organik yang tersimpan kuat di dalam sedimen/tanah pesisir.

### 2.3 Model DPSIR (Driving Forces - Pressures - State - Impact - Responses)
Model **DPSIR** merupakan kerangka kerja analitik terstandardisasi yang digunakan oleh *European Environment Agency (EEA)* dan *OECD* untuk mengevaluasi interaksi antara aktivitas manusia dan kondisi lingkungan hidup. MARIS memetakan hasil kalkulasi kuantitatif ke dalam siklus DPSIR secara dinamis guna menyajikan analisis komprehensif bagi pembuatan kebijakan publik:
* **Driving Forces (Pendorong)**: Tekanan demografi pembangunan pesisir dan kebutuhan ekonomi (misal: aktivitas tambak).
* **Pressures (Tekanan)**: Ancaman fisik langsung dari alam (abrasi, curah hujan tinggi, gelombang pasang).
* **State (Kondisi)**: Keadaan ekosistem saat ini (tutupan mangrove yang tersisa, cadangan karbon aktif).
* **Impact (Dampak)**: Dampak sosial dan ekologi (skor risiko iklim, emisi karbon akibat degradasi).
* **Responses (Respons)**: Tindakan pemulihan ekosistem berbasis evidence (rekomendasi zonasi penanaman spesies adaptif).

---

## 3. Metodologi

### 3.1 Parameter dan Rumus Perhitungan Deterministik
Seluruh perhitungan kuantitatif dihitung secara lokal dan deterministik pada backend Laravel lewat `ScientificCalculationService.php` dengan rumus-rumus sebagai berikut:

#### A. Pilar Hazard Index ($H$)
Mengukur intensitas ancaman fisik di pesisir dengan rentang nilai $0\text{--}100$.
$$H = (AbrasionNorm \times 0.40) + (FloodNorm \times 0.40) + (RainfallNorm \times 0.20)$$
Di mana:
* $AbrasionNorm = (\text{Skala Abrasi } [0\text{--}5] / 5) \times 100$
* $FloodNorm = (\text{Skala Banjir Rob } [0\text{--}5] / 5) \times 100$
* $RainfallNorm = \min(100, (\text{Curah Hujan } [mm] / 4000) \times 100)$

#### B. Pilar Exposure Index ($E$)
Mengukur intensitas keterpaparan populasi manusia dan area ekosistem pesisir dengan rentang nilai $0\text{--}100$.
$$E = (PopDensityNorm \times 0.60) + (AreaNorm \times 0.40)$$
Di mana:
* $PopDensityNorm = \min(100, (\text{Kepadatan Penduduk } [jiwa/km^2] / 10000) \times 100)$
* $AreaNorm = \min(100, (\text{Luas Kawasan } [ha] / 10000) \times 100)$

#### C. Pilar Vulnerability Index ($V$)
Mengukur sensitivitas ekosistem terhadap kerusakan dengan rentang nilai $0\text{--}100$.
$$V = \min(100, \text{Persentase Kehilangan Mangrove } [\%] + ACP)$$
Di mana $ACP$ (*Adaptive Capacity Penalty*) bernilai $+15.0$ jika luas kawasan ekologis $< 100$ hektare (sebagai konsekuensi hilangnya efek *buffer* pada fragmen hutan kecil), dan $0.0$ jika sebaliknya.

#### D. IPCC AR6 Climate Risk Index ($R$)
Dihitung menggunakan rata-rata geometris (*geometric mean*) untuk menghindari bias kompensasi linear antar-pilar (jika salah satu pilar bernilai rendah, tidak serta merta mereduksi ancaman secara drastis).
$$R = \sqrt[3]{H \times E \times V}$$
*(Jika hasil perkalian $\le 0$, maka diberlakukan batas minimum floor sebesar 5.0)*

#### E. MARIS Climate Vulnerability Index (MCVI)
Indeks komposit kerentanan terbobot yang menempatkan bobot kerentanan fungsi ekologi lebih dominan sesuai studi empiris:
$$MCVI = (H \times 0.30) + (E \times 0.25) + (V \times 0.45)$$

#### F. Blue Carbon Tier-2 Accounting
Mengkalkulasi cadangan karbon total ($C_{total}$) dalam satuan Ton C berdasarkan spesies dominan dan jenis tanah:
$$C_{total} = AGB_{total} + BGB_{total} + SOC_{total}$$
Di mana:
* $AGB_{total} = Coeff_{agb} \times Area \times (1 - LossRate)$
* $BGB_{total} = Coeff_{bgb} \times Area \times (1 - LossRate)$
* $SOC_{total} = Coeff_{soc} \times Area \times (1 - (LossRate \times 0.40))$
*(SOC diasumsikan mengalami degradasi karbon lebih lambat, yakni hanya kehilangan 40% dari total potensi emisi saat vegetasi di atasnya rusak)*

**Koefisien Spesies Utama (Ton C / Hektar):**
| Spesies Mangrove | AGB Coeff | BGB Coeff | SOC Mineral | SOC Organik |
|---|---|---|---|---|
| *Rhizophora apiculata* | 192.5 | 96.3 | 255.0 | 340.0 |
| *Rhizophora mucronata* | 178.0 | 89.0 | 255.0 | 340.0 |
| *Avicennia marina* | 143.2 | 71.6 | 265.0 | 350.0 |
| *Sonneratia alba* | 215.8 | 107.9 | 230.0 | 310.0 |
| *Bruguiera gymnorrhiza*| 203.4 | 101.7 | 248.0 | 325.0 |
| *Default (Umum)* | 175.0 | 87.5 | 250.0 | 330.0 |

#### G. MARIS Restoration Priority Score (MRPS)
Menggabungkan risiko iklim jangka pendek dengan efisiensi hilangnya kapasitas penyimpanan karbon sebagai pembobot prioritas restorasi:
$$MRPS = (ClimateRisk \times 0.60) + (CarbonEfficiencyLoss \times 0.40)$$
Klasifikasi Prioritas Restorasi:
* **Tinggi**: $MRPS \ge 70.0$
* **Sedang**: $45.0 \le MRPS < 70.0$
* **Rendah**: $MRPS < 45.0$

---

### 3.2 Alur Pemrosesan Data Sistem
Sistem MARIS membagi alur data menjadi dua jalur kerja yang saling melengkapi:
1. **Jalur A (Dataset Wilayah/Statis - Admin & Analis)**:
   * Admin mendaftarkan parameter ekologis wilayah di database.
   * Analis memicu perhitungan scientific engine untuk menghasilkan laporan IPCC, visualisasi maps, dan policy brief.
2. **Jalur B (Pemberitaan Realtime - Publik & BMKG)**:
   * Cron scheduler memicu penarikan data prakiraan cuaca otomatis per jam melalui API BMKG dan StormGlass.
   * Data disimpan di tabel `hourly_snapshots` untuk disajikan ke publik dalam bentuk grafik tren historis.

```
                  ┌────────────────────────────────────────┐
                  │          INPUT PARAMETER DATA          │
                  │   (Luas Lahan, Spesies, Laju Abrasi)   │
                  └───────────────────┬────────────────────┘
                                      │
                                      ▼
                  ┌────────────────────────────────────────┐
                  │       SCIENTIFIC CALCULATION ENGINE     │
                  │  (IPCC AR6, Tier-2 Blue Carbon, MRPS)  │
                  └───────────────────┬────────────────────┘
                                      │
                                      ▼
                  ┌────────────────────────────────────────┐
                  │          EXPLAINABLE AI (XAI)          │
                  │  (Gemini AI merumuskan narasi ilmiah)   │
                  └───────────────────┬────────────────────┘
                                      │
              ┌───────────────────────┴───────────────────────┐
              ▼                                               ▼
┌───────────────────────────┐                   ┌───────────────────────────┐
│     POLICY BRIEF STUDIO   │                   │    3D INTERACTIVE MAPS    │
│  (Ekspor PDF Laporan)     │                   │ (Visualisasi & Live BMKG) │
└───────────────────────────┘                   └───────────────────────────┘
```

---

## 4. Hasil dan Pembahasan

### 4.1 Deskripsi Wilayah Hasil Seeding
Evaluasi sistem MARIS dilakukan pada 4 kawasan pesisir rawan abrasi di Pantai Utara Jawa Tengah:
1. **Sayung-Bedono (Demak)**: Kawasan kritis dengan degradasi mangrove ekstrem mencapai 68%, tipe tanah mineral berlumpur, dan jenis spesies dominan *Rhizophora mucronata*.
2. **Kaliwungu (Kendal)**: Kawasan pesisir dengan abrasi tingkat tinggi (skala 4/5) dan ancaman rob sedang, didominasi spesies *Avicennia marina*.
3. **Tegal Barat (Kota Tegal)**: Daerah perkotaan pesisir dengan kepadatan penduduk sangat tinggi ($3.800\text{ jiwa/km}^2$) dan abrasi kritis, didominasi *Avicennia marina*.
4. **Brebes-Randusanga (Brebes)**: Lahan pesisir luas ($410\text{ ha}$) dengan tekanan alih fungsi tambak yang tinggi, didominasi spesies *Sonneratia alba*.

### 4.2 Hasil Kalkulasi Indeks Ilmiah
Setelah proses seeding database diaktifkan, scientific engine MARIS berhasil mengkalkulasi parameter ekologis secara presisi:

| Parameter Ekologis | Sayung-Bedono (Demak) | Kaliwungu (Kendal) | Tegal Barat (Tegal) | Brebes-Randusanga (Brebes) |
|---|---|---|---|---|
| **Skor Hazard ($H$)** | 91.00 / 100 | 74.00 / 100 | 73.75 / 100 | 57.50 / 100 |
| **Skor Exposure ($E$)** | 16.90 / 100 | 25.80 / 100 | 31.64 / 100 | 18.84 / 100 |
| **Skor Vulnerability ($V$)** | 68.00 / 100 | 42.00 / 100 | 52.00 / 100 | 35.00 / 100 |
| **Climate Risk Index ($R$)** | **47.12 / 100** | **43.12 / 100** | **49.52 / 100** | **33.59 / 100** |
| **MCVI (Vulnerability)** | **62.13 / 100** | **47.55 / 100** | **53.43 / 100** | **37.71 / 100** |
| **Simpanan Karbon (Ton C)** | **42,429.6 Ton** | **110,420.5 Ton** | **65,726.6 Ton** | **172,502.8 Ton** |
| **Skor Prioritas (MRPS)** | **55.51 / 100** | **42.67 / 100** | **50.51 / 100** | **34.15 / 100** |
| **Prioritas Restorasi** | **Sedang** | **Rendah** | **Sedang** | **Rendah** |

### 4.3 Analisis Komparasi dan Karbon
* **Brebes-Randusanga** menyimpan total cadangan karbon tertinggi (**172,502.8 Ton C**) karena memiliki luas kawasan terbesar ($410\text{ ha}$) dan koefisien AGB tinggi dari spesies *Sonneratia alba*. Daerah ini memiliki prioritas rendah karena tutupan mangrovenya relatif terjaga baik (kerusakan hanya 35%).
* **Sayung-Bedono** memiliki tingkat bahaya (*Hazard*) tertinggi mencapai **91.00/100** akibat ancaman rob tahunan yang memutus jalanan desa dan tingkat abrasi skala ekstrem (5/5). Wilayah ini diklasifikasikan dengan prioritas **Sedang** menuju **Tinggi** karena sistem mendeteksi kerusakan mangrove yang sudah sangat parah (68%), membutuhkan pemasangan Alat Pemecah Ombak (APO) hibrida segera untuk meredam ombak sebelum penanaman kembali dilakukan.

### 4.4 Visualisasi Spasial dan Integrasi Cuaca Realtime BMKG
Dengan implementasi marker Leaflet 3D beranimasi pulsing ring, data hasil kalkulasi deterministik berhasil dipetakan secara interaktif. 

Pencocokan nama wilayah yang fleksibel (*Fuzzy Matching*) berhasil menjembatani data spasial dengan data sensor realtime. Pengguna dapat mengeklik marker wilayah dan langsung melihat status prakiraan cuaca, temperatur udara, arah angin dari BMKG, serta tinggi pasang gelombang air laut dari StormGlass secara realtime. Indikator lampu hijau berkedip 🟢 pada pop-up peta memperkuat keyakinan pengguna bahwa data yang disajikan diperbarui secara aktual dari satelit cuaca.

---

## 5. Kesimpulan dan Saran

### 5.1 Kesimpulan
Platform MARIS membuktikan bahwa integrasi antara rekayasa perangkat lunak modern, kalkulasi deterministik ilmiah (IPCC AR6 & Blue Carbon Tier-2), dan visualisasi spasial interaktif mampu menghasilkan platform *decision support system* yang kokoh. 

Melalui uji coba 4 wilayah utama di Pantai Utara Jawa Tengah, sistem MARIS berhasil mengklasifikasikan skala kerentanan wilayah secara obyektif berdasarkan bobot risiko iklim dan hilangnya cadangan karbon. Penggunaan *Fuzzy Matching* terbukti sukses menyinkronkan data spasial dinamis dengan data cuaca BMKG dan StormGlass secara realtime, memberikan bekal informasi cuaca laut yang sangat berharga bagi nelayan dan aktivis lingkungan dalam merencanakan program penanaman mangrove di pesisir Pantura Jawa Tengah.

### 5.2 Saran dan Pengembangan ke Depan
Untuk meningkatkan akurasi sistem di masa mendatang, disarankan beberapa langkah pengembangan sebagai berikut:
1. **Otomatisasi Citra Satelit (GIS)**: Mengintegrasikan API Google Earth Engine (GEE) agar persentase kehilangan luasan mangrove pesisir dapat diperbarui secara otomatis setiap bulan berdasarkan analisis NDVI (*Normalized Difference Vegetation Index*) citra satelit Sentinel-2.
2. **Kalkulasi Tier-3 Carbon Accounting**: Melakukan integrasi data uji laboratorium sampel tanah lapangan secara berkala untuk meningkatkan presisi perhitungan karbon organik tanah (*Soil Organic Carbon*), bergeser dari model estimasi koefisien Tier-2 ke data riil Tier-3.
3. **Ekspansi Cakupan Wilayah**: Meluaskan cakupan stasiun pemantauan cuaca BMKG ke wilayah pesisir Jawa Tengah lainnya seperti Kabupaten Batang, Pekalongan, dan Pati guna memperluas wilayah perlindungan pesisir Pantura secara kolaboratif.
