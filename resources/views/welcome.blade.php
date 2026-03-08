<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Perpustakaan — Unirow</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --ink: #0f1117; --paper: #f5f0e8; --muted: #7a7469;
  --red: #c0392b; --blue: #2962a7; --green: #27784e; --gold: #c9973a;
}
html, body { height: 100%; }
body { font-family: 'DM Sans', sans-serif; background: var(--paper); color: var(--ink); overflow-x: hidden; }

header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.2rem 3rem; border-bottom: 1px solid rgba(15,17,23,0.08);
  position: relative; z-index: 100;
}
.logo { display: flex; align-items: center; gap: 0.7rem; }
.logo-icon { width: 34px; height: 34px; background: var(--ink); border-radius: 8px; display: grid; place-items: center; flex-shrink: 0; }
.logo-icon svg { width: 17px; height: 17px; fill: none; stroke: #f5f0e8; stroke-width: 1.8; }
.logo-name { font-family: 'Playfair Display', serif; font-size: 0.95rem; font-weight: 700; line-height: 1.2; }
.logo-name small { display: block; font-family: 'DM Mono', monospace; font-size: 0.58rem; font-weight: 400; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); }
#clock { font-family: 'DM Mono', monospace; font-size: 0.72rem; color: var(--muted); letter-spacing: 0.05em; white-space: nowrap; }

.hero { text-align: center; padding: 3.5rem 2rem 2rem; }
.eyebrow {
  display: inline-flex; align-items: center; gap: 0.45rem;
  font-family: 'DM Mono', monospace; font-size: 0.65rem; letter-spacing: 0.15em;
  text-transform: uppercase; color: var(--red); background: rgba(192,57,43,0.07);
  border: 1px solid rgba(192,57,43,0.2); border-radius: 100px;
  padding: 0.28rem 0.9rem; margin-bottom: 1rem;
  opacity: 0; animation: fadeUp 0.6s 0.1s ease forwards;
}
.eyebrow::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: var(--red); animation: blink 2s infinite; }
.hero h1 {
  font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3.8rem);
  font-weight: 900; line-height: 1.08; letter-spacing: -0.03em; margin-bottom: 0.8rem;
  opacity: 0; animation: fadeUp 0.6s 0.2s ease forwards;
}
.hero h1 em { font-style: italic; color: var(--red); }
.hero p {
  font-size: clamp(0.85rem, 2vw, 0.95rem); color: var(--muted); max-width: 380px;
  margin: 0 auto; line-height: 1.7; font-weight: 300;
  opacity: 0; animation: fadeUp 0.6s 0.3s ease forwards;
}

