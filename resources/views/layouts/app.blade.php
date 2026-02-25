<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan') — SiPerpus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --ink:      #0d0f12;
            --paper:    #f5f2eb;
            --accent:   #c8430a;
            --accent2:  #1a6b3a;
            --muted:    #6b7280;
            --border:   #e2ddd5;
            --card-bg:  #fffef9;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Sora', sans-serif;
            background-color: var(--paper);
            color: var(--ink);
        }

        .mono { font-family: 'JetBrains Mono', monospace; }

        /* Scanline texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,0,0,0.012) 2px,
                rgba(0,0,0,0.012) 4px
            );
            pointer-events: none;
            z-index: 9999;
        }

        .nav-link {
            position: relative;
            font-weight: 500;
            font-size: 0.875rem;
            letter-spacing: 0.02em;
            transition: color 0.2s;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.25s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after { width: 100%; }
        .nav-link.active { color: var(--accent); }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.04);
        }

        .btn-primary {
            background: var(--ink);
            color: var(--paper);
            font-weight: 600;
            border-radius: 8px;
            padding: 0.625rem 1.5rem;
            transition: transform 0.15s, box-shadow 0.15s;
            letter-spacing: 0.02em;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13,15,18,0.25);
        }
        .btn-primary:active { transform: translateY(0); }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .badge-green  { background: #dcfce7; color: var(--accent2); }
        .badge-orange { background: #fff0e6; color: var(--accent); }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-gray   { background: #f3f4f6; color: var(--muted); }

        /* Animated entrance */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-up { animation: slideUp 0.45s ease both; }
        .delay-1    { animation-delay: 0.1s; }
        .delay-2    { animation-delay: 0.2s; }
        .delay-3    { animation-delay: 0.3s; }

        /* Toast */
        #toast {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 1000;
            transform: translateX(150%);
            transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1);
        }
        #toast.show { transform: translateX(0); }

        /* Decorative stamp border */
        .stamp {
            border: 2px dashed var(--border);
            border-radius: 8px;
        }

        /* Pulsing dot indicator */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(1.4); }
        }
        .live-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse-dot 1.5s ease-in-out infinite;
        }
    </style>
    @yield('extra_head')
</head>
<body class="min-h-full flex flex-col">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 border-b" style="background:var(--card-bg);border-color:var(--border);">
        <div class="max-w-7xl mx-auto px-6 py-0 flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('scan.index') }}" class="flex items-center gap-3 group">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm" style="background:var(--ink);color:var(--paper);">
                    📚
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight" style="color:var(--ink);">SiPerpus</span>
                    <span class="text-xs block" style="color:var(--muted);line-height:1;">Sistem Perpustakaan Digital</span>
                </div>
            </a>

            {{-- Nav Links --}}
            <div class="flex items-center gap-8">
                <a href="{{ route('scan.index') }}"
                   class="nav-link {{ request()->routeIs('scan.index') ? 'active' : '' }}"
                   style="color:var(--ink);">
                    Scan Masuk
                </a>
                <a href="{{ route('dashboard.index') }}"
                   class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                   style="color:var(--ink);">
                    Dashboard
                </a>
            </div>

            {{-- Live Indicator --}}
            <div class="flex items-center gap-2">
                <div class="live-dot"></div>
                <span class="text-xs font-medium" style="color:var(--muted);" id="clock">—</span>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t py-6 text-center text-xs" style="border-color:var(--border);color:var(--muted);">
        <span class="mono">SiPerpus v1.0</span> &mdash; Sistem Informasi Perpustakaan &copy; {{ date('Y') }}
    </footer>

    {{-- Toast Notification --}}
    <div id="toast" class="card px-4 py-3 flex items-start gap-3 min-w-72 max-w-sm">
        <div id="toast-icon" class="text-lg mt-0.5">✅</div>
        <div>
            <div id="toast-title" class="font-semibold text-sm"></div>
            <div id="toast-msg"   class="text-xs mt-0.5" style="color:var(--muted);"></div>
        </div>
        <button onclick="hideToast()" class="ml-auto text-gray-400 hover:text-gray-700 text-lg leading-none">&times;</button>
    </div>

    <script>
        // Live clock
        function updateClock() {
            const el = document.getElementById('clock');
            if (el) {
                const now = new Date();
                el.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Toast
        function showToast(title, msg, type = 'success') {
            const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
            document.getElementById('toast-icon').textContent = icons[type] || icons.success;
            document.getElementById('toast-title').textContent = title;
            document.getElementById('toast-msg').innerHTML = msg;
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            setTimeout(hideToast, 4500);
        }
        function hideToast() {
            document.getElementById('toast').classList.remove('show');
        }
    </script>

    @yield('scripts')
</body>
</html>
