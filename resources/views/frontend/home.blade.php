@extends('layouts.frontend')

@section('title', 'Trang chủ - Watch Shop')

@push('styles')
<style>
    .hero-slider, .hero-slider .carousel-item { height: 540px; }
    .hero-slider .carousel-item img { height: 540px; object-fit: cover; filter: brightness(.62); }
    .hero-slider .carousel-caption { bottom: 0; top: 0; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; text-align: left; padding-left: 8%; }
    .hero-slider .carousel-caption .eyebrow { color: var(--ws-gold-light); text-transform: uppercase; letter-spacing: 3px; font-size: .8rem; font-weight: 600; margin-bottom: 10px; }
    .hero-slider .carousel-caption h2 { font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 800; color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,.5); }
    .hero-slider .carousel-caption p { font-size: 1.1rem; color: #eee; margin-bottom: 22px; }
    .promo-banner { background: linear-gradient(120deg, var(--ws-black) 0%, #2b2413 100%); color: #fff; padding: 40px; border: 1px solid var(--ws-gold); }
    .promo-banner .big { font-family: 'Playfair Display', serif; font-size: 2.2rem; font-weight: 800; color: var(--ws-gold-light); }
    .service-card { border: 1px solid var(--ws-line); background: #fff; transition: transform .2s, box-shadow .2s; border-radius: 0; }
    .service-card:hover { transform: translateY(-4px); box-shadow: 0 12px 26px rgba(0,0,0,.1); }
    .service-card i { color: var(--ws-gold); }
    .product-actions { opacity: 0; transform: translateY(6px); transition: opacity .2s, transform .2s; }
    .product-card:hover .product-actions { opacity: 1; transform: translateY(0); }
    /* Dải cam kết: chữ gõ, huy hiệu xoay, chữ chạy ngang */
    .trust-band-head { background: linear-gradient(120deg, #0c0c0c 0%, #2b2413 100%); clip-path: polygon(0 0, 100% 0, 100% 82%, 0 100%); padding: 54px 0 96px; text-align: center; }
    .trust-eyebrow { color: var(--ws-gold-light); letter-spacing: 4px; font-size: .78rem; font-weight: 700; text-transform: uppercase; }
    .trust-type { color: #fff; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; font-size: clamp(1.25rem, 3.4vw, 2.3rem); margin: 12px 0 0; min-height: 1.4em; }
    .type-cursor { color: var(--ws-gold); font-weight: 300; animation: trust-blink 1s steps(1) infinite; }
    @keyframes trust-blink { 50% { opacity: 0; } }
    .trust-items { margin-top: -64px; position: relative; z-index: 2; }
    .trust-item { text-align: center; padding: 0 12px; opacity: 0; transform: translateY(28px); transition: opacity .7s ease, transform .7s ease; }
    .trust-item.in-view { opacity: 1; transform: none; }
    .trust-medal { position: relative; width: 128px; height: 128px; margin: 0 auto 18px; border-radius: 50%; background: #101010; color: var(--ws-gold); display: flex; align-items: center; justify-content: center; font-size: 2.6rem; box-shadow: 0 14px 30px rgba(0,0,0,.25); }
    .trust-medal.gold { background: linear-gradient(140deg, #eecf7a, #b8860b); color: #191204; font-family: 'Playfair Display', serif; font-weight: 800; font-size: 2.1rem; }
    .trust-ring { position: absolute; inset: -9px; border-radius: 50%; background: conic-gradient(from 0deg, transparent 0 42%, var(--ws-gold) 50%, transparent 58% 100%); -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 3px)); mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 3px)); animation: trust-spin 12s linear infinite; }
    @keyframes trust-spin { to { transform: rotate(360deg); } }
    .trust-item h4 { font-weight: 800; text-transform: uppercase; font-size: 1.05rem; margin-bottom: 2px; }
    .trust-item p { text-transform: uppercase; color: #555; letter-spacing: .5px; margin: 0; }
    .trust-marquee { overflow: hidden; border-top: 1px solid var(--ws-line); border-bottom: 1px solid var(--ws-line); background: #fff; padding: 12px 0; margin-top: 42px; }
    .trust-marquee-track { display: flex; width: max-content; animation: trust-marquee 28s linear infinite; }
    .trust-marquee:hover .trust-marquee-track { animation-play-state: paused; }
    .trust-marquee-track .mq-item { font-weight: 700; letter-spacing: 2px; text-transform: uppercase; white-space: nowrap; color: #222; margin-right: 1.6rem; }
    .trust-marquee-track .sep { color: var(--ws-gold); margin-right: 1.6rem; }
    @keyframes trust-marquee { to { transform: translateX(-50%); } }
    @media (prefers-reduced-motion: reduce) {
        .trust-ring, .type-cursor, .trust-marquee-track { animation: none !important; }
        .trust-item { opacity: 1; transform: none; transition: none; }
    }
    @media (max-width: 767px) {
        .hero-slider, .hero-slider .carousel-item, .hero-slider .carousel-item img { height: 420px; }
        .hero-slider .carousel-caption h2 { font-size: 2rem; }
    }
</style>
@endpush

@section('content')
{{-- Hero Slider --}}
{{-- Banner trang chủ: toàn bộ slide lấy từ trang quản trị Banner, không còn slide cứng --}}
@if($banners->isNotEmpty())
<div id="heroCarousel" class="carousel slide hero-slider mb-5" data-bs-ride="carousel">
    @if($banners->count() > 1)
        <div class="carousel-indicators">
            @foreach($banners as $index => $banner)<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></button>@endforeach
        </div>
    @endif
    <div class="carousel-inner">
        @foreach($banners as $index => $banner)
            @php $bannerImage = str_starts_with($banner->hinh_anh, 'http') ? $banner->hinh_anh : route('frontend.hinhanh', ['path' => $banner->hinh_anh]); @endphp
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ $bannerImage }}" class="d-block w-100" alt="{{ $banner->tieu_de }}">
                <div class="carousel-caption">
                    <span class="eyebrow">{{ $banner->tieu_de_phu ?: 'Watch Shop' }}</span>
                    <h2>{{ $banner->tieu_de }}</h2>
                    @if($banner->mo_ta)<p>{{ $banner->mo_ta }}</p>@endif
                    @if($banner->duong_dan)<a href="{{ $banner->duong_dan }}" class="btn btn-gold btn-lg px-4">Mua ngay <i class="fas fa-arrow-right ms-1"></i></a>@endif
                </div>
            </div>
        @endforeach
    </div>
    @if($banners->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    @endif
</div>
@endif

{{-- Dải cam kết: chữ gõ typewriter, huy hiệu xoay, chữ chạy ngang --}}
<section class="mb-5">
    <div class="trust-band-head">
        <div class="container">
            <div class="trust-eyebrow">Watch Shop — Cam kết vàng</div>
            <h2 class="trust-type"><span id="typewriter"></span><span class="type-cursor">|</span></h2>
        </div>
    </div>
    <div class="container trust-items">
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 trust-item">
                <div class="trust-medal"><span class="trust-ring"></span><i class="fas fa-shield-halved"></i></div>
                <h4>Bảo hành chính hãng</h4>
                <p>Lên đến 5 năm toàn quốc</p>
            </div>
            <div class="col-md-4 trust-item" style="transition-delay:.15s">
                <div class="trust-medal gold"><span class="trust-ring"></span>WS</div>
                <h4>Thương hiệu uy tín</h4>
                <p>Được khách hàng tin chọn</p>
            </div>
            <div class="col-md-4 trust-item" style="transition-delay:.3s">
                <div class="trust-medal"><span class="trust-ring"></span><i class="fas fa-hand-holding-dollar"></i></div>
                <h4>Đền 20 lần nếu bán</h4>
                <p>Hàng fake</p>
            </div>
        </div>
    </div>
    <div class="trust-marquee" aria-hidden="true">
        <div class="trust-marquee-track">
            @for($lan = 0; $lan < 2; $lan++)
                @foreach(['Hàng chính hãng 100%', 'Bảo hành 5 năm', 'Đền 20 lần nếu bán hàng fake', 'Miễn phí vận chuyển đơn từ 1 triệu', 'Đổi trả trong 7 ngày'] as $camKet)
                    <span class="mq-item">{{ $camKet }}</span><span class="sep">✦</span>
                @endforeach
            @endfor
        </div>
    </div>
</section>

{{-- Promo banner --}}
<div class="container mb-5">
    <div class="promo-banner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="big">Đại Tiệc Thời Gian</div>
            <div class="text-uppercase" style="letter-spacing:2px;color:#ddd;">Giảm giá đến 50% — số lượng có hạn</div>
        </div>
        <a href="{{ route('frontend.sanpham') }}" class="btn btn-gold btn-lg">Khám phá ngay</a>
    </div>
</div>

{{-- Brands --}}
<section class="container mb-5">
    <h3 class="section-title text-center d-block">Thương Hiệu Nổi Tiếng</h3>
    <div class="row justify-content-center g-3">
        @foreach($hangsanxuat as $hsx)
            <div class="col-6 col-md-3 col-lg-2">
                <a href="{{ route('frontend.sanpham', ['hangsanxuat_id' => $hsx->id]) }}" class="brand-card">
                    @if(!empty($hsx->hinhanh))
                        @php $brandImg = str_starts_with($hsx->hinhanh, 'http') ? $hsx->hinhanh : route('frontend.hinhanh', ['path' => $hsx->hinhanh]); @endphp
                        <img src="{{ $brandImg }}" alt="{{ $hsx->tenhang }}">
                    @else
                        <i class="fas fa-gem"></i>
                    @endif
                    <span class="small fw-semibold mt-1">{{ $hsx->tenhang }}</span>
                </a>
            </div>
        @endforeach
    </div>
</section>

{{-- Featured products --}}
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <h3 class="section-title mb-0">Sản Phẩm Nổi Bật</h3>
        <a href="{{ route('frontend.sanpham') }}" class="btn btn-outline-gold btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($sanpham as $sp)
            @php $salePrice = $sp->dongia * (100 - $sp->giamgia) / 100; @endphp
            <div class="col">
                <div class="card product-card h-100">
                    <div class="position-relative overflow-hidden">
                        @php
                            $imgUrl = !empty($sp->hinhanh)
                                ? (str_starts_with($sp->hinhanh, 'http') ? $sp->hinhanh : route('frontend.hinhanh', ['path' => $sp->hinhanh]))
                                : route('frontend.hinhanh', ['path' => '__missing__']);
                        @endphp
                        <img src="{{ $imgUrl }}"
                             class="card-img-top product-image"
                             alt="{{ $sp->tensanpham }}"
                             loading="lazy"
                             onerror="this.onerror=null;this.src='{{ route('frontend.hinhanh', ['path' => '__missing__']) }}';">
                        @if($sp->giamgia > 0)
                            <span class="badge badge-discount position-absolute top-0 start-0 m-2">-{{ $sp->giamgia }}%</span>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-1">{{ optional($sp->HangSanXuat)->tenhang }}</small>
                        <h6 class="card-title text-truncate mb-2">{{ $sp->tensanpham }}</h6>
                        <div class="rating-stars small mb-2">
                            @for($star = 1; $star <= 5; $star++)
                                <i class="fas fa-star{{ $star <= round($sp->danhgia) ? '' : ' text-muted' }}"></i>
                            @endfor
                        </div>
                        <div class="mt-auto">
                            <div class="mb-3">
                                <span class="price">{{ number_format($salePrice, 0, ',', '.') }} đ</span>
                                @if($sp->giamgia > 0)
                                    <span class="original-price">{{ number_format($sp->dongia, 0, ',', '.') }} đ</span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('frontend.sanpham.chitiet', ['tenloai_slug' => $sp->LoaiSanPham->tenloai_slug ?? 'san-pham', 'tensanpham_slug' => $sp->tensanpham_slug]) }}" class="btn btn-outline-gold btn-sm flex-grow-1"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('frontend.giohang.them', $sp->tensanpham_slug) }}" class="btn btn-add-cart btn-sm flex-grow-1"><i class="fas fa-cart-plus me-1"></i>Thêm</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">Chưa có sản phẩm nào</div>
        @endforelse
    </div>
</section>

{{-- Service commitments --}}
<section class="container pb-4">
    <div class="row g-3">
        @foreach([
            ['icon' => 'fa-truck-fast', 'title' => 'Miễn phí vận chuyển', 'text' => 'Cho đơn hàng từ 1.000.000 đ'],
            ['icon' => 'fa-shield-halved', 'title' => 'Bảo hành chính hãng 5 năm', 'text' => 'An tâm sử dụng sản phẩm'],
            ['icon' => 'fa-rotate-left', 'title' => 'Đổi trả trong 7 ngày', 'text' => 'Hỗ trợ nhanh chóng, minh bạch'],
            ['icon' => 'fa-headset', 'title' => 'Hỗ trợ 24/7', 'text' => 'Tư vấn viên luôn sẵn sàng'],
        ] as $service)
            <div class="col-6 col-md-3">
                <div class="service-card h-100 p-3 text-center">
                    <i class="fas {{ $service['icon'] }} fa-2x mb-2"></i>
                    <h6 class="mb-1">{{ $service['title'] }}</h6>
                    <p class="mb-0 text-muted small">{{ $service['text'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    var el = document.getElementById('typewriter');
    if (el) {
        var phrases = ['Hàng chính hãng 100%', 'Bảo hành chính hãng 5 năm', 'Đền 20 lần nếu bán hàng fake', 'Miễn phí vận chuyển đơn từ 1 triệu'];
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (phrases.length > 0 && !reduceMotion) {
            var phraseIndex = 0, charIndex = 0, deleting = false;
            (function tick() {
                var current = phrases[phraseIndex];
                el.textContent = current.substring(0, charIndex);
                var delay = deleting ? 38 : 78;
                if (!deleting && charIndex === current.length) {
                    delay = 2100;
                    deleting = true;
                } else if (deleting && charIndex === 0) {
                    deleting = false;
                    phraseIndex = (phraseIndex + 1) % phrases.length;
                    delay = 350;
                }
                charIndex += deleting ? -1 : 1;
                setTimeout(tick, delay);
            })();
        } else if (phrases.length > 0) {
            el.textContent = phrases[0];
        }
    }

    var items = document.querySelectorAll('.trust-item');
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.25 });
        items.forEach(function (item) { observer.observe(item); });
    } else {
        items.forEach(function (item) { item.classList.add('in-view'); });
    }
})();
</script>
@endpush