/* DESKTOP HUB */
.portal-scene { display: flex; align-items: center; justify-content: center; padding: 1rem 2rem 4rem; }
.hub { position: relative; width: 600px; height: 600px; flex-shrink: 0; }
.hub-center {
  position: absolute; width: 160px; height: 160px; top: 50%; left: 50%;
  transform: translate(-50%, -50%); background: var(--ink); border-radius: 50%;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.3rem;
  z-index: 30; opacity: 0; overflow: hidden;
  animation: popCenter 0.7s 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards;
}
.hub-center-label { font-family: 'Playfair Display', serif; font-size: 0.68rem; color: #f5f0e8; text-align: center; line-height: 1.3; padding: 0 0.6rem; }
.hub-center-label small { display: block; font-family: 'DM Mono', monospace; font-size: 0.46rem; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.4; margin-top: 0.15rem; }
.hub-glow {
  position: absolute; width: 160px; height: 160px; top: 50%; left: 50%; transform: translate(-50%, -50%);
  border-radius: 50%; background: radial-gradient(circle, rgba(15,17,23,0.18) 0%, transparent 70%);
  z-index: 5; animation: glowPulse 3s ease-in-out infinite;
}
.hub-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; pointer-events: none; }
.ring-spin { position: absolute; top: 50%; left: 50%; border-radius: 50%; border: 1.5px dashed rgba(15,17,23,0.14); z-index: 8; pointer-events: none; }
.ring-spin-1 { width:200px;height:200px;margin-top:-100px;margin-left:-100px;animation:spin 20s linear infinite; }
.ring-spin-2 { width:290px;height:290px;margin-top:-145px;margin-left:-145px;border-style:solid;border-color:rgba(15,17,23,0.05);animation:spin 35s linear infinite reverse; }
.ring-spin-3 { width:420px;height:420px;margin-top:-210px;margin-left:-210px;border-color:rgba(15,17,23,0.04);animation:spin 50s linear infinite; }
.app-card {
  position: absolute; width: 150px; background: #fff; border-radius: 18px;
  padding: 1.2rem 0.9rem 1rem; text-align: center; text-decoration: none; color: var(--ink);
  border: 1px solid rgba(15,17,23,0.07); box-shadow: 0 4px 24px rgba(15,17,23,0.09);
  z-index: 40; cursor: pointer; transition: box-shadow 0.3s ease, transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
.app-card:hover { box-shadow: 0 20px 50px rgba(15,17,23,0.15); }
.card-top    { left:225px;top:0;    opacity:0;animation:slideFromTop    0.65s 0.7s  cubic-bezier(0.34,1.56,0.64,1) forwards; }
.card-right  { right:5px; top:220px;opacity:0;animation:slideFromRight  0.65s 0.85s cubic-bezier(0.34,1.56,0.64,1) forwards; }
.card-bottom { left:225px;top:440px;opacity:0;animation:slideFromBottom 0.65s 1.0s  cubic-bezier(0.34,1.56,0.64,1) forwards; }
.card-left   { left:5px;  top:220px;opacity:0;animation:slideFromLeft   0.65s 1.15s cubic-bezier(0.34,1.56,0.64,1) forwards; }
.card-top:hover{transform:translateY(-8px);} .card-right:hover{transform:translateX(8px);}
.card-bottom:hover{transform:translateY(8px);} .card-left:hover{transform:translateX(-8px);}

/* ═══════════════════════════
   MOBILE LAYOUT — FIXED
═══════════════════════════ */
.mobile-grid {
  display: none;
  flex-direction: column;
  align-items: center;
  padding: 0 1.25rem 3rem;
  gap: 1.25rem;
}

/* Logo wrapper — key fix: width:100% + display:flex + justify-content:center */
.mobile-logo-wrap {
  display: none;
  width: 100%;
  justify-content: center;
  align-items: center;
  padding: 0.5rem 0;
  opacity: 0;
  animation: fadeUp 0.6s 0.3s ease forwards;
}

.mobile-logo-circle {
  width: 100px; height: 100px;
  background: var(--ink); border-radius: 50%;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 0.15rem; flex-shrink: 0;
  box-shadow: 0 0 0 10px rgba(15,17,23,0.05), 0 0 0 20px rgba(15,17,23,0.025);
}
.mobile-logo-circle svg { width: 38px; height: 38px; fill: none; stroke: #f5f0e8; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; }
.mobile-logo-circle span { font-family: 'Playfair Display', serif; font-size: 0.46rem; color: rgba(245,240,232,0.55); letter-spacing: 0.06em; text-transform: uppercase; text-align: center; line-height: 1.4; padding: 0 0.5rem; }

/* Cards: CSS Grid 2x2 — guaranteed equal widths */
.mobile-cards-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.9rem;
  width: 100%;
  max-width: 400px;
}

.app-card-mobile {
  background: #fff; border-radius: 16px; padding: 1.1rem 0.75rem 1rem;
  text-align: center; text-decoration: none; color: var(--ink);
  border: 1px solid rgba(15,17,23,0.07); box-shadow: 0 4px 20px rgba(15,17,23,0.08);
  cursor: pointer; display: flex; flex-direction: column; align-items: center;
  transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.25s ease;
  opacity: 0;
}
.app-card-mobile:hover { transform: translateY(-5px); box-shadow: 0 14px 40px rgba(15,17,23,0.13); }
.app-card-mobile.d1{animation:fadeUp 0.5s 0.4s  ease forwards;}
.app-card-mobile.d2{animation:fadeUp 0.5s 0.52s ease forwards;}
.app-card-mobile.d3{animation:fadeUp 0.5s 0.64s ease forwards;}
.app-card-mobile.d4{animation:fadeUp 0.5s 0.76s ease forwards;}

/* Shared card internals */
.app-icon { width:48px;height:48px;border-radius:13px;display:grid;place-items:center;margin:0 auto 0.65rem;transition:transform 0.3s ease;flex-shrink:0; }
.app-card:hover .app-icon, .app-card-mobile:hover .app-icon { transform: scale(1.1) rotate(-4deg); }
.app-icon svg { width:22px;height:22px;fill:none;stroke-width:1.8; }
.app-card h3, .app-card-mobile h3 { font-family:'Playfair Display',serif;font-size:0.88rem;font-weight:700;margin-bottom:0.25rem;line-height:1.2; }
.app-card p, .app-card-mobile p { font-size:0.62rem;color:var(--muted);line-height:1.5;margin-bottom:0.5rem;flex:1; }
.badge { font-family:'DM Mono',monospace;font-size:0.52rem;letter-spacing:0.08em;text-transform:uppercase;padding:0.15rem 0.5rem;border-radius:100px;border:1px solid currentColor;opacity:0.6;display:inline-block; }

.c-red  .app-icon{background:rgba(192,57,43,0.08);} .c-red  .app-icon svg{stroke:var(--red);}   .c-red  .badge{color:var(--red);}
.c-blue .app-icon{background:rgba(41,98,167,0.08);}  .c-blue .app-icon svg{stroke:var(--blue);}  .c-blue .badge{color:var(--blue);}
.c-grn  .app-icon{background:rgba(39,120,78,0.08);}  .c-grn  .app-icon svg{stroke:var(--green);} .c-grn  .badge{color:var(--green);}
.c-gld  .app-icon{background:rgba(201,151,58,0.08);} .c-gld  .app-icon svg{stroke:var(--gold);}  .c-gld  .badge{color:var(--gold);}

/* MODAL */
.modal-overlay { position:fixed;inset:0;z-index:1000;background:rgba(15,17,23,0.55);backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:center;padding:1.5rem;opacity:0;pointer-events:none;transition:opacity 0.3s ease; }
.modal-overlay.active{opacity:1;pointer-events:auto;}
.modal-box { background:#fff;border-radius:24px;padding:2.5rem 2rem 2rem;width:100%;max-width:340px;text-align:center;box-shadow:0 40px 100px rgba(15,17,23,0.3);transform:scale(0.75) translateY(20px);transition:transform 0.45s cubic-bezier(0.34,1.56,0.64,1);position:relative; }
.modal-overlay.active .modal-box{transform:scale(1) translateY(0);}
.modal-close { position:absolute;top:1rem;right:1rem;width:28px;height:28px;border-radius:50%;background:rgba(15,17,23,0.06);border:none;cursor:pointer;font-size:0.85rem;color:var(--muted);display:grid;place-items:center;transition:background 0.2s ease; }
.modal-close:hover{background:rgba(15,17,23,0.12);}
.modal-icon{width:68px;height:68px;border-radius:20px;display:grid;place-items:center;margin:0 auto 1rem;}
.modal-icon svg{width:32px;height:32px;fill:none;stroke-width:1.8;}
.modal-badge{font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.1em;text-transform:uppercase;padding:0.2rem 0.7rem;border-radius:100px;border:1px solid currentColor;opacity:0.65;display:inline-block;margin-bottom:0.9rem;}
.modal-title{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;letter-spacing:-0.02em;margin-bottom:0.5rem;}
.modal-desc{font-size:0.82rem;color:var(--muted);line-height:1.65;margin-bottom:1.8rem;}
.modal-actions{display:flex;gap:0.75rem;}
.btn-cancel{flex:1;padding:0.75rem;border:1.5px solid rgba(15,17,23,0.12);border-radius:12px;background:transparent;font-family:'DM Sans',sans-serif;font-size:0.85rem;color:var(--muted);cursor:pointer;transition:background 0.2s ease,color 0.2s ease;}
.btn-cancel:hover{background:rgba(15,17,23,0.05);color:var(--ink);}
.btn-go{flex:2;padding:0.75rem;border:none;border-radius:12px;font-family:'DM Sans',sans-serif;font-size:0.85rem;font-weight:600;color:#fff;cursor:pointer;transition:transform 0.2s ease,box-shadow 0.2s ease;}
.btn-go:hover{transform:scale(1.03);box-shadow:0 8px 24px rgba(0,0,0,0.2);}

/* BERITA */
.berita-section{padding:3rem 2rem 4rem;max-width:1100px;margin:0 auto;}
.berita-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin-bottom:1.5rem;}

/* ── BERITA DETAIL MODAL ── */
.berita-modal-overlay {
  position: fixed; inset: 0; z-index: 2000;
  background: rgba(15,17,23,0.7);
  backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
  display: flex; align-items: center; justify-content: center;
  padding: 1rem;
  opacity: 0; pointer-events: none;
  transition: opacity 0.3s ease;
}
.berita-modal-overlay.open { opacity: 1; pointer-events: auto; }
.berita-modal-box {
  background: #fff; border-radius: 20px;
  width: 100%; max-width: 680px;
  max-height: 90vh; overflow-y: auto;
  box-shadow: 0 40px 120px rgba(15,17,23,0.4);
  transform: scale(0.94) translateY(16px);
  transition: transform 0.4s cubic-bezier(0.34,1.4,0.64,1);
  position: relative;
  scrollbar-width: thin; scrollbar-color: rgba(15,17,23,0.15) transparent;
}
.berita-modal-overlay.open .berita-modal-box {
  transform: scale(1) translateY(0);
}
.berita-modal-img {
  width: 100%;
  height: auto;
  max-height: 70vh;
  object-fit: contain;
  display: block;
  background: #1a1a1a;
  border-radius: 20px 20px 0 0;
}
.berita-modal-img-placeholder {
  width: 100%; height: 180px;
  background: linear-gradient(135deg, #f5f0e8, #e8e4dc);
  display: flex; align-items: center; justify-content: center;
  font-size: 4rem; border-radius: 20px 20px 0 0;
}
.berita-modal-body { padding: 1.75rem 2rem 2rem; }
.berita-modal-close {
  position: sticky; top: 0; float: right;
  margin: -1.75rem -2rem 0 0;
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(15,17,23,0.08);
  border: none; cursor: pointer; font-size: 1rem;
  color: var(--muted); display: grid; place-items: center;
  transition: background 0.2s, transform 0.2s;
  z-index: 10; flex-shrink: 0;
}
.berita-modal-close:hover { background: rgba(15,17,23,0.15); transform: rotate(90deg); }
.berita-modal-kategori {
  font-family: 'DM Mono', monospace; font-size: 0.6rem;
  letter-spacing: 0.12em; text-transform: uppercase;
  padding: 0.2rem 0.65rem; border-radius: 100px;
  background: rgba(15,17,23,0.06); color: #7a7469;
  display: inline-block; margin-bottom: 0.85rem;
}
.berita-modal-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(1.3rem, 3vw, 1.75rem);
  font-weight: 900; line-height: 1.2;
  letter-spacing: -0.025em; margin-bottom: 0.75rem;
}
.berita-modal-meta {
  display: flex; align-items: center; gap: 1rem;
  margin-bottom: 1.5rem; padding-bottom: 1.25rem;
  border-bottom: 1px solid rgba(15,17,23,0.08);
  flex-wrap: wrap;
}
.berita-modal-date {
  font-family: 'DM Mono', monospace; font-size: 0.68rem;
  color: #7a7469; display: flex; align-items: center; gap: 0.35rem;
}
.berita-modal-narasi {
  font-size: 0.92rem; line-height: 1.8;
  color: #2d2d2d; white-space: pre-wrap;
}

/* Card clickable cursor */
.berita-card-clickable { cursor: pointer; }
.berita-card-clickable:hover .berita-read-more { opacity: 1; transform: translateY(0); }
.berita-read-more {
  display: inline-flex; align-items: center; gap: 0.3rem;
  font-family: 'DM Mono', monospace; font-size: 0.6rem;
  letter-spacing: 0.08em; text-transform: uppercase;
  color: var(--red); margin-top: 0.65rem;
  opacity: 0.6; transform: translateY(3px);
  transition: opacity 0.2s, transform 0.2s;
}
.berita-read-more::after { content: '→'; }

@media (max-width: 640px) {
  .berita-modal-box { border-radius: 16px; max-height: 92vh; }
  .berita-modal-img { height: auto; max-height: 55vh; border-radius: 16px 16px 0 0; }
  .berita-modal-body { padding: 1.25rem 1.25rem 1.5rem; }
  .berita-modal-close { margin: -1.25rem -1.25rem 0 0; }
  .berita-modal-narasi { font-size: 0.85rem; }
}

footer{text-align:center;padding:1.5rem 2rem;border-top:1px solid rgba(15,17,23,0.08);font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:0.07em;color:var(--muted);}

/* KEYFRAMES */
@keyframes fadeUp      {from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes blink       {0%,100%{opacity:1}50%{opacity:0.3}}
@keyframes spin        {to{transform:rotate(360deg)}}
@keyframes glowPulse   {0%,100%{opacity:0.6;transform:translate(-50%,-50%) scale(1)}50%{opacity:1;transform:translate(-50%,-50%) scale(1.4)}}
@keyframes popCenter   {from{opacity:0;transform:translate(-50%,-50%) scale(0.5)}to{opacity:1;transform:translate(-50%,-50%) scale(1)}}
@keyframes slideFromTop    {from{opacity:0;transform:translateY(-30px)}to{opacity:1;transform:translateY(0)}}
@keyframes slideFromRight  {from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:translateX(0)}}
@keyframes slideFromBottom {from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)}}
@keyframes slideFromLeft   {from{opacity:0;transform:translateX(-30px)}to{opacity:1;transform:translateX(0)}}
@keyframes drawLine        {from{stroke-dashoffset:300}to{stroke-dashoffset:0}}

