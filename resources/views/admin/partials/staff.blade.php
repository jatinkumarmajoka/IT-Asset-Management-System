<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i> Staff Management</h5>
        <a href="{{ route('admin.index', ['tab' => 'generate']) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-person-plus me-1"></i> Add Staff
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive admin-table-wrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="min-width: 220px;">Name</th>
                        <th style="min-width: 240px;">Email</th>
                        <th style="min-width: 220px;">Department</th>
                        <th class="text-center" style="min-width: 140px;">Status</th>
                        <th class="text-end pe-4" style="min-width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $user)
                        <tr>
                            <td class="ps-4 fw-medium">
                                <div class="text-truncate" style="max-width: 220px;">{{ $user->name }}</div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 240px;">{{ $user->email }}</div>
                            </td>
                            <td>
                                <div class="text-truncate d-flex align-items-center" style="max-width: 220px;"
                                     title="{{ $user->department?->name ?? 'Not assigned' }}">
                                    @if($user->department)
                                        {{ $user->department->name }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                 </div>
                            </td>
                            <td class="text-center">
                                <span class="badge px-3 py-1
                                    {{ $user->account_status === 'approved' ? 'bg-success' : ($user->account_status === 'pending' ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ ucfirst($user->account_status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <a href="{{ route('admin.staff.edit', $user) }}"
                                       class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                        <i class="bi bi-pencil me-1"></i> <span class="d-none d-sm-inline">Edit</span>
                                    </a>

                                    @if($user->account_status === 'pending')
                                        <form method="POST" action="{{ route('admin.accounts.approve', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve this account?')" class="btn btn-sm btn-success d-inline-flex align-items-center">
                                                <i class="bi bi-check-lg me-1"></i> <span class="d-none d-sm-inline">Approve</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.accounts.reject', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Reject this account?')" class="btn btn-sm btn-danger d-inline-flex align-items-center">
                                                <i class="bi bi-x-lg me-1"></i> <span class="d-none d-sm-inline">Reject</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->account_status === 'approved')
                                        <form method="POST" action="{{ route('admin.staff.password-reset-link', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Send password reset link?')" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                                <i class="bi bi-key me-1"></i> <span class="d-none d-sm-inline">Reset Password</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No staff accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>