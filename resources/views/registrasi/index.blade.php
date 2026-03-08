@extends('layouts.app')

@section('title', 'Registrasi Anggota — SiPerpus')

@section('extra_head')
<style>
    .reg-wrap { max-width:1100px; margin:0 auto; padding:2rem 1.5rem; }

    /* Mini Stats */
    .mini-stats-grid {
        display: grid;
        grid-template-columns: repeat(3,1fr);
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .mini-stat {
        background:var(--card-bg);border:1px solid var(--border);
        border-radius:10px;padding:1rem 1.25rem;
        display:flex;align-items:center;gap:.75rem;
    }
    .mini-stat-icon { width:36px;height:36px;border-radius:8px;display:grid;place-items:center;font-size:1rem;flex-shrink:0; }
    .mini-stat-val  { font-size:1.4rem;font-weight:800;line-height:1;letter-spacing:-.03em; }
    .mini-stat-lbl  { font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-top:.2rem; }

    /* Search & filter */
    .search-bar-wrap { position:relative;flex:1;min-width:180px; }
    .search-icon { position:absolute;left:.85rem;top:50%;transform:translateY(-50%);font-size:.95rem;pointer-events:none; }
    .search-bar {
        width:100%;padding:.7rem 1rem .7rem 2.6rem;
        border:1.5px solid var(--border);border-radius:10px;
        font-size:.88rem;font-family:'Inter',sans-serif;
        background:var(--card-bg);color:var(--ink);outline:none;
        transition:border-color .2s,box-shadow .2s;
    }
    .search-bar:focus { border-color:var(--ink);box-shadow:0 0 0 3px rgba(13,15,18,.07); }

    .filter-chips { display:flex;align-items:center;gap:.4rem;flex-wrap:wrap; }
    .filter-chip {
        padding:.3rem .8rem;border-radius:100px;font-size:.75rem;font-weight:600;
        cursor:pointer;border:1.5px solid var(--border);color:var(--muted);
        background:transparent;transition:all .15s ease;font-family:'Inter',sans-serif;
        white-space:nowrap;
    }
    .filter-chip.active,.filter-chip:hover { border-color:var(--ink);background:var(--ink);color:var(--paper); }

    /* Table */
    .data-table { width:100%;border-collapse:separate;border-spacing:0; }
    .data-table thead th {
        font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
        padding:.7rem 1rem;background:var(--paper);border-bottom:1px solid var(--border);
        color:var(--muted);position:sticky;top:0;z-index:5;white-space:nowrap;
    }
    .data-table tbody td {
        padding:.85rem 1rem;font-size:.875rem;
        border-bottom:1px solid var(--border);vertical-align:middle;
    }
    .data-table tbody tr:last-child td { border-bottom:none; }
    .data-table tbody tr:hover td { background:rgba(245,242,235,.7); }

    .avatar {
        width:34px;height:34px;border-radius:8px;
        display:flex;align-items:center;justify-content:center;
        font-size:.72rem;font-weight:700;flex-shrink:0;
    }

    /* Toggle */
    .toggle-switch { position:relative;display:inline-block;width:44px;height:24px; }
    .toggle-switch input { opacity:0;width:0;height:0; }
    .toggle-slider {
        position:absolute;inset:0;cursor:pointer;
        background:#e5e7eb;border-radius:24px;transition:.3s;
    }
    .toggle-slider::before {
        content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;
        background:#fff;border-radius:50%;transition:.3s;box-shadow:0 1px 3px rgba(0,0,0,.2);
    }
    .toggle-switch input:checked + .toggle-slider { background:var(--accent2); }
    .toggle-switch input:checked + .toggle-slider::before { transform:translateX(20px); }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .mini-stats-grid { grid-template-columns: 1fr 1fr; }
        .mini-stats-grid > :last-child { grid-column: span 2; }
        .search-filter-row { flex-direction:column; align-items:stretch; }
        .search-bar-wrap { min-width:unset; }
    }
    @media (max-width: 480px) {
        .reg-wrap { padding:1.25rem .9rem; }
        .mini-stats-grid { grid-template-columns:1fr 1fr;gap:.75rem; }
        .mini-stat { padding:.85rem 1rem; }
        .mini-stat-val { font-size:1.2rem; }
        .hide-mobile { display:none; }
        .data-table thead th,
        .data-table tbody td { padding:.65rem .75rem;font-size:.78rem; }
    }
