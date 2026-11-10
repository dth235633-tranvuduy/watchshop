@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-plus me-2\"></i>Thêm Tài Khoản Mới</h2>
            <a href="{{ route('admin.user.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1\"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.user.postthem') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nhập họ và tên" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Nhập email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Quyền hạn <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Người dùng</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Nhập lại mật khẩu" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.user.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1\"></i>Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
