<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Analisis MARIS</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 12px; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: 700; color: #0f766e; }
        .subtle { color: #64748b; }
        .section { margin-top: 16px; }
        .card { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Analisis MARIS</div>
        <div class="subtle">Mangrove Risk Intelligence System</div>
        <div class="subtle">ID Analisis: {{ $analysis->id }} • {{ $analysis->created_at?->format('d M Y') }}</div>
    </div>

    <div class="section">
        <div class="card">
            <table>
                <tr>
                    <th>Region</th>
                    <td>{{ $analysis->region_name }}</td>
                    <th>Provinsi</th>
                    <td>{{ $analysis->province }}</td>
                </tr>
                <tr>
                    <th>Luas Area (ha)</th>
                    <td>{{ $analysis->area_size }}</td>
                    <th>Curah Hujan</th>
                    <td>{{ $analysis->rainfall }}</td>
                </tr>
                <tr>
                    <th>Abrasi</th>
                    <td>{{ $analysis->abrasion_level }}</td>
                    <th>Risiko Banjir</th>
                    <td>{{ $analysis->flood_risk }}</td>
                </tr>
                <tr>
                    <th>Kehilangan Mangrove</th>
                    <td>{{ $analysis->mangrove_loss }}</td>
                    <th>Kepadatan Penduduk</th>
                    <td>{{ $analysis->population_density }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="card">
            <table>
                <tr>
                    <th>Climate Risk Score</th>
                    <td>{{ $analysis->climate_risk_score ?? '-' }}</td>
                    <th>Restoration Priority</th>
                    <td>{{ $analysis->restoration_priority ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Carbon Potential</th>
                    <td>{{ $analysis->carbon_potential ?? '-' }}</td>
                    <th>Status</th>
                    <td>{{ $analysis->restoration_priority ? 'Complete' : 'Pending' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="card">
            <strong>AI Explanation</strong>
            <p>{{ $analysis->ai_explanation ?? 'Belum ada output AI.' }}</p>
        </div>
        <div class="card">
            <strong>Restoration Recommendation</strong>
            <p>{{ $analysis->restoration_recommendation ?? 'Belum ada rekomendasi.' }}</p>
        </div>
    </div>
</body>
</html>
