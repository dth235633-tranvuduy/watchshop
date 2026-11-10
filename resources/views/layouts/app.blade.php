<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Watch Shop')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ws-gold: #c9a227;
            --ws-gold-light: #e3c766;
            --ws-black: #0d0d0d;
            --ws-cream: #faf8f3;
            --ws-line: #e6e2d8;
            --ws-ink: #222;
            --ws-muted: #7a7a7a;
        }

        * { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--ws-cream);
            color: var(--ws-ink);
            padding-top: 72px;
        }

        ::-webkit-scrollbar { width: 9px; height: 9px; }
        ::-webkit-scrollbar-track { background: var(--ws-cream); }
        ::-webkit-scrollbar-thumb { background: #d8d2c4; border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--ws-gold); }

        h1, h2, h3, h4, .font-display { font-family: 'Playfair Display', serif; }

        /* ---------- Admin navbar ---------- */
        .ws-admin-nav {
            background: var(--ws-black);
            box-shadow: 0 4px 18px rgba(0,0,0,.22);
            padding: 14px 0;
            border-bottom: 1px solid rgba(201,162,39,.18);
        }
        .ws-admin-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            font-size: 1.45rem;
            color: #fff !important;
            letter-spacing: .3px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }
        .ws-admin-brand .brand-icon {
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(201,162,39,.15);
            display: flex; align-items: center; justify-content: center;
            color: var(--ws-gold); font-size: 1rem;
        }
        .ws-admin-brand .brand-icon i { font-size: 1rem; }
        .ws-admin-brand span { color: var(--ws-gold); font-weight: 700; font-size: .85rem; letter-spacing: .12em; text-transform: uppercase; margin-left: 2px; }

        .ws-admin-nav .nav-link {
            color: #cfcfcf !important;
            font-size: .9rem;
            font-weight: 600;
            padding: 14px 4px !important;
            margin: 0 12px;
            position: relative;
            transition: color .2s ease;
            display: flex;
            align-items: center;
            gap: 7px;
            text-transform: none;
            letter-spacing: 0;
        }
        .ws-admin-nav .nav-link i { font-size: .82rem; opacity: .7; transition: opacity .2s, color .2s; }
        .ws-admin-nav .nav-link::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 4px;
            transform: translateX(-50%) scaleX(0);
            width: 100%;
            height: 2px;
            background: var(--ws-gold);
            transition: transform .25s ease;
            transform-origin: center;
        }
        .ws-admin-nav .nav-link:hover { color: var(--ws-gold-light) !important; }
        .ws-admin-nav .nav-link:hover i { opacity: 1; color: var(--ws-gold); }
        .ws-admin-nav .nav-link:hover::after { transform: translateX(-50%) scaleX(1); }
        .ws-admin-nav .nav-link.active { color: var(--ws-gold-light) !important; }
        .ws-admin-nav .nav-link.active i { opacity: 1; color: var(--ws-gold); }
        .ws-admin-nav .nav-link.active::after { transform: translateX(-50%) scaleX(1); }

        .ws-admin-nav .dropdown-menu {
            border: none;
            border-radius: 14px;
            padding: 8px;
            box-shadow: 0 16px 40px rgba(0,0,0,.18);
            margin-top: 8px;
        }
        .ws-admin-nav .dropdown-item {
            border-radius: 9px;
            padding: 9px 14px;
            font-size: .9rem;
        }
        .ws-admin-nav .dropdown-item:hover { background: var(--ws-cream); color: var(--ws-gold); }

        /* ---------- Shared action buttons ---------- */
        .btn-gold {
            background: linear-gradient(135deg, var(--ws-gold) 0%, #b8911f 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(201,162,39,.28);
            transition: transform .15s, box-shadow .2s;
        }
        .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(201,162,39,.4); color: #fff; }

        .btn-outline-gold {
            border: 1px solid var(--ws-gold);
            color: var(--ws-gold);
            font-weight: 600;
            border-radius: 12px;
            transition: .2s;
        }
        .btn-outline-gold:hover { background: var(--ws-gold); color: #fff; }

        .btn-soft-danger {
            background: rgba(198,40,40,.10);
            border: 1px solid rgba(198,40,40,.22);
            color: #b3261e;
            font-weight: 600;
            border-radius: 12px;
            transition: .2s;
        }
        .btn-soft-danger:hover { background: #b3261e; border-color: #b3261e; color: #fff; }

        /* ---------- Cards & tables ---------- */
        .vip-card {
            border: 1px solid var(--ws-line);
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(13,13,13,.05);
        }
        .vip-card .card-header {
            background: #fff;
            border-bottom: 1px solid var(--ws-line);
            border-radius: 20px 20px 0 0 !important;
            font-weight: 700;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .vip-card .card-header i { color: var(--ws-gold); }

        .table-vip thead th {
            background: var(--ws-cream);
            border-bottom: 1px solid var(--ws-line);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #6f6f6f;
            font-weight: 700;
            padding: 14px 16px;
        }
        .table-vip tbody td { vertical-align: middle; padding: 14px 16px; }
        .table-vip tbody tr:hover { background: #fcfaf5; }

        /* ---------- Utility ---------- */
        .text-gold { color: var(--ws-gold) !important; }
        .bg-gold-soft { background: rgba(201,162,39,.10); color: var(--ws-gold); }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg ws-admin-nav fixed-top">
        <div class="container-xxl">
            <a class="ws-admin-brand" href="{{ route('admin.home') }}">
                <span class="brand-icon"><i class="fas fa-clock"></i></span>
                WatchShop <span>Admin</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-label="Menu">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-5">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.loaisanpham.*') ? 'active' : '' }}" href="{{ route('admin.loaisanpham.danhsach') }}"><i class="fas fa-list"></i>Loại sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.hangsanxuat.*') ? 'active' : '' }}" href="{{ route('admin.hangsanxuat.danhsach') }}"><i class="fas fa-building"></i>Hãng sản xuất</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.sanpham.*') ? 'active' : '' }}" href="{{ route('admin.sanpham.danhsach') }}"><i class="fas fa-tag"></i>Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.tinhtrang.*') ? 'active' : '' }}" href="{{ route('admin.tinhtrang.danhsach') }}"><i class="fas fa-info-circle"></i>Tình trạng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.donhang.*') ? 'active' : '' }}" href="{{ route('admin.donhang.danhsach') }}"><i class="fas fa-shopping-cart"></i>Đơn hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.nguoidung.*') ? 'active' : '' }}" href="{{ route('admin.nguoidung.danhsach') }}"><i class="fas fa-users"></i>Người dùng</a>
                    </li>
                </ul>
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" data-bs-toggle="dropdown" style="color:#fff !important;">
                            <i class="fas fa-user-circle" style="color:var(--ws-gold);"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('frontend.home') }}" target="_blank"><i class="fas fa-globe me-2 text-muted"></i>Xem cửa hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-xxl py-4">
        @if(session('success'))
            <div class="alert alert-success rounded-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>