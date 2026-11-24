@extends('layouts.frontend')

@section('title', 'Giỏ hàng - Watch Shop')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h2 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Giỏ hàng</h2>
        <a href="{{ route('frontend.home') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-1"></i>Tiếp tục mua sắm
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="vip-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:55px;">#</th>
                                <th>Sản phẩm</th>
                                <th class="text-center" style="width:130px;">Đơn giá</th>
                                <th class="text-center" style="width:160px;">Số lượng</th>
                                <th class="text-end" style="width:150px;">Thành tiền</th>
                                <th class="text-center" style="width:90px;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $rowId => $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if(! empty($item['image']))
                                                @php
                                                    $cartImage = str_starts_with($item['image'], 'http')
                                                        ? $item['image']
                                                        : route('frontend.hinhanh', ['path' => $item['image']]);
                                                @endphp
                                                <img src="{{ $cartImage }}" alt="{{ $item['name'] }}"
                                                     class="rounded"
                                                     style="width:64px;height:64px;object-fit:cover;">
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $item['name'] }}</div>
                                                <small class="text-muted">Tối đa 20 sản phẩm/dòng</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('frontend.giohang.giam', $rowId) }}"
                                               class="btn btn-outline-secondary" aria-label="Giảm số lượng">−</a>
                                            <span class="btn btn-outline-secondary disabled">{{ $item['qty'] }}</span>
                                            <a href="{{ route('frontend.giohang.tang', $rowId) }}"
                                               class="btn btn-outline-secondary" aria-label="Tăng số lượng">+</a>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }} đ</td>
                                    <td class="text-center">
                                        <a href="{{ route('frontend.giohang.xoa', $rowId) }}"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Xóa sản phẩm khỏi giỏ hàng?')"
                                           aria-label="Xóa {{ $item['name'] }}">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="vip-card p-4 sticky-top" style="top:100px;">
                <h5 class="mb-4">Tóm tắt đơn hàng</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tổng tiền</span>
                    <strong>{{ number_format($total, 0, ',', '.') }} đ</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Giảm giá</span>
                    <strong class="text-success">−{{ number_format($discount, 0, ',', '.') }} đ</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Thuế VAT (10%)</span>
                    <strong>{{ number_format($vat, 0, ',', '.') }} đ</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Phí vận chuyển</span>
                    <strong class="text-success">{{ $shipping === 0 ? 'Miễn phí' : number_format($shipping, 0, ',', '.') . ' đ' }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <strong>Tổng ước tính</strong>
                    <strong class="fs-5 text-gold">{{ number_format($grandTotal, 0, ',', '.') }} đ</strong>
                </div>
                <a href="{{ route('user.dathang') }}" class="btn btn-gold w-100">
                    <i class="fas fa-credit-card me-1"></i>Tiến hành thanh toán
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
