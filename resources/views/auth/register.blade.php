<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Access | IT Asset Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
        <h1 class="text-2xl font-bold text-slate-900">Request an account</h1>
        <p class="mt-2 text-sm text-slate-600">
            Your account will remain pending until Super Admin approval.
        </p>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-700" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-medium">Full name</label>
                <input id="name" name="name" value="{{ old('name') }}" required
                    autocomplete="name"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Work email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                    required autocomplete="email"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required
                    minlength="12" autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <p class="mt-1 text-xs text-slate-500">Use at least 12 characters.</p>
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium">
                    Confirm password
                </label>
                <input id="password_confirmation" name="password_confirmation"
                    type="password" required minlength="12" autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-blue-700 px-4 py-3 font-semibold text-white hover:bg-blue-800">
                Submit access request
            </button>
        </form>

        <p class="mt-5 text-center text-sm">
            Already registered?
            <a href="{{ route('login') }}" class="font-medium text-blue-700">Sign in</a>
        </p>
    </main>
</body>
</html>
