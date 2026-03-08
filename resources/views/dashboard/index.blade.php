@extends('layouts.app')
@section('title', 'Dashboard Perpustakaan')

@section('extra_head')
<style>
    .dash-wrap { max-width:1200px; margin:0 auto; padding:2rem 1.5rem; }

    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .stat-card {
        background:var(--card-bg);border:1px solid var(--border);
        border-radius:12px;padding:1.25rem;
        transition:transform .2s,box-shadow .2s;
    }
    .stat-card:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.07); }
    .stat-value { font-size:2rem;font-weight:800;letter-spacing:-.04em;line-height:1; }
    .stat-label { font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;margin-top:.35rem;color:var(--muted); }
    .stat-icon  { width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem; }

    /* Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .chart-container { position:relative; height:240px; }

    /* Table */
    .data-table { width:100%;border-collapse:separate;border-spacing:0; }
    .data-table thead th {
        font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
        padding:.7rem 1rem;background:var(--paper);border-bottom:1px solid var(--border);
        color:var(--muted);position:sticky;top:0;z-index:5;white-space:nowrap;
    }
    .data-table tbody td {
        padding:.8rem 1rem;font-size:.85rem;
        border-bottom:1px solid var(--border);vertical-align:middle;
    }
    .data-table tbody tr:last-child td { border-bottom:none; }
    .data-table tbody tr:hover td { background:rgba(245,242,235,.7); }

    .avatar {
        width:32px;height:32px;border-radius:8px;
        display:flex;align-items:center;justify-content:center;
        font-size:.72rem;font-weight:700;flex-shrink:0;
    }

    .tab-btn {
        padding:.35rem .8rem;border-radius:6px;font-size:.78rem;font-weight:600;
        cursor:pointer;transition:all .2s;border:1.5px solid transparent;
        font-family:'Inter',sans-serif;
    }
    .tab-btn.active { background:var(--ink);color:var(--paper); }
    .tab-btn:not(.active) { border-color:var(--border);color:var(--muted); }
    .tab-btn:not(.active):hover { border-color:var(--ink);color:var(--ink); }

    /* Filter row */
    .filter-row {
        display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;
    }
    .filter-date {
        font-size:.78rem;border:1.5px solid var(--border);border-radius:8px;
        padding:.45rem .75rem;font-family:'Inter',sans-serif;
        background:var(--card-bg);color:var(--ink);outline:none;
    }

    /* Skeleton */
    @keyframes shimmer {
        0%{background-position:-400px 0} 100%{background-position:400px 0}
    }
    .skeleton {
        background:linear-gradient(90deg,var(--border) 25%,#e9e5dc 50%,var(--border) 75%);
        background-size:400px 100%;animation:shimmer 1.4s infinite;border-radius:6px;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1024px) {
        .stats-grid    { grid-template-columns: repeat(2,1fr); }
        .charts-grid   { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .dash-wrap     { padding:1.25rem .9rem; }
        .stats-grid    { grid-template-columns: repeat(2,1fr); gap:.75rem; }
        .stat-value    { font-size:1.6rem; }
        .stat-card     { padding:1rem; }
        .charts-grid   { gap:.75rem; }
        .chart-container { height:200px; }
        .filter-row    { gap:.5rem; }
        .filter-date   { font-size:.72rem;padding:.4rem .6rem; }
        .data-table thead th,
        .data-table tbody td { padding:.65rem .75rem;font-size:.78rem; }
    }
    @media (max-width: 480px) {
        .stats-grid    { grid-template-columns: 1fr 1fr; }
        .hide-mobile   { display:none; }
        .filter-row    { flex-direction:column;align-items:flex-start; }
        .filter-row > * { width:100%; }
        .filter-date   { width:100%; }
    }
</style>
@endsection

@section('content')
<div class="dash-wrap">

    {{-- Header --}}
    <div class="flex items-end justify-between animate-up" style="margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);margin-bottom:.25rem;">Analytics</p>
            <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);font-weight:800;letter-spacing:-.03em;">Dashboard</h1>
            <p style="margin-top:.25rem;font-size:.875rem;color:var(--muted);">Monitor aktivitas kunjungan perpustakaan secara real-time</p>
        </div>
        <a href="{{ route('scan.index') }}" class="btn-primary">📱 Scan Masuk</a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid animate-up delay-1">
        <div class="stat-card">
            <div class="flex items-start justify-between" style="margin-bottom:1rem;">
                <div class="stat-icon" style="background:#fff0e6;">📅</div>
                <span class="badge badge-orange">Hari ini</span>
            </div>
            <div class="stat-value" id="statHariIni"><div class="skeleton" style="height:2rem;width:4rem;"></div></div>
            <div class="stat-label">Kunjungan Hari Ini</div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between" style="margin-bottom:1rem;">
                <div class="stat-icon" style="background:#dbeafe;">📆</div>
                <span class="badge badge-blue">Minggu ini</span>
            </div>
            <div class="stat-value" id="statMingguIni"><div class="skeleton" style="height:2rem;width:4rem;"></div></div>
            <div class="stat-label">Kunjungan Minggu Ini</div>
        </div>
        <div class="stat-card">
            <div class="flex items-start justify-between" style="margin-bottom:1rem;">
                <div class="stat-icon" style="background:#dcfce7;">🗓️</div>
                <span class="badge badge-green">Bulan ini</span>
            </div>
            <div class="stat-value" id="statBulanIni"><div class="skeleton" style="height:2rem;width:4rem;"></div></div>
            <div class="stat-label">Kunjungan Bulan Ini</div>
        </div>
        <div class="stat-card" style="background:var(--ink);border-color:var(--ink);">
            <div class="flex items-start justify-between" style="margin-bottom:1rem;">
                <div class="stat-icon" style="background:rgba(255,255,255,.1);">📊</div>
                <span class="badge" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.8);">All time</span>
            </div>
            <div class="stat-value" id="statTotal" style="color:var(--paper);"><div class="skeleton" style="height:2rem;width:5rem;"></div></div>
            <div class="stat-label" style="color:rgba(245,242,235,.5);">Total Kunjungan</div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="charts-grid animate-up delay-2">
        <div class="card" style="padding:1.5rem;">
            <div class="flex items-center justify-between" style="margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
                <div>
                    <h2 style="font-weight:700;font-size:.95rem;">Tren Kunjungan</h2>
                    <p style="font-size:.75rem;margin-top:.2rem;color:var(--muted);">Grafik kunjungan harian</p>
                </div>
                <div style="display:flex;gap:.5rem;" id="chartTabs">
                    <button class="tab-btn active" onclick="switchChart('week',this)">7 Hari</button>
                    <button class="tab-btn"        onclick="switchChart('month',this)">30 Hari</button>
                </div>
            </div>
            <div class="chart-container"><canvas id="lineChart"></canvas></div>
        </div>
        <div class="card" style="padding:1.5rem;">
            <h2 style="font-weight:700;font-size:.95rem;margin-bottom:.3rem;">Top Jurusan</h2>
            <p style="font-size:.75rem;margin-bottom:1.25rem;color:var(--muted);">Kunjungan bulan ini</p>
            <div class="chart-container" style="height:180px;"><canvas id="donutChart"></canvas></div>
            <div id="jurusanLegend" style="margin-top:1rem;display:flex;flex-direction:column;gap:.4rem;"></div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card animate-up delay-3">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:.75rem;">
                <div>
                    <h2 style="font-weight:700;font-size:.95rem;">Riwayat Kunjungan</h2>
                    <p style="font-size:.75rem;margin-top:.2rem;color:var(--muted);">10 kunjungan terbaru</p>
                </div>
            </div>
            <div class="filter-row">
                <label style="font-size:.72rem;font-weight:600;color:var(--muted);white-space:nowrap;">Dari:</label>
                <input type="date" id="filterStart" class="filter-date">
                <label style="font-size:.72rem;font-weight:600;color:var(--muted);white-space:nowrap;">Sampai:</label>
                <input type="date" id="filterEnd" class="filter-date">
                <button onclick="applyFilter()" class="btn-primary" style="font-size:.78rem;padding:.45rem .9rem;">Filter</button>
                <button onclick="resetFilter()" style="font-size:.78rem;font-weight:500;padding:.45rem .9rem;border-radius:8px;border:1px solid var(--border);background:transparent;cursor:pointer;color:var(--muted);font-family:'Inter',sans-serif;">Reset</button>
                <button onclick="exportCsv()" style="display:flex;align-items:center;gap:.4rem;font-size:.78rem;font-weight:600;padding:.45rem .9rem;background:#059669;color:#fff;border:none;border-radius:8px;cursor:pointer;font-family:'Inter',sans-serif;">
                    ↓ Export CSV
                </button>
            </div>
        </div>
        <div style="max-height:440px;overflow-y:auto;overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Mahasiswa</th>
                        <th class="text-left hide-mobile">Jurusan</th>
                        <th class="text-left">Waktu Masuk</th>
                        <th class="text-left hide-mobile">Waktu Keluar</th>
                        <th class="text-left">Durasi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--muted);">
                        <div class="skeleton" style="height:1rem;width:12rem;margin:0 auto .5rem;"></div>
                        <div class="skeleton" style="height:1rem;width:9rem;margin:0 auto;"></div>
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const COLORS = ['#0d0f12','#c8430a','#1a6b3a','#1d4ed8','#7c3aed','#b45309','#0891b2','#be123c'];
let statsData = null, lineChart = null, donutChart = null;

