<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'assets' => Asset::count(),
            'departments' => Department::count(),
            'categories' => Category::count(),
            'employees' => Employee::count(),
            'verified' => Asset::whereColumn('issued_quantity', '=', 'physically_verified_quantity')->count(),
            'pending' => Asset::whereColumn('issued_quantity', '!=', 'physically_verified_quantity')->count(),
        ];

        $departmentCounts = Department::query()
            ->withCount('assets')
            ->orderByDesc('assets_count')
            ->limit(10)
            ->get();

        $recentAssets = Asset::with(['department', 'category', 'occupant'])
            ->latest()
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'departmentCounts', 'recentAssets'));
    }
}
