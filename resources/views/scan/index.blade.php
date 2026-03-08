@extends('layouts.app')

@section('title', 'Scan KTM — Masuk Perpustakaan')

@section('extra_head')
<style>
    :root {
        --paper: #f5f2eb;
        --ink: #0d0f12;
        --muted: #64748b;
        --accent: #c8430a;
        --accent2: #1a6b3a;
        --border: #e2e8f0;
        --card-bg: #ffffff;
    }

    .scan-wrap {
        min-height: calc(100vh - var(--nav-h));
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    /* ── LEFT PANEL ── */
    .scan-left {
        background: var(--ink);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 4rem 3rem;
        position: relative;
        overflow: hidden;
    }
    .scan-left::before {
        content: '';
        position: absolute; inset: 0;
        background-image:
            radial-gradient(circle at 20% 50%, rgba(200,67,10,.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(26,107,58,.12) 0%, transparent 50%);
    }
    .qr-art { font-size: 6rem; line-height: 1; filter: drop-shadow(0 8px 32px rgba(0,0,0,.5)); }

    /* ── RIGHT PANEL ── */
    .scan-right {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem 3.5rem;
        background: var(--paper);
        overflow-y: auto;
    }

    /* ── INPUT ── */
    .scan-input {
        width: 100%;
        padding: 1rem 1.25rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 1.25rem;
        font-weight: 500;
        border: 2.5px solid var(--border);
        border-radius: 12px;
        background: var(--card-bg);
        color: var(--ink);
        outline: none;
        letter-spacing: .12em;
        transition: border-color .2s, box-shadow .2s;
        caret-color: var(--accent);
    }
    .scan-input:focus { border-color:var(--ink); box-shadow:0 0 0 4px rgba(13,15,18,.08); }
    .scan-input.error  { border-color:var(--accent); box-shadow:0 0 0 4px rgba(200,67,10,.1); animation:shake .4s ease; }
    .scan-input.success{ border-color:var(--accent2); box-shadow:0 0 0 4px rgba(26,107,58,.1); }
    @keyframes shake {
        0%,100%{transform:translateX(0)} 20%{transform:translateX(-8px)}
        40%{transform:translateX(8px)}   60%{transform:translateX(-5px)} 80%{transform:translateX(5px)}
    }

    /* ── STUDENT CARD ── */
    .student-card {
        background: linear-gradient(135deg,#0d0f12 0%,#1a2236 100%);
        color: var(--paper);
        border-radius: 16px;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
    }
    .student-card::before {
        content:'';position:absolute;top:-40px;right:-40px;
        width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);
    }
    .student-card::after {
        content:'';position:absolute;bottom:-60px;left:-30px;
        width:250px;height:250px;border-radius:50%;background:rgba(200,67,10,.12);
    }
    .nim-badge { font-family:'JetBrains Mono',monospace;font-size:.72rem;letter-spacing:.15em;opacity:.6;text-transform:uppercase; }
    .student-name { font-size:1.4rem;font-weight:800;line-height:1.15;margin:.4rem 0 .2rem;letter-spacing:-.02em; }

    /* ── HISTORY CHIP ── */
    .history-chip {
        display:inline-flex;align-items:center;gap:.5rem;
        padding:.3rem .7rem;border-radius:20px;font-size:.72rem;font-weight:500;
        background:var(--card-bg);border:1px solid var(--border);
        font-family:'JetBrains Mono',monospace;transition:all .2s;
        white-space:nowrap;
    }
    .history-chip:hover { border-color:var(--ink);transform:translateY(-1px); }

    @keyframes cardReveal {
        from{opacity:0;transform:scale(.96) translateY(12px)}
        to{opacity:1;transform:scale(1) translateY(0)}
    }
    .card-reveal { animation:cardReveal .4s cubic-bezier(0.34,1.4,0.64,1) both; }

    /* ── MODAL BELUM REGISTRASI ── */
    .reg-modal {
        display:none;position:fixed;inset:0;z-index:9999;
        background:rgba(13,15,18,.6);backdrop-filter:blur(6px);
        align-items:center;justify-content:center;padding:1.5rem;
    }
    .reg-modal.open { display:flex; }
    .reg-modal-box {
        background:#fff;border-radius:20px;padding:2rem;
        max-width:360px;width:100%;text-align:center;
        box-shadow:0 24px 80px rgba(13,15,18,.25);
        animation:cardReveal .4s cubic-bezier(0.34,1.4,0.64,1) both;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .scan-wrap { grid-template-columns: 1fr; }
        .scan-left  { display: none; }
        .scan-right { padding: 2rem 1.5rem; min-height: calc(100vh - var(--nav-h)); }
    }
    @media (max-width: 480px) {
        .scan-right { padding: 1.5rem 1.1rem; }
        .scan-input { font-size: 1rem; padding: .85rem 1rem; }
        .student-card { padding: 1.25rem; }
        .student-name { font-size: 1.15rem; }
        .grid-cols-2-scan { grid-template-columns: 1fr 1fr; }
    }
</style>
@endsection

@section('content')
<div class="scan-wrap">

    {{-- Left Panel --}}
    <div class="scan-left">
        <div style="position:relative;z-index:1;text-align:center;">
            <div class="qr-art">📱</div>
            <h2 style="color:var(--paper);font-size:1.8rem;font-weight:800;margin:1.5rem 0 .75rem;letter-spacing:-.03em;">
                Scan KTM<br>Anda
            </h2>
            <p style="color:rgba(245,242,235,.5);max-width:280px;margin:0 auto;line-height:1.7;font-size:.875rem;">
                Arahkan QR code pada Kartu Tanda Mahasiswa ke scanner untuk mencatat kehadiran di perpustakaan.
            </p>
            <div style="margin-top:2.5rem;display:flex;flex-direction:column;gap:.75rem;">
                @foreach(['Scan otomatis', 'Data real-time', 'Tanpa antri'] as $i => $f)
                <div class="animate-up flex items-center gap-3" style="animation-delay:{{ ($i+1) * 0.1 }}s;">
                    <span style="width:20px;height:20px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">✓</span>
                    <span style="font-size:.85rem;font-weight:500;color:rgba(245,242,235,.75);">{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Panel --}}
    <div class="scan-right">

        {{-- Header --}}
        <div class="animate-up" style="margin-bottom:2rem;">
            <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);margin-bottom:.25rem;">
                Perpustakaan Universitas
            </p>
            <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);font-weight:800;letter-spacing:-.03em;color:var(--ink);">
                Selamat Datang 👋
            </h1>
            <p style="margin-top:.5rem;font-size:.875rem;color:var(--muted);">
                Scan atau ketik NIM untuk mencatat kunjungan
            </p>
        </div>

        {{-- Scan Form --}}
        <div class="animate-up delay-1">
            <form id="scanForm" autocomplete="off">
                @csrf
                <label style="display:block;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);margin-bottom:.5rem;">
                    Nomor Induk Mahasiswa (NIM)
                </label>
                <div class="relative">
                    <input type="text" id="nimInput" name="nim" class="scan-input"
                        placeholder="Scan QR atau ketik NIM…"
                        autofocus maxlength="20" inputmode="numeric">
                    <div id="spinnerIcon" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                        <div style="width:20px;height:20px;border:2px solid var(--border);border-top-color:var(--ink);border-radius:50%;animation:spin 1s linear infinite;"></div>
                    </div>
                    <div id="checkIcon" class="hidden absolute right-4 top-1/2 -translate-y-1/2" style="font-size:1.2rem;">✅</div>
                    <div id="errorIcon" class="hidden absolute right-4 top-1/2 -translate-y-1/2" style="font-size:1.2rem;">❌</div>
                </div>
                <p style="margin-top:.5rem;font-size:.75rem;color:var(--muted);">
                    ⚡ QR scanner auto-submit &nbsp;·&nbsp; Atau tekan
                    <kbd style="background:var(--border);padding:.1rem .4rem;border-radius:4px;font-size:.72rem;font-family:'JetBrains Mono',monospace;">Enter</kbd>
                </p>
            </form>
        </div>

        {{-- Result Area --}}
        <div id="resultArea" class="hidden animate-up" style="margin-top:1.75rem;">

            {{-- Success Card --}}
            <div id="successCard" class="student-card card-reveal hidden">
                <div style="position:relative;z-index:1;">
                    <div class="flex items-center justify-between" style="margin-bottom:1rem;">
                        <span class="nim-badge" id="resNim">NIM: —</span>
                        <span class="badge badge-green" style="font-size:.7rem;">✓ Tercatat</span>
                    </div>
                    <div class="student-name" id="resNama">—</div>
                    <div style="font-size:.8rem;opacity:.6;margin-bottom:1.25rem;" id="resJurusan">—</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem;">
                        <div>
                            <div style="font-size:.65rem;opacity:.5;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.2rem;">Angkatan</div>
                            <div style="font-weight:600;font-size:.85rem;" id="resAngkatan">—</div>
                        </div>
                        <div>
                            <div style="font-size:.65rem;opacity:.5;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.2rem;">No. HP</div>
                            <div style="font-weight:600;font-size:.85rem;" id="resHp">—</div>
                        </div>
                        <div>
                            <div style="font-size:.65rem;opacity:.5;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.2rem;">Email</div>
                            <div style="font-weight:600;font-size:.8rem;" id="resEmail">—</div>
                        </div>
                        <div>
                            <div style="font-size:.65rem;opacity:.5;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.2rem;" id="resWaktuLabel">Waktu Masuk</div>
                            <div style="font-weight:700;font-size:.85rem;font-family:'JetBrains Mono',monospace;" id="resWaktu" style="color:#86efac;">—</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2" style="border-top:1px solid rgba(255,255,255,.1);padding-top:.75rem;">
                        <div class="live-dot"></div>
                        <span style="font-size:.72rem;opacity:.6;" id="resTanggal">—</span>
                    </div>
                </div>
            </div>

            {{-- Error Card --}}
            <div id="errorCard" class="card card-reveal hidden" style="padding:1.25rem 1.5rem;">
                <div class="flex items-start gap-4">
                    <div style="font-size:2rem;">😕</div>
                    <div>
                        <h3 id="errorTitle" style="font-weight:700;font-size:.95rem;margin-bottom:.25rem;color:var(--accent);">Tidak Ditemukan</h3>
                        <p style="font-size:.82rem;color:var(--muted);" id="errorMsg">—</p>
                        <p style="margin-top:.6rem;font-size:.75rem;color:var(--muted);">
                            Pastikan NIM terdaftar. Hubungi petugas jika butuh bantuan.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Scans --}}
        <div class="animate-up delay-3" style="margin-top:2rem;">
            <div class="flex items-center justify-between" style="margin-bottom:.75rem;">
                <span style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);">Kunjungan Hari Ini</span>
                <span id="todayCount" class="badge badge-green">— orang</span>
            </div>
            <div id="recentScans" class="flex flex-wrap gap-2">
                <span class="history-chip" style="opacity:.5;">Belum ada kunjungan hari ini</span>
            </div>
        </div>

    </div>
