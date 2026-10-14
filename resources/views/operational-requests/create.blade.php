<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Operational Request</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <main class="mx-auto max-w-3xl p-6">
        <div class="rounded-xl bg-white p-6 shadow">
            <h1 class="mb-2 text-2xl font-bold">Submit Operational Request</h1>
            <p class="mb-6 text-sm text-gray-600">
                Submit a departmental request for Super Admin review.
            </p>

            @if ($errors->any())
                <div class="mb-4 rounded bg-red-100 p-4 text-red-800">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('operational-requests.store') }}"
                  class="space-y-5">
                @csrf

                <div>
                    <label for="type" class="mb-1 block font-medium">Request Type</label>
                    <select id="type" name="type" required
                            class="w-full rounded-lg border p-3">
                        <option value="">Select a type</option>
                        @foreach ([
                            'Software Installation',
                            'Hardware Repair',
                            'Hardware Request',
                            'Accessories',
                            'Other',
                        ] as $type)
                            <option value="{{ $type }}"
                                @selected(old('type') === $type)>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="subject" class="mb-1 block font-medium">Subject</label>
                    <input id="subject" name="subject" type="text" required
                           maxlength="200" value="{{ old('subject') }}"
                           class="w-full rounded-lg border p-3"
                           placeholder="Briefly describe what you need">
                </div>

                <div>
                    <label for="description" class="mb-1 block font-medium">
                        Description
                    </label>
                    <textarea id="description" name="description" required
                              maxlength="5000" rows="5"
                              class="w-full rounded-lg border p-3"
                              placeholder="Explain the requirement and its purpose">{{ old('description') }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white">
                        Submit Request
                    </button>

                    <a href="{{ route('operational-requests.index') }}"
                       class="rounded-lg border px-5 py-3">
                        View My Requests
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="rounded-lg border px-5 py-3">
                        Dashboard
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>