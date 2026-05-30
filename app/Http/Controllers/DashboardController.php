<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $userId = auth()->id();

        // 1. Fetch real stats
        $totalAnalyses = Analysis::where('user_id', $userId)->count();
        $avgRiskScore = round(Analysis::where('user_id', $userId)->avg('climate_risk_score') ?? 0, 1);
        $totalCarbon = round(Analysis::where('user_id', $userId)->sum('carbon_potential') ?? 0, 2);
        $highPriorityCount = Analysis::where('user_id', $userId)
            ->whereIn('restoration_priority', ['Tinggi', 'High'])
            ->count();

        // 2. Recent Analyses
        $recentAnalysis = Analysis::where('user_id', $userId)
            ->latest()
            ->take(4)
            ->get(['id', 'region_name', 'province', 'climate_risk_score', 'created_at'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'region' => $item->region_name . ' - ' . $item->province,
                    'risk' => $item->climate_risk_score,
                    'date' => $item->created_at->format('d M Y')
                ];
            });

        // 3. Priority Regions list
        $priorityRegions = Analysis::where('user_id', $userId)
            ->whereIn('restoration_priority', ['Tinggi', 'High'])
            ->latest()
            ->take(3)
            ->get(['region_name', 'restoration_priority', 'dominant_species'])
            ->map(function ($item) {
                return [
                    'name' => $item->region_name,
                    'note' => "Spesies: {$item->dominant_species} · Prioritas: {$item->restoration_priority}"
                ];
            });

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'avg_risk' => $avgRiskScore,
                'total_carbon' => $totalCarbon,
                'high_priority' => $highPriorityCount,
                'total_analyses' => $totalAnalyses,
            ],
            'recentAnalysis' => $recentAnalysis,
            'priorityRegions' => $priorityRegions,
        ]);
    }
}
