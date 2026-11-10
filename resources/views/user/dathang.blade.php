@extends('layouts.frontend')

@section('title', 'Đặt Hàng - Watch Shop')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Thông tin đặt hàng</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.dathang') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="tennguoinhan" class="form-label">Họ tên người nhận</label>
                                <input id="tennguoinhan" name="tennguoinhan" type="text" value="{{ old('tennguoinhan', $user->name) }}" class="form-control @error('tennguoinhan') is-invalid @enderror" required>
                                @error('tennguoinhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="dienthoaigiaohang" class="form-label">Điện thoại giao hàng</label>
                                <input id="dienthoaigiaohang" name="dienthoaigiaohang" type="text" value="{{ old('dienthoaigiaohang', $user->phone ?? '') }}" class="form-control @error('dienthoaigiaohang') is-invalid @enderror" required>
                                @error('dienthoaigiaohang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="ghichu" class="form-label">Ghi chú</label>
                                <textarea id="ghichu" name="ghichu" rows="3" class="form-control @error('ghichu') is-invalid @enderror">{{ old('ghichu') }}</textarea>
                                @error('ghichu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="diachigiaohang" class="form-label">Địa chỉ giao hàng</label>
                                <textarea id="diachigiaohang" name="diachigiaohang" rows="4" class="form-control @error('diachigiaohang') is-invalid @enderror" required>{{ old('diachigiaohang') }}</textarea>
                                @error('diachigiaohang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-check me-1"></i>Đặt hàng</button>
                            <a href="{{ route('frontend.giohang') }}" class="btn btn-outline-secondary">Quay lại giỏ hàng</a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng của bạn</h5>
                        @foreach($cart as $item)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $item['name'] }} x {{ $item['qty'] }}</span>
                                <span>{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }} đ</span>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between mt-3 fw-bold">
                            <span>Tổng cộng</span>
                            <span class="text-danger">{{ number_format($total, 0, ',', '.') }} đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
