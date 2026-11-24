@extends('layouts.app')

@section('title', ($banner->exists ? 'Sửa' : 'Thêm').' banner - Watch Shop Admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <p class="text-muted small text-uppercase fw-bold mb-1">Marketing</p>
        <h1 class="mb-0">{{ $banner->exists ? 'Sửa' : 'Thêm' }} banner</h1>
    </div>
    <a href="{{ route('admin.banner.danhsach') }}" class="btn btn-outline-secondary">Quay lại</a>
</div>

<div class="vip-card">
    <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data"
              action="{{ $banner->exists ? route('admin.banner.capnhat', $banner) : route('admin.banner.luu') }}">
            @csrf
            @if($banner->exists)
                @method('PUT')
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề <span class="text-danger">*</span></label>
                        <input name="tieu_de" class="form-control @error('tieu_de') is-invalid @enderror"
                               value="{{ old('tieu_de', $banner->tieu_de) }}" required>
                        @error('tieu_de')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Đường dẫn khi bấm</label>
                        <input name="duong_dan" class="form-control @error('duong_dan') is-invalid @enderror"
                               value="{{ old('duong_dan', $banner->duong_dan) }}" placeholder="/san-pham">
                        @error('duong_dan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="mo_ta" class="form-control @error('mo_ta') is-invalid @enderror" rows="3">{{ old('mo_ta', $banner->mo_ta) }}</textarea>
                        @error('mo_ta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thứ tự hiển thị</label>
                            <input type="text" inputmode="numeric" data-thousands data-decimals="0" name="thu_tu" class="form-control" min="0"
                                   value="{{ old('thu_tu', $banner->thu_tu ?? 0) }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="trang_thai" value="1" class="form-check-input" id="trang_thai"
                                       @checked(old('trang_thai', $banner->exists ? $banner->trang_thai : true))>
                                <label for="trang_thai" class="form-check-label fw-semibold">Hiển thị banner</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <label class="form-label fw-semibold">
                        Hình ảnh {{ $banner->exists ? '(để trống nếu giữ ảnh cũ)' : '' }} <span class="text-danger">*</span>
                    </label>
                    <input type="file" name="hinh_anh" id="hinh_anh"
                           class="form-control @error('hinh_anh') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png,.webp,image/*"
                           {{ $banner->exists ? '' : 'required' }}>
                    @error('hinh_anh')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div class="mt-3 border rounded-3 overflow-hidden bg-light" style="min-height:220px;">
                        @if($banner->exists && $banner->hinh_anh)
                            <img id="bannerPreview"
                                 src="{{ str_starts_with($banner->hinh_anh, 'http') ? $banner->hinh_anh : route('frontend.hinhanh', ['path' => $banner->hinh_anh]) }}"
                                 alt="{{ $banner->tieu_de }}"
                                 class="w-100 d-block"
                                 style="height:220px;object-fit:cover;">
                        @else
                            <img id="bannerPreview" class="w-100 d-none" style="height:220px;object-fit:cover;" alt="Xem trước banner">
                        @endif
                    </div>
                    <div class="form-text">Khuyến nghị ảnh ngang, tối thiểu 1600 × 600 px, dung lượng tối đa 4 MB.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.banner.danhsach') }}" class="btn btn-outline-secondary">Hủy</a>
                <button class="btn btn-gold" type="submit"><i class="fas fa-save me-1"></i>Lưu banner</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    var input = document.getElementById('hinh_anh');
    var preview = document.getElementById('bannerPreview');

    input.addEventListener('change', function () {
        var file = input.files[0];
        if (! file) return;

        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
    });
</script>
@endpush
@endsection