</div>

{{-- Modal Belum Registrasi --}}
<div class="reg-modal" id="modalBelumRegistrasi">
    <div class="reg-modal-box">
        <div style="font-size:3rem;margin-bottom:1rem;">🚫</div>
        <div style="display:inline-block;background:rgba(200,67,10,.1);color:var(--accent);font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:.2rem .75rem;border-radius:100px;margin-bottom:1rem;">Belum Registrasi</div>
        <h3 style="font-size:1.2rem;font-weight:800;margin-bottom:.35rem;" id="modalNama">—</h3>
        <p style="font-size:.8rem;color:var(--muted);margin-bottom:.25rem;" id="modalNim">—</p>
        <p style="font-size:.8rem;color:var(--muted);margin-bottom:1.25rem;" id="modalJurusan">—</p>
        <p style="font-size:.82rem;color:var(--ink);line-height:1.6;margin-bottom:1.5rem;">
            Mahasiswa ini <strong>belum terdaftar</strong> sebagai anggota perpustakaan.<br>
            Silakan hubungi petugas untuk melakukan registrasi.
        </p>
        <button onclick="tutupModalRegistrasi()" style="width:100%;padding:.75rem;background:var(--ink);color:var(--paper);border:none;border-radius:10px;font-size:.9rem;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">Tutup</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
