<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('category')->orderBy('purchase_date', 'desc')->paginate(15);
        
        $totalAssets = Asset::sum('current_value');
        $totalPurchase = Asset::sum('purchase_price');
        $totalDepreciation = Asset::sum('purchase_price') - Asset::sum('current_value');
        $activeAssets = Asset::where('status', 'active')->count();
        
        return view('assets.index', compact('assets', 'totalAssets', 'totalPurchase', 'totalDepreciation', 'activeAssets'));
    }

    public function create()
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_code' => 'required|unique:assets',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'serial_number' => 'nullable|string',
            'location' => 'required|string',
            'assigned_to' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate current value based on depreciation
            $category = AssetCategory::find($request->category_id);
            $purchaseDate = Carbon::parse($request->purchase_date);
            $monthsOld = $purchaseDate->diffInMonths(Carbon::now());
            $depreciationRate = $category->depreciation_rate / 100; // Convert to decimal
            $annualDepreciation = $request->purchase_price * $depreciationRate;
            $monthlyDepreciation = $annualDepreciation / 12;
            $totalDepreciation = $monthlyDepreciation * $monthsOld;
            $currentValue = max(0, $request->purchase_price - $totalDepreciation);

            $asset = Asset::create([
                'asset_code' => $request->asset_code,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'purchase_price' => $request->purchase_price,
                'current_value' => $currentValue,
                'purchase_date' => $request->purchase_date,
                'supplier' => $request->supplier,
                'serial_number' => $request->serial_number,
                'location' => $request->location,
                'assigned_to' => $request->assigned_to,
                'status' => 'active',
                'remarks' => $request->remarks
            ]);

            DB::commit();

            return redirect()->route('assets.index')
                ->with('success', 'Asset added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding asset: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $asset = Asset::with('category')->findOrFail($id);
        $depreciationSchedule = $this->calculateDepreciationSchedule($asset);
        
        return view('assets.show', compact('asset', 'depreciationSchedule'));
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $categories = AssetCategory::orderBy('name')->get();
        
        return view('assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        
        $request->validate([
            'asset_code' => 'required|unique:assets,asset_code,' . $id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'serial_number' => 'nullable|string',
            'location' => 'required|string',
            'status' => 'required|in:active,depreciated,disposed',
            'assigned_to' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Recalculate current value
            $category = AssetCategory::find($request->category_id);
            $purchaseDate = Carbon::parse($request->purchase_date);
            $monthsOld = $purchaseDate->diffInMonths(Carbon::now());
            $depreciationRate = $category->depreciation_rate / 100;
            $annualDepreciation = $request->purchase_price * $depreciationRate;
            $monthlyDepreciation = $annualDepreciation / 12;
            $totalDepreciation = $monthlyDepreciation * $monthsOld;
            $currentValue = max(0, $request->purchase_price - $totalDepreciation);

            $asset->update([
                'asset_code' => $request->asset_code,
                'name' => $request->name,
                'category_id' => $request->category_id,
                'purchase_price' => $request->purchase_price,
                'current_value' => $currentValue,
                'purchase_date' => $request->purchase_date,
                'supplier' => $request->supplier,
                'serial_number' => $request->serial_number,
                'location' => $request->location,
                'assigned_to' => $request->assigned_to,
                'status' => $request->status,
                'remarks' => $request->remarks
            ]);

            DB::commit();

            return redirect()->route('assets.show', $asset->id)
                ->with('success', 'Asset updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating asset: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $asset = Asset::findOrFail($id);
            $asset->delete();

            return redirect()->route('assets.index')
                ->with('success', 'Asset deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting asset: ' . $e->getMessage());
        }
    }

    public function depreciation()
    {
        $assets = Asset::with('category')->orderBy('purchase_date', 'desc')->get();
        
        // Calculate depreciation summary
        $totalOriginalCost = $assets->sum('purchase_price');
        $totalCurrentValue = $assets->sum('current_value');
        $totalDepreciation = $totalOriginalCost - $totalCurrentValue;
        $depreciationPercentage = $totalOriginalCost > 0 ? ($totalDepreciation / $totalOriginalCost) * 100 : 0;
        
        // Depreciation by category
        $depreciationByCategory = Asset::join('asset_categories', 'assets.category_id', '=', 'asset_categories.id')
            ->select('asset_categories.name',
                DB::raw('SUM(assets.purchase_price) as original_cost'),
                DB::raw('SUM(assets.current_value) as current_value'),
                DB::raw('SUM(assets.purchase_price) - SUM(assets.current_value) as depreciation'),
                DB::raw('COUNT(*) as count'))
            ->groupBy('asset_categories.id')
            ->get();
        
        // Assets nearing end of life (value < 10% of original)
        $nearingEndOfLife = $assets->filter(function($asset) {
            return $asset->current_value < ($asset->purchase_price * 0.1) && $asset->status == 'active';
        });
        
        // Fully depreciated assets
        $fullyDepreciated = $assets->filter(function($asset) {
            return $asset->current_value <= 0 || $asset->status == 'depreciated';
        });
        
        return view('assets.depreciation', compact('assets', 'totalOriginalCost', 'totalCurrentValue', 
            'totalDepreciation', 'depreciationPercentage', 'depreciationByCategory', 
            'nearingEndOfLife', 'fullyDepreciated'));
    }

    public function calculateDepreciation($id)
    {
        $asset = Asset::findOrFail($id);
        $schedule = $this->calculateDepreciationSchedule($asset);
        
        return response()->json($schedule);
    }

    private function calculateDepreciationSchedule($asset)
    {
        $schedule = [];
        $category = $asset->category;
        $purchaseDate = Carbon::parse($asset->purchase_date);
        $currentDate = Carbon::now();
        $yearsToCalculate = min(10, $currentDate->diffInYears($purchaseDate) + 5);
        
        $annualDepreciation = ($asset->purchase_price * ($category->depreciation_rate / 100));
        $remainingValue = $asset->purchase_price;
        
        for ($year = 1; $year <= $yearsToCalculate; $year++) {
            $yearDate = $purchaseDate->copy()->addYears($year);
            $depreciationAmount = ($year <= $currentDate->diffInYears($purchaseDate)) ? $annualDepreciation : 0;
            $remainingValue = max(0, $remainingValue - $depreciationAmount);
            
            $schedule[] = [
                'year' => $year,
                'year_end_date' => $yearDate->format('Y-m-d'),
                'depreciation' => $depreciationAmount,
                'remaining_value' => $remainingValue,
                'is_future' => $yearDate > $currentDate
            ];
        }
        
        return $schedule;
    }
}