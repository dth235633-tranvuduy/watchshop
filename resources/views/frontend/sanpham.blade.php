@extends('layouts.frontend')

@section('title', 'Sản Phẩm - Watch Shop')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="section-title mb-0">Danh Sách Sản Phẩm</h2>
        <p class="text-muted small mb-0 mt-3">Tìm thấy {{ $sanpham->total() }} sản phẩm</p>
    </div>
    <a href="{{ route('frontend.home') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i>Trang chủ</a>
</div>

<div class="card border-0 shadow-sm mb-4" style="background:#fff;">
    <div class="card-body">
        <form method="GET" action="{{ route('frontend.sanpham') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Danh mục</label>
                <select class="form-select form-select-sm" name="category" onchange="this.form.submit()">
                    <option value="">Tất cả danh mục</option>
                    @foreach($loaisanpham as $lsp)
                        <option value="{{ $lsp->tenloai_slug }}" {{ $tenloai_slug === $lsp->tenloai_slug ? 'selected' : '' }}>{{ $lsp->tenloai }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Thương hiệu</label>
                <select class="form-select form-select-sm" name="hangsanxuat_id" onchange="this.form.submit()">
                    <option value="">Tất cả thương hiệu</option>
                    @foreach($hangsanxuat as $hsx)
                        <option value="{{ $hsx->id }}" {{ (int) $hangsanxuatId === $hsx->id ? 'selected' : '' }}>{{ $hsx->tenhang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Giá từ</label>
                <input type="number" name="price_min" min="0" step="1000" value="{{ $priceMin ?: '' }}" class="form-control form-control-sm" placeholder="0">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Giá đến</label>
                <input type="number" name="price_max" min="0" step="1000" value="{{ $priceMax ?: '' }}" class="form-control form-control-sm" placeholder="Không giới hạn">
            </div>
            <div class="col-md-2">
                <button class="btn btn-gold btn-sm w-100" type="submit"><i class="fas fa-filter me-1"></i>Lọc</button>
            </div>
            <div class="col-12">
                <div class="input-group input-group-sm">
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Tìm theo tên hoặc mô tả sản phẩm...">
                    <button class="btn btn-outline-gold" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="productList">
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
                        <div class="mb-2">
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
        <div class="col-12 text-center py-5">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h5>Không tìm thấy sản phẩm nào</h5>
            <p class="text-muted">Hãy thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm.</p>
            <a href="{{ route('frontend.sanpham') }}" class="btn btn-gold">Xem tất cả sản phẩm</a>
        </div>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $sanpham->links() }}
</div>
@endsection