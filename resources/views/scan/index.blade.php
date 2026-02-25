@extends('layouts.app')

@section('title', 'Scan KTM — Masuk Perpustakaan')

@section('extra_head')
<style>
    .scan-wrap {
        min-height: calc(100vh - 5rem);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    @media (max-width: 900px) {
        .scan-wrap { grid-template-columns: 1fr; }
        .scan-left  { display: none; }
    }

    /* Input Scan Field */
    .scan-input {
        width: 100%;
        padding: 1rem 1.25rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.35rem;
        font-weight: 500;
        border: 2.5px solid var(--border);
        border-radius: 12px;
        background: var(--card-bg);
        color: var(--ink);
        outline: none;
        letter-spacing: 0.12em;
        transition: border-color 0.2s, box-shadow 0.2s;
        caret-color: var(--accent);
    }
    .scan-input:focus {
        border-color: var(--ink);
        box-shadow: 0 0 0 4px rgba(13,15,18,0.08);
    }
    .scan-input.error {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(200,67,10,0.1);
        animation: shake 0.4s ease;
    }
    .scan-input.success {
        border-color: var(--accent2);
        box-shadow: 0 0 0 4px rgba(26,107,58,0.1);
    }

    @keyframes shake {
        0%,100% { transform: translateX(0); }
        20%      { transform: translateX(-8px); }
        40%      { transform: translateX(8px); }
        60%      { transform: translateX(-5px); }
        80%      { transform: translateX(5px); }
    }

    /* Student Card */
    .student-card {
        background: linear-gradient(135deg, #0d0f12 0%, #1a2236 100%);
        color: var(--paper);
        border-radius: 16px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    .student-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .student-card::after {
        content: '';
        position: absolute;
        bottom: -60px; left: -30px;
        width: 250px; height: 250px;
        border-radius: 50%;
        background: rgba(200,67,10,0.12);
    }
    .student-card .nim-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        opacity: 0.6;
        text-transform: uppercase;
    }
    .student-card .student-name {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.15;
        margin: 0.5rem 0 0.25rem;
        letter-spacing: -0.02em;
    }

    /* Left panel illustration */
    .scan-left {
        background: var(--ink);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 4rem;
        position: relative;
        overflow: hidden;
    }
    .scan-left::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(circle at 20% 50%, rgba(200,67,10,0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(26,107,58,0.12) 0%, transparent 50%);
    }
    .qr-art {
        font-size: 7rem;
        line-height: 1;
        filter: drop-shadow(0 8px 32px rgba(0,0,0,0.5));
    }

    /* History chips */
    .history-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        background: var(--card-bg);
        border: 1px solid var(--border);
        font-family: 'JetBrains Mono', monospace;
        transition: all 0.2s;
    }
    .history-chip:hover {
        border-color: var(--ink);
        transform: translateY(-1px);
    }

    @keyframes cardReveal {
        from { opacity: 0; transform: scale(0.96) translateY(12px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .card-reveal { animation: cardReveal 0.4s cubic-bezier(0.34,1.4,0.64,1) both; }
</style>
@endsection

@section('content')
<div class="scan-wrap">

    {{-- Left Panel --}}
    <div class="scan-left">
        <div style="position:relative;z-index:1;text-align:center;">
            <div class="qr-art">📱</div>
            <h2 class="text-3xl font-extrabold mt-6 mb-3" style="color:var(--paper);letter-spacing:-0.03em;">
                Scan KTM<br>Anda
            </h2>
            <p class="text-sm" style="color:rgba(245,242,235,0.5);max-width:280px;margin:0 auto;line-height:1.7;">
                Arahkan QR code pada Kartu Tanda Mahasiswa ke scanner untuk mencatat kehadiran di perpustakaan.
            </p>

            {{-- Animated Lines --}}
            <div class="mt-10 space-y-3">
                @foreach(['Scan otomatis', 'Data real-time', 'Tanpa antri'] as $i => $f)
                <div class="flex items-center gap-3 animate-up" style="animation-delay:{{ ($i+1) * 0.1 }}s;">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                          style="background:var(--accent);color:white;">✓</span>
                    <span class="text-sm font-medium" style="color:rgba(245,242,235,0.75);">{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="flex flex-col justify-center px-10 py-12 max-w-2xl" style="background:var(--paper);">

        {{-- Header --}}
        <div class="animate-up mb-8">
            <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:var(--accent);">
                Perpustakaan Universitas
            </p>
            <h1 class="text-4xl font-extrabold" style="letter-spacing:-0.03em;color:var(--ink);">
                Selamat Datang 👋
            </h1>
            <p class="mt-2 text-sm" style="color:var(--muted);">
                Scan atau ketik NIM untuk mencatat kunjungan
            </p>
        </div>

        {{-- Scan Form --}}
        <div class="animate-up delay-1">
            <form id="scanForm" autocomplete="off">
                @csrf
                <label class="block text-xs font-semibold uppercase tracking-widest mb-2" style="color:var(--muted);">
                    Nomor Induk Mahasiswa (NIM)
                </label>
                <div class="relative">
                    <input
                        type="text"
                        id="nimInput"
                        name="nim"
                        class="scan-input"
                        placeholder="Scan QR atau ketik NIM…"
                        autofocus
                        maxlength="20"
                        inputmode="numeric"
                    >
                    <div id="spinnerIcon" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                        <div class="w-5 h-5 border-2 rounded-full animate-spin" style="border-color:var(--border);border-top-color:var(--ink);"></div>
                    </div>
                    <div id="checkIcon" class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-xl">✅</div>
                    <div id="errorIcon" class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-xl">❌</div>
                </div>

                {{-- Hint --}}
                <p class="mt-2 text-xs" style="color:var(--muted);">
                    ⚡ QR scanner akan submit otomatis &nbsp;·&nbsp; Atau tekan
                    <kbd class="px-1.5 py-0.5 rounded text-xs mono" style="background:var(--border)">Enter</kbd>
                </p>
            </form>
        </div>

        {{-- Result Area --}}
        <div id="resultArea" class="mt-8 hidden animate-up">

            {{-- Success Card --}}
            <div id="successCard" class="student-card card-reveal hidden">
                <div style="position:relative;z-index:1;">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <span class="nim-badge" id="resNim">NIM: —</span>
                        <span class="badge badge-green text-xs">✓ Tercatat</span>
                    </div>

                    {{-- Name --}}
                    <div class="student-name" id="resNama">—</div>
                    <div class="text-sm opacity-60 mb-6" id="resJurusan">—</div>

                    {{-- Grid Info --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <div class="text-xs opacity-50 uppercase tracking-wider mb-1">Angkatan</div>
                            <div class="font-semibold text-sm" id="resAngkatan">—</div>
                        </div>
                        <div>
                            <div class="text-xs opacity-50 uppercase tracking-wider mb-1">No. HP</div>
                            <div class="font-semibold text-sm" id="resHp">—</div>
                        </div>
                        <div>
                            <div class="text-xs opacity-50 uppercase tracking-wider mb-1">Email</div>
                            <div class="font-semibold text-sm text-xs" id="resEmail">—</div>
                        </div>
                        <div>
                            <div class="text-xs opacity-50 uppercase tracking-wider mb-1">Waktu Masuk</div>
                            <div class="font-bold text-sm mono" id="resWaktu" style="color:#86efac;">—</div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center gap-2 pt-4" style="border-top:1px solid rgba(255,255,255,0.1);">
                        <div class="live-dot"></div>
                        <span class="text-xs opacity-60" id="resTanggal">—</span>
                    </div>
                </div>
            </div>

            {{-- Error Card --}}
            <div id="errorCard" class="card card-reveal hidden p-6">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">😕</div>
                    <div>
                        <h3 class="font-bold text-base mb-1" style="color:var(--accent);">Mahasiswa Tidak Ditemukan</h3>
                        <p class="text-sm" id="errorMsg" style="color:var(--muted);">—</p>
                        <p class="mt-3 text-xs" style="color:var(--muted);">
                            Pastikan NIM terdaftar dalam sistem. Hubungi petugas perpustakaan jika butuh bantuan.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Scans --}}
        <div class="mt-10 animate-up delay-3">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-widest" style="color:var(--muted);">Kunjungan Hari Ini</span>
                <span id="todayCount" class="badge badge-green">— orang</span>
            </div>
            <div id="recentScans" class="flex flex-wrap gap-2">
                <span class="history-chip" style="opacity:0.5;">Belum ada kunjungan hari ini</span>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
const nimInput     = document.getElementById('nimInput');
const scanForm     = document.getElementById('scanForm');
const resultArea   = document.getElementById('resultArea');
const successCard  = document.getElementById('successCard');
const errorCard    = document.getElementById('errorCard');
const spinnerIcon  = document.getElementById('spinnerIcon');
const checkIcon    = document.getElementById('checkIcon');
const errorIcon    = document.getElementById('errorIcon');

let debounceTimer = null;
let isSubmitting  = false;

// Auto-submit: when QR scanner fires Enter, or when input stops changing
nimInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        if (nimInput.value.trim().length >= 5) {
            submitScan();
        }
    }
});

// Auto-detect QR scan: scanner types fast and appends Enter
nimInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const val = nimInput.value.trim();
    if (val.length >= 5) {
        debounceTimer = setTimeout(() => {
            // If value hasn't changed after 500ms, assume scan complete
            submitScan();
        }, 600);
    }
});

