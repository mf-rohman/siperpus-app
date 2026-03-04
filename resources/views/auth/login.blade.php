<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — SiPerpus Unirow</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --ink:    #0f1117;
      --paper:  #f5f0e8;
      --accent: #c0392b;
      --muted:  #7a7469;
    }
    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--paper);
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 2rem;
    }
    .card {
      background: #fff;
      border-radius: 24px;
      padding: 3rem 2.5rem;
      width: 100%; max-width: 400px;
      box-shadow: 0 20px 60px rgba(15,17,23,0.12);
      border: 1px solid rgba(15,17,23,0.06);
      animation: popIn 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
    }
    .logo {
      display: flex; align-items: center; gap: 0.75rem;
      margin-bottom: 2rem;
    }
    .logo-icon {
      width: 40px; height: 40px; background: var(--ink);
      border-radius: 10px; display: grid; place-items: center;
    }
    .logo-icon svg { width: 20px; height: 20px; fill: none; stroke: #f5f0e8; stroke-width: 1.8; }
    .logo-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.1rem; font-weight: 700; line-height: 1.2;
    }
    .logo-name small {
      display: block; font-family: 'DM Mono', monospace;
      font-size: 0.58rem; letter-spacing: 0.1em;
      text-transform: uppercase; color: var(--muted);
    }
    h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem; font-weight: 900;
      letter-spacing: -0.02em; margin-bottom: 0.4rem;
    }
    .subtitle {
      font-size: 0.85rem; color: var(--muted);
      margin-bottom: 2rem; font-weight: 300;
    }
    .field { margin-bottom: 1.1rem; }
    label {
      display: block; font-size: 0.75rem; font-weight: 600;
      letter-spacing: 0.05em; text-transform: uppercase;
      margin-bottom: 0.4rem; color: var(--ink);
    }
    input[type="email"],
    input[type="password"] {
      width: 100%; padding: 0.75rem 1rem;
      border: 1.5px solid rgba(15,17,23,0.12);
      border-radius: 12px; font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem; color: var(--ink);
      background: #fafafa;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      outline: none;
    }
    input:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(192,57,43,0.1);
      background: #fff;
    }
    .remember {
      display: flex; align-items: center; gap: 0.5rem;
      font-size: 0.8rem; color: var(--muted);
      margin-bottom: 1.5rem;
    }
    .remember input { width: auto; }
    .btn-login {
      width: 100%; padding: 0.85rem;
      background: var(--ink); color: #f5f0e8;
      border: none; border-radius: 12px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem; font-weight: 600;
      cursor: pointer; letter-spacing: 0.01em;
      transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }
    .btn-login:hover {
      background: var(--accent);
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(192,57,43,0.25);
    }
    .error-msg {
      background: rgba(192,57,43,0.08);
      border: 1px solid rgba(192,57,43,0.2);
      border-radius: 10px; padding: 0.7rem 1rem;
      font-size: 0.8rem; color: var(--accent);
      margin-bottom: 1.2rem;
    }
    .footer-note {
      text-align: center; margin-top: 1.5rem;
      font-family: 'DM Mono', monospace;
      font-size: 0.6rem; letter-spacing: 0.07em; color: var(--muted);
    }
    @keyframes popIn {
      from { opacity: 0; transform: scale(0.95) translateY(10px); }
      to   { opacity: 1; transform: scale(1)    translateY(0); }
    }
  </style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 00-2 2v14a2 2 0 002 2h3M16 3h3a2 2 0 012 2v14a2 2 0 01-2 2h-3M12 3v18"/></svg>
    </div>
    <div class="logo-name">
      SiPerpus
      <small>Unirow — Perpustakaan</small>
    </div>
  </div>

  <h2>Selamat Datang</h2>
  <p class="subtitle">Masuk untuk mengakses sistem perpustakaan.</p>

  {{-- Error message --}}
  @if ($errors->any())
    <div class="error-msg">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="field">
      <label for="email">Email</label>
      <input type="email" id="email" name="email"
             value="{{ old('email') }}"
             placeholder="admin@perpus.unirow.ac.id"
             required autofocus>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             placeholder="••••••••" required>
    </div>
    <div class="remember">
      <input type="checkbox" id="remember" name="remember">
      <label for="remember" style="text-transform:none;font-weight:400;letter-spacing:0;">
        Ingat saya
      </label>
    </div>
    <button type="submit" class="btn-login">Masuk ke Sistem</button>
  </form>

  <p class="footer-note">
    &copy; {{ date('Y') }} UPT Perpustakaan Unirow
  </p>
</div>
</body>
</html>