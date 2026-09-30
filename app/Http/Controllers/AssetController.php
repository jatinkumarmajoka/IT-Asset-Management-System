<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;
use App\Services\AssetService;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct(private AssetService $assetService) {}

    public function index(Request $request)
    {
        $query = Asset::with(['department', 'category', 'occupant']);

        if ($request->filled('search')) {
            $term = trim($request->string('search'));
            $query->where(function ($q) use ($term) {
                $q->where('asset_code', 'like', "%{$term}%")
                  ->orWhere('item_name', 'like', "%{$term}%")
                  ->orWhere('make', 'like', "%{$term}%")
                  ->orWhere('model', 'like', "%{$term}%")
                  ->orWhere('barcode_number', 'like', "%{$term}%")
                  ->orWhere('asset_serial_number', 'like', "%{$term}%");
            });
        }

        $query->when($request->filled('department_id'),
            fn ($q) => $q->where('department_id', $request->integer('department_id'))
        );

        $query->when($request->filled('category_id'),
            fn ($q) => $q->where('category_id', $request->integer('category_id'))
        );

        $query->when($request->filled('asset_status'),
            fn ($q) => $q->where('asset_status', $request->string('asset_status'))
        );

        $assets = $query->latest()->paginate(15)->withQueryString();

        $departments = Department::where('status', 'active')->orderBy('name')->get();
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('assets.index', compact('assets', 'departments', 'categories'));
    }

    public function create()
    {
        return view('assets.create', [
            'departments' => Department::where('status', 'active')->orderBy('name')->get(),
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAssetRequest $request)
    {
        $this->assetService->create($request->validated());

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['department', 'category', 'occupant', 'history']);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit', [
            'asset' => $asset,
            'departments' => Department::where('status', 'active')->orderBy('name')->get(),
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $this->assetService->update($asset, $request->validated());

        return redirect()->route('assets.show', $asset)->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->history()->create([
            'action' => 'deleted',
            'old_value' => json_encode($asset->toArray()),
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }
}
