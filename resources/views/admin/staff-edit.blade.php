
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Staff | IT Asset Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="mx-auto max-w-2xl p-5 sm:p-8">
        <div class="mb-6">
            <a
                href="{{ route('admin.index', ['tab' => 'staff']) }}"
                class="text-sm font-medium text-blue-700 hover:underline"
            >
                &larr; Back to Staff Management
            </a>

            <h1 class="mt-4 text-2xl font-bold">Edit Staff Account</h1>
            <p class="mt-1 text-sm text-slate-600">
                Update the staff member's name, email, and department.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form
                method="POST"
                action="{{ route('admin.staff.update', $user) }}"
                class="space-y-5"
            >
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium">
                        Full name
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        maxlength="255"
                        autocomplete="name"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium">
                        Email address
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        maxlength="255"
                        autocomplete="email"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >

                    <p class="mt-1 text-xs text-slate-500">
                        This email is used for the staff login and password reset.
                    </p>
                </div>

                <div>
                    <label for="department_id" class="mb-1 block text-sm font-medium">
                        Department
                    </label>

                    <select
                        id="department_id"
                        name="department_id"
                        required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">Select a department</option>

                        @foreach ($departments as $department)
                            <option
                                value="{{ $department->id }}"
                                @selected(
                                    (string) old('department_id', $user->department_id)
                                    === (string) $department->id
                                )
                            >
                                {{ $department->name }}
                                @if ($department->code)
                                    ({{ $department->code }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @if ($departments->isEmpty())
                        <p class="mt-1 text-sm text-red-600">
                            No departments exist yet. Create a department before editing this account.
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button
                        type="submit"
                        @disabled($departments->isEmpty())
                        class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Save Changes
                    </button>

                    <a
                        href="{{ route('admin.index', ['tab' => 'staff']) }}"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
