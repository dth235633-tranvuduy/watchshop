@extends('layouts.frontend')

@section('title', 'Trang cá nhân - Watch Shop')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-4">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:84px;height:84px;background:var(--ws-black);color:var(--ws-gold);font-size:2rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <span class="badge" style="background:var(--ws-gold);">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="list-group list-group-flush text-start">
                <a href="{{ route('user.home') }}" class="list-group-item list-group-item-action active" style="background:var(--ws-black);border-color:var(--ws-black);"><i class="fas fa-tachometer-alt me-2"></i>Hồ sơ cá nhân</a>
                <a href="{{ route('user.donhang') }}" class="list-group-item list-group-item-action"><i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi</a>
                <a href="{{ route('user.doimatkhau') }}" class="list-group-item list-group-item-action"><i class="fas fa-key me-2"></i>Đổi mật khẩu</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="list-group-item list-group-item-action text-danger w-100 text-start border-0"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button></form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fas fa-shopping-bag fa-2x" style="color:var(--ws-gold);"></i>
                        <div><div class="fs-4 fw-bold">{{ $user->DonHang->count() }}</div><div class="text-muted small">Tổng đơn hàng</div></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="fas fa-wallet fa-2x" style="color:var(--ws-gold);"></i>
                        <div>
                            <div class="fs-4 fw-bold">{{ number_format($user->DonHang->sum(fn ($dh) => $dh->DonHang_ChiTiet->sum(fn ($ct) => $ct->soluongban * $ct->dongiaban)), 0, ',', '.') }} đ</div>
                            <div class="text-muted small">Tổng chi tiêu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-4"><i class="fas fa-user-edit me-2" style="color:var(--ws-gold);"></i>Thông tin tài khoản</h5>
                <form method="POST" action="{{ route('user.hoso') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-gold" type="submit"><i class="fas fa-save me-1"></i>Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection