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
            font-size: 16px;
            line-height: 1.65;
        }

        ::-webkit-scrollbar { width: 9px; height: 9px; }
        ::-webkit-scrollbar-track { background: var(--ws-cream); }
        ::-webkit-scrollbar-thumb { background: #d8d2c4; border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--ws-gold); }

        h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Inter', system-ui, sans-serif; }
        h1 { font-size: 2rem; font-weight: 800; line-height: 1.25; }
        h2 { font-size: 1.65rem; font-weight: 800; line-height: 1.3; }
        h3 { font-size: 1.35rem; font-weight: 750; line-height: 1.35; }
        h4, h5, h6 { font-weight: 700; line-height: 1.4; }
        p, td, th, label, input, select, textarea, button, a { line-height: 1.55; }
        .table { font-size: .95rem; color: var(--ws-ink); }
        .table td, .table th { padding-top: .85rem; padding-bottom: .85rem; }
        .form-control, .form-select { font-size: 1rem; }
        .btn { font-size: .95rem; font-weight: 700; }
        .text-muted { color: #686868 !important; }

        /* ---------- Admin navbar ---------- */
        .ws-admin-nav {
            background: var(--ws-black);
            box-shadow: 0 4px 18px rgba(0,0,0,.22);
            padding: 14px 0;
            border-bottom: 1px solid rgba(201,162,39,.18);
        }
        .ws-admin-brand {
            font-family: 'Inter', system-ui, sans-serif;
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
        .status-update-form { display: none !important; }

        /* ---------- Desktop sidebar ---------- */
        @media (min-width: 992px) {
            body {
                padding-top: 0;
            }

            .ws-admin-nav {
                width: 252px;
                height: 100vh;
                padding: 24px 16px;
                align-items: stretch;
                overflow: hidden;
            }

            .ws-admin-nav > .container-xxl {
                width: 100%;
                height: 100%;
                padding: 0;
                margin: 0;
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }

            .ws-admin-brand {
                margin: 0 8px 30px;
                font-size: 1.25rem;
            }

            .ws-admin-brand .brand-icon { width: 36px; height: 36px; }

            .ws-admin-nav .navbar-collapse {
                display: flex !important;
                flex: 1;
                flex-direction: column;
                align-items: stretch;
                min-height: 0;
            }

            .ws-admin-nav .navbar-nav {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: stretch;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .ws-admin-nav .navbar-nav:first-child { gap: 5px; }

            .sidebar-menu {
                flex: 1;
                min-height: 0;
                overflow-y: auto;
                padding-right: 4px;
            }

            .sidebar-menu::-webkit-scrollbar { width: 5px; }
            .sidebar-menu::-webkit-scrollbar-thumb {
                background: rgba(201,162,39,.45);
                border-radius: 999px;
            }

            .sidebar-section { margin-bottom: 10px; }

            .sidebar-section-title {
                padding: 8px 14px 5px;
                color: #9b9b9b;
                font-size: .69rem;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .sidebar-section .navbar-nav { gap: 2px; }

            .ws-admin-nav .nav-link {
                margin: 0;
                padding: 12px 14px !important;
                border-radius: 12px;
                justify-content: flex-start;
            }

            .ws-admin-nav .nav-link::after {
                left: 0;
                bottom: 0;
                width: 3px;
                height: 100%;
                border-radius: 0 4px 4px 0;
                transform: scaleY(0);
                transform-origin: center;
            }

            .ws-admin-nav .nav-link:hover::after,
            .ws-admin-nav .nav-link.active::after {
                transform: scaleY(1);
            }

            .ws-admin-nav .nav-link:hover,
            .ws-admin-nav .nav-link.active { background: rgba(201,162,39,.12); }

            .ws-admin-nav .admin-account {
                flex: 0 0 auto;
                padding-top: 18px;
                border-top: 1px solid rgba(255,255,255,.10);
                margin-top: 8px;
            }

            .ws-admin-nav .admin-account .nav-link {
                padding: 10px 8px !important;
            }

            .ws-admin-nav .admin-account .dropdown-menu {
                top: auto;
                bottom: 100%;
                right: 0;
                left: auto;
                margin-top: 0;
                margin-bottom: 8px;
            }

            main.container-xxl {
                max-width: none;
                margin-left: 252px;
                width: calc(100% - 252px);
                padding-left: clamp(24px, 4vw, 64px) !important;
                padding-right: clamp(24px, 4vw, 64px) !important;
            }
        }

        @media (max-width: 991.98px) {
            .ws-admin-nav .navbar-nav { gap: 0; }
        }

        .admin-store-link {
            position: fixed;
            bottom: 24px;
            right: 32px;
            z-index: 1031;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--ws-ink);
            text-decoration: none;
            font-size: .88rem;
            font-weight: 600;
            padding: 9px 14px;
            border: 1px solid var(--ws-line);
            border-radius: 999px;
            background: rgba(255,255,255,.92);
            box-shadow: 0 5px 16px rgba(13,13,13,.08);
            transition: .2s ease;
        }

        .admin-store-link i { color: var(--ws-gold); }
        .admin-store-link:hover { color: var(--ws-gold); border-color: var(--ws-gold); transform: translateY(-1px); }

        @media (max-width: 991.98px) {
            .admin-store-link { display: none; }
        }
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
                <div class="sidebar-menu">
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Quản trị dữ liệu</div>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.loaisanpham.*') ? 'active' : '' }}" href="{{ route('admin.loaisanpham.danhsach') }}"><i class="fas fa-copyright"></i>Thương hiệu</a>
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
                        </ul>
                    </div>
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Bán hàng</div>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.donhang.*') ? 'active' : '' }}" href="{{ route('admin.donhang.danhsach') }}"><i class="fas fa-shopping-cart"></i>Đơn hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.kho.*') ? 'active' : '' }}" href="{{ route('admin.kho.danhsach') }}"><i class="fas fa-boxes-stacked"></i>Kho hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.nguoidung.*') ? 'active' : '' }}" href="{{ route('admin.nguoidung.danhsach') }}"><i class="fas fa-users"></i>Người dùng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.lichsu.*') ? 'active' : '' }}" href="{{ route('admin.lichsu.danhsach') }}"><i class="fas fa-clock-rotate-left"></i>Lịch sử</a>
                            </li>
                        </ul>
                    </div>
                    <div class="sidebar-section">
                        <div class="sidebar-section-title">Marketing</div>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.voucher.*') ? 'active' : '' }}" href="{{ route('admin.voucher.danhsach') }}"><i class="fas fa-ticket"></i>Voucher</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.danhgia.*') ? 'active' : '' }}" href="{{ route('admin.danhgia.danhsach') }}"><i class="fas fa-star"></i>Đánh giá</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.banner.*') ? 'active' : '' }}" href="{{ route('admin.banner.danhsach') }}"><i class="fas fa-images"></i>Banner</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <ul class="navbar-nav admin-account">
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

    <a class="admin-store-link" href="{{ route('frontend.home') }}" aria-label="Xem cửa hàng">
        <i class="fas fa-store"></i>Xem cửa hàng
    </a>

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
