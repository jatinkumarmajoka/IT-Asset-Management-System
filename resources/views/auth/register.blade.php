@extends('layouts.app')

@section('page-title', 'Request Access')

@section('content')
<div class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
        <div class="text-center mb-8">
            @if(config('branding.show_logo_in_sidebar') && config('branding.logo'))
                <img src="{{ asset(config('branding.logo')) }}"
                     alt="{{ config('branding.logo_alt') }}"
                     class="mx-auto mb-4"
                     style="height: 48px; width: auto;">
            @else
                <i class="bi bi-pc-display text-5xl" style="color: var(--theme-primary);"></i>
            @endif
            <h1 class="text-2xl font-bold text-slate-900">Request an account</h1>
            <p class="mt-2 text-sm text-slate-600">
                Your account will remain pending until Super Admin approval.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Full name</label>
                <input id="name" name="name" value="{{ old('name') }}" required
                    autocomplete="name"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Work email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                    required autocomplete="email"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" required
                    minlength="12" autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-slate-500">Use at least 12 characters.</p>
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">
                    Confirm password
                </label>
                <input id="password_confirmation" name="password_confirmation"
                    type="password" required minlength="12" autocomplete="new-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                           focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-blue-700 px-4 py-3
                       font-semibold text-white transition hover:bg-blue-800
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Submit access request
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Already registered?
            <a href="{{ route('login') }}" class="font-medium text-blue-700 hover:underline">Sign in</a>
        </p>
    </main>
</div>
@endsection