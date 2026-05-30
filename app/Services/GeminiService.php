<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * MARIS Gemini Explainable AI (XAI) Layer
 *
 * Peran Gemini AI dalam MARIS adalah BUKAN penentu keputusan utama.
 * Semua perhitungan kuantitatif dilakukan oleh ScientificCalculationService.
 * Gemini bertugas sebagai:
 * 1. Scientific Narrator — menerjemahkan angka indeks menjadi narasi ilmiah
 * 2. Policy Translator   — menyusun rekomendasi spesifik berbasis konteks lokal
 * 3. Explainability Layer— memberikan justifikasi keputusan yang auditabel
 *
 * @standard DARPA XAI Program | EU AI Act Article 13 (Transparency)
 */
class GeminiService
{
    public function generateExplanation(array $data, array $computed): array
    {
        $key = config('services.gemini.key');

        if (!$key) {
            return $this->buildFallbackExplanation($data, $computed);
        }

        $prompt = $this->buildExplainabilityPrompt($data, $computed);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$key}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature'     => 0.3,  // rendah agar output deterministik & ilmiah
                'topP'            => 0.85,
                'maxOutputTokens' => 600,
            ],
        ]);

        if (!$response->successful()) {
            return $this->buildFallbackExplanation($data, $computed);
        }

        $text    = data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $decoded = $this->decodeJson($text);

        return $decoded ?: $this->buildFallbackExplanation($data, $computed);
    }

    /**
     * Prompt rekayasa untuk Explainable AI Layer
     * Mengintegrasikan semua indeks terkomputasi sebagai konteks
     */
    private function buildExplainabilityPrompt(array $data, array $computed): string
    {
        $speciesName = $data['dominant_species'] ?? 'Rhizophora apiculata';
        $soilType    = $data['soil_type'] ?? 'mineral';
        $priority    = $computed['restoration_priority'];

        return <<<PROMPT
You are a senior IPCC Lead Author specializing in Coastal Ecosystem Assessment and a Blue Carbon technical auditor certified by the Verified Carbon Standard (VCS). You are preparing a scientific policy brief for a local government (Pemerintah Daerah) in Indonesia.

The MARIS system has already computed the following deterministic indices for region "{$data['region_name']}", {$data['province']}:

[IPCC AR6 RISK PILLARS]
- Hazard Score (H):       {$computed['hazard_score']} / 100
- Exposure Score (E):     {$computed['exposure_score']} / 100
- Vulnerability Score (V): {$computed['vulnerability_score']} / 100

[COMPUTED INDICES]
- Climate Risk Index (∛H×E×V): {$computed['climate_risk_score']} / 100
- MCVI (MARIS Vulnerability Index): {$computed['mcvi']} / 100
- MRPS (Restoration Priority Score): {$computed['mrps']} / 100
- Restoration Priority Class: {$priority}

[BLUE CARBON TIER-2 ACCOUNTING — Species: {$speciesName}, Substrate: {$soilType}]
- Aboveground Biomass Carbon (AGB): {$computed['carbon_agb']} ton C
- Belowground Biomass Carbon (BGB): {$computed['carbon_bgb']} ton C
- Soil Organic Carbon (SOC):        {$computed['carbon_soc']} ton C
- Total Blue Carbon Potential:      {$computed['carbon_potential']} ton C
- CO₂ Equivalent:                   {$computed['carbon_potential']} × 3.67 = {$this->formatCO2($computed['carbon_potential'])} ton CO₂

[INPUT PARAMETERS]
- Area: {$data['area_size']} ha | Rainfall: {$data['rainfall']} mm/year
- Abrasion Level: {$data['abrasion_level']}/5 | Flood Risk: {$data['flood_risk']}/5
- Mangrove Loss: {$data['mangrove_loss']}% | Population Density: {$data['population_density']} people/km²

Your task is to generate a scientific explanation and policy recommendation. Write in formal Indonesian (Bahasa Indonesia ilmiah). Keep sentences concise, clear, and highly focused to minimize token size.

Return ONLY a valid JSON object with these exact keys (no markdown formatting outside the JSON code block):
{
  "scientific_explanation": "<1-2 paragraf singkat penjelasan ilmiah yang menjelaskan MENGAPA nilai MCVI dan MRPS tersebut terjadi, mengacu pada korelasi antar pilar IPCC AR6 dan kondisi ekologis kawasan>",
  "restoration_recommendation": "<Rekomendasi konkret 3 poin singkat untuk pemerintah daerah, spesifik pada spesies {$speciesName} dan substrat {$soilType}>"
}
PROMPT;
    }

    private function formatCO2(float $carbon): string
    {
        return number_format($carbon * 3.67, 1);
    }

    private function decodeJson(string $text): ?array
    {
        if (!$text) {
            return null;
        }

        $start = strpos($text, '{');
        $end   = strrpos($text, '}');

        if ($start === false || $end === false) {
            return null;
        }

        $json    = substr($text, $start, $end - $start + 1);
        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Fallback Explanation — digunakan jika API Gemini tidak tersedia
     * Menghasilkan narasi deterministik berdasarkan threshold IPCC AR6
     */
    private function buildFallbackExplanation(array $data, array $computed): array
    {
        $priority = $computed['restoration_priority'];
        $risk     = $computed['climate_risk_score'];
        $mcvi     = $computed['mcvi'];
        $species  = $data['dominant_species'] ?? 'Rhizophora apiculata';

        $riskCategory = $risk >= 70 ? 'sangat tinggi' : ($risk >= 50 ? 'sedang-tinggi' : 'rendah-sedang');

        $explanation = "Berdasarkan analisis menggunakan kerangka risiko IPCC AR6, wilayah {$data['region_name']} "
            . "menunjukkan tingkat risiko iklim kategori {$riskCategory} dengan skor {$risk}/100. "
            . "Pilar Hazard (H={$computed['hazard_score']}) mencerminkan intensitas ancaman fisik dari abrasi pantai "
            . "dan risiko banjir rob yang terukur melalui parameter lapangan. "
            . "Pilar Exposure (E={$computed['exposure_score']}) menunjukkan seberapa besar kapasitas ekosistem dan komunitas "
            . "yang terekspos langsung terhadap ancaman tersebut, sementara Pilar Vulnerability (V={$computed['vulnerability_score']}) "
            . "merefleksikan kondisi ekologi kawasan setelah kehilangan tutupan mangrove sebesar {$data['mangrove_loss']}%. "
            . "MCVI sebesar {$mcvi}/100 mengindikasikan bahwa kawasan ini memerlukan perhatian kebijakan segera "
            . "untuk mencegah kaskade degradasi ekosistem yang irreversibel.";

        $recommendation = "Rekomendasi prioritas {$priority} untuk wilayah {$data['region_name']}: "
            . "(1) Lakukan rehabilitasi segera dengan penanaman jenis {$species} yang adaptif terhadap substrat setempat, "
            . "dengan kerapatan penanaman mengacu pada standar Kementerian Lingkungan Hidup dan Kehutanan RI. "
            . "(2) Evaluasi cadangan blue carbon sebesar {$computed['carbon_potential']} ton C sebagai aset potensial "
            . "dalam mekanisme pembayaran jasa lingkungan (PES) dan karbon kredit mangrove. "
            . "(3) Integrasikan data ini ke dalam Rencana Aksi Daerah Penurunan Emisi GRK (RAD-GRK) "
            . "dan dokumen RPJMD sebagai basis kebijakan pesisir yang berbasis data.";

        return [
            'scientific_explanation'    => $explanation,
            'restoration_recommendation' => $recommendation,
        ];
    }

    /**
     * @deprecated Gunakan generateExplanation() — metode lama dihapus
     * agar AI tidak lagi menentukan skoring secara langsung
     */
    public function analyze(array $data): array
    {
        // Redirect ke ScientificCalculationService untuk kalkulasi
        $calc     = app(ScientificCalculationService::class);
        $computed = $calc->compute($data);
        $ai       = $this->generateExplanation($data, $computed);

        return array_merge($computed, [
            'scientific_explanation'    => $ai['scientific_explanation'] ?? '',
            'restoration_recommendation' => $ai['restoration_recommendation'] ?? '',
        ]);
    }
}
