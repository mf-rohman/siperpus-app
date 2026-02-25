@extends('layouts.app')

@section('title', 'Dashboard Perpustakaan')

@section('extra_head')
<style>
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.07);
    }
    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -0.04em;
        line-height: 1;
    }
    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-top: 0.4rem;
    }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
    }

    .chart-container {
        position: relative;
        height: 260px;
    }

    /* Table */
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .data-table thead th {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.75rem 1rem;
        background: var(--paper);
        border-bottom: 1px solid var(--border);
        color: var(--muted);
        position: sticky;
        top: 0;
    }
    .data-table tbody tr {
        transition: background 0.15s;
    }
    .data-table tbody tr:hover td {
        background: rgba(245,242,235,0.7);
    }
    .data-table tbody td {
        padding: 0.875rem 1rem;
        font-size: 0.875rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }

    .avatar {
        width: 34px; height: 34px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .tab-btn {
        padding: 0.4rem 0.875rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: 1.5px solid transparent;
    }
    .tab-btn.active {
        background: var(--ink);
        color: var(--paper);
    }
    .tab-btn:not(.active) {
        border-color: var(--border);
        color: var(--muted);
    }
    .tab-btn:not(.active):hover {
        border-color: var(--ink);
        color: var(--ink);
    }

    /* Loading skeleton */
    @keyframes shimmer {
        0%   { background-position: -400px 0; }
        100% { background-position: 400px 0; }
    }
    .skeleton {
        background: linear-gradient(90deg, var(--border) 25%, #e9e5dc 50%, var(--border) 75%);
        background-size: 400px 100%;
        animation: shimmer 1.4s infinite;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">

    {{-- Page Header --}}
    <div class="flex items-end justify-between mb-10 animate-up">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:var(--accent);">Analytics</p>
            <h1 class="text-4xl font-extrabold" style="letter-spacing:-0.03em;">Dashboard</h1>
            <p class="mt-1 text-sm" style="color:var(--muted);">Monitor aktivitas kunjungan perpustakaan secara real-time</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('scan.index') }}" class="btn-primary text-sm flex items-center gap-2">
                <span>📱</span> Scan Masuk
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8 animate-up delay-1">
        <!-- Hari Ini -->
        <div class="stat-card">
            <div class="flex items-start justify-between mb-4">
                <div class="stat-icon" style="background:#fff0e6;">📅</div>
                <span class="badge badge-orange">Hari ini</span>
            </div>
            <div class="stat-value" id="statHariIni">
                <div class="skeleton h-10 w-16"></div>
            </div>
            <div class="stat-label" style="color:var(--muted);">Kunjungan Hari Ini</div>
        </div>
        <!-- Minggu ini -->
        <div class="stat-card">
            <div class="flex items-start justify-between mb-4">
                <div class="stat-icon" style="background:#dbeafe;">📆</div>
                <span class="badge badge-blue">Minggu ini</span>
            </div>
            <div class="stat-value" id="statMingguIni">
                <div class="skeleton h-10 w-16"></div>
            </div>
            <div class="stat-label" style="color:var(--muted);">Kunjungan Minggu Ini</div>
        </div>
        <!-- Bulan ini -->
        <div class="stat-card">
            <div class="flex items-start justify-between mb-4">
                <div class="stat-icon" style="background:#dcfce7;">🗓️</div>
                <span class="badge badge-green">Bulan ini</span>
            </div>
            <div class="stat-value" id="statBulanIni">
                <div class="skeleton h-10 w-16"></div>
            </div>
            <div class="stat-label" style="color:var(--muted);">Kunjungan Bulan Ini</div>
        </div>
        <!-- Total -->
        <div class="stat-card" style="background:var(--ink);border-color:var(--ink);">
            <div class="flex items-start justify-between mb-4">
                <div class="stat-icon" style="background:rgba(255,255,255,0.1);">📊</div>
                <span class="badge" style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.8);">All time</span>
            </div>
            <div class="stat-value" id="statTotal" style="color:var(--paper);">
                <div class="skeleton h-10 w-20"></div>
            </div>
            <div class="stat-label" style="color:rgba(245,242,235,0.5);">Total Kunjungan</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 animate-up delay-2">

        {{-- Line Chart - Tren 7 Hari --}}
        <div class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-bold text-base">Tren Kunjungan</h2>
                    <p class="text-xs mt-0.5" style="color:var(--muted);">Grafik kunjungan harian</p>
                </div>
                <div class="flex gap-2" id="chartTabs">
                    <button class="tab-btn active" data-period="week" onclick="switchChart('week', this)">7 Hari</button>
                    <button class="tab-btn" data-period="month" onclick="switchChart('month', this)">30 Hari</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        {{-- Donut Chart - Jurusan --}}
        <div class="card p-6">
            <h2 class="font-bold text-base mb-1">Top Jurusan</h2>
            <p class="text-xs mb-6" style="color:var(--muted);">Kunjungan bulan ini</p>
            <div class="chart-container" style="height:200px;">
                <canvas id="donutChart"></canvas>
            </div>
            <div id="jurusanLegend" class="mt-4 space-y-2"></div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card animate-up delay-3">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
             style="border-bottom:1px solid var(--border);">
            <div>
                <h2 class="font-bold text-base">Riwayat Kunjungan</h2>
                <p class="text-xs mt-0.5" style="color:var(--muted);">10 kunjungan terbaru</p>
            </div>

            {{-- Date Filter --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium" style="color:var(--muted);">Dari:</label>
                    <input type="date" id="filterStart"
                           class="text-xs border rounded-lg px-3 py-2 font-medium"
                           style="border-color:var(--border);background:var(--card-bg);">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium" style="color:var(--muted);">Sampai:</label>
                    <input type="date" id="filterEnd"
                           class="text-xs border rounded-lg px-3 py-2 font-medium"
                           style="border-color:var(--border);background:var(--card-bg);">
                </div>
                <button onclick="applyFilter()" class="btn-primary text-xs px-4 py-2">Filter</button>
                <button onclick="resetFilter()" class="text-xs font-medium px-3 py-2 rounded-lg hover:bg-gray-100 transition"
                        style="color:var(--muted);">Reset</button>
            </div>
        </div>

        <div class="overflow-x-auto" style="max-height:480px;overflow-y:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Mahasiswa</th>
                        <th class="text-left">Jurusan</th>
                        <th class="text-left">Waktu Masuk</th>
                        <th class="text-left">Waktu Keluar</th>
                        <th class="text-left">Durasi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="6" class="text-center py-12" style="color:var(--muted);">
                            <div class="flex flex-col items-center gap-2">
                                <div class="skeleton h-4 w-48 mb-1"></div>
                                <div class="skeleton h-4 w-36"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// Color palette
const COLORS = ['#0d0f12','#c8430a','#1a6b3a','#1d4ed8','#7c3aed','#b45309','#0891b2','#be123c'];

let statsData   = null;
let lineChart   = null;
let donutChart  = null;
let currentPeriod = 'week';

async function loadStats() {
    try {
        const res  = await fetch('/api/kunjungan/stats');
        const data = await res.json();
        statsData  = data;
        renderStats(data.stats);
        renderLineChart(data.chart_week);
        renderDonutChart(data.jurusan_populer);
        renderTable(data.terbaru);
    } catch(e) {
        console.error('Failed to load stats', e);
    }
}

function renderStats(stats) {
    animateCount('statHariIni',  stats.hari_ini);
    animateCount('statMingguIni', stats.minggu_ini);
    animateCount('statBulanIni',  stats.bulan_ini);
    animateCount('statTotal',     stats.total);
}

function animateCount(id, target) {
    const el = document.getElementById(id);
    el.innerHTML = '';
    let start = 0;
    const duration = 1000;
    const step = Math.ceil(target / (duration / 16));
    const timer = setInterval(() => {
        start = Math.min(start + step, target);
        el.textContent = start.toLocaleString('id-ID');
        if (start >= target) clearInterval(timer);
    }, 16);
}

function renderLineChart(chartData) {
    const ctx = document.getElementById('lineChart').getContext('2d');
    if (lineChart) lineChart.destroy();

    lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels:   chartData.map(d => d.tanggal),
            datasets: [{
                label: 'Kunjungan',
                data:  chartData.map(d => d.total),
                borderColor: '#0d0f12',
                backgroundColor: 'rgba(13,15,18,0.06)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#0d0f12',
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d0f12',
                    titleColor: 'rgba(245,242,235,0.7)',
                    bodyColor: '#f5f2eb',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => `${ctx.parsed.y} kunjungan`,
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { size: 11, family: 'Sora' }, color: '#9ca3af' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11, family: 'JetBrains Mono' },
                        color: '#9ca3af',
                        stepSize: 1,
                    }
                }
            }
        }
    });
}

