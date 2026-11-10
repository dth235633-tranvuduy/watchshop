<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Watch Shop - Đồng Hồ Chính Hãng')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    <style>
        :root {
            --ws-gold: #c9a227;
            --ws-gold-light: #e3c766;
            --ws-black: #0d0d0d;
            --ws-charcoal: #1a1a1a;
            --ws-ink: #222;
            --ws-muted: #7a7a7a;
            --ws-line: #e6e2d8;
            --ws-cream: #faf8f3;
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--ws-cream); color: var(--ws-ink); padding-top: 145px; }
        h1, h2, h3, .font-display { font-family: 'Playfair Display', serif; }

        /* Top bar */
        .ws-topbar { background: var(--ws-black); color: #cfcfcf; font-size: .8rem; padding: 7px 0; }
        .ws-topbar a { color: #cfcfcf; text-decoration: none; transition: color .2s; }
        .ws-topbar a:hover { color: var(--ws-gold-light); }
        .ws-topbar .divider { color: #4a4a4a; margin: 0 10px; }

        /* Header */
        .ws-header { background: #fff; border-bottom: 1px solid var(--ws-line); }
        .ws-brand { font-family: 'Playfair Display', serif; font-weight: 800; font-size: 1.7rem; color: var(--ws-black); text-decoration: none; letter-spacing: .5px; }
        .ws-brand i { color: var(--ws-gold); }
        .ws-brand span { color: var(--ws-gold); }
        .ws-search .form-control { border: 1px solid var(--ws-line); border-right: 0; border-radius: 40px 0 0 40px; background: var(--ws-cream); font-size: .9rem; }
        .ws-search .form-control:focus { box-shadow: none; border-color: var(--ws-gold); }
        .ws-search .btn { border-radius: 0 40px 40px 0; background: var(--ws-gold); color: #fff; border: 1px solid var(--ws-gold); padding: 0 20px; }
        .ws-search .btn:hover { background: var(--ws-gold-light); border-color: var(--ws-gold-light); }
        .ws-icon-btn { position: relative; background: none; border: none; color: var(--ws-black); font-size: 1.15rem; padding: 6px 10px; transition: color .2s; text-decoration: none; }
        .ws-icon-btn:hover { color: var(--ws-gold); }
        .cart-badge { position: absolute; top: -2px; right: 0; background: var(--ws-gold); color: #fff; border-radius: 50%; min-width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; padding: 0 4px; }

        /* Nav */
        .ws-nav { background: var(--ws-black); }
        .ws-nav .nav-link {
            color: #e8e8e8;
            font-weight: 600;
            font-size: .88rem;
            text-transform: uppercase;
            letter-spacing: .8px;
            padding: 13px 4px;
            margin: 0 14px;
            position: relative;
            transition: color .2s ease;
        }
        .ws-nav .nav-link::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 6px;
            transform: translateX(-50%) scaleX(0);
            width: 100%;
            height: 2px;
            background: var(--ws-gold);
            transition: transform .25s ease;
            transform-origin: center;
        }
        .ws-nav .nav-link:hover { color: var(--ws-gold-light); }
        .ws-nav .nav-link:hover::after { transform: translateX(-50%) scaleX(1); }
        .ws-nav .nav-link.active { color: var(--ws-gold-light); }
        .ws-nav .nav-link.active::after { transform: translateX(-50%) scaleX(1); }
        .ws-nav .nav-link.dropdown-toggle::after {
            display: inline-block;
            margin-left: 6px;
            vertical-align: middle;
            border-top-color: currentColor;
        }
        .ws-nav .dropdown-menu {
            border-radius: 14px;
            border: 1px solid var(--ws-line);
            box-shadow: 0 16px 40px rgba(0,0,0,.18);
            margin-top: 8px;
            padding: 8px;
            animation: wsDropFade .2s ease;
        }
        @keyframes wsDropFade {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .ws-nav .dropdown-item {
            font-size: .9rem;
            padding: 9px 14px;
            border-radius: 9px;
            transition: background .15s, color .15s, padding-left .15s;
        }
        .ws-nav .dropdown-item:hover {
            background: var(--ws-cream);
            color: var(--ws-gold);
            padding-left: 18px;
        }

        /* Product cards */
        .product-card { border: 1px solid transparent; border-radius: 0; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.06); transition: transform .25s, box-shadow .25s, border-color .25s; }
        .product-card:hover { transform: translateY(-6px); box-shadow: 0 16px 34px rgba(0,0,0,.12); border-color: var(--ws-gold-light); }
        .product-image { width: 100%; height: 240px; object-fit: cover; background: var(--ws-cream); display: block; }
        .product-card .card-body { padding: 16px; }
        .product-card h6 { font-size: .92rem; font-weight: 600; }
        .price { font-weight: 700; color: var(--ws-black); font-size: 1.05rem; }
        .original-price { text-decoration: line-through; color: var(--ws-muted); font-size: .85rem; margin-left: 6px; }
        .badge-discount { background: var(--ws-gold); color: #fff; font-weight: 600; }
        .rating-stars { color: var(--ws-gold); letter-spacing: 1px; }
        .btn-add-cart { background: var(--ws-black); border: 1px solid var(--ws-black); color: #fff; font-size: .85rem; }
        .btn-add-cart:hover { background: var(--ws-gold); border-color: var(--ws-gold); color: #fff; }
        .btn-gold { background: var(--ws-gold); border: 1px solid var(--ws-gold); color: #fff; font-weight: 600; }
        .btn-gold:hover { background: var(--ws-gold-light); border-color: var(--ws-gold-light); color: #fff; }
        .btn-outline-gold { border: 1px solid var(--ws-gold); color: var(--ws-gold); font-weight: 600; }
        .btn-outline-gold:hover { background: var(--ws-gold); color: #fff; }
        .section-title { font-size: 1.75rem; font-weight: 700; margin-bottom: 26px; position: relative; display: inline-block; }
        .section-title::after { content: ''; position: absolute; bottom: -10px; left: 0; width: 54px; height: 3px; background: var(--ws-gold); }
        .brand-card { height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column; border: 1px solid var(--ws-line); background: #fff; transition: .3s; text-decoration: none; color: var(--ws-ink); }
        .brand-card:hover { border-color: var(--ws-gold); transform: translateY(-3px); box-shadow: 0 10px 22px rgba(0,0,0,.08); }
        .brand-card i { font-size: 1.9rem; color: var(--ws-gold); margin-bottom: 6px; }
        .brand-card img { max-height: 44px; max-width: 80%; object-fit: contain; }

        /* Footer */
        footer { background: var(--ws-black); color: #cfcfcf; padding: 60px 0 20px; margin-top: 60px; }
        footer h5 { color: #fff; margin-bottom: 20px; font-family: 'Playfair Display', serif; font-size: 1.15rem; }
        footer a { color: #9d9d9d; text-decoration: none; transition: .2s; }
        footer a:hover { color: var(--ws-gold-light); }
        footer ul.list-unstyled li { margin-bottom: 9px; font-size: .9rem; }
        .pay-methods { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .pay-method { min-width: 52px; height: 34px; border: 1px solid #3a3a3a; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 600; background: #171717; color: #d8d8d8; padding: 0 8px; }
        .offcanvas-header { background: var(--ws-black); color: #fff; }

        @media (max-width: 991px) {
            body { padding-top: 111px; }
            .ws-header { top: 0 !important; }
        }
    </style>
</head>
<body>
    @php
        $loaisanpham = App\Models\LoaiSanPham::all();
        $hangsanxuatMenu = App\Models\HangSanXuat::all();
        $cart = session('cart', []);
        $cartTotal = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
        $cartQuantity = collect($cart)->sum(fn (array $item): int => (int) $item['qty']);
    @endphp

    <!-- Top bar -->
    <div class="ws-topbar fixed-top d-none d-lg-block" style="top: 0; height: 34px;">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-headset me-1"></i> Hotline: <a href="tel:0909123456"><strong>0909 123 456</strong></a>
                <span class="divider">|</span>
                <i class="fas fa-truck-fast me-1"></i> Miễn phí vận chuyển đơn từ 1.000.000 đ
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('frontend.lienhe') }}">Liên hệ</a>
                <span class="divider">|</span>
                <a href="{{ route('frontend.tuyendung') }}">Tuyển dụng</a>
                <span class="divider">|</span>
                <span><i class="fas fa-globe me-1"></i>VN / <strong>VND ₫</strong></span>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="ws-header fixed-top shadow-sm" style="top: 34px;">
        <div class="container py-3">
            <div class="row align-items-center g-2">
                <div class="col-lg-3 col-7">
                    <a class="ws-brand" href="{{ route('frontend.home') }}"><i class="fas fa-clock me-1"></i>Watch<span>Shop</span></a>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <form class="ws-search" method="GET" action="{{ route('frontend.sanpham') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control py-2" placeholder="Tìm kiếm đồng hồ, thương hiệu...">
                            <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-5 d-flex justify-content-end align-items-center">
                    <button class="ws-icon-btn d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch" aria-label="Tìm kiếm"><i class="fas fa-search"></i></button>
                    <a href="{{ route('frontend.giohang') }}" class="ws-icon-btn ms-2" aria-label="Giỏ hàng">
                        <i class="fas fa-shopping-bag"></i><span class="cart-badge" id="cartBadge">{{ $cartQuantity }}</span>
                    </a>
                    @guest
                        <a href="{{ route('login') }}" class="ws-icon-btn ms-2 d-none d-sm-inline" aria-label="Đăng nhập"><i class="fas fa-user"></i></a>
                        <a href="{{ route('register') }}" class="btn btn-gold btn-sm ms-3 d-none d-md-inline">Đăng ký</a>
                    @else
                        <div class="dropdown ms-2">
                            <a class="ws-icon-btn dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="fas fa-user-circle me-1"></i><span class="d-none d-md-inline">{{ Auth::user()->name }}</span></a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="{{ route('user.home') }}"><i class="fas fa-tachometer-alt me-2"></i>Trang cá nhân</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.donhang') }}"><i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi</a></li>
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.home') }}"><i class="fas fa-cog me-2"></i>Quản trị</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button></form></li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
        <!-- Nav row -->
        <nav class="ws-nav">
            <div class="container d-flex justify-content-between align-items-center">
                <ul class="nav align-items-center">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}" href="{{ route('frontend.home') }}">Trang chủ</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('frontend.sanpham*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Danh mục</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('frontend.sanpham') }}"><i class="fas fa-layer-group me-2" style="color:var(--ws-gold);"></i>Tất cả sản phẩm</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach($loaisanpham as $lsp)
                                <li><a class="dropdown-item" href="{{ route('frontend.sanpham.phanloai', $lsp->tenloai_slug) }}"><i class="fas fa-angle-right me-2" style="color:var(--ws-muted);"></i>{{ $lsp->tenloai }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Thương hiệu</a>
                        <ul class="dropdown-menu">
                            @foreach($hangsanxuatMenu as $hsx)
                                <li><a class="dropdown-item" href="{{ route('frontend.sanpham', ['hangsanxuat_id' => $hsx->id]) }}"><i class="fas fa-angle-right me-2" style="color:var(--ws-muted);"></i>{{ $hsx->tenhang }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.sanpham') && !request()->route('tenloai_slug') ? 'active' : '' }}" href="{{ route('frontend.sanpham') }}">Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.tuyendung') ? 'active' : '' }}" href="{{ route('frontend.tuyendung') }}">Tuyển dụng</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('frontend.lienhe') ? 'active' : '' }}" href="{{ route('frontend.lienhe') }}">Liên hệ</a></li>
                </ul>
                <div class="d-none d-lg-flex align-items-center gap-3" style="color:var(--ws-gold-light); font-size:.82rem; letter-spacing:.5px;">
                    <i class="fas fa-gem"></i>
                    <span style="color:#b8b8b8;">Hàng chính hãng · Bảo hành 5 năm</span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Quick search offcanvas (mobile) -->
    <div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasSearch" style="height: auto;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="fas fa-search me-2"></i>Tìm kiếm</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="{{ route('frontend.sanpham') }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." autofocus>
                    <button class="btn btn-gold" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mini cart offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" style="width: 400px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="fas fa-shopping-bag me-2"></i>Giỏ hàng ({{ $cartQuantity }})</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @forelse($cart as $rowId => $item)
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div class="me-2">
                        <div class="fw-semibold" style="font-size:.9rem;">{{ $item['name'] }}</div>
                        <small class="text-muted">{{ $item['qty'] }} x {{ number_format($item['price'], 0, ',', '.') }} đ</small>
                    </div>
                    <a href="{{ route('frontend.giohang.xoa', $rowId) }}" class="text-danger" aria-label="Xóa"><i class="fas fa-trash"></i></a>
                </div>
            @empty
                <p class="text-center text-muted py-4"><i class="fas fa-box-open fa-2x mb-2 d-block"></i>Giỏ hàng đang trống</p>
            @endforelse
        </div>
        <div class="border-top p-3 bg-white">
            <div class="d-flex justify-content-between mb-3"><span><strong>Tổng tiền:</strong></span><span class="fw-bold" style="color:var(--ws-gold);">{{ number_format($cartTotal, 0, ',', '.') }} đ</span></div>
            @if($cartQuantity > 0)
                <a href="{{ route('user.dathang') }}" class="btn btn-gold w-100 mb-2">Tiến hành thanh toán</a>
            @endif
            <a href="{{ route('frontend.giohang') }}" class="btn btn-outline-dark w-100">Xem giỏ hàng</a>
        </div>
    </div>

    <!-- Main content -->
    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <h5><i class="fas fa-clock me-2" style="color:var(--ws-gold);"></i>Watch Shop</h5>
                    <p class="small">Website bán đồng hồ chính hãng, chất lượng cao, giá tốt nhất thị trường. Đồng hành cùng bạn trên mọi hành trình.</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Hỗ trợ khách hàng</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('frontend.lienhe') }}"><i class="fas fa-headset me-2"></i>Liên hệ hỗ trợ</a></li>
                        <li><a href="#"><i class="fas fa-shipping-fast me-2"></i>Chính sách giao hàng</a></li>
                        <li><a href="#"><i class="fas fa-undo me-2"></i>Chính sách đổi trả</a></li>
                        <li><a href="{{ route('frontend.tuyendung') }}"><i class="fas fa-briefcase me-2"></i>Tuyển dụng</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Phương thức thanh toán</h5>
                    <div class="pay-methods">
                        <span class="pay-method">Tiền mặt</span><span class="pay-method">Visa</span><span class="pay-method">MoMo</span><span class="pay-method">ZaloPay</span><span class="pay-method">ATM</span>
                    </div>
                    <div class="mt-3">
                        <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Đăng ký nhận tin</h5>
                    <form class="d-flex"><input type="email" class="form-control form-control-sm" placeholder="Email của bạn"><button class="btn btn-gold btn-sm ms-2">Gửi</button></form>
                    <p class="small mt-3">Nhận thông tin khuyến mãi và sản phẩm mới.</p>
                </div>
            </div>
            <hr class="my-4" style="border-color:#2a2a2a;">
            <div class="text-center small">&copy; {{ date('Y') }} Watch Shop. All rights reserved.</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>