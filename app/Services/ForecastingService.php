<?php

namespace App\Services;

use App\Models\HourlySnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * MARIS 2.0 Climate Forecasting Engine
 *
 * Mengimplementasikan model prediktif berbasis time-series untuk
 * memprediksi tren risiko iklim pesisir 7 hari ke depan.
 *
 * Metode:
 * - Holt's Linear Exponential Smoothing (double exponential smoothing)
 * - Linear Regression untuk trend detection
 * - 95% Confidence Interval (±1.96σ)
 *
 * @standard WMO Guidelines on Seasonal Forecasting (2017)
 * @standard IPCC AR6 WG1 Chapter 4 — Near-term Climate Projections
 * @author   MARIS 2.0 Development Team
 */
class ForecastingService
{
    // Smoothing parameters (Holt's method)
    private const ALPHA = 0.3;  // Level smoothing (responsiveness to recent data)
    private const BETA  = 0.1;  // Trend smoothing (responsiveness to trend changes)

    // Forecast horizon
    private const FORECAST_DAYS = 7;

    /**
     * Generate 7-day climate risk forecast for a specific region.
     *
     * @param string $regionName Nama wilayah yang akan diprediksi
     * @return array{
     *   region: string,
     *   historical: array,
     *   forecast: array,
     *   trend: string,
     *   confidence: float,
     *   model_metrics: array
     * }
     */
    public function forecastRegion(string $regionName): array
    {
        // Ambil data historis 30 hari terakhir (1 data per hari = daily aggregation)
        $historical = $this->getHistoricalDaily($regionName, 30);

        if ($historical->count() < 3) {
            return $this->buildEmptyForecast($regionName, 'Insufficient data (minimum 3 days required)');
        }

        $values = $historical->pluck('value')->toArray();
        $dates  = $historical->pluck('date')->toArray();

        // ── STEP 1: Holt's Linear Exponential Smoothing ──
        $holtResult = $this->holtSmoothing($values);

        // ── STEP 2: Generate forecast points ──
        $forecastPoints = $this->generateForecast($holtResult, $dates);

        // ── STEP 3: Linear Regression for trend detection ──
        $regression = $this->linearRegression($values);

        // ── STEP 4: Model evaluation metrics ──
        $metrics = $this->evaluateModel($values, $holtResult['fitted']);

        // ── STEP 5: Trend classification ──
        $trend = $this->classifyTrend($regression['slope'], $metrics['mae']);

        return [
            'region'     => $regionName,
            'historical' => $historical->map(fn ($item) => [
                'date'  => $item['date'],
                'value' => round($item['value'], 2),
            ])->values()->toArray(),
            'forecast'   => $forecastPoints,
            'trend'      => $trend,
            'confidence' => round(max(0, 100 - $metrics['mape']), 1),
            'model_metrics' => [
                'mae'        => round($metrics['mae'], 3),
                'rmse'       => round($metrics['rmse'], 3),
                'mape'       => round($metrics['mape'], 2),
                'r_squared'  => round($regression['r_squared'], 4),
                'slope'      => round($regression['slope'], 4),
                'method'     => "Holt's Linear Exponential Smoothing (α=" . self::ALPHA . ", β=" . self::BETA . ")",
            ],
        ];
    }

    /**
     * Generate forecast for ALL monitored regions.
     */
    public function forecastAll(): array
    {
        $regions = HourlySnapshot::query()
            ->where('source', 'bmkg.prakiraan-cuaca')
            ->distinct('region_name')
            ->pluck('region_name');

        return $regions->map(fn ($name) => $this->forecastRegion($name))->toArray();
    }

    /**
     * Aggregate hourly snapshots into daily MCVI averages.
     */
    private function getHistoricalDaily(string $regionName, int $days): Collection
    {
        $since = now()->subDays($days)->startOfDay();

        $snapshots = HourlySnapshot::query()
            ->where('source', 'bmkg.prakiraan-cuaca')
            ->where('region_name', $regionName)
            ->where('snapshot_at', '>=', $since)
            ->orderBy('snapshot_at')
            ->get(['snapshot_at', 'mcvi_score', 'hazard_score', 'mrps_score']);

        if ($snapshots->isEmpty()) {
            return collect();
        }

        // Group by date, average MCVI per day
        return $snapshots
            ->groupBy(fn ($s) => Carbon::parse($s->snapshot_at)->toDateString())
            ->map(function ($group, $date) {
                return [
                    'date'  => $date,
                    'value' => $group->avg('mcvi_score') ?? 0,
                    'hazard_avg' => $group->avg('hazard_score') ?? 0,
                    'mrps_avg'   => $group->avg('mrps_score') ?? 0,
                ];
            })
            ->values();
    }

