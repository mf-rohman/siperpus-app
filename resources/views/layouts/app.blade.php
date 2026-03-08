<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SiPerpus'))</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:      #0d0f12;
            --paper:    #f5f2eb;
            --muted:    #6b7280;
            --border:   #e5e7eb;
            --card-bg:  #ffffff;
            --accent:   #c8430a;
            --accent2:  #1a6b3a;
            --nav-h:    56px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--paper); color: var(--ink); }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .hidden { display: none !important; }

        /* ── BADGES ── */
        .badge { display:inline-flex;align-items:center;padding:.25rem .75rem;border-radius:100px;font-size:.75rem;font-weight:600; }
        .badge-green  { background:rgba(26,107,58,.12);  color:var(--accent2); }
        .badge-orange { background:rgba(200,67,10,.1);   color:var(--accent); }
        .badge-blue   { background:rgba(29,78,216,.1);   color:#1d4ed8; }
        .badge-gray   { background:rgba(0,0,0,.05);      color:var(--muted); }

        /* ── CARD ── */
        .card { background:var(--card-bg);border-radius:16px;border:1px solid var(--border);box-shadow:0 1px 3px rgba(0,0,0,.06); }

        /* ── BUTTON ── */
        .btn-primary {
            display:inline-flex;align-items:center;gap:.4rem;
            padding:.5rem 1rem;background:var(--ink);color:var(--paper);
            border:none;border-radius:8px;font-size:.85rem;font-weight:600;
            text-decoration:none;cursor:pointer;transition:opacity .2s;
            font-family:'Inter',sans-serif;
        }
        .btn-primary:hover { opacity:.85; }

        /* ── MISC UTILS ── */
        .live-dot { width:8px;height:8px;border-radius:50%;background:var(--accent2);animation:livePulse 2s infinite; }
        @keyframes livePulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }
        .animate-up { animation:animUp .5s ease both; }
        .delay-1 { animation-delay:.1s; } .delay-2 { animation-delay:.2s; } .delay-3 { animation-delay:.3s; }
        @keyframes animUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

        /* Flex/grid utils */
        .flex{display:flex}.flex-col{flex-direction:column}.items-center{align-items:center}
        .items-start{align-items:flex-start}.items-end{align-items:flex-end}
        .justify-between{justify-content:space-between}.justify-center{justify-content:center}
        .gap-2{gap:.5rem}.gap-3{gap:.75rem}.gap-4{gap:1rem}.gap-6{gap:1.5rem}
        .grid{display:grid}.grid-cols-2{grid-template-columns:repeat(2,1fr)}

        /* Spacing */
        .mt-1{margin-top:.25rem}.mt-2{margin-top:.5rem}.mt-3{margin-top:.75rem}
        .mt-4{margin-top:1rem}.mt-6{margin-top:1.5rem}.mt-8{margin-top:2rem}.mt-10{margin-top:2.5rem}
        .mb-1{margin-bottom:.25rem}.mb-2{margin-bottom:.5rem}.mb-3{margin-bottom:.75rem}
        .mb-4{margin-bottom:1rem}.mb-6{margin-bottom:1.5rem}.mb-8{margin-bottom:2rem}.mb-10{margin-bottom:2.5rem}
        .ml-2{margin-left:.5rem}.mr-2{margin-right:.5rem}
        .p-6{padding:1.5rem}.px-3{padding-left:.75rem;padding-right:.75rem}
        .px-4{padding-left:1rem;padding-right:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}
        .px-10{padding-left:2.5rem;padding-right:2.5rem}
        .py-2{padding-top:.5rem;padding-bottom:.5rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}
        .py-12{padding-top:3rem;padding-bottom:3rem}.py-16{padding-top:4rem;padding-bottom:4rem}
        .pt-4{padding-top:1rem}.px-1\.5{padding-left:.375rem;padding-right:.375rem}
        .py-0\.5{padding-top:.125rem;padding-bottom:.125rem}

        /* Size */
        .w-5{width:1.25rem}.h-5{height:1.25rem}.w-2\.5{width:.625rem}.h-2\.5{height:.625rem}
        .w-3\.5{width:.875rem}.h-3\.5{height:.875rem}.h-4{height:1rem}.h-10{height:2.5rem}
        .w-16{width:4rem}.w-20{width:5rem}.w-36{width:9rem}.w-48{width:12rem}
        .min-w-0{min-width:0}.max-w-2xl{max-width:42rem}.max-w-7xl{max-width:80rem}
        .mx-auto{margin-left:auto;margin-right:auto}
        .min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}
        .overflow-x-auto{overflow-x:auto}.overflow-y-auto{overflow-y:auto}

        /* Typography */
        .text-xs{font-size:.75rem}.text-sm{font-size:.875rem}.text-base{font-size:1rem}
        .text-xl{font-size:1.25rem}.text-3xl{font-size:1.875rem}.text-4xl{font-size:2.25rem}
        .font-medium{font-weight:500}.font-semibold{font-weight:600}.font-bold{font-weight:700}.font-extrabold{font-weight:800}
        .uppercase{text-transform:uppercase}.tracking-widest{letter-spacing:.1em}.tracking-wider{letter-spacing:.05em}
        .text-center{text-align:center}.text-left{text-align:left}.text-white{color:#fff}
        .truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .opacity-50{opacity:.5}.opacity-60{opacity:.6}.block{display:block}

        /* Borders/radius */
        .rounded{border-radius:.25rem}.rounded-sm{border-radius:.125rem}
        .rounded-full{border-radius:9999px}.rounded-lg{border-radius:.5rem}
        .border{border-width:1px}.border-2{border-width:2px}

        /* Misc */
        .relative{position:relative}.absolute{position:absolute}.right-4{right:1rem}
        .top-1\/2{top:50%}.-translate-y-1\/2{transform:translateY(-50%)}
        .space-y-3>*+*{margin-top:.75rem}.flex-wrap{flex-wrap:wrap}.flex-shrink-0{flex-shrink:0}
        .shadow-sm{box-shadow:0 1px 2px rgba(0,0,0,.05)}.transition{transition:all .15s ease}
        .hover\:bg-gray-100:hover{background:rgba(0,0,0,.05)}
        .animate-spin{animation:spin 1s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        .sm\:flex-row{flex-direction:row}.sm\:items-center{align-items:center}
        .lg\:grid-cols-4{grid-template-columns:repeat(4,1fr)}
        .lg\:grid-cols-3{grid-template-columns:repeat(3,1fr)}
        .lg\:col-span-2{grid-column:span 2}
        .mt-0\.5{margin-top:.125rem}
        .bg-emerald-600{background:#059669}.hover\:bg-emerald-700:hover{background:#047857}

        /* ── NAVBAR ── */
        .navbar {
            height: var(--nav-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            background: #fff;
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 999;
        }
        .navbar-left { display:flex;align-items:center;gap:1rem; }
        .navbar-logo {
            display:flex;align-items:center;gap:.6rem;
            text-decoration:none;color:var(--ink);flex-shrink:0;
        }
        .navbar-logo-icon {
            width:32px;height:32px;background:var(--ink);
            border-radius:8px;display:grid;place-items:center;font-size:1rem;
        }
        .navbar-logo-name { font-weight:700;font-size:.9rem;line-height:1.2; }
        .navbar-logo-name small { display:block;font-weight:400;font-size:.6rem;color:var(--muted); }
        .navbar-divider { width:1px;height:20px;background:var(--border);flex-shrink:0; }
        .navbar-tabs { display:flex;align-items:center;gap:.1rem; }
        .nav-tab {
            padding:.4rem .9rem;border-radius:8px;font-size:.82rem;font-weight:600;
            text-decoration:none;color:var(--muted);transition:all .15s ease;
            border-bottom:2px solid transparent;white-space:nowrap;
        }
        .nav-tab:hover { color:var(--ink);background:rgba(13,15,18,.04); }
        .nav-tab.active { color:var(--ink);border-bottom:2px solid var(--accent); }
        .navbar-right { display:flex;align-items:center;gap:.75rem; }
        .navbar-clock {
            font-family:'JetBrains Mono',monospace;font-size:.72rem;
            color:var(--accent2);display:flex;align-items:center;gap:.4rem;
            white-space:nowrap;
        }
        .navbar-clock::before {
            content:'';width:7px;height:7px;border-radius:50%;
            background:var(--accent2);animation:livePulse 2s infinite;
        }
        .btn-logout {
            padding:.35rem .85rem;background:rgba(13,15,18,.05);
            border:1px solid var(--border);border-radius:8px;
            font-size:.75rem;font-family:'Inter',sans-serif;
            color:var(--muted);cursor:pointer;transition:all .2s ease;
            white-space:nowrap;
        }
        .btn-logout:hover { background:rgba(200,67,10,.08);color:var(--accent);border-color:rgba(200,67,10,.2); }

        /* Mobile hamburger */
        .navbar-hamburger {
            display:none;flex-direction:column;gap:5px;
            background:none;border:none;cursor:pointer;padding:.4rem;
        }
        .navbar-hamburger span {
            display:block;width:22px;height:2px;
            background:var(--ink);border-radius:2px;
            transition:all .3s ease;
        }

        /* Mobile drawer */
        .mobile-drawer {
            display:none;position:fixed;top:var(--nav-h);left:0;right:0;
            background:#fff;border-bottom:1px solid var(--border);
            z-index:998;padding:1rem 1.5rem;
            flex-direction:column;gap:.25rem;
            box-shadow:0 8px 24px rgba(0,0,0,.1);
        }
        .mobile-drawer.open { display:flex; }
        .mobile-drawer .nav-tab {
            padding:.65rem 1rem;border-radius:8px;border-bottom:none;
            display:block;font-size:.9rem;
        }
        .mobile-drawer .nav-tab.active {
            background:rgba(13,15,18,.06);border-bottom:none;
            border-left:3px solid var(--accent);border-radius:0 8px 8px 0;
        }
        .mobile-drawer-footer {
            margin-top:.75rem;padding-top:.75rem;
            border-top:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .navbar { padding:0 1.25rem; }
            .navbar-divider { display:none; }
            .navbar-tabs { display:none; }
            .navbar-clock { display:none; }
            .btn-logout { display:none; }
            .navbar-hamburger { display:flex; }
            .navbar-logo-name small { display:none; }
        }

        @media (max-width: 480px) {
            .navbar-logo-name { font-size:.82rem; }
        }
    </style>
    @yield('extra_head')
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar">
        <div class="navbar-left">
            <a href="{{ route('scan.index') }}" class="navbar-logo">
                <div class="navbar-logo-icon">📚</div>
                <div class="navbar-logo-name">
                    SiPerpus
                    <small>Sistem Perpustakaan Digital</small>
                </div>
            </a>
            <div class="navbar-divider"></div>
            <div class="navbar-tabs">
                <a href="{{ route('scan.index') }}"   class="nav-tab {{ request()->routeIs('scan.index')    ? 'active' : '' }}">Scan Masuk</a>
                <a href="{{ route('dashboard') }}"    class="nav-tab {{ request()->routeIs('dashboard')     ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('registrasi.index') }}" class="nav-tab {{ request()->routeIs('registrasi.index') ? 'active' : '' }}">Registrasi</a>
                <a href="{{ route('berita.index') }}" class="nav-tab {{ request()->routeIs('berita.index')  ? 'active' : '' }}">Berita</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="navbar-clock" id="navClock">--:--:--</div>
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">⎋ Keluar</button>
            </form>
            @endauth
            <button class="navbar-hamburger" id="hamburgerBtn" onclick="toggleDrawer()">
                <span id="hb1"></span><span id="hb2"></span><span id="hb3"></span>
            </button>
        </div>
    </nav>

    {{-- MOBILE DRAWER --}}
    <div class="mobile-drawer" id="mobileDrawer">
        <a href="{{ route('scan.index') }}"       class="nav-tab {{ request()->routeIs('scan.index')        ? 'active' : '' }}" onclick="closeDrawer()">📱 Scan Masuk</a>
        <a href="{{ route('dashboard') }}"        class="nav-tab {{ request()->routeIs('dashboard')         ? 'active' : '' }}" onclick="closeDrawer()">📊 Dashboard</a>
        <a href="{{ route('registrasi.index') }}" class="nav-tab {{ request()->routeIs('registrasi.index')  ? 'active' : '' }}" onclick="closeDrawer()">📋 Registrasi</a>
        <a href="{{ route('berita.index') }}"     class="nav-tab {{ request()->routeIs('berita.index')      ? 'active' : '' }}" onclick="closeDrawer()">📢 Berita</a>
        <div class="mobile-drawer-footer">
            <span style="font-family:'JetBrains Mono',monospace;font-size:.72rem;color:var(--accent2);" id="mobileClockDrawer">--:--:--</span>
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">⎋ Keluar</button>
            </form>
            @endauth
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    @yield('scripts')

    <script>
        function tick() {
            const t = new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
            document.getElementById('navClock').textContent = t;
            const m = document.getElementById('mobileClockDrawer');
            if (m) m.textContent = t;
        }
        tick(); setInterval(tick, 1000);

        let drawerOpen = false;
        function toggleDrawer() {
            drawerOpen = !drawerOpen;
            document.getElementById('mobileDrawer').classList.toggle('open', drawerOpen);
            // Animate hamburger to X
            document.getElementById('hb1').style.transform = drawerOpen ? 'translateY(7px) rotate(45deg)' : '';
            document.getElementById('hb2').style.opacity   = drawerOpen ? '0' : '1';
            document.getElementById('hb3').style.transform = drawerOpen ? 'translateY(-7px) rotate(-45deg)' : '';
        }
        function closeDrawer() {
            drawerOpen = false;
            document.getElementById('mobileDrawer').classList.remove('open');
            document.getElementById('hb1').style.transform = '';
            document.getElementById('hb2').style.opacity   = '1';
            document.getElementById('hb3').style.transform = '';
        }

        function showToast(title, msg, type='success') {
            const colors = {success:'#1a6b3a', error:'#c8430a', warning:'#b45309'};
            const t = document.createElement('div');
            t.style.cssText = `
                position:fixed;bottom:1.5rem;right:1.5rem;z-index:99999;
                background:#0d0f12;color:#f5f2eb;
                padding:.875rem 1.25rem;border-radius:12px;
                box-shadow:0 8px 32px rgba(0,0,0,.25);
                display:flex;gap:.75rem;align-items:flex-start;
                max-width:300px;width:calc(100vw - 3rem);
                animation:slideInToast .3s ease;
                border-left:3px solid ${colors[type]||colors.success};
                font-family:'Inter',sans-serif;
            `;
            t.innerHTML = `<div><div style="font-weight:700;font-size:.82rem;margin-bottom:.2rem;">${title}</div>
                <div style="font-size:.75rem;opacity:.7;line-height:1.4;">${msg}</div></div>`;
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity='0';t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, 3200);
        }

        // Close drawer on outside click
        document.addEventListener('click', e => {
            if (drawerOpen && !e.target.closest('#mobileDrawer') && !e.target.closest('#hamburgerBtn')) {
                closeDrawer();
            }
        });
    </script>

    <style>
        @keyframes slideInToast {
            from{opacity:0;transform:translateY(12px)}
            to{opacity:1;transform:translateY(0)}
        }
    </style>
</body>
</html>