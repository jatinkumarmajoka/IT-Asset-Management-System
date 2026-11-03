@extends('layouts.app')
@section('page-title','Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="mb-1">Dashboard</h2>
        <p class="text-muted mb-0">Live database statistics</p>
    </div>
    <a class="btn btn-primary" href="{{ route('assets.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Asset</a>
</div>

<div class="row g-3 mb-4" id="statsGrid">
    @foreach([
        ['assets','Total Assets','bi-box-seam'],
        ['departments','Departments','bi-building'],
        ['categories','Categories','bi-tags'],
        ['employees','Employees','bi-people'],
        ['verified','Verified Assets','bi-check-circle'],
        ['pending','Pending Verification','bi-clock']
    ] as [$key,$label,$icon])
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card card-stat shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="text-muted small fw-medium">{{ $label }}</div>
                <div class="fs-3 fw-bold mt-auto mb-2">{{ $stats[$key] }}</div>
                <div class="stat-icon"><i class="bi {{ $icon }} text-primary fs-1"></i></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4" id="dashboardSections">
    {{-- Assets by Department --}}
    <div class="col-12 col-lg-6 col-xl-5">
        <div class="card table-card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Assets by Department</span>
                <span class="badge bg-light text-dark small">{{ $departmentCounts->count() }}</span>
            </div>
            <div class="card-body">
                @forelse($departmentCounts as $department)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-truncate me-2" style="max-width: 70%;">{{ $department->name }}</span>
                        <strong class="text-nowrap">{{ $department->assets_count }}</strong>
                    </div>
                    <div class="progress mb-3" style="height: 8px;" role="progressbar" aria-valuenow="{{ $stats['assets'] ? min(100, round(($department->assets_count/$stats['assets'])*100)) : 0 }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ $stats['assets'] ? min(100, round(($department->assets_count/$stats['assets'])*100)) : 0 }}%"></div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No departments yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Assets --}}
    <div class="col-12 col-lg-6 col-xl-7">
        <div class="card table-card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Recent Assets</span>
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-link text-primary p-0">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 180px;">Asset</th>
                                <th style="min-width: 140px;">Department</th>
                                <th style="min-width: 160px;">Occupant</th>
                                <th style="min-width: 120px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAssets as $asset)
                                <tr>
                                    <td>
                                        <a href="{{ route('assets.show',$asset) }}" class="fw-semibold text-decoration-none text-truncate d-block" style="max-width: 200px;">{{ $asset->asset_code }}</a>
                                        <small class="text-muted text-truncate d-block" style="max-width: 200px;">{{ $asset->item_name }}</small>
                                    </td>
                                    <td>{{ $asset->department->name }}</td>
                                    <td>
    @if($asset->occupant)
        {{ $asset->occupant->name }}
    @else
        <span class="text-muted">Unassigned</span>
    @endif
</td>
                                    <td><span class="badge text-bg-light">{{ $asset->asset_status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No assets.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection