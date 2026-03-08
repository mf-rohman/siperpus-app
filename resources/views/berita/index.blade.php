@extends('layouts.app')
@section('title', 'Berita & Pengumuman — SiPerpus')

@section('extra_head')
<style>
    .berita-wrap { max-width:1200px; margin:0 auto; padding:2rem 1.5rem; }

    /* Grid */
    .berita-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-top: 1.5rem;
    }

    /* Card */
    .berita-card {
        background:var(--card-bg);border:1px solid var(--border);
        border-radius:14px;overflow:hidden;
        transition:box-shadow .2s,transform .2s;
        display:flex;flex-direction:column;
    }
    .berita-card:hover { box-shadow:0 8px 28px rgba(0,0,0,.08);transform:translateY(-2px); }
    .berita-card-img { width:100%;height:160px;object-fit:cover;display:block;background:#e5e7eb; }
    .berita-card-placeholder {
        width:100%;height:160px;
        background:linear-gradient(135deg,#e5e7eb,#f3f4f6);
        display:flex;align-items:center;justify-content:center;font-size:2.5rem;
    }
    .berita-card-body { padding:1.1rem 1.2rem 1rem;display:flex;flex-direction:column;flex:1; }
    .berita-card-title { font-weight:700;font-size:.92rem;line-height:1.35;margin-bottom:.4rem;letter-spacing:-.01em; }
    .berita-card-narasi {
        font-size:.78rem;color:var(--muted);line-height:1.6;margin-bottom:.75rem;flex:1;
        display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;
    }
    .berita-card-footer {
        display:flex;align-items:center;justify-content:space-between;
        padding-top:.75rem;border-top:1px solid var(--border);margin-top:auto;flex-wrap:wrap;gap:.5rem;
    }
    .berita-card-date { font-size:.68rem;color:var(--muted);font-family:'JetBrains Mono',monospace; }

    /* Toggle */
    .toggle-switch { position:relative;display:inline-block;width:40px;height:22px; }
    .toggle-switch input { opacity:0;width:0;height:0; }
    .toggle-slider {
        position:absolute;inset:0;cursor:pointer;background:#e5e7eb;border-radius:22px;transition:.3s;
    }
    .toggle-slider::before {
        content:'';position:absolute;width:16px;height:16px;left:3px;bottom:3px;
        background:#fff;border-radius:50%;transition:.3s;box-shadow:0 1px 3px rgba(0,0,0,.2);
    }
    .toggle-switch input:checked + .toggle-slider { background:var(--accent2); }
    .toggle-switch input:checked + .toggle-slider::before { transform:translateX(18px); }

    /* Modal */
    .modal-overlay {
        display:none;position:fixed;inset:0;z-index:9999;
        background:rgba(13,15,18,.6);backdrop-filter:blur(6px);
        align-items:center;justify-content:center;padding:1rem;
    }
    .modal-overlay.open { display:flex; }
    .modal-box {
        background:#fff;border-radius:20px;padding:1.75rem;
        width:100%;max-width:520px;
        box-shadow:0 24px 80px rgba(13,15,18,.25);
        animation:cardReveal .4s cubic-bezier(0.34,1.4,0.64,1) both;
        max-height:92vh;overflow-y:auto;
    }
    @keyframes cardReveal {
        from{opacity:0;transform:scale(.95) translateY(12px)}
        to{opacity:1;transform:scale(1) translateY(0)}
    }

    /* Form */
    .form-group { margin-bottom:1rem; }
    .form-label {
        display:block;font-size:.72rem;font-weight:700;
        text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.4rem;
    }
    .form-input {
        width:100%;padding:.65rem .9rem;border:1.5px solid var(--border);border-radius:10px;
        font-size:.88rem;font-family:'Inter',sans-serif;background:var(--card-bg);color:var(--ink);
        outline:none;transition:border-color .2s,box-shadow .2s;
    }
    .form-input:focus { border-color:var(--ink);box-shadow:0 0 0 3px rgba(13,15,18,.07); }
    textarea.form-input { resize:vertical;min-height:110px; }
    select.form-input   { cursor:pointer; }

    /* Foto upload */
    #fotoPreview { width:100%;height:160px;object-fit:cover;border-radius:10px;display:none;margin-bottom:.5rem; }
    .foto-upload-area {
        border:2px dashed var(--border);border-radius:10px;
        padding:1.25rem;text-align:center;cursor:pointer;transition:border-color .2s;
    }
    .foto-upload-area:hover { border-color:var(--ink); }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px)  { .berita-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 580px)  {
        .berita-wrap   { padding:1.25rem .9rem; }
        .berita-grid   { grid-template-columns: 1fr; }
        .header-row    { flex-direction:column;align-items:flex-start;gap:.75rem; }
        .modal-box     { padding:1.25rem; }
    }
</style>
@endsection

@section('content')
<div class="berita-wrap">

    {{-- Header --}}
    <div class="header-row animate-up" style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);margin-bottom:.25rem;">Konten Portal</p>
            <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);font-weight:800;letter-spacing:-.03em;">Berita & Pengumuman</h1>
            <p style="margin-top:.25rem;font-size:.875rem;color:var(--muted);">Kelola konten yang tampil di portal perpustakaan</p>
        </div>
        <button onclick="openModal()" class="btn-primary">＋ Tambah Berita</button>
    </div>

    {{-- Grid --}}
    <div class="berita-grid animate-up delay-1" id="beritaGrid">
        <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--muted);">
            <div style="font-size:2rem;margin-bottom:.5rem;">⏳</div>
            Memuat berita…
        </div>
    </div>

</div>

{{-- Modal --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <h2 style="font-weight:700;font-size:1rem;" id="modalTitle">Tambah Berita</h2>
            <button onclick="closeModal()" style="background:rgba(13,15,18,.07);border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;font-size:.85rem;display:grid;place-items:center;">✕</button>
        </div>

        <input type="hidden" id="editId">

        {{-- Foto --}}
        <div class="form-group">
            <label class="form-label">Foto / Dokumentasi</label>
            <img id="fotoPreview" src="" alt="preview">
            <div class="foto-upload-area" onclick="document.getElementById('fotoInput').click()">
                <div style="font-size:1.75rem;margin-bottom:.35rem;">📸</div>
                <p style="font-size:.82rem;color:var(--muted);">Klik untuk upload foto</p>
                <p style="font-size:.72rem;color:var(--muted);margin-top:.15rem;">JPG, PNG — maks 2MB</p>
            </div>
            <input type="file" id="fotoInput" accept="image/*" style="display:none" onchange="previewFoto(this)">
        </div>

        {{-- Judul --}}
        <div class="form-group">
            <label class="form-label">Judul</label>
            <input type="text" id="inputJudul" class="form-input" placeholder="Judul berita atau pengumuman…">
        </div>

        {{-- Kategori --}}
        <div class="form-group">
            <label class="form-label">Kategori</label>
            <select id="inputKategori" class="form-input">
                <option value="Pengumuman">📢 Pengumuman</option>
                <option value="Berita">📰 Berita</option>
                <option value="Kegiatan">🎉 Kegiatan</option>
            </select>
        </div>

        {{-- Narasi --}}
        <div class="form-group">
            <label class="form-label">Narasi / Isi</label>
            <textarea id="inputNarasi" class="form-input" placeholder="Tulis isi berita atau pengumuman…"></textarea>
        </div>

        {{-- Published --}}
        <div class="form-group" style="display:flex;align-items:center;gap:.65rem;">
            <label class="toggle-switch">
                <input type="checkbox" id="inputPublished" checked>
                <span class="toggle-slider"></span>
            </label>
            <span style="font-size:.875rem;font-weight:500;">Langsung dipublikasikan</span>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:.65rem;margin-top:1.25rem;">
            <button type="button" onclick="closeModal()" style="flex:1;padding:.7rem;border:1.5px solid var(--border);border-radius:10px;background:transparent;font-family:'Inter',sans-serif;font-size:.85rem;color:var(--muted);cursor:pointer;">Batal</button>
            <button type="button" onclick="submitBerita()" id="btnSubmit" style="flex:2;padding:.7rem;background:var(--ink);color:var(--paper);border:none;border-radius:10px;font-family:'Inter',sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;">Simpan Berita</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const ICON = { Pengumuman:'📢', Berita:'📰', Kegiatan:'🎉' };

function openModal(data=null) {
    document.getElementById('editId').value        = data?.id ?? '';
    document.getElementById('inputJudul').value    = data?.judul ?? '';
    document.getElementById('inputNarasi').value   = data?.narasi ?? '';
    document.getElementById('inputKategori').value = data?.kategori ?? 'Pengumuman';
    document.getElementById('inputPublished').checked = data ? !!data.published : true;
    document.getElementById('modalTitle').textContent = data ? 'Edit Berita' : 'Tambah Berita';
    const p = document.getElementById('fotoPreview');
    if (data?.foto) { p.src=`/storage/${data.foto}`;p.style.display='block'; }
    else             { p.style.display='none'; }
    document.getElementById('fotoInput').value = '';
    document.getElementById('modalOverlay').classList.add('open');
}
function closeModal() { document.getElementById('modalOverlay').classList.remove('open'); }
document.getElementById('modalOverlay').addEventListener('click', e => { if(e.target===document.getElementById('modalOverlay')) closeModal(); });

function previewFoto(input) {
    const f=input.files[0]; if(!f) return;
    const p=document.getElementById('fotoPreview');
    p.src=URL.createObjectURL(f); p.style.display='block';
}

async function submitBerita() {
    const id     = document.getElementById('editId').value;
    const judul  = document.getElementById('inputJudul').value.trim();
    const narasi = document.getElementById('inputNarasi').value.trim();
    if (!judul||!narasi) { showToast('Tidak Lengkap','Judul dan narasi wajib diisi.','warning'); return; }

    const fd = new FormData();
    fd.append('judul',    judul);
    fd.append('narasi',   narasi);
    fd.append('kategori', document.getElementById('inputKategori').value);
    fd.append('published',document.getElementById('inputPublished').checked ? '1' : '0');
    const foto = document.getElementById('fotoInput').files[0];
    if (foto) fd.append('foto', foto);

    const btn = document.getElementById('btnSubmit');
    btn.textContent='Menyimpan…'; btn.disabled=true;
    try {
        const res  = await fetch(id ? `/api/berita/${id}` : '/api/berita', {
            method:'POST',
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body:fd,
        });
        const data = await res.json();
        if (data.success) { closeModal(); loadBerita(); showToast(id?'Diperbarui':'Ditambahkan',judul,'success'); }
    } catch(e) { showToast('Gagal','Terjadi kesalahan.','error'); }
    finally { btn.textContent='Simpan Berita'; btn.disabled=false; }
}

async function togglePublish(id, cb) {
    const res  = await fetch(`/api/berita/${id}/publish`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}});
    const data = await res.json();
    showToast(data.published?'Dipublikasikan':'Disembunyikan',data.published?'Tampil di portal.':'Disembunyikan dari portal.',data.published?'success':'warning');
}