/* RESPONSIVE */
@media (max-width:900px) and (min-width:700px){
  .hub{width:500px;height:500px;}
  .hub-center{width:130px;height:130px;}
  .hub-center-label{font-size:0.65rem;}
  .ring-spin-1{width:165px;height:165px;margin-top:-82px;margin-left:-82px;}
  .ring-spin-2{width:240px;height:240px;margin-top:-120px;margin-left:-120px;}
  .ring-spin-3{width:350px;height:350px;margin-top:-175px;margin-left:-175px;}
  .app-card{width:130px;}
  .card-top{left:185px;top:0;} .card-right{right:5px;top:185px;}
  .card-bottom{left:185px;top:365px;} .card-left{left:5px;top:185px;}
}

@media (max-width:699px){
  header{padding:1rem 1.25rem;}
  #clock{font-size:0.65rem;}
  .logo-name small{display:none;}
  .hero{padding:2rem 1.25rem 1.5rem;}
  .portal-scene{display:none;}
  .mobile-grid{display:flex;}
  .mobile-logo-wrap{display:flex;}
  .berita-section{padding:2rem 1.25rem 3rem;}
  .berita-grid{grid-template-columns:1fr;}
  footer{font-size:0.55rem;padding:1.2rem 1rem;}
}

@media (max-width:399px){
  .mobile-logo-circle{width:84px;height:84px;}
  .mobile-logo-circle svg{width:32px;height:32px;}
  .mobile-cards-grid{gap:0.65rem;}
  .app-card-mobile{padding:0.9rem 0.6rem 0.85rem;}
  .app-card-mobile h3{font-size:0.78rem;}
  .app-card-mobile p{font-size:0.57rem;}
  .app-icon{width:40px;height:40px;}
}
</style>
</head>
<body>

