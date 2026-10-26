<div class="row g-4">
    <div class="col-md-6 col-xl-3">
        <div class="card card-stat shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Total Staff</p>
                        <h3 class="fw-bold fs-3 mb-0">{{ $staffCount }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-people fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-md-6 col-xl-3">
        <div class="card card-stat shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Approved Staff</p>
                        <h3 class="fw-bold fs-3 mb-0 text-success">{{ $approvedCount }}</h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-md-6 col-xl-3">
        <div class="card card-stat shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Pending Approval</p>
                        <h3 class="fw-bold fs-3 mb-0 text-warning">{{ $staffCount - $approvedCount }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-md-6 col-xl-3">
        <div class="card card-stat shadow-sm h-100 border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-1">Rejected</p>
                        <h3 class="fw-bold fs-3 mb-0 text-danger">{{ \App\Models\User::where('role', 'staff')->where('account_status', 'rejected')->count() }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="bi bi-x-circle fs-2 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-arrow-left-right me-2"></i> Transfer Requests</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1">{{ $assetTransfers->count() }} total</span>
            </div>
            <div class="card-body p-0">
                @forelse($assetTransfers as $transfer)
                    <div class="border-top px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $transfer->reference_number ?? 'Transfer #' . $transfer->id }}</div>
                                <small class="text-muted">{{ $transfer->fromDepartment?->name ?? 'Unknown' }} &rarr; {{ $transfer->toDepartment?->name ?? 'Unknown' }}</small>
                            </div>
                            <span class="badge px-3 py-1
                                {{ $transfer->status === 'completed' ? 'bg-success' : ($transfer->status === 'pending' ? 'bg-warning' : ($transfer->status === 'approved' ? 'bg-info' : 'bg-secondary')) }}">
                                {{ ucfirst($transfer->status) }}
                            </span>
                        </div>
                        @if($transfer->reason)
                            <p class="mb-1 small text-muted mt-2"><strong>Reason:</strong> {{ $transfer->reason }}</p>
                        @endif
                        @if($transfer->status === 'pending')
                            <form method="POST" action="{{ route('admin.transfers.approve', $transfer) }}" class="mt-2">
                                @csrf
                                <button type="submit" onclick="return confirm('Approve this transfer?')"
                                        class="btn btn-sm btn-primary">Approve</button>
                            </form>
                        @elseif($transfer->status === 'approved')
                            <form method="POST" action="{{ route('admin.transfers.complete', $transfer) }}" class="mt-2">
                                @csrf
                                <button type="submit" onclick="return confirm('Complete this transfer?')"
                                        class="btn btn-sm btn-success">Complete</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">No transfer requests found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-file-text me-2"></i> Operational Requests</h6>
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1">{{ $operationalRequests->count() }} total</span>
            </div>
            <div class="card-body p-0">
                @forelse($operationalRequests as $request)
                    <div class="border-top px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <h6 class="mb-1 fw-semibold">{{ $request->subject }}</h6>
                                <small class="text-muted">{{ $request->type }} &middot; {{ $request->department?->name ?? $request->user?->department?->name ?? 'No department' }}</small>
                                <br><small class="text-muted">Submitted by {{ $request->user?->name ?? 'Unknown' }} &middot; {{ $request->created_at?->format('M d, Y h:i A') }}</small>
                            </div>
                            <span class="badge px-3 py-1
                                {{ $request->status === 'approved' ? 'bg-success' : ($request->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                        <p class="mb-2 small text-slate-700 mt-2">{{ $request->description }}</p>
                        @if($request->admin_response)
                            <div class="mb-2 p-2 rounded bg-slate-50 small"><strong>Admin response:</strong> {{ $request->admin_response }}</div>
                        @endif
                        @if($request->status === 'pending')
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.operational-requests.approve', $request) }}" class="p-3 rounded bg-green-50 border border-green-200">
                                        @csrf
                                        <label class="form-label small fw-bold text-green-900">Approval note (optional)</label>
                                        <textarea name="admin_response" rows="2" maxlength="5000" class="form-control form-control-sm" placeholder="Add instructions..."></textarea>
                                        <button type="submit" onclick="return confirm('Approve this request?')" class="btn btn-sm btn-success mt-2 w-100">Approve</button>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.operational-requests.reject', $request) }}" class="p-3 rounded bg-red-50 border border-red-200">
                                        @csrf
                                        <label class="form-label small fw-bold text-red-900">Reason for rejection</label>
                                        <textarea name="admin_response" rows="2" maxlength="5000" class="form-control form-control-sm" placeholder="Explain why..."></textarea>
                                        <button type="submit" onclick="return confirm('Reject this request?')" class="btn btn-sm btn-danger mt-2 w-100">Reject</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <p class="mt-2 small text-muted">Reviewed by {{ $request->reviewer?->name ?? 'Super Admin' }}{{ $request->reviewed_at ? ' &middot; ' . $request->reviewed_at->format('M d, Y h:i A') : '' }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">No operational requests found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>