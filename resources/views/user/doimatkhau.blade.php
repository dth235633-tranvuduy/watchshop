@extends('layouts.frontend')

@section('title', 'Đổi mật khẩu - Watch Shop')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4"><i class="fas fa-key me-2" style="color:var(--ws-gold);"></i>Đổi mật khẩu</h4>
                <form method="POST" action="{{ route('user.doimatkhau') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Mật khẩu hiện tại</label>
                        <input id="old_password" name="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" required>
                        @error('old_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input id="new_password" name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" required>
                        @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Nhập lại mật khẩu mới</label>
                        <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="form-control" required>
                    </div>
                    <button class="btn btn-gold" type="submit"><i class="fas fa-save me-1"></i>Cập nhật mật khẩu</button>
                    <a href="{{ route('user.home') }}" class="btn btn-outline-dark">Quay lại</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection