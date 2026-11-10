@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2\"></i>Sửa Đơn Hàng</h2>
            <a href="{{ route('admin.donhang.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1\"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.donhang.postsua', ['id' => $donhang->id]) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="tennguoinhan" class="form-label">Họ tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="tennguoinhan" id="tennguoinhan" class="form-control @error('tennguoinhan') is-invalid @enderror" value="{{ old('tennguoinhan', $donhang->tennguoinhan) }}" required>
                        @error('tennguoinhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dienthoaigiaohang" class="form-label">Điện thoại giao hàng <span class="text-danger">*</span></label>
                        <input type="text" name="dienthoaigiaohang" id="dienthoaigiaohang" class="form-control @error('dienthoaigiaohang') is-invalid @enderror" value="{{ old('dienthoaigiaohang') ?? $donhang->dienthoaigiaohang }}" placeholder="Nhập điện thoại giao hàng" required>
                        @error('dienthoaigiaohang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="ghichu" class="form-label">Ghi chú</label>
                        <textarea name="ghichu" id="ghichu" rows="3" class="form-control @error('ghichu') is-invalid @enderror">{{ old('ghichu', $donhang->ghichu) }}</textarea>
                        @error('ghichu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="diachigiaohang" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                        <textarea name="diachigiaohang" id="diachigiaohang" class="form-control @error('diachigiaohang') is-invalid @enderror" rows="3" placeholder="Nhập địa chỉ giao hàng" required>{{ old('diachigiaohang') ?? $donhang->diachigiaohang }}</textarea>
                        @error('diachigiaohang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tinhtrang_id" class="form-label">Tình trạng <span class="text-danger">*</span></label>
                        <select name="tinhtrang_id" id="tinhtrang_id" class="form-select @error('tinhtrang_id') is-invalid @enderror" required>
                            <option value="">-- Chọn tình trạng --</option>
                            @foreach($tinhtrang as $tt)
                                <option value="{{ $tt->id }}" {{ (old('tinhtrang_id') ?? $donhang->tinhtrang_id) == $tt->id ? 'selected' : '' }}>{{ $tt->tinhtrang }}</option>
                            @endforeach
                        </select>
                        @error('tinhtrang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.donhang.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1\"></i>Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