async function loadStats() {
    try {
        const res  = await fetch('/api/kunjungan/stats');
        const data = await res.json();
        statsData  = data;
        renderStats(data.stats);
        renderLineChart(data.chart_week);
        renderDonutChart(data.jurusan_populer);
        renderTable(data.terbaru);
    } catch(e) { console.error(e); }
}

function renderStats(s) {
    animateCount('statHariIni',  s.hari_ini);
    animateCount('statMingguIni', s.minggu_ini);
    animateCount('statBulanIni',  s.bulan_ini);
    animateCount('statTotal',     s.total);
}

function animateCount(id, target) {
    const el = document.getElementById(id);
    el.innerHTML = '';
    let v = 0;
    const step  = Math.max(1, Math.ceil(target / 60));
    const timer = setInterval(() => {
        v = Math.min(v + step, target);
        el.textContent = v.toLocaleString('id-ID');
        if (v >= target) clearInterval(timer);
    }, 16);
}

function renderLineChart(d) {
    const ctx = document.getElementById('lineChart').getContext('2d');
    if (lineChart) lineChart.destroy();
    lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: d.map(x => x.tanggal),
            datasets: [{
                label:'Kunjungan', data:d.map(x => x.total),
                borderColor:'#0d0f12', backgroundColor:'rgba(13,15,18,.06)',
                borderWidth:2.5, tension:.4, fill:true,
                pointBackgroundColor:'#0d0f12', pointRadius:4, pointHoverRadius:6,
                pointBorderColor:'#fff', pointBorderWidth:2,
            }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{display:false}, tooltip:{ backgroundColor:'#0d0f12', titleColor:'rgba(245,242,235,.7)', bodyColor:'#f5f2eb', padding:10, cornerRadius:8, callbacks:{label:c=>`${c.parsed.y} kunjungan`} } },
            scales:{
                x:{ grid:{color:'rgba(0,0,0,.04)'}, ticks:{font:{size:10},color:'#9ca3af'} },
                y:{ beginAtZero:true, grid:{color:'rgba(0,0,0,.04)'}, ticks:{font:{size:10,family:'JetBrains Mono'},color:'#9ca3af',stepSize:1} }
            }
        }
    });
}

