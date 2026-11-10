@extends('layouts.auth')

@section('title', 'Đăng nhập - Watch Shop')

@section('cover-eyebrow', 'Chào mừng trở lại')
@section('cover-title', 'Đăng nhập để tiếp tục hành trình')
@section('cover-text', 'Quản lý đơn hàng, theo dõi ưu đãi và khám phá bộ sưu tập đồng hồ cao cấp dành riêng cho bạn.')

@section('form')
<div class="text-center text-lg-start mb-4">
    <h2 class="font-display fw-bold mb-1">Đăng nhập</h2>
    <p class="text-muted mb-0">Chào mừng bạn quay lại Watch Shop</p>
</div>

@if(session('status'))
    <div class="alert alert-success small py-2">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('login.submit') }}" novalidate>
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label small fw-semibold">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}"
               class="form-control form-control-lg auth-control @error('email') is-invalid @enderror"
               placeholder="ban@example.com" required autofocus autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label small fw-semibold">Mật khẩu</label>
        <div class="password-field">
            <input id="password" name="password" type="password"
                   class="form-control form-control-lg auth-control @error('password') is-invalid @enderror"
                   placeholder="Nhập mật khẩu" required autocomplete="current-password">
            <button type="button" class="password-toggle" data-target="password" aria-label="Hiện/ẩn mật khẩu">
                <i class="fas fa-eye"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input id="remember" name="remember" type="checkbox" value="1" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember" class="form-check-label small">Ghi nhớ đăng nhập</label>
        </div>
        <a href="#" class="small text-decoration-none" style="color:var(--ws-gold);">Quên mật khẩu?</a>
    </div>

    <button type="submit" class="btn btn-auth btn-lg rounded-pill shadow-sm w-100 mb-4">Đăng nhập</button>

    <div class="auth-divider mb-4">hoặc tiếp tục với</div>

    <div class="row g-2 mb-4">
        <div class="col-4"><a href="#" class="btn btn-social w-100 py-2" aria-label="Google"><i class="fab fa-google"></i></a></div>
        <div class="col-4"><a href="#" class="btn btn-social w-100 py-2" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></div>
        <div class="col-4"><a href="#" class="btn btn-social w-100 py-2" aria-label="Apple"><i class="fab fa-apple"></i></a></div>
    </div>

    <p class="text-center small text-muted mb-0">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none" style="color:var(--ws-gold);">Đăng ký ngay</a>
    </p>
</form>
@endsection