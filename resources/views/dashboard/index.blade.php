@extends('layouts.app')
@section('page-title','Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2 class="mb-1">Dashboard</h2><p class="text-muted mb-0">Live database statistics</p></div>
    <a class="btn btn-primary" href="{{ route('assets.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Asset</a>
</div>

<div class="row g-3 mb-4">
@foreach([
 ['assets','Total Assets','bi-box-seam'],
 ['departments','Departments','bi-building'],
 ['categories','Categories','bi-tags'],
 ['employees','Employees','bi-people'],
 ['verified','Verified Assets','bi-check-circle'],
 ['pending','Pending Verification','bi-clock']
] as [$key,$label,$icon])
<div class="col-md-4 col-xl-2">
    <div class="card card-stat shadow-sm h-100"><div class="card-body">
        <div class="text-muted small">{{ $label }}</div>
        <div class="fs-3 fw-bold mt-2">{{ $stats[$key] }}</div>
        <i class="bi {{ $icon }} text-primary"></i>
    </div></div>
</div>
@endforeach
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white fw-semibold">Assets by Department</div>
            <div class="card-body">
                @forelse($departmentCounts as $department)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $department->name }}</span><strong>{{ $department->assets_count }}</strong>
                    </div>
                    <div class="progress mb-3" style="height:6px"><div class="progress-bar" style="width:{{ $stats['assets'] ? min(100,($department->assets_count/$stats['assets'])*100) : 0 }}%"></div></div>
                @empty
                    <p class="text-muted mb-0">No departments yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white fw-semibold">Recent Assets</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Asset</th><th>Department</th><th>Occupant</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recentAssets as $asset)
                        <tr>
                            <td><a href="{{ route('assets.show',$asset) }}" class="fw-semibold text-decoration-none">{{ $asset->asset_code }}</a><br><small class="text-muted">{{ $asset->item_name }}</small></td>
                            <td>{{ $asset->department->name }}</td>
                            <td>{{ $asset->occupant?->name ?? 'Unassigned' }}</td>
                            <td><span class="badge text-bg-light">{{ $asset->asset_status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No assets.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
