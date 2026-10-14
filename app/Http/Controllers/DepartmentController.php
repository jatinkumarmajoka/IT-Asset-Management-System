<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    /**
     * Display all departments.
     */
    public function index(Request $request)
    {
        $departments = Department::withCount([
            'assets',
            'employees',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the create department form.
     */
    public function create()
    {
        return view('departments.create');
    }

    /**
     * Store a new department.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:departments,code'],
            'type' => ['required', Rule::in(['academic', 'non_teaching'])],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $data['name'] = trim($data['name']);
        $data['code'] = strtoupper(trim($data['code']));

        Department::create($data);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Show the edit department form.
     */
    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    /**
     * Update an existing department.
     */
    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'code')->ignore($department->id),
            ],
            'type' => ['required', Rule::in(['academic', 'non_teaching'])],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $data['name'] = trim($data['name']);
        $data['code'] = strtoupper(trim($data['code']));

        $department->update($data);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Delete a department only when it has no related records.
     */
    public function destroy(Department $department)
    {
        $hasRelatedRecords = $department->assets()->exists()
            || $department->employees()->exists()
            || $department->subDepartments()->exists();

        if ($hasRelatedRecords) {
            return back()->with(
                'error',
                'Cannot delete this department while assets, employees, or sub-departments are associated with it.'
            );
        }

        $department->delete();

        return back()->with('success', 'Department deleted successfully.');
    }

    /**
     * Return assets allocated to a department for the View Assets modal.
     */
    public function assets(Department $department)
    {
        $assets = $department->assets()
            ->with(['department', 'occupant', 'presentSubDepartment'])
            ->orderBy('item_name')
            ->get();

        return response()->json([
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
                'code' => $department->code,
            ],
            'assets' => $assets->map(function ($asset) {
                $occupant = $asset->occupant?->name
                    ?? $asset->present_occupant_name
                    ?? $asset->department?->name;

                return [
                    'id' => $asset->id,
                    'asset_code' => $asset->asset_code,
                    'item_name' => $asset->item_name,
                    'make' => $asset->make,
                    'model' => $asset->model,
                    'issued_quantity' => $asset->issued_quantity,
                    'physically_verified_quantity' => $asset->physically_verified_quantity,
                    'occupant' => $occupant,
                    'sub_department' => $asset->presentSubDepartment?->name,
                    'barcode_number' => $asset->barcode_number,
                    'status' => $asset->asset_status,
                ];
            })->values(),
        ]);
    }
}