<header>
  <div class="logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 00-2 2v14a2 2 0 002 2h3M16 3h3a2 2 0 012 2v14a2 2 0 01-2 2h-3M12 3v18"/></svg>
    </div>
    <div class="logo-name">Portal Perpustakaan<small>Universitas PGRI Ronggolawe</small></div>
  </div>
  <div id="clock"></div>
</header>

<section class="hero">
  <div class="eyebrow">Sistem Informasi Perpustakaan</div>
  <h1>Satu Portal,<br><em>Semua Layanan</em></h1>
  <p>Akses seluruh layanan perpustakaan digital Unirow dalam satu tempat.</p>
</section>

<!-- DESKTOP -->
<div class="portal-scene">
  <div class="hub">
    <div class="ring-spin ring-spin-1"></div>
    <div class="ring-spin ring-spin-2"></div>
    <div class="ring-spin ring-spin-3"></div>
    <svg class="hub-svg" viewBox="0 0 600 600">
      <defs><style>
        .spoke{stroke:#0f1117;stroke-width:1;stroke-opacity:0.12;stroke-dasharray:6 4;fill:none;stroke-dashoffset:300;animation:drawLine 0.8s ease forwards;}
        .spoke-top{animation-delay:0.7s;}.spoke-right{animation-delay:0.85s;}.spoke-bottom{animation-delay:1.0s;}.spoke-left{animation-delay:1.15s;}
        .dot{opacity:0;animation:fadeUp 0.3s ease forwards;}
        .dot-top{animation-delay:1.35s;}.dot-right{animation-delay:1.5s;}.dot-bottom{animation-delay:1.65s;}.dot-left{animation-delay:1.8s;}
      </style></defs>
      <line class="spoke spoke-top"    x1="300" y1="300" x2="300" y2="80"/>
      <line class="spoke spoke-right"  x1="300" y1="300" x2="520" y2="300"/>
      <line class="spoke spoke-bottom" x1="300" y1="300" x2="300" y2="520"/>
      <line class="spoke spoke-left"   x1="300" y1="300" x2="80"  y2="300"/>
      <circle class="dot dot-top"    cx="300" cy="80"  r="3.5" fill="#0f1117" opacity="0.2"/>
      <circle class="dot dot-right"  cx="520" cy="300" r="3.5" fill="#0f1117" opacity="0.2"/>
      <circle class="dot dot-bottom" cx="300" cy="520" r="3.5" fill="#0f1117" opacity="0.2"/>
      <circle class="dot dot-left"   cx="80"  cy="300" r="3.5" fill="#0f1117" opacity="0.2"/>
    </svg>
    <div class="hub-glow"></div>
    <div class="hub-center">
      <!-- Logo gedung perpustakaan desktop -->
      <svg viewBox="0 0 48 48" style="width:54px;height:54px;">
        <polygon points="24,6 40,16 8,16"
          fill="rgba(245,240,232,0.18)" stroke="#f5f0e8" stroke-width="1.2" stroke-linejoin="round"/>
        <rect x="10" y="16" width="28" height="20"
          fill="rgba(245,240,232,0.1)" stroke="#f5f0e8" stroke-width="1.2" rx="0.5"/>
        <rect x="13" y="18" width="3.5" height="16" fill="rgba(245,240,232,0.25)" rx="0.5"/>
        <rect x="22.25" y="18" width="3.5" height="16" fill="rgba(245,240,232,0.25)" rx="0.5"/>
        <rect x="31.5" y="18" width="3.5" height="16" fill="rgba(245,240,232,0.25)" rx="0.5"/>
        <path d="M20.5 36 L20.5 27 Q24 23.5 27.5 27 L27.5 36"
          fill="rgba(245,240,232,0.15)" stroke="#f5f0e8" stroke-width="1"/>
        <line x1="7" y1="36" x2="41" y2="36" stroke="#f5f0e8" stroke-width="1.4"/>
        <path d="M24 2 L24.9 4.8 L27.8 4.8 L25.5 6.5 L26.3 9.3 L24 7.6 L21.7 9.3 L22.5 6.5 L20.2 4.8 L23.1 4.8 Z"
          fill="rgba(245,240,232,0.85)" stroke="none"/>
        <line x1="6" y1="39" x2="42" y2="39" stroke="#f5f0e8" stroke-width="1.2"/>
        <line x1="4" y1="42" x2="44" y2="42" stroke="#f5f0e8" stroke-width="1.2"/>
      </svg>
      <div class="hub-center-label">Perpustakaan<br>Unirow<small>portal v1.0</small></div>
    </div>
    <a href="/siperpus" class="app-card card-top c-red" data-title="SiPerpus" data-desc="Sistem absensi pengunjung perpustakaan berbasis scan QR Code KTM mahasiswa." data-badge="Absensi">
      <div class="app-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
      <h3>SiPerpus</h3><p>Absensi pengunjung via scan QR KTM</p><span class="badge">Absensi</span>
    </a>
    <a href="/katalog" class="app-card card-right c-blue" data-title="SiKatalog" data-desc="Katalog buku digital dan pencarian koleksi perpustakaan Unirow secara online." data-badge="Katalog">
      <div class="app-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg></div>
      <h3>SiKatalog</h3><p>Katalog buku & koleksi perpustakaan</p><span class="badge">Katalog</span>
    </a>
    <a href="#" class="app-card card-bottom c-grn" data-title="SiPinjam" data-desc="Sistem peminjaman dan pengembalian buku perpustakaan secara digital." data-badge="Sirkulasi">
      <div class="app-icon"><svg viewBox="0 0 24 24"><path d="M16 3h5v5M8 21H3v-5M21 3l-7 7M3 21l7-7"/></svg></div>
      <h3>SiPinjam</h3><p>Peminjaman & pengembalian buku</p><span class="badge">Sirkulasi</span>
    </a>
    <a href="#" class="app-card card-left c-gld" data-title="SiLaporan" data-desc="Dashboard statistik dan laporan analitik kunjungan serta koleksi perpustakaan." data-badge="Laporan">
      <div class="app-icon"><svg viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
      <h3>SiLaporan</h3><p>Statistik & analitik perpustakaan</p><span class="badge">Laporan</span>
    </a>
  </div>
</div>

<!-- MOBILE -->
<div class="mobile-grid">
  <div class="mobile-logo-wrap">
    <div class="mobile-logo-circle">
      <!-- Logo Perpustakaan: gedung dengan kolom dan buku -->
      <svg viewBox="0 0 48 48" style="width:44px;height:44px;">
        <!-- Atap / pediment gedung -->
        <polygon points="24,6 40,16 8,16"
          fill="rgba(245,240,232,0.18)" stroke="#f5f0e8" stroke-width="1.2"
          stroke-linejoin="round"/>
        <!-- Badan gedung -->
        <rect x="10" y="16" width="28" height="20"
          fill="rgba(245,240,232,0.1)" stroke="#f5f0e8" stroke-width="1.2"
          rx="0.5"/>
        <!-- Kolom kiri -->
        <rect x="13" y="18" width="3.5" height="16" fill="rgba(245,240,232,0.25)" rx="0.5"/>
        <!-- Kolom tengah -->
        <rect x="22.25" y="18" width="3.5" height="16" fill="rgba(245,240,232,0.25)" rx="0.5"/>
        <!-- Kolom kanan -->
        <rect x="31.5" y="18" width="3.5" height="16" fill="rgba(245,240,232,0.25)" rx="0.5"/>
        <!-- Pintu tengah -->
        <path d="M20.5 36 L20.5 27 Q24 23.5 27.5 27 L27.5 36"
          fill="rgba(245,240,232,0.15)" stroke="#f5f0e8" stroke-width="1"/>
        <!-- Lantai dasar -->
        <line x1="7" y1="36" x2="41" y2="36" stroke="#f5f0e8" stroke-width="1.4"/>
        <!-- Bintang / cahaya di puncak -->
        <path d="M24 2 L24.9 4.8 L27.8 4.8 L25.5 6.5 L26.3 9.3 L24 7.6 L21.7 9.3 L22.5 6.5 L20.2 4.8 L23.1 4.8 Z"
          fill="rgba(245,240,232,0.8)" stroke="none"/>
        <!-- Tangga bawah -->
        <line x1="6" y1="39" x2="42" y2="39" stroke="#f5f0e8" stroke-width="1.2"/>
        <line x1="4" y1="42" x2="44" y2="42" stroke="#f5f0e8" stroke-width="1.2"/>
      </svg>
      <span style="font-family:'Playfair Display',serif;font-size:0.44rem;color:rgba(245,240,232,0.55);letter-spacing:0.06em;text-transform:uppercase;text-align:center;line-height:1.4;padding:0 0.4rem;">Perpus<br>Unirow</span>
    </div>
  </div>

  <div class="mobile-cards-grid">
    <a href="/siperpus" class="app-card-mobile c-red d1" data-title="SiPerpus" data-desc="Sistem absensi pengunjung perpustakaan berbasis scan QR Code KTM mahasiswa." data-badge="Absensi">
      <div class="app-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
      <h3>SiPerpus</h3><p>Absensi via QR KTM</p><span class="badge">Absensi</span>
    </a>
    <a href="/katalog" class="app-card-mobile c-blue d2" data-title="SiKatalog" data-desc="Katalog buku digital dan pencarian koleksi perpustakaan Unirow secara online." data-badge="Katalog">
      <div class="app-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg></div>
      <h3>SiKatalog</h3><p>Katalog koleksi buku</p><span class="badge">Katalog</span>
    </a>
    <a href="#" class="app-card-mobile c-grn d3" data-title="SiPinjam" data-desc="Sistem peminjaman dan pengembalian buku perpustakaan secara digital." data-badge="Sirkulasi">
      <div class="app-icon"><svg viewBox="0 0 24 24"><path d="M16 3h5v5M8 21H3v-5M21 3l-7 7M3 21l7-7"/></svg></div>
      <h3>SiPinjam</h3><p>Pinjam & kembalikan buku</p><span class="badge">Sirkulasi</span>
    </a>
    <a href="#" class="app-card-mobile c-gld d4" data-title="SiLaporan" data-desc="Dashboard statistik dan laporan analitik kunjungan serta koleksi perpustakaan." data-badge="Laporan">
      <div class="app-icon"><svg viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg></div>
      <h3>SiLaporan</h3><p>Statistik perpustakaan</p><span class="badge">Laporan</span>
    </a>
  </div>
</div>

<!-- BERITA -->
<section class="berita-section">
  <div style="text-align:center;margin-bottom:2.5rem;">
    <div style="display:inline-flex;align-items:center;gap:.45rem;font-family:'DM Mono',monospace;font-size:.65rem;letter-spacing:.15em;text-transform:uppercase;color:#c0392b;background:rgba(192,57,43,.07);border:1px solid rgba(192,57,43,.2);border-radius:100px;padding:.28rem .9rem;margin-bottom:.9rem;">📢 Berita & Pengumuman</div>
    <h2 style="font-family:'Playfair Display',serif;font-size:clamp(1.5rem,4vw,2rem);font-weight:900;letter-spacing:-.02em;">Info Terkini</h2>
    <p style="font-size:.9rem;color:#7a7469;margin-top:.4rem;">Berita dan pengumuman terbaru dari perpustakaan</p>
  </div>
  <div class="berita-grid" id="portalBeritaGrid"></div>
  <div style="text-align:center;margin-top:1.5rem;">
    <button id="btnLoadMore" onclick="loadMoreBerita()" style="display:none;padding:.65rem 2rem;background:#0f1117;color:#f5f0e8;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.85rem;font-weight:600;cursor:pointer;">Lihat Lebih Banyak</button>
  </div>
</section>


<!-- ══ BERITA DETAIL MODAL ══ -->
<div class="berita-modal-overlay" id="beritaModal" onclick="closeBeritaModal(event)">
  <div class="berita-modal-box" id="beritaModalBox">
    <div id="beritaModalImg"></div>
    <div class="berita-modal-body">
      <button class="berita-modal-close" onclick="tutupBeritaModal()">✕</button>
      <div class="berita-modal-kategori" id="beritaModalKategori"></div>
      <h2 class="berita-modal-title" id="beritaModalTitle"></h2>
      <div class="berita-modal-meta">
        <span class="berita-modal-date">📅 <span id="beritaModalDate"></span></span>
      </div>
      <div class="berita-modal-narasi" id="beritaModalNarasi"></div>
    </div>
  </div>
</div>
<footer>&copy; <span id="yr"></span> &nbsp;·&nbsp; UPT Perpustakaan Universitas PGRI Ronggolawe Tuban</footer>

<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <div class="modal-icon" id="modalIcon"></div>
    <div class="modal-badge" id="modalBadge"></div>
    <div class="modal-title" id="modalTitle"></div>
    <div class="modal-desc"  id="modalDesc"></div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-go" id="btnGo">Buka Aplikasi →</button>
    </div>
  </div>
</div>

<script>
function tick(){document.getElementById('clock').textContent=new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit',second:'2-digit'});}
tick();setInterval(tick,1000);
document.getElementById('yr').textContent=new Date().getFullYear();

const overlay=document.getElementById('modalOverlay');
let currentUrl='#';
const colorMap={
  'c-red': {bg:'rgba(192,57,43,0.09)', stroke:'#c0392b',btn:'#c0392b'},
  'c-blue':{bg:'rgba(41,98,167,0.09)', stroke:'#2962a7',btn:'#2962a7'},
  'c-grn': {bg:'rgba(39,120,78,0.09)', stroke:'#27784e',btn:'#27784e'},
  'c-gld': {bg:'rgba(201,151,58,0.09)',stroke:'#c9973a',btn:'#c9973a'},
};
document.querySelectorAll('.app-card,.app-card-mobile').forEach(card=>{
  card.addEventListener('click',e=>{
    e.preventDefault();
    const theme=[...card.classList].find(c=>c.startsWith('c-'))||'c-red';
    const colors=colorMap[theme];
    currentUrl=card.getAttribute('href');
    document.getElementById('modalTitle').textContent=card.dataset.title;
    document.getElementById('modalDesc').textContent=card.dataset.desc;
    const badge=document.getElementById('modalBadge');
    badge.textContent=card.dataset.badge;badge.style.color=colors.stroke;badge.style.borderColor=colors.stroke;
    const iconWrap=document.getElementById('modalIcon');
    iconWrap.innerHTML=card.querySelector('.app-icon').innerHTML;
    iconWrap.style.background=colors.bg;
    const svg=iconWrap.querySelector('svg');
    if(svg){svg.style.stroke=colors.stroke;svg.style.width='32px';svg.style.height='32px';}
    document.getElementById('btnGo').style.background=colors.btn;
    overlay.classList.add('active');
  });
});
function closeModal(){overlay.classList.remove('active');}
function goToApp(){if(currentUrl&&currentUrl!=='#')window.location.href=currentUrl;else closeModal();}
document.getElementById('btnGo').onclick=goToApp;
overlay.addEventListener('click',e=>{if(e.target===overlay)closeModal();});
document.addEventListener('keydown',e=>{if(e.key==='Escape')closeModal();});

const SIPERPUS_URL='';
let beritaOffset=0;
const BERITA_LIMIT=6;
async function loadBeritaPortal(reset=false){
  if(reset){beritaOffset=0;document.getElementById('portalBeritaGrid').innerHTML='';}
  try{
    const res=await fetch(`${SIPERPUS_URL}/api/berita?offset=${beritaOffset}&limit=${BERITA_LIMIT}`);
    const data=await res.json();
    const grid=document.getElementById('portalBeritaGrid');
    const ICON={Pengumuman:'📢',Berita:'📰',Kegiatan:'🎉'};
    if(!data.data.length&&beritaOffset===0){grid.innerHTML='<div style="grid-column:1/-1;text-align:center;padding:2rem;color:#7a7469;">Belum ada berita.</div>';return;}
    data.data.forEach(b=>{
      const tanggal=new Date(b.created_at).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});
      const icon=ICON[b.kategori]??'📄';
      const card=document.createElement('div');
      card.style.cssText='background:#fff;border-radius:16px;overflow:hidden;border:1px solid rgba(15,17,23,.07);box-shadow:0 4px 20px rgba(15,17,23,.07);transition:transform .25s ease,box-shadow .25s ease;opacity:0;animation:fadeUp .5s ease forwards;';
      card.innerHTML=`${b.foto?`<img src="${SIPERPUS_URL}/storage/${b.foto}" alt="${b.judul}" style="width:100%;height:180px;object-fit:cover;display:block;">`:`<div style="width:100%;height:180px;background:linear-gradient(135deg,#f5f0e8,#e8e4dc);display:flex;align-items:center;justify-content:center;font-size:3rem;">${icon}</div>`}<div style="padding:1.1rem 1.25rem 1.25rem;"><span style="font-family:'DM Mono',monospace;font-size:.6rem;letter-spacing:.08em;text-transform:uppercase;padding:.15rem .5rem;border-radius:100px;background:rgba(15,17,23,.06);color:#7a7469;">${icon} ${b.kategori}</span><h3 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;line-height:1.3;margin:.5rem 0;letter-spacing:-.01em;">${b.judul}</h3><p style="font-size:.78rem;color:#7a7469;line-height:1.6;margin-bottom:.75rem;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">${b.narasi}</p><div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;"><span style="font-family:'DM Mono',monospace;font-size:.65rem;color:#7a7469;">📅 ${tanggal}</span><span style="font-family:'DM Mono',monospace;font-size:.6rem;letter-spacing:.06em;text-transform:uppercase;color:#c0392b;opacity:.7;">Baca →</span></div></div>`;
      card.addEventListener('mouseenter',()=>{card.style.transform='translateY(-4px)';card.style.boxShadow='0 12px 36px rgba(15,17,23,.12)';});
      card.addEventListener('mouseleave',()=>{card.style.transform='translateY(0)';card.style.boxShadow='0 4px 20px rgba(15,17,23,.07)';});
      card.style.cursor = 'pointer';
      card.addEventListener('click', () => bukaBeritaModal(b));
      grid.appendChild(card);
    });
    beritaOffset+=BERITA_LIMIT;
    document.getElementById('btnLoadMore').style.display=data.has_more?'inline-block':'none';
  }catch(e){console.log('Berita tidak tersedia:',e);}
}
function bukaBeritaModal(b) {
  const ICON = {Pengumuman:'📢',Berita:'📰',Kegiatan:'🎉'};
  const icon = ICON[b.kategori] ?? '📄';
  const tanggal = new Date(b.created_at).toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});

  // Gambar
  const imgWrap = document.getElementById('beritaModalImg');
  if (b.foto) {
    imgWrap.innerHTML = `<img class="berita-modal-img" src="${SIPERPUS_URL}/storage/${b.foto}" alt="${b.judul}">`;
  } else {
    imgWrap.innerHTML = `<div class="berita-modal-img-placeholder">${icon}</div>`;
  }

  document.getElementById('beritaModalKategori').textContent = icon + ' ' + b.kategori;
  document.getElementById('beritaModalTitle').textContent    = b.judul;
  document.getElementById('beritaModalDate').textContent     = tanggal;
  document.getElementById('beritaModalNarasi').textContent   = b.narasi;

  // Buka modal
  const modal = document.getElementById('beritaModal');
  modal.classList.add('open');
  document.getElementById('beritaModalBox').scrollTop = 0;
  document.body.style.overflow = 'hidden';

  // Update URL hash tanpa reload
  history.pushState({ beritaId: b.id }, '', '#berita-' + b.id);
}

function tutupBeritaModal() {
  document.getElementById('beritaModal').classList.remove('open');
  document.body.style.overflow = '';
  if (location.hash.startsWith('#berita-')) history.pushState({}, '', location.pathname);
}

function closeBeritaModal(e) {
  if (e.target === document.getElementById('beritaModal')) tutupBeritaModal();
}

// Tutup dengan Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && document.getElementById('beritaModal').classList.contains('open')) {
    tutupBeritaModal();
  }
});

// Handle browser back button
window.addEventListener('popstate', () => {
  if (document.getElementById('beritaModal').classList.contains('open')) {
    document.getElementById('beritaModal').classList.remove('open');
    document.body.style.overflow = '';
  }
});

function loadMoreBerita(){loadBeritaPortal();}
loadBeritaPortal(true);
</script>
</body>
</html>