async function hapusBerita(id) {
    if (!confirm('Hapus berita ini?')) return;
    await fetch(`/api/berita/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}});
    loadBerita(); showToast('Dihapus','Berita berhasil dihapus.','warning');
}

async function loadBerita() {
    const res  = await fetch('/api/berita/admin');
    const data = await res.json();
    const grid = document.getElementById('beritaGrid');

    if (!data.length) {
        grid.innerHTML=`<div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--muted);">
            <div style="font-size:2.5rem;margin-bottom:.5rem;">📭</div>
            Belum ada berita. Klik <b>Tambah Berita</b> untuk mulai.</div>`;
        return;
    }

    grid.innerHTML = data.map(b => {
        const tgl  = new Date(b.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});
        const icon = ICON[b.kategori] ?? '📄';
        // Escape for JSON attr
        const bJson = JSON.stringify(b).replace(/'/g, '&#39;');
        return `
        <div class="berita-card" id="card-${b.id}">
            ${b.foto
                ? `<img class="berita-card-img" src="/storage/${b.foto}" alt="${b.judul}">`
                : `<div class="berita-card-placeholder">${icon}</div>`}
            <div class="berita-card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem;">
                    <span class="badge badge-gray" style="font-size:.65rem;">${icon} ${b.kategori}</span>
                    <label class="toggle-switch" title="${b.published?'Publik':'Tersembunyi'}">
                        <input type="checkbox" ${b.published?'checked':''} onchange="togglePublish(${b.id},this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="berita-card-title">${b.judul}</div>
                <div class="berita-card-narasi">${b.narasi}</div>
                <div class="berita-card-footer">
                    <span class="berita-card-date">📅 ${tgl}</span>
                    <div style="display:flex;gap:.4rem;">
                        <button onclick='openModal(${bJson})' style="padding:.28rem .65rem;border-radius:6px;border:1px solid var(--border);background:transparent;font-size:.72rem;cursor:pointer;color:var(--ink);font-family:'Inter',sans-serif;">Edit</button>
                        <button onclick="hapusBerita(${b.id})" style="padding:.28rem .65rem;border-radius:6px;border:1px solid rgba(200,67,10,.2);background:rgba(200,67,10,.07);font-size:.72rem;cursor:pointer;color:var(--accent);font-family:'Inter',sans-serif;">Hapus</button>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

loadBerita();
</script>
@endsection