    /**
     * Holt's Linear Exponential Smoothing (Double Exponential Smoothing).
     *
     * Suitable for time-series with trend but no seasonality.
     * Formula:
     *   Level:  L_t = α × Y_t + (1 - α) × (L_{t-1} + T_{t-1})
     *   Trend:  T_t = β × (L_t - L_{t-1}) + (1 - β) × T_{t-1}
     *   Forecast: F_{t+k} = L_t + k × T_t
     *
     * @param float[] $values Time-series values
     * @return array{level: float, trend: float, fitted: float[], residuals: float[]}
     */
    private function holtSmoothing(array $values): array
    {
        $n = count($values);

        // Initialize: Level = first value, Trend = difference of first two values
        $level = $values[0];
        $trend = ($n >= 2) ? ($values[1] - $values[0]) : 0;

        $fitted    = [$level];
        $residuals = [0];

        for ($t = 1; $t < $n; $t++) {
            $prevLevel = $level;

            // Update level
            $level = self::ALPHA * $values[$t] + (1 - self::ALPHA) * ($prevLevel + $trend);

            // Update trend
            $trend = self::BETA * ($level - $prevLevel) + (1 - self::BETA) * $trend;

            $fitted[]    = $level + $trend;
            $residuals[] = $values[$t] - ($level + $trend);
        }

        return [
            'level'     => $level,
            'trend'     => $trend,
            'fitted'    => $fitted,
            'residuals' => $residuals,
        ];
    }

    /**
     * Generate forecast points with confidence intervals.
     */
    private function generateForecast(array $holtResult, array $historicalDates): array
    {
        $lastDate = Carbon::parse(end($historicalDates));
        $level    = $holtResult['level'];
        $trend    = $holtResult['trend'];
        $residuals = $holtResult['residuals'];

        // Calculate standard deviation of residuals for CI
        $sigma = $this->standardDeviation($residuals);

        $forecast = [];
        for ($k = 1; $k <= self::FORECAST_DAYS; $k++) {
            $pointForecast = $level + $k * $trend;

            // Confidence interval widens with forecast horizon
            // CI = ±z × σ × √k (prediction error grows with √k)
            $ciWidth = 1.96 * $sigma * sqrt($k);

            $forecast[] = [
                'date'       => $lastDate->copy()->addDays($k)->toDateString(),
                'predicted'  => round(max(0, min(100, $pointForecast)), 2),
                'lower_ci'   => round(max(0, $pointForecast - $ciWidth), 2),
                'upper_ci'   => round(min(100, $pointForecast + $ciWidth), 2),
                'horizon_day' => $k,
            ];
        }

        return $forecast;
    }

    /**
     * Simple Linear Regression: y = mx + b
     * Used for overall trend detection and R² calculation.
     */
    private function linearRegression(array $values): array
    {
        $n    = count($values);
        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0; $sumY2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX  += $i;
            $sumY  += $values[$i];
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
            $sumY2 += $values[$i] * $values[$i];
        }

        $denominator = $n * $sumX2 - $sumX * $sumX;
        if ($denominator == 0) {
            return ['slope' => 0, 'intercept' => $values[0] ?? 0, 'r_squared' => 0];
        }

        $slope     = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $intercept = ($sumY - $slope * $sumX) / $n;

        // R² (coefficient of determination)
        $ssRes = 0; $ssTot = 0;
        $meanY = $sumY / $n;
        for ($i = 0; $i < $n; $i++) {
            $predicted = $slope * $i + $intercept;
            $ssRes += pow($values[$i] - $predicted, 2);
            $ssTot += pow($values[$i] - $meanY, 2);
        }
        $rSquared = $ssTot > 0 ? 1 - ($ssRes / $ssTot) : 0;

        return [
            'slope'     => $slope,
            'intercept' => $intercept,
            'r_squared' => max(0, $rSquared),
        ];
    }

    /**
     * Calculate model evaluation metrics.
     */
    private function evaluateModel(array $actual, array $fitted): array
    {
        $n   = min(count($actual), count($fitted));
        $mae = 0; $mse = 0; $mape = 0;
        $validMape = 0;

        for ($i = 0; $i < $n; $i++) {
            $error = abs($actual[$i] - $fitted[$i]);
            $mae += $error;
            $mse += $error * $error;

            if ($actual[$i] != 0) {
                $mape += ($error / abs($actual[$i])) * 100;
                $validMape++;
            }
        }

        return [
            'mae'  => $n > 0 ? $mae / $n : 0,
            'rmse' => $n > 0 ? sqrt($mse / $n) : 0,
            'mape' => $validMape > 0 ? $mape / $validMape : 0,
        ];
    }

    /**
     * Classify trend direction based on regression slope.
     */
    private function classifyTrend(float $slope, float $mae): string
    {
        $threshold = max(0.5, $mae * 0.1); // Adaptive threshold

        if ($slope > $threshold) return 'increasing';  // Risiko meningkat
        if ($slope < -$threshold) return 'decreasing'; // Risiko menurun
        return 'stable'; // Risiko stabil
    }

    /**
     * Standard deviation helper.
     */
    private function standardDeviation(array $values): float
    {
        $n = count($values);
        if ($n <= 1) return 0;

        $mean = array_sum($values) / $n;
        $variance = 0;
        foreach ($values as $v) {
            $variance += pow($v - $mean, 2);
        }

        return sqrt($variance / ($n - 1));
    }

    /**
     * Build empty forecast response when data is insufficient.
     */
    private function buildEmptyForecast(string $regionName, string $reason): array
    {
        return [
            'region'        => $regionName,
            'historical'    => [],
            'forecast'      => [],
            'trend'         => 'unknown',
            'confidence'    => 0,
            'model_metrics' => [
                'error' => $reason,
                'method' => "Holt's Linear Exponential Smoothing",
            ],
        ];
    }
}
