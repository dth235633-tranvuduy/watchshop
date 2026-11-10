@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2"></i>Sửa Sản Phẩm</h2>
            <a href="{{ route('admin.sanpham.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.sanpham.postsua', ['id' => $sanpham->id]) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="giamgia" class="form-label">Giảm giá (%)</label>
                        <input type="number" name="giamgia" id="giamgia" class="form-control @error('giamgia') is-invalid @enderror" value="{{ old('giamgia', $sanpham->giamgia) }}" min="0" max="100" required>
                        @error('giamgia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="danhgia" class="form-label">Đánh giá (0 - 5)</label>
                        <input type="number" name="danhgia" id="danhgia" class="form-control @error('danhgia') is-invalid @enderror" value="{{ old('danhgia', $sanpham->danhgia) }}" min="0" max="5" step="0.1" required>
                        @error('danhgia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="row">
                        @foreach(['chatlieuday' => 'Chất liệu dây', 'matkinh' => 'Mặt kính', 'dochieunuoc' => 'Độ chịu nước'] as $field => $label)
                            <div class="col-md-4 mb-3">
                                <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                                <input type="text" name="{{ $field }}" id="{{ $field }}" value="{{ old($field, $sanpham->{$field}) }}" class="form-control @error($field) is-invalid @enderror">
                                @error($field) <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <label for="loaisanpham_id" class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
                        <select name="loaisanpham_id" id="loaisanpham_id" class="form-select @error('loaisanpham_id') is-invalid @enderror" required>
                            <option value="">-- Chọn loại sản phẩm --</option>
                            @foreach($loaisanpham as $lsp)
                                <option value="{{ $lsp->id }}" {{ (old('loaisanpham_id') ?? $sanpham->loaisanpham_id) == $lsp->id ? 'selected' : '' }}>{{ $lsp->tenloai }}</option>
                            @endforeach
                        </select>
                        @error('loaisanpham_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="hangsanxuat_id" class="form-label">Hãng sản xuất <span class="text-danger">*</span></label>
                        <select name="hangsanxuat_id" id="hangsanxuat_id" class="form-select @error('hangsanxuat_id') is-invalid @enderror" required>
                            <option value="">-- Chọn hãng sản xuất --</option>
                            @foreach($hangsanxuat as $hsx)
                                <option value="{{ $hsx->id }}" {{ (old('hangsanxuat_id') ?? $sanpham->hangsanxuat_id) == $hsx->id ? 'selected' : '' }}>{{ $hsx->tenhang }}</option>
                            @endforeach
                        </select>
                        @error('hangsanxuat_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tensanpham" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="tensanpham" id="tensanpham" class="form-control @error('tensanpham') is-invalid @enderror" value="{{ old('tensanpham') ?? $sanpham->tensanpham }}" placeholder="Nhập tên sản phẩm" required>
                        @error('tensanpham')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="soluong" class="form-label">Số lượng <span class="text-danger">*</span></label>
                        <input type="number" name="soluong" id="soluong" class="form-control @error('soluong') is-invalid @enderror" value="{{ old('soluong') ?? $sanpham->soluong }}" min="0" required>
                        @error('soluong')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dongia" class="form-label">Đơn giá <span class="text-danger">*</span></label>
                        <input type="number" name="dongia" id="dongia" class="form-control @error('dongia') is-invalid @enderror" value="{{ old('dongia') ?? $sanpham->dongia }}" min="0" step="1000" required>
                        @error('dongia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        @if(!empty($sanpham->hinhanh))
                        <label class="form-label">Hình ảnh hiện tại</label>
                        <div class="mb-2">
                            <img src="{{ route('frontend.hinhanh', ['path' => $sanpham->hinhanh]) }}" alt="{{ $sanpham->tensanpham }}" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        @endif

                        <label for="hinhanh" class="form-label">Hình ảnh mới (để trống nếu không thay đổi)</label>
                        <input type="file" name="hinhanh" id="hinhanh" class="form-control @error('hinhanh') is-invalid @enderror" accept="image/*">
                        @error('hinhanh')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Chọn file ảnh mới nếu muốn thay đổi, để trống nếu giữ nguyên ảnh cũ.</div>
                    </div>

                    <div class="mb-3">
                        <label for="motasanpham" class="form-label">Mô tả sản phẩm</label>
                        <textarea name="motasanpham" id="motasanpham" class="form-control @error('motasanpham') is-invalid @enderror" rows="4">{{ old('motasanpham') ?? $sanpham->motasanpham }}</textarea>
                        @error('motasanpham')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.sanpham.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection