<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i> Staff Module Permissions</h5>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 small">
            Auto-save enabled
        </span>
    </div>
    <div class="card-body p-0">
        @if($staff->where('account_status', 'approved')->isEmpty())
            <div class="p-4 text-center text-muted">No approved staff accounts to configure.</div>
        @else
            <div class="table-responsive permissions-table-wrap">
                <table class="table table-hover align-middle mb-0 permissions-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="min-width: 250px; width: 250px;">Staff Member</th>
                            @foreach($modules as $key => $label)
                                <th class="text-center permission-col" style="min-width: 120px; width: 120px;">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff->where('account_status', 'approved') as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-medium text-truncate" style="max-width: 230px;">{{ $user->name }}</div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 230px;">{{ $user->email }}</small>
                                </td>
                                @foreach($modules as $key => $label)
                                    <td class="text-center permission-col">
                                        <div class="form-check form-switch d-flex justify-content-center align-items-center h-100">
                                            <input class="form-check-input permission-toggle" type="checkbox"
                                                   name="permissions[{{ $key }}]"
                                                   value="1"
                                                   @checked($user->canManageModule($key))
                                                   data-user-id="{{ $user->id }}"
                                                   data-module="{{ $key }}"
                                                   data-route="{{ route('admin.staff.permission.update', ['user' => $user->id, 'module' => $key]) }}">
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.permission-toggle');
    const pendingRequests = new Map(); // Track pending requests per toggle

    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const module = this.dataset.module;
            const route = this.dataset.route;
            const newValue = this.checked;

            // Prevent double-clicks / rapid toggling
            const key = `${userId}:${module}`;
            if (pendingRequests.has(key)) {
                // Revert immediately if there's already a pending request
                this.checked = !newValue;
                return;
            }

            // Show loading state
            this.disabled = true;
            this.style.opacity = '0.5';

            // Store original value for potential rollback
            const originalValue = !newValue;
            pendingRequests.set(key, true);

            // Send AJAX request
            fetch(route, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ value: newValue })
            })
            .then(response => response.json())
            .then(data => {
                pendingRequests.delete(key);
                this.disabled = false;
                this.style.opacity = '';

                if (data.success) {
                    // Show brief success indication
                    showToast('Permission updated', 'success');
                } else {
                    // Revert on error
                    this.checked = originalValue;
                    showToast(data.message || 'Failed to update permission', 'error');
                }
            })
            .catch(error => {
                pendingRequests.delete(key);
                this.disabled = false;
                this.style.opacity = '';
                this.checked = originalValue;
                showToast('Network error. Please try again.', 'error');
                console.error('Permission update failed:', error);
            });
        });
    });

    function showToast(message, type) {
        // Remove existing toasts
        const existingToast = document.querySelector('.permission-toast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = `permission-toast alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'bottom: 1rem; right: 1rem; z-index: 1060; min-width: 250px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        `;
        document.body.appendChild(toast);

        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 150);
            }
        }, 3000);
    }
});
</script>
@endpush