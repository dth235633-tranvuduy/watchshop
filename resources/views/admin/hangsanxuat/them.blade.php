@extends('layouts.app')

@section('title', 'Thêm thương hiệu - Watch Shop Admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white rounded-4 border-0 pt-4 px-4 pb-0">
                <h4 class="mb-1" style="font-family:'Playfair Display',serif; font-weight:800;">Thêm thương hiệu</h4>
                <p class="text-muted small mb-3">Tạo hãng sản xuất mới cho cửa hàng.</p>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.hangsanxuat.postthem') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="tenhang" class="form-label fw-semibold">Tên thương hiệu</label>
                        <input id="tenhang" name="tenhang" type="text" value="{{ old('tenhang') }}"
                               class="form-control form-control-lg rounded-3 @error('tenhang') is-invalid @enderror"
                               placeholder="VD: Rolex, Omega, Casio...">
                        @error('tenhang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Hình ảnh thương hiệu</label>
                        <label id="brandDrop" for="brandImage"
                               class="d-flex flex-column align-items-center justify-content-center text-center border rounded-4 p-4 w-100 @error('hinhanh') border-danger @enderror"
                               style="border-style:dashed !important; background:#faf8f3; cursor:pointer; min-height:180px;">
                            <i class="fas fa-cloud-arrow-up fa-2x mb-2" style="color:#c9a227;"></i>
                            <span class="fw-semibold small">Kéo thả ảnh vào đây hoặc bấm để chọn</span>
                            <span class="text-muted small">PNG, JPG, WEBP &middot; tối đa 2MB</span>
                            <img id="brandPreview" class="mt-3 d-none rounded-3" style="max-height:120px; object-fit:contain;" alt="Xem trước">
                        </label>
                        <input id="brandImage" name="hinhanh" type="file" accept="image/*" class="d-none @error('hinhanh') is-invalid @enderror">
                        @error('hinhanh')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill px-4"><i class="fas fa-save me-2"></i>Lưu thương hiệu</button>
                        <a href="{{ route('admin.hangsanxuat.danhsach') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('brandImage');
        var drop = document.getElementById('brandDrop');
        var preview = document.getElementById('brandPreview');

        function show(file) {
            if (!file || !file.type.startsWith('image/')) { return; }
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        }

        input.addEventListener('change', function () { show(input.files[0]); });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            drop.addEventListener(eventName, function (ev) { ev.preventDefault(); drop.classList.add('border-warning'); });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            drop.addEventListener(eventName, function (ev) { ev.preventDefault(); drop.classList.remove('border-warning'); });
        });

        drop.addEventListener('drop', function (ev) {
            if (ev.dataTransfer.files.length) {
                input.files = ev.dataTransfer.files;
                show(ev.dataTransfer.files[0]);
            }
        });
    })();
</script>
@endpush