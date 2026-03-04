<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">

            @auth
            <div style="position:fixed;top:0;right:0;z-index:999;padding:0.6rem 1.25rem;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="
                        background:rgba(15,17,23,0.07);
                        border:1px solid rgba(15,17,23,0.1);
                        border-radius:8px;
                        padding:0.35rem 0.9rem;
                        font-family:'DM Sans',sans-serif;
                        font-size:0.75rem;
                        color:#7a7469;
                        cursor:pointer;
                    ">⎋ Keluar</button>
                </form>
            </div>
            @endauth

            <main>
                @yield('content')
            </main>

        </div>
    </body>
</html>