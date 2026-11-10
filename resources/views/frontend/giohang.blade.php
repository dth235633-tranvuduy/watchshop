@extends('layouts.frontend')

@section('title', 'Giỏ Hàng - Watch Shop')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-shopping-cart me-2"></i>Giỏ Hàng</h2>
            <a href="{{ route('frontend.sanpham') }}" class="btn btn-outline-info btn-sm"><i class="fas fa-tag me-1"></i>Tiếp tục mua sắm</a>
        </div>

        <div class="row">
            <!-- Danh sách sản phẩm trong giỏ -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="cartTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">#</th>
                                        <th>Sản phẩm</th>
                                        <th class="text-center" style="width: 120px;">Đơn giá</th>
                                        <th class="text-center" style="width: 100px;">Số lượng</th>
                                        <th class="text-end" style="width: 140px;">Thành tiền</th>
                                        <th class="text-center" style="width: 80px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    @forelse($cart as $rowId => $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(!empty($item['image']))
                                                        @php $cartImg = str_starts_with($item['image'], 'http') ? $item['image'] : route('frontend.hinhanh', ['path' => $item['image']]); @endphp
                                                        <img src="{{ $cartImg }}" alt="{{ $item['name'] }}" width="56" height="56" class="rounded object-fit-cover">
                                                    @endif
                                                    <span>{{ $item['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('frontend.giohang.giam', $rowId) }}" class="btn btn-outline-secondary">-</a>
                                                    <span class="btn btn-outline-secondary disabled">{{ $item['qty'] }}</span>
                                                    <a href="{{ route('frontend.giohang.tang', $rowId) }}" class="btn btn-outline-secondary">+</a>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }} đ</td>
                                            <td class="text-center">
                                                <a href="{{ route('frontend.giohang.xoa', $rowId) }}" class="btn btn-outline-danger btn-sm" aria-label="Xóa sản phẩm">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Giỏ hàng đang trống</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('frontend.home') }}" class="btn btn-outline-secondary me-2">Tiếp tục mua sắm</a>
                    @if(count($cart) > 0)
                        <a href="{{ route('user.dathang') }}" class="btn btn-warning"><i class="fas fa-credit-card me-1"></i>Tiến hành thanh toán</a>
                    @endif
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Tóm tắt đơn hàng</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span id="subtotal" class="fw-bold">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span class="text-muted">Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Tổng cộng:</strong>
                            <strong id="grandTotal" class="text-info fs-5">{{ number_format($total, 0, ',', '.') }} đ</strong>
                        </div>
                        @if(count($cart) > 0)
                            <a href="{{ route('user.dathang') }}" class="btn btn-warning w-100"><i class="fas fa-credit-card me-1"></i>Thanh toán ngay</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
