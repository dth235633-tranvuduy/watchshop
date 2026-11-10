@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2\"></i>Sửa Tài Khoản</h2>
            <a href="{{ route('admin.user.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1\"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.user.postsua', ['id' => $user->id]) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') ?? $user->name }}" placeholder="Nhập họ và tên" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') ?? $user->email }}" placeholder="Nhập email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Quyền hạn <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" {{ (old('role') ?? $user->role) == 'user' ? 'selected' : '' }}>Người dùng</option>
                            <option value="admin" {{ (old('role') ?? $user->role) == 'admin' ? 'selected' : '' }}>Quản trị</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="doimatkhau" id="doimatkhau" class="form-check-input" value="1">
                        <label class="form-check-label" for="doimatkhau">Đổi mật khẩu</label>
                    </div>

                    <div id="matkhaumoi_box" class="card mb-3" style="display: none;">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu mới">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Nhập lại mật khẩu mới">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.user.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1\"></i>Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // JavaScript để ẩn/hiện form nhập mật khẩu mới khi đánh dấu checkbox "Đổi mật khẩu"
        var doiMatKhauCheckbox = document.getElementById('doimatkhau');
        var matKhauMoiBox = document.getElementById('matkhaumoi_box');

        function toggleMatKhauMoi() {
            if (doiMatKhauCheckbox.checked) {
                matKhauMoiBox.style.display = 'block';
                document.getElementById('password').setAttribute('required', 'required');
                document.getElementById('password_confirmation').setAttribute('required', 'required');
            } else {
                matKhauMoiBox.style.display = 'none';
                document.getElementById('password').removeAttribute('required');
                document.getElementById('password_confirmation').removeAttribute('required');
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            }
        }

        doiMatKhauCheckbox.addEventListener('change', toggleMatKhauMoi);

        // Đặt trạng thái ban đầu theo giá trị checkbox khi trang tải
        toggleMatKhauMoi();
    </script>
@endsection
