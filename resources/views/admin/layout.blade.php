<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Softie's Cookie</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root{--blue:#1A3FCC;--dark:#0D2490;--yellow:#FFD230;--text:#0A1540;--mid:#3A4A80;--line:#E0E7FF;--bg:#F4F6FF}
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DM Sans',system-ui,sans-serif;background:var(--bg);color:var(--text);line-height:1.5}
        a{color:inherit;text-decoration:none}

        /* sidebar */
        .side{position:fixed;inset:0 auto 0 0;width:240px;background:var(--dark);color:#fff;display:flex;flex-direction:column;padding:20px 14px;z-index:20}
        .brand{font-family:'Bebas Neue',sans-serif;font-size:1.8rem;letter-spacing:2px;padding:0 10px 16px;border-bottom:1px solid rgba(255,255,255,.12)}
        .brand span{color:var(--yellow);margin-left:6px;font-size:1.1rem;letter-spacing:1px}
        .side nav{display:flex;flex-direction:column;gap:4px;margin-top:16px}
        .side nav a{padding:10px 12px;border-radius:10px;display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.82);font-weight:700;font-size:.92rem}
        .side nav a:hover{background:rgba(255,255,255,.09)}
        .side nav a.active{background:var(--yellow);color:var(--dark)}
        .pill{margin-left:auto;background:#e63946;color:#fff;border-radius:99px;font-size:.7rem;padding:1px 8px;font-style:normal;font-weight:700}
        .side-foot{margin-top:auto;display:flex;flex-direction:column;gap:8px}
        .side-foot a,.side-foot button{display:block;width:100%;text-align:left;padding:10px 12px;border-radius:10px;border:0;background:rgba(255,255,255,.08);color:#fff;font:inherit;font-weight:700;font-size:.88rem;cursor:pointer}
        .side-foot a:hover,.side-foot button:hover{background:rgba(255,255,255,.16)}
        .main{margin-left:240px;padding:28px 32px;max-width:1320px}
        body.guest .main{margin:0 auto;padding:60px 20px;max-width:520px}

        h1{font-family:'Bebas Neue',sans-serif;font-size:2.2rem;letter-spacing:1.5px;color:var(--dark);line-height:1.1}
        h2{font-size:1.05rem;color:var(--dark);margin-bottom:10px}
        .bar{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px}
        .small{color:var(--mid);font-weight:400;font-size:.8rem}

        .btn{display:inline-block;padding:9px 18px;border-radius:99px;border:0;background:var(--blue);color:#fff;font:inherit;font-weight:700;font-size:.85rem;cursor:pointer;transition:background .15s}
        .btn:hover{background:var(--dark)}
        .btn.gray{background:#8a93b8}.btn.gray:hover{background:#6c7599}
        .btn.red{background:#d62839}.btn.red:hover{background:#a81e2d}
        .btn.green{background:#1f9d55}.btn.green:hover{background:#177a42}
        .btn.sm{padding:5px 12px;font-size:.78rem}

        .ok{background:#e3f5e9;border:1px solid #9fd6b0;padding:10px 16px;border-radius:10px;margin-bottom:16px;font-weight:500}
        .err{background:#fde6e8;border:1px solid #e8a3ab;padding:10px 16px;border-radius:10px;margin-bottom:16px}

        .tbl{overflow-x:auto;background:#fff;border-radius:14px;box-shadow:0 2px 14px rgba(26,63,204,.07)}
        table{width:100%;border-collapse:collapse}
        th,td{padding:11px 14px;text-align:left;border-bottom:1px solid var(--line);font-size:.88rem;vertical-align:top}
        th{background:#EEF2FF;color:var(--dark);font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
        tr:last-child td{border-bottom:0}
        tr.row-click{cursor:pointer}
        tr.row-click:hover td{background:#F7F9FF}
        tr.is-new td{background:#FFFBE6}
        .actions{display:flex;gap:6px;white-space:nowrap}

        .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:22px}
        .stat{background:#fff;border-radius:14px;padding:16px 18px;box-shadow:0 2px 14px rgba(26,63,204,.07);border-left:5px solid var(--blue)}
        .stat b{display:block;font-family:'Bebas Neue',sans-serif;font-size:2.2rem;letter-spacing:1px;color:var(--dark);line-height:1}
        .stat span{font-size:.8rem;color:var(--mid)}
        .stat.hot{border-color:#e63946}.stat.gold{border-color:var(--yellow)}.stat.green{border-color:#1f9d55}
        .grid2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
        .panel{background:#fff;border-radius:14px;padding:18px;box-shadow:0 2px 14px rgba(26,63,204,.07)}
        .panel table th,.panel table td{padding:8px 6px}

        .badge{display:inline-block;padding:2px 10px;border-radius:99px;font-size:.74rem;font-weight:700;white-space:nowrap}
        .st-menunggu{background:#FFF1C2;color:#8a6100}
        .st-diproses{background:#DCE6FF;color:#1A3FCC}
        .st-siap_diambil{background:#E6DBFF;color:#5b2fb5}
        .st-selesai{background:#D8F3E2;color:#17703f}
        .st-dibatalkan{background:#ECECEC;color:#666}
        .tag-new{display:inline-block;background:#e63946;color:#fff;font-size:.65rem;font-weight:700;padding:1px 7px;border-radius:99px;margin-right:6px;letter-spacing:.5px}

        .filters{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;background:#fff;padding:14px 16px;border-radius:14px;margin-bottom:16px;box-shadow:0 2px 14px rgba(26,63,204,.07)}
        .filters label{font-size:.75rem;font-weight:700;color:var(--mid);margin:0}
        .tabs{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}
        .tabs a{padding:7px 16px;border-radius:99px;background:#fff;font-weight:700;font-size:.85rem;border:2px solid var(--line)}
        .tabs a.on{background:var(--blue);border-color:var(--blue);color:#fff}

        form.box{background:#fff;padding:26px;border-radius:14px;box-shadow:0 2px 14px rgba(26,63,204,.07);max-width:680px}
        label{display:block;font-weight:700;margin-bottom:14px;font-size:.88rem}
        input,select,textarea{display:block;width:100%;margin-top:5px;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font:inherit;background:#FAFBFF;font-weight:400}
        input:focus,select:focus,textarea:focus{outline:none;border-color:var(--blue)}
        input[type=checkbox]{display:inline-block;width:auto;margin-right:6px}
        .filters input,.filters select{margin-top:3px;padding:8px 10px}

        /* modal */
        .modal{position:fixed;inset:0;background:rgba(10,21,64,.6);display:none;align-items:center;justify-content:center;padding:16px;z-index:100}
        .modal.open{display:flex}
        .modal-box{background:#fff;border-radius:16px;width:100%;max-width:640px;max-height:92vh;overflow-y:auto;padding:24px;position:relative;border-top:5px solid var(--yellow)}
        .modal-x{position:absolute;top:12px;right:14px;border:0;background:#EEF2FF;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:1rem}
        .kv{display:grid;grid-template-columns:130px 1fr;gap:6px 12px;font-size:.88rem;margin:14px 0}
        .kv dt{color:var(--mid);font-weight:700}
        .kv dd{margin:0}
        .modal table th,.modal table td{padding:7px 8px}

        @media(max-width:860px){
            .side{position:static;width:auto;flex-direction:row;flex-wrap:wrap;align-items:center;padding:10px 12px;gap:8px}
            .brand{border:0;padding:0 8px 0 0;font-size:1.4rem}
            .side nav{flex-direction:row;flex-wrap:wrap;margin:0}
            .side-foot{margin:0 0 0 auto;flex-direction:row}
            .side-foot a,.side-foot button{width:auto}
            .main{margin-left:0;padding:18px 14px}
            .grid2{grid-template-columns:1fr}
        }
    </style>
</head>
<body class="{{ auth()->check() ? 'with-side' : 'guest' }}">
@auth
@php
    $navNew = \App\Models\Order::whereNull('opened_at')->count();
    $navRev = \App\Models\Review::where('status', 'pending')->count();
@endphp
<aside class="side">
    <div class="brand">Softie's<span>Admin</span></div>
    <nav>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">🧾 Pesanan @if($navNew)<em class="pill">{{ $navNew }}</em>@endif</a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">🍪 Menu</a>
        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">💬 Komentar @if($navRev)<em class="pill">{{ $navRev }}</em>@endif</a>
    </nav>
    <div class="side-foot">
        <a href="{{ route('home') }}">🌐 Lihat Website</a>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button>↩ Logout</button></form>
    </div>
</aside>
@endauth
<main class="main">
    @if (session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
    @if ($errors->any())<div class="err">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
    @yield('content')
</main>
</body>
</html>