</style>
@endsection

@section('content')
<div class="reg-wrap">

    {{-- Header --}}
    <div class="animate-up" style="margin-bottom:1.75rem;">
        <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);margin-bottom:.25rem;">Manajemen Anggota</p>
        <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);font-weight:800;letter-spacing:-.03em;">Registrasi</h1>
        <p style="margin-top:.25rem;font-size:.875rem;color:var(--muted);">Kelola mahasiswa yang berhak mengakses perpustakaan</p>
    </div>

    {{-- Mini Stats --}}
    <div class="mini-stats-grid animate-up delay-1">
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:rgba(13,15,18,.07);">👥</div>
            <div>
                <div class="mini-stat-val" id="statTotal">—</div>
                <div class="mini-stat-lbl">Total Mahasiswa</div>
            </div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:rgba(26,107,58,.1);">✅</div>
            <div>
                <div class="mini-stat-val" style="color:var(--accent2);" id="statTerdaftar">—</div>
                <div class="mini-stat-lbl">Sudah Registrasi</div>
            </div>
        </div>
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background:rgba(200,67,10,.1);">⏳</div>
            <div>
                <div class="mini-stat-val" style="color:var(--accent);" id="statBelum">—</div>
                <div class="mini-stat-lbl">Belum Registrasi</div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card animate-up delay-2" style="margin-bottom:1.25rem;padding:1.1rem 1.25rem;">
        <div class="search-filter-row" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
            <div class="search-bar-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-bar"
                    placeholder="Cari NIM, nama, atau jurusan…"
                    oninput="onSearch(this.value)">
            </div>
            <div class="filter-chips">
                <span style="font-size:.72rem;font-weight:600;color:var(--muted);white-space:nowrap;">Status:</span>
                <button class="filter-chip active" onclick="setFilter('all',this)">Semua</button>
                <button class="filter-chip"        onclick="setFilter('yes',this)">Terdaftar</button>
                <button class="filter-chip"        onclick="setFilter('no',this)">Belum</button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card animate-up delay-3">
        <div style="padding:.9rem 1.25rem;border-bottom:1px solid var(--border);">
            <p style="font-size:.78rem;color:var(--muted);">
                Menampilkan <span id="countShown" style="font-weight:700;color:var(--ink);">0</span> mahasiswa
            </p>
        </div>
        <div style="max-height:520px;overflow-y:auto;overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">Mahasiswa</th>
                        <th class="text-left hide-mobile">Jurusan / Angkatan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center hide-mobile">Tgl Registrasi</th>
                        <th class="text-center">Toggle</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--muted);">
                        <div style="font-size:2rem;margin-bottom:.5rem;">🔍</div>
                        Ketik untuk mencari mahasiswa
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const COLORS = ['#0d0f12','#c8430a','#1a6b3a','#1d4ed8','#7c3aed','#b45309','#0891b2','#be123c'];
let allData = [], filterStatus = 'all', searchQuery = '', searchTimer = null;

async function loadStats() {
    try {
        const res  = await fetch('/api/mahasiswa/search?q=a');
        const data = await res.json();
        document.getElementById('statTotal').textContent     = (data.stats?.total     ?? '—').toLocaleString?.() ?? '—';
        document.getElementById('statTerdaftar').textContent = (data.stats?.terdaftar ?? '—').toLocaleString?.() ?? '—';
        document.getElementById('statBelum').textContent     = (data.stats?.belum     ?? '—').toLocaleString?.() ?? '—';
    } catch(e) {}
}

function onSearch(val) {
    searchQuery = val;
    clearTimeout(searchTimer);
    if (val.length < 2) {
        renderTable([]);
        document.getElementById('countShown').textContent = '0';
        return;
    }
    searchTimer = setTimeout(fetchMahasiswa, 300);
}

