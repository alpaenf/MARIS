<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Analysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Dataset;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(Request $request): Response
    {
        // 1. Fetch Users
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($user) {
                // Since there is no 'active' column in migration 0001, we will mock or dynamically set it to true
                $user->active = true;
                return $user;
            });

        // 2. Fetch Analyses
        $analyses = Analysis::latest()->take(10)->get();

        // 3. Count Stats
        $stats = [
            'users_count' => User::count(),
            'regions_count' => Analysis::distinct('region_name')->count('region_name'),
            'total_analyses' => Analysis::count(),
            'total_simulations' => 92, // Mocked simulation run count
            'total_carbon' => round(Analysis::sum('carbon_potential'), 2)
        ];

        // 4. Fetch Datasets
        $datasets = Dataset::latest()->get()->map(function ($ds) {
            return [
                'id' => $ds->id,
                'name' => $ds->name,
                'type' => $ds->type,
                'records' => $ds->records,
                'date' => $ds->created_at->format('d M Y')
            ];
        });

        // 5. Calculate real TrendBars
        $months = [];
        for ($i = 4; $i >= 0; $i--) {
            $date = now()->subMonthsNoOverflow($i);
            $months[] = [
                'label' => Carbon::parse($date)->locale('id')->isoFormat('MMM'),
                'count' => Analysis::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count()
            ];
        }
        $maxCount = collect($months)->max('count') ?: 1;
        $trendBars = collect($months)->map(function ($item) use ($maxCount) {
            return [
                'label' => $item['label'],
                'height' => round(($item['count'] / $maxCount) * 90 + 10) . '%' // min 10% height
            ];
        });

        return Inertia::render('Admin/Index', [
            'initialUsers' => $users,
            'initialAnalyses' => $analyses,
            'stats' => $stats,
            'datasets' => $datasets,
            'trendBars' => $trendBars,
        ]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|string|in:admin,analis',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back();
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|in:admin,analis',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        return redirect()->back();
    }

    public function storeRegion(Request $request)
    {
        $request->validate([
            'region_name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'area_size' => 'required|numeric|min:0',
        ]);

        // Creating custom base analysis records for new region
        Analysis::create([
            'user_id' => auth()->id(),
            'seeded_by_admin' => true,
            'region_name' => $request->region_name,
            'province' => $request->province,
            'area_size' => $request->area_size,
            'rainfall' => 2000,
            'abrasion_level' => 3,
            'flood_risk' => 3,
            'mangrove_loss' => 20,
            'population_density' => 1000,
            'climate_risk_score' => 50,
            'restoration_priority' => 'Medium',
            'carbon_potential' => round($request->area_size * 2.1, 2)
        ]);

        return redirect()->back();
    }

    public function destroyRegion(Analysis $analysis)
    {
        $analysis->delete();
        return redirect()->back();
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $csvData = file_get_contents($file->getRealPath());
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        $header = array_shift($rows);

        // Standardize header columns to match schema mapping
        $mappings = [
            'name' => ['region_name', 'nama', 'wilayah', 'region', 'name', 'nama wilayah', 'kabupaten', 'kota'],
            'province' => ['province', 'provinsi', 'lokasi', 'location', 'wilayah provinsi'],
            'area' => ['area_size', 'luas', 'area', 'size', 'luas area', 'luas wilayah']
        ];

        foreach ($rows as $row) {
            if (count($row) < count($header) || empty($row[0])) continue;
            
            $mappedData = [];
            foreach ($header as $index => $colName) {
                $cleanCol = strtolower(trim($colName));
                
                // 1. Exact or Synonym match
                $matchedKey = null;
                foreach ($mappings as $key => $synonyms) {
                    if (in_array($cleanCol, $synonyms, true)) {
                        $matchedKey = $key;
                        break;
                    }
                }
                
                // 2. Fuzzy Matching fallback (Levenshtein distance)
                if (!$matchedKey) {
                    $bestScore = 999;
                    $bestKey = null;
                    foreach ($mappings as $key => $synonyms) {
                        foreach ($synonyms as $synonym) {
                            $dist = levenshtein($cleanCol, $synonym);
                            if ($dist < $bestScore && $dist <= 3) { // Threshold max 3 edits difference
                                $bestScore = $dist;
                                $bestKey = $key;
                            }
                        }
                    }
                    if ($bestKey) {
                        $matchedKey = $bestKey;
                    }
                }

                if ($matchedKey) {
                    $mappedData[$matchedKey] = $row[$index];
                }
            }

            if (!empty($mappedData['name'])) {
                Analysis::create([
                    'user_id' => auth()->id(),
                    'seeded_by_admin' => true,
                    'region_name' => $mappedData['name'],
                    'province' => $mappedData['province'] ?? 'Jawa Tengah',
                    'area_size' => floatval($mappedData['area'] ?? 100),
                    'rainfall' => rand(1500, 2500),
                    'abrasion_level' => rand(1, 5),
                    'flood_risk' => rand(1, 5),
                    'mangrove_loss' => rand(10, 80),
                    'population_density' => rand(500, 4000),
                    'climate_risk_score' => rand(40, 90),
                    'restoration_priority' => rand(0, 1) ? 'Tinggi' : 'Sedang',
                    'carbon_potential' => floatval($mappedData['area'] ?? 100) * 2.1
                ]);
            }
        }

        return redirect()->back();
    }

    public function destroyUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['message' => 'Anda tidak bisa menghapus akun sendiri.']);
        }

        $user->delete();
        return redirect()->back();
    }

    public function storeDataset(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,json|max:10240', // max 10MB
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->store('datasets', 'public');
        
        $recordsCount = 0;
        if (in_array($extension, ['csv', 'txt'])) {
            $lines = file($file->getRealPath());
            $recordsCount = count($lines) - 1; // minus header
        } elseif ($extension === 'json') {
            $json = json_decode(file_get_contents($file->getRealPath()), true);
            if (is_array($json)) {
                $recordsCount = count($json);
            }
        }

        Dataset::create([
            'name' => $file->getClientOriginalName(),
            'type' => $extension === 'json' ? 'Parameter (JSON)' : 'Geospasial (CSV)',
            'records' => max(0, $recordsCount),
            'file_path' => $path,
            'file_size' => $file->getSize(),
        ]);

        return redirect()->back();
    }

    public function destroyDataset(Dataset $dataset)
    {
        if (Storage::disk('public')->exists($dataset->file_path)) {
            Storage::disk('public')->delete($dataset->file_path);
        }
        $dataset->delete();
        return redirect()->back();
    }
}