function renderDonutChart(jurusan) {
    const ctx = document.getElementById('donutChart').getContext('2d');
    if (donutChart) donutChart.destroy();

    const labels = jurusan.map(j => j.jurusan.length > 20 ? j.jurusan.slice(0,18) + '…' : j.jurusan);
    const values = jurusan.map(j => j.total);

    donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: COLORS.slice(0, jurusan.length),
                borderWidth: 3,
                borderColor: '#fffef9',
                hoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0d0f12',
                    titleColor: 'rgba(245,242,235,0.7)',
                    bodyColor: '#f5f2eb',
                    padding: 12,
                    cornerRadius: 8,
                }
            }
        }
    });

    // Custom legend
    const legend = document.getElementById('jurusanLegend');
    legend.innerHTML = jurusan.map((j, i) => `
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-2.5 h-2.5 rounded-sm flex-shrink-0" style="background:${COLORS[i]};"></div>
                <span class="text-xs truncate" style="color:var(--muted);">${j.jurusan}</span>
            </div>
            <span class="text-xs font-bold mono flex-shrink-0">${j.total}</span>
        </div>
    `).join('');
}

function switchChart(period, btn) {
    currentPeriod = period;
    document.querySelectorAll('#chartTabs .tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    if (statsData) {
        renderLineChart(period === 'week' ? statsData.chart_week : statsData.chart_month);
    }
}

