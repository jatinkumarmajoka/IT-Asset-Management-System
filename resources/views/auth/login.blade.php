<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ config('branding.org_name') }} - {{ config('branding.product_name') }}</title>

    {{-- Bootstrap CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    {{-- SweetAlert2 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
        rel="stylesheet">

    {{-- Mobile-first responsive CSS --}}
    <link href="{{ asset('css/mobile-first.css') }}" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --theme-primary: #08203C;
            --theme-primary-light: #0d2f56;
            --theme-primary-lighter: #123d6e;
            --theme-primary-rgb: 8, 32, 60;
            --sidebar-bg: #08203C;
            --sidebar-hover: #0d2f56;
            --sidebar-text: #cbd5e1;
            --sidebar-active: #ffffff;
            --navbar-bg: #ffffff;
            --navbar-border: #e5e7eb;
            --body-bg: #f5f7fb;
            --card-border-radius: 14px;
            --focus-ring: rgba(8, 32, 60, 0.25);
        }

        body {
            background: var(--body-bg);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
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
            <h1 class="text-2xl font-bold text-slate-900">Sign in</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ config('branding.product_name') }}
            </p>
        </div>

        @if (session('status'))
            <div role="status"
                 class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert"
                 class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('login.store') }}"
              class="space-y-4">
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
                           focus:ring-blue-200">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                           focus:ring-blue-200">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="rounded border-slate-300">
                Remember me
            </label>

            <button
                type="submit"
                class="w-full rounded-lg bg-blue-700 px-4 py-3
                       font-semibold text-white transition hover:bg-blue-800
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       focus:ring-offset-2">
                Sign in
            </button>
        </form>
    </main>