const nimInput    = document.getElementById('nimInput');
const resultArea  = document.getElementById('resultArea');
const successCard = document.getElementById('successCard');
const errorCard   = document.getElementById('errorCard');
const spinnerIcon = document.getElementById('spinnerIcon');
const checkIcon   = document.getElementById('checkIcon');
const errorIcon   = document.getElementById('errorIcon');

let debounceTimer = null;
let isSubmitting  = false;

nimInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        if (nimInput.value.trim().length >= 5) submitScan();
    }
});

nimInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    if (nimInput.value.trim().length >= 5)
        debounceTimer = setTimeout(submitScan, 600);
});

async function submitScan() {
    if (isSubmitting) return;
    const nim = nimInput.value.trim();
    if (!nim || nim.length < 5) return;
    isSubmitting = true;
    setLoading(true);
    try {
        const res    = await fetch('/api/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ nim }),
        });
        const status = res.status;
        const data   = await res.json();

        if (data.success) {
            showSuccess(data);
        } else if (status === 403 && data.tipe === 'belum_registrasi') {
            showModalBelumRegistrasi(data.mahasiswa);
        } else if (status === 409) {
            showWarning(data.message);
        } else {
            showError(data.message);
        }
    } catch(err) {
        showError('Terjadi kesalahan koneksi. Silakan coba lagi.');
    } finally {
        setLoading(false);
        isSubmitting  = false;
        nimInput.value = '';
        setTimeout(() => nimInput.focus(), 400);
    }
}

