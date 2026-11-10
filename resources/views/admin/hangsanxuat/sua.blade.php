@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2"></i>Sửa Hãng Sản Xuất</h2>
            <a href="{{ route('admin.hangsanxuat.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.hangsanxuat.postsua', ['id' => $hangsanxuat->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="tenhang" class="form-label">Tên hãng <span class="text-danger">*</span></label>
                        <input type="text" name="tenhang" id="tenhang" class="form-control @error('tenhang') is-invalid @enderror" 
                               value="{{ old('tenhang') ?? $hangsanxuat->tenhang }}" placeholder="Nhập tên hãng sản xuất" required>
                        @error('tenhang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(!empty($hangsanxuat->hinhanh))
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh hiện tại</label>
                        <div class="mb-2">
                            <img src="{{ route('frontend.hinhanh', ['path' => $hangsanxuat->hinhanh]) }}" alt="{{ $hangsanxuat->tenhang }}" 
                                 class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="hinhanh" class="form-label">Hình ảnh mới (để trống nếu không thay đổi)</label>
                        <input type="file" name="hinhanh" id="hinhanh" class="form-control @error('hinhanh') is-invalid @enderror" 
                               accept="image/*">
                        @error('hinhanh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Chọn file ảnh mới nếu muốn thay đổi, để trống nếu giữ nguyên ảnh cũ.</div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.hangsanxuat.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
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