async function submitScan() {
    if (isSubmitting) return;
    const nim = nimInput.value.trim();
    if (!nim || nim.length < 5) return;

    isSubmitting = true;
    setLoading(true);

    try {
        const res = await fetch('/api/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ nim }),
        });

        const data = await res.json();

        if (data.success) {
            showSuccess(data);
        } else {
            showError(data.message);
        }
    } catch (err) {
        showError('Terjadi kesalahan koneksi. Silakan coba lagi.');
    } finally {
        setLoading(false);
        isSubmitting = false;
        nimInput.value = '';
        // Refocus for next scan
        setTimeout(() => { nimInput.focus(); }, 400);
    }
}

function setLoading(loading) {
    spinnerIcon.classList.toggle('hidden', !loading);
    checkIcon.classList.add('hidden');
    errorIcon.classList.add('hidden');
    nimInput.disabled = loading;
}

function showSuccess(data) {
    const m = data.mahasiswa;
    const k = data.kunjungan;

    document.getElementById('resNim').textContent     = 'NIM: ' + m.nim;
    document.getElementById('resNama').textContent    = m.nama;
    document.getElementById('resJurusan').textContent = m.jurusan;
    document.getElementById('resAngkatan').textContent = m.angkatan;
    document.getElementById('resHp').textContent      = m.no_hp || '—';
    document.getElementById('resEmail').textContent   = m.email;
    document.getElementById('resWaktu').textContent   = k.waktu_masuk;
    document.getElementById('resTanggal').textContent = k.tanggal;

    resultArea.classList.remove('hidden');
    successCard.classList.remove('hidden');
    errorCard.classList.add('hidden');

    // Icons
    checkIcon.classList.remove('hidden');
    nimInput.classList.remove('error');
    nimInput.classList.add('success');
    setTimeout(() => nimInput.classList.remove('success'), 2000);

    showToast('Berhasil Dicatat!', `${m.nama} · ${m.jurusan}`, 'success');
    loadRecentScans();
}

function showError(msg) {
    document.getElementById('errorMsg').innerHTML = msg;

    resultArea.classList.remove('hidden');
    errorCard.classList.remove('hidden');
    successCard.classList.add('hidden');

    errorIcon.classList.remove('hidden');
    nimInput.classList.add('error');
    setTimeout(() => nimInput.classList.remove('error'), 1000);

    showToast('Tidak Ditemukan', msg, 'error');
}

// Load today's visits
async function loadRecentScans() {
    try {
        const res  = await fetch('/api/kunjungan/stats');
        const data = await res.json();
        const count = data.stats.hari_ini;
        document.getElementById('todayCount').textContent = count + ' orang';

        // Recent names
        const container = document.getElementById('recentScans');
        const recent = data.terbaru.slice(0, 8);
        if (recent.length) {
            container.innerHTML = recent.map(k => `
                <span class="history-chip">
                    <span>${k.nama.split(' ')[0]}</span>
                    <span style="opacity:0.5;font-size:0.65rem;">${k.waktu_masuk.split(' ')[1]}</span>
                </span>
            `).join('');
        }
    } catch(e) {}
}

loadRecentScans();
nimInput.focus();
document.addEventListener('click', () => {
    nimInput.focus()
});
</script>
@endsection
