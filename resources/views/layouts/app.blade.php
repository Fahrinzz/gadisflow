<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Document System') — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root{
            --pink:#FFC5D3; --pink-soft:#ffe6ec; --maroon:#6e2033; --maroon-dk:#551826;
            --ink:#2f2a2c; --muted:#8a7d81; --line:#f0e5e8; --bg:#f6f1f3;
            --radius:14px; --shadow:0 2px 12px rgba(110,32,51,.07), 0 1px 3px rgba(0,0,0,.04);
        }
        /* Cross-browser consistency (Chrome, Firefox, Safari, Edge) */
        *,*::before,*::after{ box-sizing:border-box; }
        html{ -webkit-text-size-adjust:100%; text-size-adjust:100%; }
        body{ background:var(--bg); color:var(--ink);
            font-family:"Segoe UI",system-ui,-apple-system,"Helvetica Neue",Arial,sans-serif;
            -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
            -webkit-print-color-adjust:exact; print-color-adjust:exact; }

        /* ---- Navbar ---- */
        .appbar{ background:linear-gradient(100deg,var(--maroon),var(--maroon-dk)); box-shadow:0 3px 14px rgba(110,32,51,.28); }
        .appbar .navbar-brand{ font-weight:800; letter-spacing:.4px; color:#fff; display:flex; align-items:center; gap:.55rem; }
        .appbar .navbar-brand .logo-badge{
            background:var(--pink); color:var(--maroon); font-weight:900; font-style:italic;
            padding:.12rem .5rem; border-radius:9px; font-size:.95rem; letter-spacing:.5px;
        }
        .appbar .nav-link{ color:rgba(255,255,255,.82)!important; border-radius:999px; padding:.42rem .95rem!important; margin:0 .12rem; transition:.15s; font-size:.95rem; }
        .appbar .nav-link:hover{ color:#fff!important; background:rgba(255,255,255,.14); }
        .appbar .nav-link.active{ color:var(--maroon)!important; background:var(--pink); font-weight:600; }
        .appbar .nav-link i{ margin-right:.25rem; opacity:.9; }
        .appbar .btn-logout{ color:#fff; border:1px solid rgba(255,255,255,.4); border-radius:999px; padding:.35rem .9rem; font-size:.9rem; }
        .appbar .btn-logout:hover{ background:rgba(255,255,255,.15); }

        /* ---- Page heading ---- */
        .page-head{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:.75rem; margin-bottom:1.4rem; }
        .page-head h3{ margin:0; font-weight:800; color:var(--maroon); letter-spacing:.2px; }

        /* ---- Cards ---- */
        .card{ border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); }
        .card-header{ background:#fff!important; border-bottom:1px solid var(--line); font-weight:700; color:var(--maroon); border-radius:var(--radius) var(--radius) 0 0!important; }

        /* ---- Buttons ---- */
        .btn{ border-radius:9px; font-weight:600; }
        .btn-primary,.btn-dark{ background:var(--maroon); border-color:var(--maroon); }
        .btn-primary:hover,.btn-dark:hover{ background:var(--maroon-dk); border-color:var(--maroon-dk); }
        .btn-outline-primary{ color:var(--maroon); border-color:var(--maroon); }
        .btn-outline-primary:hover{ background:var(--maroon); border-color:var(--maroon); }
        .btn-purple{ background:#6f42c1; color:#fff; }
        .btn-purple:hover{ background:#5a34a3; color:#fff; }

        /* ---- Tables ---- */
        .table thead th{ background:var(--pink-soft); color:var(--maroon); border-bottom:0; font-size:.82rem; text-transform:uppercase; letter-spacing:.4px; }
        .table td{ vertical-align:middle; }
        .table > :not(caption) > * > *{ padding:.7rem .8rem; }

        /* ---- Badges ---- */
        .badge{ font-weight:600; padding:.4em .7em; border-radius:7px; }
        .doc-badge-quotation{ background:#eaf1ff; color:#1e5eff; }
        .doc-badge-invoice{ background:#e5f6ec; color:#177245; }
        .doc-badge-delivery_order{ background:#efe8fb; color:#6f42c1; }

        /* ---- Stat cards (dashboard) ---- */
        .stat{ display:flex; align-items:center; gap:.9rem; padding:1.1rem 1.2rem; }
        .stat .ic{ width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; flex:0 0 auto; }
        .stat .lbl{ color:var(--muted); font-size:.8rem; text-transform:uppercase; letter-spacing:.5px; }
        .stat .val{ font-size:1.7rem; font-weight:800; line-height:1; color:var(--ink); }
        .ic-q{ background:#eaf1ff; color:#1e5eff; } .ic-i{ background:#e5f6ec; color:#177245; }
        .ic-d{ background:#efe8fb; color:#6f42c1; } .ic-u{ background:#ffeaf0; color:var(--maroon); }

        .alert{ border-radius:11px; border:0; }
        .alert-success{ background:#e5f6ec; color:#177245; }
        a{ color:var(--maroon); }

        /* ---- Responsive ---- */
        html,body{ overflow-x:hidden; }
        .container{ max-width:1140px; }
        img{ max-width:100%; height:auto; }
        .table-responsive{ -webkit-overflow-scrolling:touch; }

        @media (max-width:575.98px){
            .page-head h3{ font-size:1.4rem; }
            .stat{ padding:.85rem .9rem; gap:.6rem; }
            .stat .ic{ width:38px; height:38px; font-size:1.05rem; }
            .stat .val{ font-size:1.3rem; }
            .stat .lbl{ font-size:.68rem; }

            /* Turn wide tables into stacked cards */
            .table-stack thead{ display:none; }
            .table-stack tbody tr{
                display:block; background:#fff; border:1px solid var(--line);
                border-radius:12px; box-shadow:var(--shadow); padding:.35rem .85rem; margin-bottom:.7rem;
            }
            .table-stack tbody td{
                display:flex; justify-content:space-between; align-items:center; gap:1rem;
                border:0!important; padding:.4rem 0!important; text-align:right;
            }
            .table-stack tbody td::before{
                content:attr(data-label); font-weight:700; color:var(--maroon);
                text-transform:uppercase; font-size:.68rem; letter-spacing:.4px; text-align:left; flex:0 0 auto;
            }
            .table-stack tbody td[data-label=""]::before{ content:none; }
            .table-stack tbody td.stack-actions{ justify-content:flex-end; padding-top:.5rem!important; border-top:1px solid var(--line)!important; }
        }
    </style>
    @stack('head')
</head>
<body>
@auth
@php $t = request('type'); @endphp
<nav class="navbar navbar-expand-lg navbar-dark appbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <span class="logo-badge">GK</span> {{ config('app.name') }}
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('documents.*') && $t=='quotation' ? 'active' : '' }}" href="{{ route('documents.index', ['type' => 'quotation']) }}"><i class="bi bi-file-earmark-text"></i> Quotation</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('documents.*') && $t=='invoice' ? 'active' : '' }}" href="{{ route('documents.index', ['type' => 'invoice']) }}"><i class="bi bi-receipt"></i> Invoice</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('documents.*') && $t=='delivery_order' ? 'active' : '' }}" href="{{ route('documents.index', ['type' => 'delivery_order']) }}"><i class="bi bi-truck"></i> Delivery Order</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}"><i class="bi bi-gear-fill"></i> Settings</a></li>
            </ul>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-logout btn-sm" type="submit"><i class="bi bi-box-arrow-right"></i> Log Out</button>
            </form>
        </div>
    </div>
</nav>
@endauth

<main class="container py-4">
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
