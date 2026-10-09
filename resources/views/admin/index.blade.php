

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Super Admin | IT Asset Management</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

<div class="flex min-h-screen">

    {{-- Admin sidebar --}}

    <aside class="hidden w-64 shrink-0 bg-slate-900 p-5 text-white md:block">

        <a href="{{ route('admin.index') }}" class="text-lg font-bold">

            IT Asset Management

        </a>

        <p class="mt-1 text-xs text-slate-400">SUPER ADMIN</p>

        <nav class="mt-8 space-y-2">

            <a href="{{ route('admin.index', ['tab' => 'overview']) }}"

               class="block rounded-lg px-3 py-2 hover:bg-slate-800">

                Overview

            </a>

            <a href="{{ route('admin.index', ['tab' => 'accounts']) }}"

               class="block rounded-lg px-3 py-2 hover:bg-slate-800">

                Account Requests

                @if($pendingCount)

                    <span class="ml-1 rounded-full bg-amber-400 px-2 py-0.5 text-xs text-slate-900">

                        {{ $pendingCount }}

                    </span>

                @endif

            </a>

            <a href="{{ route('admin.index', ['tab' => 'staff']) }}"

               class="block rounded-lg px-3 py-2 hover:bg-slate-800">

                Staff Management

            </a>

            <a href="{{ route('admin.index', ['tab' => 'generate']) }}"
               class="block rounded-lg px-3 py-2 hover:bg-slate-800">
                Generate Account
            </a>

            <a href="{{ route('admin.index', ['tab' => 'permissions']) }}"

               class="block rounded-lg px-3 py-2 hover:bg-slate-800">

                Permissions

            </a>

            <a href="{{ route('admin.index', ['tab' => 'requests']) }}"

               class="block rounded-lg px-3 py-2 hover:bg-slate-800">

                Requests

            </a>

            <a href="{{ route('admin.index', ['tab' => 'audit']) }}"

               class="block rounded-lg px-3 py-2 hover:bg-slate-800">

                Audit Logs

            </a>

        </nav>

        <a href="{{ route('dashboard') }}"

           class="mt-8 block rounded-lg border border-slate-700 px-3 py-2 text-sm hover:bg-slate-800">

            ← Back to application

        </a>

    </aside>

    <main class="min-w-0 flex-1 p-5 sm:p-8">

        <header class="mb-8 flex flex-wrap items-center justify-between gap-4">

            <div>

                <p class="text-sm text-slate-500">Administration</p>

                <h1 class="mt-1 text-2xl font-bold">Super Admin Panel</h1>

                <p class="mt-1 text-sm text-slate-500">

                    Manage access to your IT Asset Management System.

                </p>

            </div>

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm hover:bg-slate-50">

                    Sign out

                </button>

            </form>

        </header>

        @if(session('status'))

            <div role="status" class="mb-5 rounded-lg bg-green-50 p-3 text-sm text-green-800">

                {{ session('status') }}

            </div>

        @endif

        @if($errors->any())

            <div role="alert" class="mb-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">

                {{ $errors->first() }}

            </div>

        @endif

        @php

            $tab = request('tab', 'overview');

            $validTabs = ['overview', 'accounts', 'staff', 'generate', 'permissions', 'requests', 'audit'];

            if (!in_array($tab, $validTabs, true)) {

                $tab = 'overview';

            }

        @endphp

        {{-- Mobile navigation --}}

        <nav class="mb-6 flex flex-wrap gap-2 md:hidden">

            @foreach([

                'overview' => 'Overview',

                'accounts' => 'Accounts',

                'staff' => 'Staff',
                'generate' => 'Generate Account',

                'permissions' => 'Permissions',

                'requests' => 'Requests',

                'audit' => 'Audit Logs',

            ] as $key => $label)

                <a href="{{ route('admin.index', ['tab' => $key]) }}"

                   class="rounded-lg border px-3 py-2 text-sm

                   {{ $tab === $key ? 'border-blue-700 bg-blue-700 text-white' : 'border-slate-300 bg-white' }}">

                    {{ $label }}

                </a>

            @endforeach

        </nav>

        @if($tab === 'overview')

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm text-slate-500">Total Staff Accounts</p>

                    <p class="mt-3 text-3xl font-bold">{{ $staffCount }}</p>

                </article>

                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm text-slate-500">Pending Registrations</p>

                    <p class="mt-3 text-3xl font-bold text-amber-600">{{ $pendingCount }}</p>

                    <a href="{{ route('admin.index', ['tab' => 'accounts']) }}"

                       class="mt-3 inline-block text-sm font-medium text-blue-700">

                        Review requests →

                    </a>

                </article>

                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm text-slate-500">Approved Staff</p>

                    <p class="mt-3 text-3xl font-bold text-green-700">{{ $approvedCount }}</p>

                </article>

            </section>

            <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="font-semibold">Quick actions</h2>

                <div class="mt-4 flex flex-wrap gap-3">

                    <a href="{{ route('admin.index', ['tab' => 'accounts']) }}"

                       class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white">

                        Review account requests

                    </a>

                    <a href="{{ route('admin.index', ['tab' => 'permissions']) }}"

                       class="rounded-lg border border-slate-300 px-4 py-2 text-sm">

                        Manage permissions

                    </a>

                </div>

            </section>

        @elseif($tab === 'accounts')

            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="text-lg font-semibold">Account Requests</h2>

                @forelse($pendingUsers as $user)

                    <div class="flex flex-wrap items-center justify-between gap-4 border-b py-4 last:border-0">

                        <div>

                            <p class="font-medium">{{ $user->name }}</p>

                            <p class="text-sm text-slate-500">{{ $user->email }}</p>

                            <p class="mt-1 text-xs text-amber-700">Pending approval</p>

                        </div>

                        <div class="flex gap-2">

                            <form method="POST" action="{{ route('admin.accounts.approve', $user) }}">

                                @csrf

                                <button class="rounded-lg bg-green-700 px-3 py-2 text-sm text-white">

                                    Approve

                                </button>

                            </form>

                            <form method="POST" action="{{ route('admin.accounts.reject', $user) }}"

                                  onsubmit="return confirm('Reject this registration?')">

                                @csrf

                                <button class="rounded-lg border border-red-300 px-3 py-2 text-sm text-red-700">

                                    Reject

                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <p class="py-6 text-sm text-slate-500">No pending account requests.</p>

                @endforelse

            </section>

        @elseif($tab === 'staff')

            <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="text-lg font-semibold">Staff Management</h2>

                <table class="mt-4 w-full text-left text-sm">

                    <thead>

                        <tr class="border-b text-slate-500">

                            <th class="p-3">Name</th>

                            <th class="p-3">Email</th>

                            <th class="p-3">Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($staff as $user)

                            <tr class="border-b last:border-0">

                                <td class="p-3 font-medium">{{ $user->name }}</td>

                                <td class="p-3">{{ $user->email }}</td>

                                <td class="p-3">

                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs">

                                        {{ ucfirst($user->account_status) }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3" class="p-3 text-slate-500">No Staff accounts found.</td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </section>

        @elseif($tab === 'generate')
            <section class="max-w-2xl rounded-xl border border-slate-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Generate Staff Account</h2>
                <p class="mt-2 text-sm text-slate-600">Create an approved Staff account. Share the temporary credentials with the user through a secure channel.</p>

                <form method="POST" action="{{ route('admin.accounts.generate') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="generated-name" class="mb-1 block text-sm font-medium">Full name</label>
                        <input id="generated-name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label for="generated-email" class="mb-1 block text-sm font-medium">Email address</label>
                        <input id="generated-email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <div>
                        <label for="generated-password" class="mb-1 block text-sm font-medium">Temporary password</label>
                        <input id="generated-password" name="password" type="password" required minlength="12" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        <p class="mt-1 text-xs text-slate-500">Use at least 12 characters.</p>
                    </div>
                    <div>
                        <label for="generated-password-confirmation" class="mb-1 block text-sm font-medium">Confirm password</label>
                        <input id="generated-password-confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>
                    <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">Generate approved Staff account</button>
                </form>
            </section>

        @elseif($tab === 'permissions')

            <section class="space-y-5">

                <h2 class="text-lg font-semibold">Staff Module Permissions</h2>

                @forelse($staff->where('account_status', 'approved') as $user)

                    <form method="POST"

                          action="{{ route('admin.staff.permissions', $user) }}"

                          class="rounded-xl border border-slate-200 bg-white p-5">

                        @csrf

                        @method('PUT')

                        <div class="mb-4">

                            <h3 class="font-semibold">{{ $user->name }}</h3>

                            <p class="text-sm text-slate-500">{{ $user->email }}</p>

                        </div>

                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                            @foreach($modules as $key => $label)

                                <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-3">

                                    <input

                                        type="hidden"

                                        name="permissions[{{ $key }}]"

                                        value="0"

                                    >

                                    <input

                                        type="checkbox"

                                        name="permissions[{{ $key }}]"

                                        value="1"

                                        @checked(($user->permissions[$key] ?? false) === true)

                                    >

                                    <span class="text-sm">{{ $label }}</span>

                                </label>

                            @endforeach

                        </div>

                        <button type="submit"

                                class="mt-5 rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white">

                            Save permissions

                        </button>

                    </form>

                @empty

                    <div class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-500">

                        No approved Staff accounts are available to configure.

                    </div>

                @endforelse

            </section>

        @elseif($tab === 'requests')

            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="text-lg font-semibold">Operational Requests</h2>

                <p class="mt-2 text-sm text-slate-600">

                    This tab is a placeholder until request types, database tables,

                    and approval workflows are connected.

                </p>

            </section>

        @elseif($tab === 'audit')

            <section class="rounded-xl border border-slate-200 bg-white p-5">

                <h2 class="text-lg font-semibold">Audit Logs</h2>

                <p class="mt-2 text-sm text-slate-600">

                    Audit logging needs a database table and event recording.

                    This tab will be connected when that component is implemented.

                </p>

            </section>

        @endif

    </main>

</div>

</body>

</html>
