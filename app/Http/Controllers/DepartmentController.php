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
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search = $request->string('search');

                    $query->where(function ($query) use ($search) {

                        $query->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        )->orWhere(
                            'code',
                            'like',
                            '%' . $search . '%'
                        );

                    });

                }
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'departments.index',
            compact('departments')
        );
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

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                'unique:departments,code',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

        ]);

        Department::create($data);

        return redirect()
            ->route('departments.index')
            ->with(
                'success',
                'Department created successfully.'
            );
    }


    /**
     * Show the edit department form.
     */
    public function edit(Department $department)
    {
        return view(
            'departments.edit',
            compact('department')
        );
    }


    /**
     * Update an existing department.
     */
    public function update(
        Request $request,
        Department $department
    ) {
        $data = $request->validate([

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'departments',
                    'code'
                )->ignore($department->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

        ]);

        $department->update($data);

        return redirect()
            ->route('departments.index')
            ->with(
                'success',
                'Department updated successfully.'
            );
    }


    /**
     * Delete a department.
     *
     * A department cannot be deleted while
     * assets or employees are associated with it.
     */
    public function destroy(Department $department)
    {
        if (
            $department->assets()->exists() ||
            $department->employees()->exists()
        ) {
            return back()->with(
                'error',
                'Cannot delete a department that has assets or employees.'
            );
        }

        $department->delete();

        return back()->with(
            'success',
            'Department deleted successfully.'
        );
    }


    /**
     * Return assets allocated to a department.
     *
     * This endpoint is used by the View Assets
     * modal on the departments page.
     */
    public function assets(Department $department)
    {
        $assets = $department->assets()
            ->with([
                'department',
                'occupant',
            ])
            ->orderBy('item_name')
            ->get();

        return response()->json([

            'department' => [

                'id' =>
                    $department->id,

                'name' =>
                    $department->name,

                'code' =>
                    $department->code,

            ],

            'assets' => $assets->map(
                function ($asset) {

                    /*
                     * IMPORTANT:
                     *
                     * If an employee is assigned,
                     * return employee name.
                     *
                     * Otherwise return the department name.
                     *
                     * We DO NOT create a fake employee.
                     */
                    $occupant =
                        $asset->occupant?->name
                        ?? $asset->department?->name;


                    return [

                        'id' =>
                            $asset->id,

                        'asset_code' =>
                            $asset->asset_code,

                        'item_name' =>
                            $asset->item_name,

                        'make' =>
                            $asset->make,

                        'model' =>
                            $asset->model,

                        'issued_quantity' =>
                            $asset->issued_quantity,

                        'physically_verified_quantity' =>
                            $asset->physically_verified_quantity,

                        'occupant' =>
                            $occupant,

                        'barcode_number' =>
                            $asset->barcode_number,

                        'status' =>
                            $asset->asset_status,

                    ];

                }
            ),

        ]);
    }
}