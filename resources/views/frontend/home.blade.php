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
    @media (max-width: 767px) {
        .hero-slider, .hero-slider .carousel-item, .hero-slider .carousel-item img { height: 420px; }
        .hero-slider .carousel-caption h2 { font-size: 2rem; }
    }
</style>
@endpush

@section('content')
{{-- Hero Slider --}}
<div id="heroCarousel" class="carousel slide hero-slider mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1600&h=600&fit=crop" class="d-block w-100" alt="Đồng hồ nam">
            <div class="carousel-caption">
                <span class="eyebrow">Bộ sưu tập 2025</span>
                <h2>Đồng Hồ Nam<br>Sang Trọng & Đẳng Cấp</h2>
                <p>Giảm đến 30% cho dòng sản phẩm mới</p>
                <a href="{{ route('frontend.sanpham') }}" class="btn btn-gold btn-lg px-4">Mua ngay <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=1600&h=600&fit=crop" class="d-block w-100" alt="Đồng hồ nữ">
            <div class="carousel-caption">
                <span class="eyebrow">Tinh tế & Quyến rũ</span>
                <h2>Đồng Hồ Nữ<br>Duyên Dáng Mỗi Khoảnh Khắc</h2>
                <p>Sự tinh tế trong từng chi tiết</p>
                <a href="{{ route('frontend.sanpham') }}" class="btn btn-gold btn-lg px-4">Mua ngay <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1548135170-77b9e51226a9?w=1600&h=600&fit=crop" class="d-block w-100" alt="Đồng hồ thể thao">
            <div class="carousel-caption">
                <span class="eyebrow">Chinh phục mọi thách thức</span>
                <h2>Đồng Hồ Thể Thao<br>Bền Bỉ & Mạnh Mẽ</h2>
                <p>Đồng hành cùng mọi hành trình</p>
                <a href="{{ route('frontend.sanpham') }}" class="btn btn-gold btn-lg px-4">Mua ngay <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>

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