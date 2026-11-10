@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2"></i>Sửa Loại Sản Phẩm</h2>
            <a href="{{ route('admin.loaisanpham.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.loaisanpham.postsua', ['id' => $loaisanpham->id]) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="tenloai" class="form-label">Tên loại <span class="text-danger">*</span></label>
                        <input type="text" name="tenloai" id="tenloai" class="form-control @error('tenloai') is-invalid @enderror" 
                               value="{{ old('tenloai') ?? $loaisanpham->tenloai }}" placeholder="Nhập tên loại sản phẩm" required>
                        @error('tenloai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.loaisanpham.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection