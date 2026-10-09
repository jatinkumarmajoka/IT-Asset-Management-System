
@extends('layouts.app')

@section('page-title', 'Edit Category')

@section('content')
    @php
        $canManageCategories = auth()->user()->isSuperAdmin()
            || auth()->user()->canManageModule('categories');
    @endphp

    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-4">Edit Category</h3>

            @if (!$canManageCategories)
                <div class="alert alert-info">
                    You have view-only access to Categories.
                    You cannot edit categories.
                </div>

                <a href="{{ route('categories.index') }}"
                   class="btn btn-secondary">
                    Back to Categories
                </a>
            @else
                <form method="POST"
                      action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Category Name
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', $category->name) }}"
                                required
                            >
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="code" class="form-label">
                                Category Code
                            </label>
                            <input
                                id="code"
                                type="text"
                                name="code"
                                class="form-control"
                                value="{{ old('code', $category->code) }}"
                                required
                            >
                            @error('code')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="active" @selected(old('status', $category->status) === 'active')>
                                    Active
                                </option>
                                <option value="inactive" @selected(old('status', $category->status) === 'inactive')>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="3"
                            >{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        Save Changes
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