function setLoading(loading) {
    spinnerIcon.classList.toggle('hidden', !loading);
    checkIcon.classList.add('hidden');
    errorIcon.classList.add('hidden');
    nimInput.disabled = loading;
}

function showSuccess(data) {
    const m = data.mahasiswa, k = data.kunjungan;
    document.getElementById('resNim').textContent      = 'NIM: ' + m.nim;
    document.getElementById('resNama').textContent     = m.nama;
    document.getElementById('resJurusan').textContent  = m.jurusan;
    document.getElementById('resAngkatan').textContent = m.angkatan;
    document.getElementById('resHp').textContent       = m.no_hp || '—';
    document.getElementById('resEmail').textContent    = m.email;
    document.getElementById('resTanggal').textContent  = k.tanggal;

    if (data.tipe === 'keluar') {
        document.getElementById('resWaktuLabel').textContent = 'Waktu Keluar';
        document.getElementById('resWaktu').textContent      = k.waktu_keluar;
        document.getElementById('resWaktu').style.color      = '#fb923c';
        showToast('Sampai Jumpa!', `${m.nama} · Durasi: ${k.durasi}`, 'warning');
    } else {
        document.getElementById('resWaktuLabel').textContent = 'Waktu Masuk';
        document.getElementById('resWaktu').textContent      = k.waktu_masuk;
        document.getElementById('resWaktu').style.color      = '#86efac';
        showToast('Berhasil Dicatat!', `${m.nama} · ${m.jurusan}`, 'success');
    }

    resultArea.classList.remove('hidden');
    successCard.classList.remove('hidden');
    errorCard.classList.add('hidden');
    checkIcon.classList.remove('hidden');
    nimInput.classList.remove('error');
    nimInput.classList.add('success');
    setTimeout(() => nimInput.classList.remove('success'), 2000);
    loadRecentScans();
}

function showError(msg) {
    document.getElementById('errorTitle').textContent = 'Mahasiswa Tidak Ditemukan';
    document.getElementById('errorTitle').style.color = 'var(--accent)';
    document.getElementById('errorMsg').innerHTML     = msg;
    resultArea.classList.remove('hidden');
    errorCard.classList.remove('hidden');
    successCard.classList.add('hidden');
    errorIcon.classList.remove('hidden');
    nimInput.classList.add('error');
    setTimeout(() => nimInput.classList.remove('error'), 1000);
    showToast('Tidak Ditemukan', msg, 'error');
}

function showWarning(msg) {
    document.getElementById('errorTitle').textContent = '⚠ Sudah Tercatat';
    document.getElementById('errorTitle').style.color = '#f59e0b';
    document.getElementById('errorMsg').innerHTML     = msg;
    errorCard.style.borderColor = '#f59e0b';
    resultArea.classList.remove('hidden');
    errorCard.classList.remove('hidden');
    successCard.classList.add('hidden');
    nimInput.classList.add('error');
    setTimeout(() => nimInput.classList.remove('error'), 1000);
    showToast('Sudah Tercatat', msg, 'warning');
}

function showModalBelumRegistrasi(m) {
    document.getElementById('modalNama').textContent    = m.nama;
    document.getElementById('modalNim').textContent     = 'NIM: ' + m.nim;
    document.getElementById('modalJurusan').textContent = m.jurusan;
    document.getElementById('modalBelumRegistrasi').classList.add('open');
}
function tutupModalRegistrasi() {
    document.getElementById('modalBelumRegistrasi').classList.remove('open');
}

async function loadRecentScans() {
    try {
        const res  = await fetch('/api/kunjungan/stats');
        const data = await res.json();
        document.getElementById('todayCount').textContent = data.stats.hari_ini + ' orang';
        const container = document.getElementById('recentScans');
        const recent    = data.terbaru.slice(0, 8);
        if (recent.length) {
            container.innerHTML = recent.map(k => `
                <span class="history-chip">
                    <span>${k.nama.split(' ')[0]}</span>
                    <span style="opacity:.5;font-size:.62rem;">${k.waktu_masuk.split(' ')[1]}</span>
                </span>`).join('');
        }
    } catch(e) {}
}

loadRecentScans();
nimInput.focus();
document.addEventListener('click', () => nimInput.focus());
</script>
@endsection