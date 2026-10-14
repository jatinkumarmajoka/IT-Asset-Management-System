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



            $validTabs = ['overview', 'staff', 'generate', 'permissions', 'requests', 'audit'];



            if (!in_array($tab, $validTabs, true)) {



            $tab = 'overview';



            }



            @endphp



            {{-- Mobile navigation --}}



            <nav class="mb-6 flex flex-wrap gap-2 md:hidden">



                @foreach([



                'overview' => 'Overview',

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



                    <p class="text-sm text-slate-500">Approved Staff</p>



                    <p class="mt-3 text-3xl font-bold text-green-700">{{ $approvedCount }}</p>



                </article>



            </section>



            <section class="mt-6 rounded-xl border border-slate-200 bg-white p-5">



                <h2 class="font-semibold">Quick actions</h2>



                <div class="mt-4 flex flex-wrap gap-3">

                    <a href="{{ route('admin.index', ['tab' => 'permissions']) }}"



                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm">



                        Manage permissions



                    </a>



                </div>



            </section>





            @elseif($tab === 'staff')



            <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white p-5">



                <h2 class="text-lg font-semibold">Staff Management</h2>



                <table class="mt-4 w-full text-left text-sm">



                    <thead>



                        <tr class="border-b text-slate-500">



                            <th class="p-3">Name</th>



                            <th class="p-3">Email</th>



                            <th class="p-3">Department</th>



                            <th class="p-3">Status</th>



                            <th class="p-3">Actions</th>



                        </tr>



                    </thead>



                    <tbody>



                        @forelse($staff as $user)



                        <tr class="border-b last:border-0">



                            <td class="p-3 font-medium">{{ $user->name }}</td>



                            <td class="p-3">{{ $user->email }}</td>



                            <td class="p-3">{{ $user->department?->name ?? 'Not assigned' }}</td>



                            <td class="p-3">



                                <span class="rounded-full px-2 py-1 text-xs {{ $user->account_status === 'approved' ? 'bg-green-100 text-green-800' : ($user->account_status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">



                                    {{ ucfirst($user->account_status) }}



                                </span>



                            </td>



                            <td class="p-3">



                                <a href="{{ route('admin.staff.edit', $user) }}"



                                    class="inline-block rounded-lg border border-blue-200 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-50">



                                    Edit



                                </a>



                                @if($user->account_status === 'approved')



                                <form method="POST"



                                    action="{{ route('admin.staff.password-reset-link', $user) }}"



                                    onsubmit="return confirm('Send a password reset link to this staff member?')"



                                    class="mt-2">



                                    @csrf



                                    <button type="submit"



                                        class="rounded-lg border border-amber-300 px-3 py-1.5 text-sm font-medium text-amber-800 hover:bg-amber-50">



                                        Send Password Reset Link



                                    </button>



                                </form>



                                @endif



                            </td>



                        </tr>



                        @empty



                        <tr>



                            <td colspan="5" class="p-3 text-slate-500">No Staff accounts found.</td>



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



                        <label for="generated-department" class="mb-1 block text-sm font-medium">Department</label>



                        <select id="generated-department" name="department_id" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2">



                            <option value="">Select department</option>



                            @foreach($departments as $department)



                            <option value="{{ $department->id }}" @selected(old('department_id')==$department->id)>{{ $department->name }}</option>



                            @endforeach



                        </select>



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



                                value="0">



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
            <section class="space-y-8">
                <div>
                    <h2 class="text-xl font-semibold">Requests &amp; Approvals</h2>
                    <p class="mt-1 text-sm text-slate-600">Review existing asset transfers and general operational requests submitted by staff.</p>
                </div>

                @if(session('success'))
                <div role="status" class="rounded-lg bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</div>
                @endif

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold">Asset Transfer Requests</h3>
                            <p class="mt-1 text-sm text-slate-500">Existing transfer workflow. Asset details change only after a transfer is completed.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium">{{ isset($assetTransfers) ? $assetTransfers->count() : 0 }} total</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse(($assetTransfers ?? collect()) as $transfer)
                        <article class="rounded-lg border border-slate-200 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-semibold">{{ $transfer->reference_number ?: 'Transfer #'.$transfer->id }} — {{ $transfer->asset?->asset_tag ?? $transfer->asset?->name ?? 'Asset #'.$transfer->asset_id }}</h4>
                                    <p class="mt-1 text-sm text-slate-600">{{ $transfer->fromDepartment?->name ?? 'Unknown department' }} → {{ $transfer->toDepartment?->name ?? 'Unknown department' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Requested by {{ $transfer->initiatedBy?->name ?? 'Unknown user' }} · {{ optional($transfer->transfer_date)->format('d M Y') ?? 'No date' }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $transfer->status === 'completed' ? 'bg-green-100 text-green-800' : ($transfer->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($transfer->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700')) }}">{{ ucfirst($transfer->status) }}</span>
                            </div>
                            @if($transfer->reason)
                            <p class="mt-3 text-sm"><span class="font-medium">Reason:</span> {{ $transfer->reason }}</p>
                            @endif
                            @if($transfer->remarks)
                            <p class="mt-1 text-sm text-slate-600">{{ $transfer->remarks }}</p>
                            @endif
                            @if($transfer->status === 'pending')
                            <form method="POST" action="{{ route('admin.transfers.approve', $transfer) }}" class="mt-4">
                                @csrf
                                <button type="submit" onclick="return confirm('Approve this asset transfer?')" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">Approve transfer</button>
                            </form>
                            @elseif($transfer->status === 'approved')
                            <form method="POST" action="{{ route('admin.transfers.complete', $transfer) }}" class="mt-4">
                                @csrf
                                <button type="submit" onclick="return confirm('Complete this transfer and update the asset department/occupant?')" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800">Mark as completed</button>
                            </form>
                            @endif
                        </article>
                        @empty
                        <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No asset transfer requests found.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold">General Operational Requests</h3>
                            <p class="mt-1 text-sm text-slate-500">Staff requests for hardware, software, repairs, accessories, and other operational needs.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium">{{ isset($operationalRequests) ? $operationalRequests->count() : 0 }} total</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse(($operationalRequests ?? collect()) as $operationalRequest)
                        <article class="rounded-lg border border-slate-200 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-semibold">{{ $operationalRequest->subject }}</h4>
                                    <p class="mt-1 text-sm text-slate-600">{{ $operationalRequest->type }} · {{ $operationalRequest->department?->name ?? $operationalRequest->user?->department?->name ?? 'No department' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Submitted by {{ $operationalRequest->user?->name ?? 'Unknown user' }} · {{ $operationalRequest->created_at?->format('d M Y, h:i A') }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $operationalRequest->status === 'approved' ? 'bg-green-100 text-green-800' : ($operationalRequest->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($operationalRequest->status) }}</span>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm text-slate-700">{{ $operationalRequest->description }}</p>
                            @if($operationalRequest->admin_response)
                            <div class="mt-3 rounded-lg bg-slate-50 p-3 text-sm"><span class="font-medium">Admin response:</span> {{ $operationalRequest->admin_response }}</div>
                            @endif
                            @if($operationalRequest->status === 'pending')
                            <div class="mt-4 grid gap-3 lg:grid-cols-2">
                                <form method="POST" action="{{ route('admin.operational-requests.approve', $operationalRequest) }}" class="space-y-2 rounded-lg bg-green-50 p-3">
                                    @csrf
                                    <label for="approve-response-{{ $operationalRequest->id }}" class="block text-sm font-medium text-green-900">Approval note (optional)</label>
                                    <textarea id="approve-response-{{ $operationalRequest->id }}" name="admin_response" rows="2" maxlength="5000" class="w-full rounded-lg border border-green-200 bg-white px-3 py-2 text-sm" placeholder="Add instructions or approval details">{{ old('admin_response') }}</textarea>
                                    <button type="submit" onclick="return confirm('Approve this operational request?')" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800">Approve request</button>
                                </form>
                                <form method="POST" action="{{ route('admin.operational-requests.reject', $operationalRequest) }}" class="space-y-2 rounded-lg bg-red-50 p-3">
                                    @csrf
                                    <label for="reject-response-{{ $operationalRequest->id }}" class="block text-sm font-medium text-red-900">Reason for rejection (optional)</label>
                                    <textarea id="reject-response-{{ $operationalRequest->id }}" name="admin_response" rows="2" maxlength="5000" class="w-full rounded-lg border border-red-200 bg-white px-3 py-2 text-sm" placeholder="Explain why this request is rejected"></textarea>
                                    <button type="submit" onclick="return confirm('Reject this operational request?')" class="rounded-lg bg-red-700 px-4 py-2 text-sm font-medium text-white hover:bg-red-800">Reject request</button>
                                </form>
                            </div>
                            @else
                            <p class="mt-3 text-xs text-slate-500">Reviewed by {{ $operationalRequest->reviewer?->name ?? 'Super Admin' }}{{ $operationalRequest->reviewed_at ? ' · '.$operationalRequest->reviewed_at->format('d M Y, h:i A') : '' }}</p>
                            @endif
                        </article>
                        @empty
                        <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No general operational requests found.</p>
                        @endforelse
                    </div>
                </section>
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