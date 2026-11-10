@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2"></i>Sửa Tình Trạng</h2>
            <a href="{{ route('admin.tinhtrang.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tinhtrang.postsua', ['id' => $tinhtrang->id]) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="tinhtrang" class="form-label">Tình trạng <span class="text-danger">*</span></label>
                        <input type="text" name="tinhtrang" id="tinhtrang" class="form-control @error('tinhtrang') is-invalid @enderror" 
                               value="{{ old('tinhtrang') ?? $tinhtrang->tinhtrang }}" placeholder="Nhập tình trạng" required>
                        @error('tinhtrang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.tinhtrang.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection