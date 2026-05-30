<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        $analyses = Analysis::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->take(20)
            ->get([
                'id',
                'region_name',
                'province',
                'climate_risk_score',
                'restoration_priority',
                'created_at',
            ]);

        return Inertia::render('Reports/Index', [
            'analyses' => $analyses,
        ]);
    }

    public function export(Analysis $analysis): HttpResponse
    {
        abort_unless($analysis->user_id === auth()->id(), 403);

        $pdf = Pdf::loadView('reports.analysis', [
            'analysis' => $analysis,
        ])->setPaper('a4');

        $filename = 'maris-report-'.$analysis->id.'.pdf';

        return $pdf->download($filename);
    }
}
