@extends('layouts.auth')

@section('title', 'Đăng ký - Watch Shop')

@section('cover-eyebrow', 'Tham gia cùng chúng tôi')
@section('cover-title', 'Tạo tài khoản, mở ra đẳng cấp')
@section('cover-text', 'Đăng ký thành viên để nhận ưu đãi độc quyền, theo dõi đơn hàng và trải nghiệm dịch vụ đẳng cấp từ Watch Shop.')

@section('form')
<div class="text-center text-lg-start mb-3">
    <h2 class="font-display fw-bold mb-1">Đăng ký</h2>
    <p class="text-muted mb-0">Đã có tài khoản? <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" style="color:var(--ws-gold);">Đăng nhập</a></p>
</div>

<form method="POST" action="{{ route('register.submit') }}" novalidate>
    @csrf

    <div class="mb-2">
        <label for="name" class="form-label small fw-semibold mb-1">Họ và tên</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}"
               class="form-control auth-control @error('name') is-invalid @enderror"
               placeholder="Nguyễn Văn A" required autofocus autocomplete="name">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-2">
        <label for="email" class="form-label small fw-semibold mb-1">Địa chỉ email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}"
               class="form-control auth-control @error('email') is-invalid @enderror"
               placeholder="ban@example.com" required autocomplete="email">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-2">
        <label for="password" class="form-label small fw-semibold mb-1">Mật khẩu</label>
        <div class="password-field">
            <input id="password" name="password" type="password" minlength="8"
                   class="form-control auth-control @error('password') is-invalid @enderror"
                   placeholder="Tối thiểu 8 ký tự" required autocomplete="new-password">
            <button type="button" class="password-toggle" data-target="password" aria-label="Hiện/ẩn mật khẩu"><i class="fas fa-eye"></i></button>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-2">
        <label for="password_confirmation" class="form-label small fw-semibold mb-1">Xác nhận mật khẩu</label>
        <div class="password-field">
            <input id="password_confirmation" name="password_confirmation" type="password"
                   class="form-control auth-control @error('password_confirmation') is-invalid @enderror"
                   placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
            <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Hiện/ẩn mật khẩu"><i class="fas fa-eye"></i></button>
            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input id="privacy" name="privacy" type="checkbox" value="1"
                   class="form-check-input @error('privacy') is-invalid @enderror"
                   {{ old('privacy') ? 'checked' : '' }} required>
            <label for="privacy" class="form-check-label small">
                Tôi đồng ý với <a href="#" class="text-decoration-none" style="color:var(--ws-gold);">Chính sách bảo mật</a> và Điều khoản sử dụng.
            </label>
            @error('privacy') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-auth rounded-pill shadow-sm w-100 mb-3 py-2">Tạo tài khoản</button>

    <div class="auth-divider mb-3">hoặc đăng ký với</div>

    <div class="row g-2">
        <div class="col-4"><a href="#" class="btn btn-social w-100 py-2" aria-label="Google"><i class="fab fa-google"></i></a></div>
        <div class="col-4"><a href="#" class="btn btn-social w-100 py-2" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></div>
        <div class="col-4"><a href="#" class="btn btn-social w-100 py-2" aria-label="Apple"><i class="fab fa-apple"></i></a></div>
    </div>
</form>
@endsection