function renderTable(rows, isFiltered = false) {
    const tbody = document.getElementById('tableBody');

    if (!rows || rows.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="6" class="text-center py-16" style="color:var(--muted);">
                <div class="text-4xl mb-3">📭</div>
                <div class="font-medium">Tidak ada data kunjungan${isFiltered ? ' pada rentang tanggal tersebut' : ''}</div>
            </td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map((k, i) => {
        const initials = k.nama.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
        const color    = COLORS[i % COLORS.length];
        const isInside = k.waktu_keluar === '—';

        return `
        <tr>
            <td class="text-xs mono" style="color:var(--muted);">#${k.id}</td>
            <td>
                <div class="flex items-center gap-3">
                    <div class="avatar text-xs font-bold" style="background:${color}20;color:${color};">${initials}</div>
                    <div>
                        <div class="font-semibold text-sm">${k.nama}</div>
                        <div class="text-xs mono" style="color:var(--muted);">${k.nim}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge badge-gray text-xs">${k.jurusan}</span>
            </td>
            <td class="mono text-xs font-medium">${k.waktu_masuk}</td>
            <td class="mono text-xs" style="color:var(--muted);">${k.waktu_keluar}</td>
            <td>
                <span class="badge ${isInside ? 'badge-green' : 'badge-blue'}">
                    ${isInside ? '🟢 Di dalam' : k.durasi}
                </span>
            </td>
        </tr>`;
    }).join('');
}

async function applyFilter() {
    const start = document.getElementById('filterStart').value;
    const end   = document.getElementById('filterEnd').value;

    if (!start || !end) {
        showToast('Filter Tidak Lengkap', 'Isi kedua tanggal terlebih dahulu.', 'warning');
        return;
    }
    if (start > end) {
        showToast('Tanggal Tidak Valid', 'Tanggal mulai harus sebelum tanggal akhir.', 'error');
        return;
    }

    try {
        const res  = await fetch(`/api/kunjungan/stats?start=${start}&end=${end}`);
        const data = await res.json();
        renderTable(data.filtered, true);
    } catch(e) {
        showToast('Gagal memuat data', 'Terjadi kesalahan.', 'error');
    }
}

function resetFilter() {
    document.getElementById('filterStart').value = '';
    document.getElementById('filterEnd').value   = '';
    if (statsData) renderTable(statsData.terbaru);
}

// Set default filter dates
const today = new Date().toISOString().split('T')[0];
const weekAgo = new Date(Date.now() - 7 * 86400000).toISOString().split('T')[0];
document.getElementById('filterStart').value = weekAgo;
document.getElementById('filterEnd').value   = today;

// Auto-refresh every 30 seconds
loadStats();
setInterval(loadStats, 30000);
</script>
@endsection