function renderDonutChart(j) {
    const ctx = document.getElementById('donutChart').getContext('2d');
    if (donutChart) donutChart.destroy();
    donutChart = new Chart(ctx, {
        type:'doughnut',
        data:{ labels:j.map(x=>x.jurusan.length>18?x.jurusan.slice(0,16)+'…':x.jurusan), datasets:[{data:j.map(x=>x.total),backgroundColor:COLORS.slice(0,j.length),borderWidth:3,borderColor:'#fffef9'}] },
        options:{ responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{legend:{display:false},tooltip:{backgroundColor:'#0d0f12',bodyColor:'#f5f2eb',padding:10,cornerRadius:8}} }
    });
    document.getElementById('jurusanLegend').innerHTML = j.map((x,i) => `
        <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
            <div style="display:flex;align-items:center;gap:.5rem;min-width:0;">
                <div style="width:8px;height:8px;border-radius:2px;flex-shrink:0;background:${COLORS[i]};"></div>
                <span style="font-size:.72rem;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${x.jurusan}</span>
            </div>
            <span style="font-size:.72rem;font-weight:700;font-family:'JetBrains Mono',monospace;flex-shrink:0;">${x.total}</span>
        </div>`).join('');
}

function switchChart(period, btn) {
    document.querySelectorAll('#chartTabs .tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    if (statsData) renderLineChart(period === 'week' ? statsData.chart_week : statsData.chart_month);
}

function renderTable(rows, isFiltered=false) {
    const tbody = document.getElementById('tableBody');
    if (!rows?.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--muted);">
            <div style="font-size:2.5rem;margin-bottom:.5rem;">📭</div>
            Tidak ada data${isFiltered?' pada rentang tanggal tersebut':''}</td></tr>`;
        return;
    }
    tbody.innerHTML = rows.map((k,i) => {
        const init  = k.nama.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase();
        const color = COLORS[i % COLORS.length];
        return `<tr>
            <td style="font-size:.72rem;font-family:'JetBrains Mono',monospace;color:var(--muted);">#${k.id}</td>
            <td><div style="display:flex;align-items:center;gap:.6rem;">
                <div class="avatar" style="background:${color}20;color:${color};">${init}</div>
                <div>
                    <div style="font-weight:600;font-size:.85rem;">${k.nama}</div>
                    <div style="font-size:.7rem;font-family:'JetBrains Mono',monospace;color:var(--muted);">${k.nim}</div>
                </div></div></td>
            <td class="hide-mobile"><span class="badge badge-gray" style="font-size:.7rem;">${k.jurusan}</span></td>
            <td style="font-family:'JetBrains Mono',monospace;font-size:.78rem;">${k.waktu_masuk}</td>
            <td class="hide-mobile" style="font-family:'JetBrains Mono',monospace;font-size:.78rem;color:var(--muted);">${k.waktu_keluar}</td>
            <td><span class="badge ${k.waktu_keluar==='—'?'badge-green':'badge-blue'}" style="font-size:.7rem;">${k.waktu_keluar==='—'?'🟢 Di dalam':k.durasi}</span></td>
        </tr>`;
    }).join('');
}

async function applyFilter() {
    const start = document.getElementById('filterStart').value;
    const end   = document.getElementById('filterEnd').value;
    if (!start||!end) { showToast('Filter Tidak Lengkap','Isi kedua tanggal.','warning'); return; }
    if (start>end)    { showToast('Tanggal Tidak Valid','Tanggal mulai harus sebelum akhir.','error'); return; }
    try {
        const res  = await fetch(`/api/kunjungan/stats?start=${start}&end=${end}`);
        const data = await res.json();
        renderTable(data.filtered, true);
    } catch(e) { showToast('Gagal','Terjadi kesalahan.','error'); }
}

function resetFilter() {
    document.getElementById('filterStart').value = weekAgo;
    document.getElementById('filterEnd').value   = today;
    if (statsData) renderTable(statsData.terbaru);
}

function exportCsv() {
    const start = document.getElementById('filterStart').value;
    const end   = document.getElementById('filterEnd').value;
    let url = '/dashboard/export';
    if (start && end) url += `?start=${start}&end=${end}`;
    window.location.href = url;
}

const today   = new Date().toISOString().split('T')[0];
const weekAgo = new Date(Date.now()-7*86400000).toISOString().split('T')[0];
document.getElementById('filterStart').value = weekAgo;
document.getElementById('filterEnd').value   = today;

loadStats();
setInterval(loadStats, 30000);
</script>
@endsection