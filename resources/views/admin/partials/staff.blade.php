<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i> Staff Management</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $user)
                        <tr>
                            <td class="ps-4 fw-medium">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department?->name ?? 'Not assigned' }}</td>
                            <td>
                                <span class="badge px-3 py-1
                                    {{ $user->account_status === 'approved' ? 'bg-success' : ($user->account_status === 'pending' ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ ucfirst($user->account_status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.staff.edit', $user) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>

                                    @if($user->account_status === 'pending')
                                        <form method="POST" action="{{ route('admin.accounts.approve', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve this account?')" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.accounts.reject', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Reject this account?')" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @endif

                                    @if($user->account_status === 'approved')
                                        <form method="POST" action="{{ route('admin.staff.password-reset-link', $user) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Send password reset link?')" class="btn btn-sm btn-outline-secondary">Reset Password</button>
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