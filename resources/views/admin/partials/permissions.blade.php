<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i> Staff Module Permissions</h5>
    </div>
    <div class="card-body p-0">
        @if($staff->where('account_status', 'approved')->isEmpty())
            <div class="p-4 text-center text-muted">No approved staff accounts to configure.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 25%;">Staff Member</th>
                            @foreach($modules as $key => $label)
                                <th class="text-center" style="width: {{ floor(75 / count($modules)) }}%;">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff->where('account_status', 'approved') as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>
                                @foreach($modules as $key => $label)
                                    <td class="text-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   name="permissions[{{ $key }}]"
                                                   value="1"
                                                   @checked($user->canManageModule($key))
                                                   data-user-id="{{ $user->id }}"
                                                   data-module="{{ $key }}">
                                        </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary" id="savePermissions">
                <i class="bi bi-save me-1"></i> Save All Permissions
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('savePermissions')?.addEventListener('click', function() {
    const userIds = [...new Set(document.querySelectorAll('[data-user-id]').map(el => el.dataset.userId))];
    const promises = userIds.map(userId => {
        const permissions = {};
        document.querySelectorAll(`[data-user-id="${userId}"]`).forEach(el => {
            permissions[el.dataset.module] = el.checked;
        });

        return fetch('{{ route("admin.staff.permissions", ":userId") }}'.replace(':userId', userId), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ permissions })
        });
    });

    Promise.all(promises)
        .then(responses => Promise.all(responses.map(r => r.json())))
        .then(results => {
            if (results.every(r => r.success)) {
                Swal.fire('Saved!', 'All permissions updated.', 'success');
            } else {
                Swal.fire('Error', 'Some permissions failed to save.', 'error');
            }
        });
});
</script>
@endpush