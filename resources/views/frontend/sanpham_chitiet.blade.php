@extends('layouts.frontend')

@section('title', $sanpham->tensanpham . ' - Watch Shop')

@push('styles')
<style>
    .product-gallery { border: 1px solid var(--ws-line); background: #fff; padding: 16px; }
    .product-gallery img { width: 100%; aspect-ratio: 1/1; object-fit: cover; }
    .spec-table th { width: 40%; background: var(--ws-cream); font-weight: 600; }
    .qty-input { max-width: 90px; text-align: center; }
</style>
@endpush

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}" class="text-decoration-none">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('frontend.sanpham') }}" class="text-decoration-none">Sản phẩm</a></li>
        @if($sanpham->LoaiSanPham)
            <li class="breadcrumb-item"><a href="{{ route('frontend.sanpham.phanloai', $sanpham->LoaiSanPham->tenloai_slug) }}" class="text-decoration-none">{{ $sanpham->LoaiSanPham->tenloai }}</a></li>
        @endif
        <li class="breadcrumb-item active" aria-current="page">{{ $sanpham->tensanpham }}</li>
    </ol>
</nav>

<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="product-gallery">
            @php
                $imgUrl = !empty($sanpham->hinhanh)
                    ? (str_starts_with($sanpham->hinhanh, 'http') ? $sanpham->hinhanh : route('frontend.hinhanh', ['path' => $sanpham->hinhanh]))
                    : route('frontend.hinhanh', ['path' => '__missing__']);
            @endphp
            <img src="{{ $imgUrl }}"
                 alt="{{ $sanpham->tensanpham }}"
                 onerror="this.onerror=null;this.src='{{ route('frontend.hinhanh', ['path' => '__missing__']) }}';">
        </div>
    </div>

    <div class="col-lg-6">
        @if($sanpham->HangSanXuat)
            <span class="badge bg-dark mb-2">{{ $sanpham->HangSanXuat->tenhang }}</span>
        @endif
        <h1 class="h2 mb-2">{{ $sanpham->tensanpham }}</h1>
        <div class="rating-stars mb-3">
            @for($star = 1; $star <= 5; $star++)
                <i class="fas fa-star{{ $star <= round($sanpham->danhgia) ? '' : ' text-muted' }}"></i>
            @endfor
            <span class="text-muted ms-2 small">{{ number_format($sanpham->danhgia, 1) }}/5</span>
        </div>

        @php $salePrice = $sanpham->dongia * (100 - $sanpham->giamgia) / 100; @endphp
        <div class="mb-3">
            <span class="price fs-3">{{ number_format($salePrice, 0, ',', '.') }} đ</span>
            @if($sanpham->giamgia > 0)
                <span class="original-price fs-6">{{ number_format($sanpham->dongia, 0, ',', '.') }} đ</span>
                <span class="badge badge-discount ms-2">-{{ $sanpham->giamgia }}%</span>
            @endif
        </div>

        <p class="text-muted">{{ $sanpham->motasanpham ?? 'Chưa có mô tả chi tiết.' }}</p>

        <table class="table table-sm table-bordered spec-table mb-4">
            <tbody>
                <tr><th>Chất liệu dây</th><td>{{ $sanpham->chatlieuday ?: 'Đang cập nhật' }}</td></tr>
                <tr><th>Mặt kính</th><td>{{ $sanpham->matkinh ?: 'Đang cập nhật' }}</td></tr>
                <tr><th>Độ chịu nước</th><td>{{ $sanpham->dochieunuoc ?: 'Đang cập nhật' }}</td></tr>
                <tr>
                    <th>Tình trạng</th>
                    <td>
                        @if($sanpham->soluong > 0)
                            <span class="badge rounded-pill" style="background:rgba(46,125,50,.12); color:#2e7d32;"><i class="fas fa-check-circle me-1"></i>Còn hàng</span>
                        @else
                            <span class="badge rounded-pill" style="background:rgba(198,40,40,.10); color:#c62828;"><i class="fas fa-times-circle me-1"></i>Hết hàng</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        @if($sanpham->soluong > 0)
            <form method="GET" action="{{ route('frontend.giohang.them', $sanpham->tensanpham_slug) }}" class="d-flex gap-2 align-items-center mb-3">
                <label for="quantity" class="fw-semibold mb-0">Số lượng</label>
                <input id="quantity" name="quantity" type="number" value="1" min="1" max="{{ $sanpham->soluong }}" class="form-control qty-input">
                <button class="btn btn-gold flex-grow-1" type="submit"><i class="fas fa-cart-plus me-1"></i>Thêm vào giỏ hàng</button>
            </form>
        @else
            <button class="btn btn-secondary w-100 mb-3" disabled>Hết hàng</button>
        @endif
        <a href="{{ route('frontend.sanpham') }}" class="btn btn-outline-dark"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<hr class="my-5">
<h3 class="section-title">Sản Phẩm Liên Quan</h3>
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
    @forelse($related as $sp)
        @php $rSale = $sp->dongia * (100 - $sp->giamgia) / 100; @endphp
        <div class="col">
            <div class="card product-card h-100">
                @if(!empty($sp->hinhanh))
                    @php $relatedImg = str_starts_with($sp->hinhanh, 'http') ? $sp->hinhanh : route('frontend.hinhanh', ['path' => $sp->hinhanh]); @endphp
                    <img src="{{ $relatedImg }}" class="card-img-top product-image" alt="{{ $sp->tensanpham }}">
                @else
                    <img src="https://via.placeholder.com/300x250?text=No+Image" class="card-img-top product-image" alt="{{ $sp->tensanpham }}">
                @endif
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title text-truncate">{{ $sp->tensanpham }}</h6>
                    <div class="mt-auto">
                        <span class="price">{{ number_format($rSale, 0, ',', '.') }} đ</span>
                        <a href="{{ route('frontend.giohang.them', $sp->tensanpham_slug) }}" class="btn btn-add-cart btn-sm mt-2 w-100"><i class="fas fa-cart-plus me-1"></i>Thêm</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">Không có sản phẩm liên quan.</p>
    @endforelse
</div>
@endsection