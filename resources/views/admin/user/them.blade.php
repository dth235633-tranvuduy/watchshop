@extends('layouts.app')

@section('title', 'Thêm tài khoản - Watch Shop Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-plus me-2"></i>Thêm Tài Khoản Mới</h2>
        <a href="{{ route('admin.nguoidung.danhsach') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    <div class="card vip-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.nguoidung.postthem') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="role" class="form-label">Quyền hạn <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" @selected(old('role', 'user') === 'user')>Người dùng</option>
                            <option value="admin" @selected(old('role') === 'admin')>Quản trị</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                            <option value="1" @selected(old('is_active', '1') === '1')>Hoạt động</option>
                            <option value="0" @selected(old('is_active') === '0')>Không hoạt động</option>
                        </select>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password" aria-label="Hiện mật khẩu">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.nguoidung.danhsach') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save me-1"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.target);
                var icon = button.querySelector('i');
                input.type = input.type === 'password' ? 'text' : 'password';
                icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
            });
        });
    </script>
    @endpush
@endsection
