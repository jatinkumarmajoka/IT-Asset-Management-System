<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-inbox me-2"></i> Operational Requests</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Subject</th>
                        <th>Type</th>
                        <th>Department</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operationalRequests as $request)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $request->subject }}</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $request->type }}</span></td>
                            <td>{{ $request->department?->name ?? $request->user?->department?->name ?? 'No department' }}</td>
                            <td>{{ $request->user?->name ?? 'Unknown' }}</td>
                            <td>{{ $request->created_at?->format('M d, Y') }}</td>
                            <td class="text-center">
                                <span class="badge px-3 py-1
                                    {{ $request->status === 'approved' ? 'bg-success' : ($request->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($request->status === 'pending')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                            <i class="bi bi-check-lg me-1"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                            <i class="bi bi-x-lg me-1"></i> Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted small">Reviewed by {{ $request->reviewer?->name ?? 'Admin' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No operational requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals for approve/reject -->
    @foreach($operationalRequests as $request)
        @if($request->status === 'pending')
        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.operational-requests.approve', $request) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Approve Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="fw-bold">{{ $request->subject }}</p>
                            <p class="text-muted small">{{ $request->description }}</p>
                            <hr>
                            <label class="form-label fw-bold">Approval note (optional)</label>
                            <textarea name="admin_response" rows="3" maxlength="5000" class="form-control" placeholder="Add instructions..."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Approve</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.operational-requests.reject', $request) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Reject Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="fw-bold">{{ $request->subject }}</p>
                                <hr>
                                <label class="form-label fw-bold text-danger">Reason for rejection</label>
                                <textarea name="admin_response" rows="3" maxlength="5000" class="form-control" placeholder="Explain why..." required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </div>
                        </form>
                </div>
            </div>
        @endif
    @endforeach
</div>