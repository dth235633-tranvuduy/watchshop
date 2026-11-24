@extends('layouts.app')

@section('title', 'Sửa tài khoản - Watch Shop Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-edit me-2"></i>Sửa Tài Khoản</h2>
        <a href="{{ route('admin.nguoidung.danhsach') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    <div class="card vip-card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.nguoidung.postsua', ['id' => $user->id]) }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="role" class="form-label">Quyền hạn <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="user" @selected(old('role', $user->role) === 'user')>Người dùng</option>
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>Quản trị</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required @disabled($user->id === auth()->id())>
                            <option value="1" @selected(old('is_active', $user->is_active ? '1' : '0') === '1')>Hoạt động</option>
                            <option value="0" @selected(old('is_active', $user->is_active ? '1' : '0') === '0')>Không hoạt động</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="is_active" value="1">
                            <div class="form-text">Không thể vô hiệu hóa tài khoản đang đăng nhập.</div>
                        @endif
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="doimatkhau" id="doimatkhau" class="form-check-input" value="1" @checked(old('doimatkhau'))>
                    <label class="form-check-label fw-semibold" for="doimatkhau">Đổi mật khẩu tài khoản này</label>
                </div>

                <div id="matkhaumoi_box" class="card border-0 bg-light mb-3" style="display:none;">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu quản trị hiện tại <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password" aria-label="Hiện mật khẩu">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password" aria-label="Hiện mật khẩu">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirmation" aria-label="Hiện mật khẩu">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.nguoidung.danhsach') }}" class="btn btn-outline-secondary">Hủy bỏ</a>
                    <button type="submit" class="btn btn-gold"><i class="fas fa-save me-1"></i>Cập nhật</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        var doiMatKhau = document.getElementById('doimatkhau');
        var matKhauMoiBox = document.getElementById('matkhaumoi_box');

        function toggleMatKhauMoi() {
            var visible = doiMatKhau.checked;
            matKhauMoiBox.style.display = visible ? 'block' : 'none';
            ['current_password', 'password', 'password_confirmation'].forEach(function (id) {
                var input = document.getElementById(id);
                if (visible) {
                    input.setAttribute('required', 'required');
                } else {
                    input.removeAttribute('required');
                    input.value = '';
                }
            });
        }

        doiMatKhau.addEventListener('change', toggleMatKhauMoi);
        toggleMatKhauMoi();

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
