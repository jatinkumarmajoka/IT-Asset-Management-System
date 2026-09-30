@extends('layouts.app')

@section('page-title', 'Categories')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="mb-1">Categories</h2>

        <p class="text-muted mb-0">
            Manage asset categories.
        </p>
    </div>

    <a
        href="{{ route('categories.create') }}"
        class="btn btn-primary"
    >
        <i class="bi bi-plus-lg me-1"></i>
        Add Category
    </a>

</div>


<div class="card shadow-sm table-card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Assets</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($categories as $category)

                        <tr>

                            <td>
                                <span class="fw-semibold">
                                    {{ $category->name }}
                                </span>
                            </td>

                            <td>
                                {{ $category->description ?: '—' }}
                            </td>

                            <td>
                                <span class="badge text-bg-primary">
                                    {{ $category->assets_count }}
                                </span>
                            </td>

                            <td>

                                @if($category->status === 'active')

                                    <span class="badge text-bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Active
                                    </span>

                                @else

                                    <span class="badge text-bg-secondary">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td class="text-end">

                                <a
                                    href="{{ route('categories.edit', $category) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    <i class="bi bi-pencil me-1"></i>
                                    Edit
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-muted py-5"
                            >

                                <i class="bi bi-tags fs-2 d-block mb-2"></i>

                                No categories found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-3">

            {{ $categories->links() }}

        </div>

    </div>

</div>

@endsection