function setFilter(status, btn) {
    filterStatus = status;
    document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (allData.length) renderTable(filterData(allData));
    else if (searchQuery.length >= 2) fetchMahasiswa();
}

function filterData(data) {
    if (filterStatus === 'yes') return data.filter(m => m.registrasi);
    if (filterStatus === 'no')  return data.filter(m => !m.registrasi);
    return data;
}

async function fetchMahasiswa() {
    try {
        const res  = await fetch(`/api/mahasiswa/search?q=${encodeURIComponent(searchQuery)}`);
        const data = await res.json();
        allData = data.mahasiswa ?? data;
        // Update stats from fresh data
        if (data.stats) {
            document.getElementById('statTotal').textContent     = data.stats.total.toLocaleString('id-ID');
            document.getElementById('statTerdaftar').textContent = data.stats.terdaftar.toLocaleString('id-ID');
            document.getElementById('statBelum').textContent     = data.stats.belum.toLocaleString('id-ID');
        }
        renderTable(filterData(allData));
    } catch(e) {}
}

function renderTable(rows) {
    const tbody = document.getElementById('tableBody');
    document.getElementById('countShown').textContent = rows.length;

    if (!rows.length) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--muted);">
            <div style="font-size:2rem;margin-bottom:.5rem;">📭</div>
            Tidak ada mahasiswa ditemukan</td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map((m, i) => {
        const initials = m.nama.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
        const color    = COLORS[i % COLORS.length];
        const regAt    = m.registrasi_at
            ? new Date(m.registrasi_at).toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'})
            : '—';
        return `
        <tr id="row-${m.nim}">
            <td>
                <div style="display:flex;align-items:center;gap:.65rem;">
                    <div class="avatar" style="background:${color}20;color:${color};">${initials}</div>
                    <div>
                        <div style="font-weight:600;font-size:.85rem;">${m.nama}</div>
                        <div style="font-size:.7rem;font-family:'JetBrains Mono',monospace;color:var(--muted);">${m.nim}</div>
                    </div>
                </div>
            </td>
            <td class="hide-mobile">
                <div style="font-size:.85rem;">${m.jurusan ?? '—'}</div>
                <div style="font-size:.7rem;font-family:'JetBrains Mono',monospace;color:var(--muted);">${m.angkatan ?? '—'}</div>
            </td>
            <td style="text-align:center;" id="badge-${m.nim}">
                ${m.registrasi
                    ? `<span class="badge badge-green">✓ Terdaftar</span>`
                    : `<span class="badge badge-gray">Belum</span>`}
            </td>
            <td style="text-align:center;font-size:.75rem;font-family:'JetBrains Mono',monospace;color:var(--muted);" class="hide-mobile" id="regat-${m.nim}">
                ${regAt}
            </td>
            <td style="text-align:center;">
                <label class="toggle-switch">
                    <input type="checkbox" ${m.registrasi ? 'checked' : ''} onchange="toggleRegistrasi('${m.nim}',this)">
                    <span class="toggle-slider"></span>
                </label>
            </td>
        </tr>`;
    }).join('');
}

async function toggleRegistrasi(nim, checkbox) {
    try {
        const res  = await fetch('/api/mahasiswa/registrasi', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({ nim }),
        });
        const data = await res.json();
        document.getElementById(`badge-${nim}`).innerHTML = data.registrasi
            ? `<span class="badge badge-green">✓ Terdaftar</span>`
            : `<span class="badge badge-gray">Belum</span>`;
        const regAtEl = document.getElementById(`regat-${nim}`);
        if (regAtEl) regAtEl.textContent = data.registrasi
            ? new Date().toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'})
            : '—';
        loadStats();
        showToast(
            data.registrasi ? '✅ Registrasi Aktif' : '⏸ Dinonaktifkan',
            data.registrasi ? `${nim} kini bisa masuk perpustakaan.` : `${nim} tidak bisa masuk.`,
            data.registrasi ? 'success' : 'warning'
        );
    } catch(e) {
        checkbox.checked = !checkbox.checked;
        showToast('Gagal','Terjadi kesalahan, coba lagi.','error');
    }
}

loadStats();
</script>
@endsection