</div>

    {{-- Bootstrap JavaScript --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Vite Assets --}}
    @vite(['resources/js/app.js'])

    {{-- Global confirmation modal --}}
    <div id="app-confirm-modal" class="app-confirm-overlay" hidden>
        <section
            class="app-confirm-dialog"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="app-confirm-title"
            aria-describedby="app-confirm-message"
        >
            <div id="app-confirm-icon" class="app-confirm-icon">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.3 3.9 2.8 17.2A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.8L13.7 3.9a2 2 0 0 0-3.4 0Z"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h2 id="app-confirm-title">Confirm action</h2>
            <p id="app-confirm-message">Are you sure you want to continue?</p>

            <div class="app-confirm-actions">
                <button type="button" id="app-confirm-cancel" class="app-confirm-cancel">
                    Cancel
                </button>
                <button type="button" id="app-confirm-accept" class="app-confirm-accept">
                    Continue
                </button>
            </div>
        </section>
    </div>

    <style>
        .app-confirm-overlay[hidden] { display: none !important; }

        .app-confirm-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: grid;
            place-items: center;
            padding: 20px;
            background: rgba(15, 23, 42, .52);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            animation: appConfirmFade .16s ease-out;
        }

        .app-confirm-dialog {
            width: min(100%, 430px);
            padding: 28px;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #fff;
            color: #0f172a;
            box-shadow: 0 24px 80px rgba(15, 23, 42, .25);
            animation: appConfirmEnter .18s ease-out;
        }

        .app-confirm-icon {
            display: grid;
            width: 48px;
            height: 48px;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 14px;
            background: #fff7ed;
            color: #c2410c;
        }

        .app-confirm-icon svg { width: 25px; height: 25px; }

        #app-confirm-title {
            margin: 0;
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -.025em;
        }

        #app-confirm-message {
            margin: 10px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .app-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 26px;
        }

        .app-confirm-actions button {
            min-height: 42px;
            padding: 10px 17px;
            border: 1px solid transparent;
            border-radius: 10px;
            font: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s ease, transform .15s ease;
        }

        .app-confirm-actions button:active { transform: scale(.98); }

        .app-confirm-cancel {
            border-color: #dbe2ea !important;
            background: #fff;
            color: #334155;
        }

        .app-confirm-cancel:hover { background: #f8fafc; }

        .app-confirm-accept {
            background: var(--theme-primary);
            color: #fff;
        }

        .app-confirm-accept:hover { background: var(--theme-primary-light); }

        .app-confirm-accept.is-danger { background: #dc2626; }
        .app-confirm-accept.is-danger:hover { background: #b91c1c; }

        @keyframes appConfirmFade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes appConfirmEnter {
            from { opacity: 0; transform: translateY(8px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @media (max-width: 480px) {
            .app-confirm-dialog { padding: 23px; border-radius: 16px; }
            .app-confirm-actions button { flex: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            .app-confirm-overlay, .app-confirm-dialog { animation: none; }
        }
    </style>

    <script>
    (function () {
        const modal = document.getElementById('app-confirm-modal');
        if (!modal || modal.dataset.initialized === 'true') return;
        modal.dataset.initialized = 'true';

        const title = document.getElementById('app-confirm-title');
        const message = document.getElementById('app-confirm-message');
        const icon = document.getElementById('app-confirm-icon');
        const cancel = document.getElementById('app-confirm-cancel');
        const accept = document.getElementById('app-confirm-accept');

        let pendingAction = null;
        let previousFocus = null;

        function closeModal() {
            modal.hidden = true;
            pendingAction = null;
            if (previousFocus && previousFocus.isConnected) previousFocus.focus();
        }

        function openModal(options, action) {
            previousFocus = document.activeElement;
            pendingAction = action;

            title.textContent = options.title || 'Confirm action';
            message.textContent = options.message || 'Are you sure you want to continue?';
            cancel.textContent = options.cancelText || 'Cancel';
            accept.textContent = options.confirmText || 'Continue';

            const danger = options.danger === true;
            accept.classList.toggle('is-danger', danger);
            icon.style.background = danger ? '#fef2f2' : '#fff7ed';
            icon.style.color = danger ? '#b91c1c' : '#c2410c';

            modal.hidden = false;
            cancel.focus();
        }

        cancel.addEventListener('click', closeModal);

        accept.addEventListener('click', function () {
            const action = pendingAction;
            modal.hidden = true;
            pendingAction = null;
            if (action) action();
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeModal();
        });

        document.addEventListener('keydown', function (event) {
            if (modal.hidden) return;

            if (event.key === 'Escape') {
                event.preventDefault();
                closeModal();
            }

            if (event.key === 'Tab') {
                event.preventDefault();
                (document.activeElement === cancel ? accept : cancel).focus();
            }
        });

        /*
         * Intercept forms whose existing inline onsubmit handler calls confirm().
         * The original confirmation is replaced by this modal. On approval,
         * the original form is submitted without rerunning the inline handler.
         */
        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;

            const handler = form.getAttribute('onsubmit') || '';
            if (!/\bconfirm\s*\(/i.test(handler)) return;

            event.preventDefault();
            event.stopImmediatePropagation();

            const text = form.dataset.confirmMessage || '';
            const isDelete = (form.method || 'get').toLowerCase() === 'delete'
                || form.querySelector('input[name="_method"][value="DELETE"]')
                || /delete|remove|permanent|destroy/i.test(handler + ' ' + text);

            openModal({
                title: form.dataset.confirmTitle || (
                    isDelete ? 'Delete permanently?' : 'Are you sure?'
                ),
                message: text || (
                    isDelete
                        ? 'This action may be permanent. Do you want to continue?'
                        : 'Please confirm that you want to perform this action.'
                ),
                confirmText: form.dataset.confirmButton || (
                    isDelete ? 'Delete' : 'Confirm'
                ),
                cancelText: 'Cancel',
                danger: Boolean(isDelete)
            }, function () {
                /*
                 * Native form.submit() avoids firing the intercepted submit event
                 * a second time. Laravel still receives the form's CSRF token.
                 */
                HTMLFormElement.prototype.submit.call(form);
            });
        }, true);
    })();
    </script>
</body>
</html>