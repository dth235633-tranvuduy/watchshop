@extends('layouts.app')

@section('title', 'Nhập sản phẩm từ file - Watch Shop Admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <p class="text-muted small text-uppercase fw-bold mb-1">Sản phẩm</p>
        <h1 class="mb-0">Nhập sản phẩm từ file</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.sanpham.mau') }}" class="btn btn-outline-gold">
            <i class="fas fa-download me-1"></i>Tải file mẫu
        </a>
        <a href="{{ route('admin.sanpham.danhsach') }}" class="btn btn-outline-secondary">Quay lại</a>
    </div>
</div>

@if(session('import_errors') && count(session('import_errors')) > 0)
    <div class="alert alert-warning rounded-3">
        <p class="fw-semibold mb-2"><i class="fas fa-triangle-exclamation me-1"></i>Các dòng không nhập được:</p>
        <ul class="mb-0 small">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="vip-card h-100">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.sanpham.postnhap') }}" enctype="multipart/form-data">
                    @csrf

                    <label for="file" class="form-label fw-semibold">Chọn file dữ liệu</label>
                    <label for="file"
                           class="d-flex flex-column align-items-center justify-content-center text-center border rounded-4 p-4 w-100 mb-3 @error('file') border-danger @enderror"
                           style="border-style:dashed !important; background:#faf8f3; cursor:pointer; min-height:170px;">
                        <i class="fas fa-file-excel fa-2x mb-2" style="color:#c9a227;"></i>
                        <span class="fw-semibold">Chọn hoặc kéo thả file</span>
                        <span class="small text-muted mt-1">Hỗ trợ XLSX, XLS (tối đa 20MB)</span>
                        <span id="fileName" class="small fw-semibold mt-2 text-gold"></span>
                    </label>
                    <input id="file" name="file" type="file" accept=".xlsx,.xls" class="d-none">
                    @error('file')
                        <div class="text-danger small mb-3">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-gold w-100">
                        <i class="fas fa-file-import me-1"></i>Bắt đầu nhập
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="vip-card h-100">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fas fa-circle-info text-gold me-2"></i>Hướng dẫn</h5>
                <ol class="small text-muted ps-3 mb-3">
                    <li class="mb-1">Tải <a href="{{ route('admin.sanpham.mau') }}">file mẫu</a> để có đúng tên cột.</li>
                    <li class="mb-1">Dòng đầu tiên là tiêu đề, dữ liệu bắt đầu từ dòng thứ hai.</li>
                    <li class="mb-1">Cột <strong>Tên sản phẩm</strong> là bắt buộc.</li>
                    <li class="mb-1">Cột <strong>Loại sản phẩm</strong> và <strong>Hãng sản xuất</strong> phải khớp tên đã có trong hệ thống.</li>
                    <li class="mb-1">Sản phẩm trùng tên sẽ được bỏ qua, không ghi đè.</li>
                    <li>Hình ảnh để trống, có thể cập nhật sau khi sửa sản phẩm.</li>
                </ol>

                <p class="fw-semibold mb-2">Các cột được hỗ trợ</p>
                <div class="d-flex flex-wrap gap-1">
                    @foreach(['Tên sản phẩm', 'Loại sản phẩm', 'Hãng sản xuất', 'Số lượng', 'Đơn giá', 'Giảm giá (%)', 'Đánh giá', 'Mô tả', 'Chất liệu dây', 'Mặt kính', 'Độ chịu nước'] as $column)
                        <span class="badge bg-gold-soft rounded-pill">{{ $column }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('file');
        var fileName = document.getElementById('fileName');

        input.addEventListener('change', function () {
            fileName.textContent = input.files.length ? input.files[0].name : '';
        });
    })();
</script>
@endpush
