@extends('layouts.app')

@section('page-title', 'Add Category')

@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">Add Category</h2>

            <p class="text-muted mb-0">
                Create a new asset category.
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


    {{-- Category Form --}}
    <div class="card shadow-sm border-0">

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('categories.store') }}"
            >

                @csrf


                <div class="row g-3">


                    {{-- Name --}}
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
                            value="{{ old('name') }}"
                            placeholder="Enter category name"
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

                            <option value="active"
                                @selected(old('status', 'active') === 'active')
                            >
                                Active
                            </option>

                            <option value="inactive"
                                @selected(old('status') === 'inactive')
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
                            rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Enter category description"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Form Buttons --}}
                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="{{ route('categories.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Add Category
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection