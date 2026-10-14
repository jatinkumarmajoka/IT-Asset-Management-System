<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Operational Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <main class="mx-auto max-w-6xl p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">My Operational Requests</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Track requests submitted by your account.
                </p>
            </div>

            <a href="{{ route('operational-requests.create') }}"
               class="rounded-lg bg-blue-600 px-4 py-3 font-semibold text-white">
                + New Request
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl bg-white shadow">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4">Date</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Subject</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Admin Response</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $item)
                        <tr class="border-t">
                            <td class="p-4">
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                            <td class="p-4">{{ $item->type }}</td>
                            <td class="p-4">
                                <div class="font-medium">{{ $item->subject }}</div>
                                <div class="mt-1 max-w-sm text-gray-600">
                                    {{ $item->description }}
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $item->status === 'approved'
                                        ? 'bg-green-100 text-green-800'
                                        : ($item->status === 'rejected'
                                            ? 'bg-red-100 text-red-800'
                                            : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="p-4">
                                {{ $item->admin_response ?: 'No response yet' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                You have not submitted any operational requests.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>

        <a href="{{ route('dashboard') }}"
           class="mt-5 inline-block text-blue-700 underline">
            Back to Dashboard
        </a>
    </main>
</body>
</html>