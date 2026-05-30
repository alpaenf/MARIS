<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Services\GeminiService;
use App\Services\ScientificCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalysisController extends Controller
{
    public function __construct(
        private readonly ScientificCalculationService $calculator,
        private readonly GeminiService $gemini,
    ) {}

    public function index(): Response
    {
        $analyses = Analysis::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get([
                'id',
                'region_name',
                'province',
                'climate_risk_score',
                'mcvi',
                'mrps',
                'restoration_priority',
                'carbon_potential',
                'result_payload',
                'created_at',
            ]);

        return Inertia::render('Analysis/Index', [
            'analyses' => $analyses,
        ]);
    }

    public function create(): Response
    {
        $regionsReady = Analysis::query()->where('seeded_by_admin', true)->exists();
        $presets = Analysis::query()
            ->where('seeded_by_admin', true)
            ->latest()
            ->take(4)
            ->get([
                'region_name',
                'province',
                'area_size',
                'rainfall',
                'abrasion_level',
                'flood_risk',
                'mangrove_loss',
                'population_density',
                'dominant_species',
                'soil_type',
            ])
            ->map(function ($item) {
                return [
                    'region_name' => $item->region_name,
                    'province' => $item->province,
                    'area_size' => $item->area_size,
                    'rainfall' => $item->rainfall,
                    'abrasion_level' => min(5, max(0, (int) round($item->abrasion_level))),
                    'flood_risk' => min(5, max(0, (int) round($item->flood_risk))),
                    'mangrove_loss' => $item->mangrove_loss,
                    'population_density' => $item->population_density,
                    'dominant_species' => $item->dominant_species ?: 'Rhizophora mucronata',
                    'soil_type' => $item->soil_type ?: 'mineral',
                ];
            });

        return Inertia::render('Analysis/Create', [
            'aiAvailable'      => (bool) config('services.gemini.key'),
            'availableSpecies' => ScientificCalculationService::availableSpecies(),
            'regionsReady'      => $regionsReady,
            'presetDatasets'    => $presets,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $regionsReady = Analysis::query()->where('seeded_by_admin', true)->exists();
        if (!$regionsReady) {
            return redirect()->back()->withErrors([
                'regions' => 'Admin harus menyiapkan dataset wilayah terlebih dahulu.',
            ]);
        }

        $validated = $request->validate([
            'region_name'        => ['required', 'string'],
            'province'           => ['required', 'string'],
            'area_size'          => ['required', 'numeric', 'min:0.1'],
            'rainfall'           => ['required', 'integer', 'min:0', 'max:10000'],
            'abrasion_level'     => ['required', 'integer', 'min:0', 'max:5'],
            'flood_risk'         => ['required', 'integer', 'min:0', 'max:5'],
            'mangrove_loss'      => ['required', 'integer', 'min:0', 'max:100'],
            'population_density' => ['required', 'integer', 'min:0'],
            'dominant_species'   => ['required', 'string'],
            'soil_type'          => ['required', 'string', 'in:mineral,organik'],
            'ai_engine'          => ['nullable', 'string', 'in:gemini,rule_based'],
        ]);

        // ── STEP 1: Kalkulasi Ilmiah Deterministik ──────────────────
        // Semua penghitungan dilakukan oleh ScientificCalculationService
        // BUKAN oleh AI. Ini memastikan auditabilitas dan reprodusibilitas.
        $computed = $this->calculator->compute($validated);

        // ── STEP 2: AI sebagai Explainable Layer (Opsional) ─────────
        // AI hanya menghasilkan narasi & rekomendasi berbasis konteks,
        // BUKAN menentukan skor atau prioritas restorasi.
        $aiEngine = $validated['ai_engine'] ?? 'gemini';

        if ($aiEngine === 'rule_based') {
            $aiOutput = [
                'scientific_explanation'    => $this->buildRuleBasedExplanation($validated, $computed),
                'restoration_recommendation' => $this->buildRuleBasedRecommendation($validated, $computed),
            ];
        } else {
            $aiOutput = $this->gemini->generateExplanation($validated, $computed);
        }

        // ── STEP 3: Simpan Analisis ke Database ─────────────────────
        $analysis = Analysis::create([
            'user_id'             => $request->user()->id,
            'seeded_by_admin'      => false,
            'region_name'         => $validated['region_name'],
            'province'            => $validated['province'],
            'area_size'           => $validated['area_size'],
            'rainfall'            => $validated['rainfall'],
            'abrasion_level'      => $validated['abrasion_level'],
            'flood_risk'          => $validated['flood_risk'],
            'mangrove_loss'       => $validated['mangrove_loss'],
            'population_density'  => $validated['population_density'],
            'dominant_species'    => $validated['dominant_species'],
            'soil_type'           => $validated['soil_type'],
            // IPCC AR6 Pillars
            'hazard_score'        => $computed['hazard_score'],
            'exposure_score'      => $computed['exposure_score'],
            'vulnerability_score' => $computed['vulnerability_score'],
            // Core Indices (dedicated columns)
            'climate_risk_score'  => $computed['climate_risk_score'],
            'mcvi'                => $computed['mcvi'],
            'mrps'                => $computed['mrps'],
            'restoration_priority' => $computed['restoration_priority'],
            // Blue Carbon Tier-2
            'carbon_agb'          => $computed['carbon_agb'],
            'carbon_bgb'          => $computed['carbon_bgb'],
            'carbon_soc'          => $computed['carbon_soc'],
            'carbon_potential'    => $computed['carbon_potential'],
            // AI Explanation (XAI Layer)
            'ai_explanation'      => $aiOutput['scientific_explanation'] ?? null,
            'restoration_recommendation' => $aiOutput['restoration_recommendation'] ?? null,
            // Full Payloads
            'result_payload'      => array_merge($computed, $aiOutput),
            'dpsir_payload'       => $computed['dpsir_payload'],
        ]);

        return redirect()->route('analysis.show', ['id' => $analysis->id]);
    }

    public function show(Request $request, int $id): Response
    {
        $analysis = Analysis::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return Inertia::render('Analysis/Show', [
            'analysis' => $analysis,
        ]);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $analysis = Analysis::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $analysis->delete();

        return redirect()->route('analysis.index');
    }

    // ─── Rule-Based Explanation (tanpa API) ──────────────────────
    private function buildRuleBasedExplanation(array $data, array $computed): string
    {
        $riskCat = $computed['climate_risk_score'] >= 70 ? 'sangat tinggi'
            : ($computed['climate_risk_score'] >= 50 ? 'tinggi' : 'sedang');

        return "Analisis menggunakan Rule-Based Expert System MARIS berbasis metodologi IPCC AR6. "
            . "Wilayah {$data['region_name']} menunjukkan risiko iklim {$riskCat} dengan skor "
            . "{$computed['climate_risk_score']}/100 (MCVI: {$computed['mcvi']}/100). "
            . "Kondisi ini dipicu oleh interaksi antara tekanan abrasi ({$data['abrasion_level']}/5), "
            . "risiko banjir ({$data['flood_risk']}/5), dan kehilangan mangrove {$data['mangrove_loss']}%. "
            . "Total cadangan blue carbon yang terancam: {$computed['carbon_potential']} ton C "
            . "(AGB: {$computed['carbon_agb']} | BGB: {$computed['carbon_bgb']} | SOC: {$computed['carbon_soc']} ton C), "
            . "ekuivalen dengan " . round($computed['carbon_potential'] * 3.67, 1) . " ton CO₂.";
    }

    private function buildRuleBasedRecommendation(array $data, array $computed): string
    {
        $species = $data['dominant_species'];
        $actions = $computed['dpsir_payload']['responses']['actions'] ?? [];
        return implode(' ', array_map(fn($a, $i) => ($i + 1) . ". {$a}", $actions, array_keys($actions)));
    }
}
