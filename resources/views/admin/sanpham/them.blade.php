@extends('layouts.app')

@section('title', 'Thêm sản phẩm - Watch Shop Admin')

@section('content')
<form method="POST" action="{{ route('admin.sanpham.postthem') }}" enctype="multipart/form-data">
    @csrf

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h3 class="mb-0" style="font-family:'Playfair Display',serif; font-weight:800;">Thêm sản phẩm</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.sanpham.danhsach') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
            <button type="submit" class="btn btn-dark rounded-pill px-4"><i class="fas fa-save me-2"></i>Lưu sản phẩm</button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left column: core info --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="tensanpham" class="form-label fw-semibold">Tên sản phẩm</label>
                        <input id="tensanpham" name="tensanpham" type="text" value="{{ old('tensanpham') }}"
                               class="form-control form-control-lg rounded-3 @error('tensanpham') is-invalid @enderror"
                               placeholder="VD: Rolex Submariner Date">
                        @error('tensanpham')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="gia" class="form-label fw-semibold">Giá (đ)</label>
                            <input id="gia" name="gia" type="number" min="0" value="{{ old('gia') }}"
                                   class="form-control form-control-lg rounded-3 @error('gia') is-invalid @enderror" placeholder="0">
                            @error('gia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="soluong" class="form-label fw-semibold">Số lượng</label>
                            <input id="soluong" name="soluong" type="number" min="0" value="{{ old('soluong', 0) }}"
                                   class="form-control form-control-lg rounded-3 @error('soluong') is-invalid @enderror">
                            @error('soluong')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="loaisanpham_id" class="form-label fw-semibold">Loại sản phẩm</label>
                            <select id="loaisanpham_id" name="loaisanpham_id"
                                    class="form-select form-select-lg rounded-3 @error('loaisanpham_id') is-invalid @enderror">
                                <option value="">-- Chọn loại --</option>
                                @foreach($loaisanpham as $lsp)
                                    <option value="{{ $lsp->id }}" @selected(old('loaisanpham_id') == $lsp->id)>{{ $lsp->tenloai }}</option>
                                @endforeach
                            </select>
                            @error('loaisanpham_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="hangsanxuat_id" class="form-label fw-semibold">Hãng sản xuất</label>
                            <select id="hangsanxuat_id" name="hangsanxuat_id"
                                    class="form-select form-select-lg rounded-3 @error('hangsanxuat_id') is-invalid @enderror">
                                <option value="">-- Chọn hãng --</option>
                                @foreach($hangsanxuat as $hsx)
                                    <option value="{{ $hsx->id }}" @selected(old('hangsanxuat_id') == $hsx->id)>{{ $hsx->tenhang }}</option>
                                @endforeach
                            </select>
                            @error('hangsanxuat_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="mota" class="form-label fw-semibold">Mô tả chi tiết</label>
                        <textarea id="mota" name="mota" rows="8"
                                  class="form-control rounded-3 @error('mota') is-invalid @enderror">{{ old('mota') }}</textarea>
                        @error('mota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column: image + status --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <label class="form-label fw-semibold">Hình ảnh sản phẩm</label>
                    <label for="hinhanh"
                           class="d-flex flex-column align-items-center justify-content-center text-center border rounded-4 p-3 w-100 @error('hinhanh') border-danger @enderror"
                           style="border-style:dashed !important; background:#faf8f3; cursor:pointer; min-height:150px;">
                        <i class="fas fa-image fa-2x mb-2" style="color:#c9a227;"></i>
                        <span class="small fw-semibold">Chọn hoặc kéo thả ảnh</span>
                        <img id="spPreview" class="mt-3 d-none rounded-3" style="max-height:140px; object-fit:contain;" alt="Xem trước">
                    </label>
                    <input id="hinhanh" name="hinhanh" type="file" accept="image/*" class="d-none">
                    @error('hinhanh')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <label class="form-label fw-semibold">Trạng thái hiển thị</label>
                    <select name="trangthai" class="form-select form-select-lg rounded-3 @error('trangthai') is-invalid @enderror">
                        <option value="1" @selected(old('trangthai', 1) == 1)>Hiển thị</option>
                        <option value="0" @selected(old('trangthai') === '0')>Ẩn</option>
                    </select>
                    @error('trangthai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
    if (window.tinymce) {
        tinymce.init({
            selector: '#mota',
            height: 380,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code'
        });
    }

    (function () {
        var input = document.getElementById('hinhanh');
        var preview = document.getElementById('spPreview');
        input.addEventListener('change', function () {
            var file = input.files[0];
            if (file && file.type.startsWith('image/')) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        });
    })();
</script>
@endpush