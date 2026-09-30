@extends('layouts.app')

@section('page-title', 'Edit Category')

@section('content')

<div class="card shadow-sm table-card">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="mb-1">Edit Category</h3>

                <p class="text-muted mb-0">
                    Update asset category information.
                </p>
            </div>

            <a
                href="{{ route('categories.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Categories
            </a>

        </div>


        <form
            method="POST"
            action="{{ route('categories.update', $category) }}"
        >

            @csrf

            @method('PUT')


            <div class="row g-3">

                {{-- Category Name --}}
                <div class="col-md-6">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $category->name) }}"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Status --}}
                <div class="col-md-3">

                    <label
                        for="status"
                        class="form-label"
                    >
                        Status
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select @error('status') is-invalid @enderror"
                        required
                    >

                        <option
                            value="active"
                            @selected(
                                old(
                                    'status',
                                    $category->status
                                ) === 'active'
                            )
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            @selected(
                                old(
                                    'status',
                                    $category->status
                                ) === 'inactive'
                            )
                        >
                            Inactive
                        </option>

                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Description --}}
                <div class="col-12">

                    <label
                        for="description"
                        class="form-label"
                    >
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="4"
                        placeholder="Enter category description"
                    >{{ old('description', $category->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Actions --}}
            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    {{ $buttonText ?? 'Save Changes' }}
                </button>

                <a
                    href="{{ route('categories.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection