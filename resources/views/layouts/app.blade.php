<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- SCRIPT KEEPALIVE & DETEKSI SESI EXPIRED OTOMATIS KE LOGIN --}}
        <script>
            (function() {
                const loginUrl = "{{ route('login') }}";
                const pingIntervalMs = 5 * 60 * 1000;
                let lastPing = Date.now();
                let isRedirecting = false;

                function doRedirectLogin() {
                    if (isRedirecting) return;
                    isRedirecting = true;
                    window.location.replace(loginUrl);
                    setTimeout(() => { window.location.href = loginUrl; }, 100);
                }

                if (window.fetch) {
                    const originalFetch = window.fetch;
                    window.fetch = async function(...args) {
                        try {
                            const res = await originalFetch(...args);
                            if (res && (res.status === 419 || res.status === 401)) {
                                doRedirectLogin();
                            }
                            return res;
                        } catch(err) {
                            throw err;
                        }
                    };
                }

                function refreshCsrfToken() {
                    fetch("{{ route('refresh.csrf') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.status === 401 || response.status === 419 || response.status === 403 || response.redirected) {
                            doRedirectLogin();
                            return null;
                        }
                        if (response.ok) return response.json();
                        throw new Error('Gagal ping refresh csrf');
                    })
                    .then(data => {
                        if (data && data.token) {
                            const metaToken = document.querySelector('meta[name="csrf-token"]');
                            if (metaToken) metaToken.setAttribute('content', data.token);

                            document.querySelectorAll('input[name="_token"]').forEach(input => {
                                input.value = data.token;
                            });
                        }
                    })
                    .catch(err => {
                        console.debug('Keepalive ping check:', err);
                    });
                }

                setInterval(function() {
                    refreshCsrfToken();
                    lastPing = Date.now();
                }, pingIntervalMs);

                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'visible') {
                        if (Date.now() - lastPing > 2 * 60 * 1000) {
                            refreshCsrfToken();
                            lastPing = Date.now();
                        }
                    }
                });

                window.addEventListener('focus', function() {
                    if (Date.now() - lastPing > 2 * 60 * 1000) {
                        refreshCsrfToken();
                        lastPing = Date.now();
                    }
                });
            })();
        </script>
    </body>
</html>
