<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i> Generate Staff Account</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.accounts.generate') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="generated-name" class="form-label fw-bold">Full name</label>
                <input id="generated-name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"
                    class="form-control @error('name') is-invalid @enderror" placeholder="John Doe">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="generated-email" class="form-label fw-bold">Email address</label>
                <input id="generated-email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                    class="form-control @error('email') is-invalid @enderror" placeholder="john.doe@example.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="generated-department" class="form-label fw-bold">Department</label>
                <select id="generated-department" name="department_id" required class="form-select @error('department_id') is-invalid @enderror">
                    <option value="">Select department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="generated-password" class="form-label fw-bold">Temporary password</label>
                <input id="generated-password" name="password" type="password" required minlength="12" autocomplete="new-password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="Min 12 characters">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Use at least 12 characters.</div>
            </div>

            <div class="col-md-6">
                <label for="generated-password-confirmation" class="form-label fw-bold">Confirm password</label>
                <input id="generated-password-confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password"
                    class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Generate approved Staff account
                </button>
            </div>
        </form>
    </div>
</div>