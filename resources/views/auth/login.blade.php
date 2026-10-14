<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | IT Asset Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
        <h1 class="text-2xl font-bold text-slate-900">Sign in</h1>
        <p class="mt-2 text-sm text-slate-600">
            IT Asset Management System
        </p>

        @if (session('status'))
            <div role="status"
                 class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert"
                 class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('login.store') }}"
              class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email"
                       class="mb-1 block text-sm font-medium text-slate-700">
                    Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:border-blue-600 focus:outline-none focus:ring-2
                           focus:ring-blue-200"
                >
            </div>

            <div>
                <label for="password"
                       class="mb-1 block text-sm font-medium text-slate-700">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:border-blue-600 focus:outline-none focus:ring-2
                           focus:ring-blue-200"
                >
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="rounded border-slate-300"
                >
                Remember me
            </label>

            <button
                type="submit"
                class="w-full rounded-lg bg-blue-700 px-4 py-3
                       font-semibold text-white transition hover:bg-blue-800
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       focus:ring-offset-2"
            >
                Sign in
            </button>
        </form>
    </main>
</body>
</html>
