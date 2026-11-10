@extends('layouts.app')

@section('title', 'Loại sản phẩm - Watch Shop Admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h2 class="mb-0" style="font-family:'Playfair Display',serif; font-weight:800;">
        <i class="fas fa-list me-2" style="color:var(--ws-gold);"></i>Danh sách Loại Sản Phẩm
    </h2>
    <a href="{{ route('admin.loaisanpham.them') }}" class="btn btn-gold px-4 py-2">
        <i class="fas fa-plus me-1"></i>Thêm mới
    </a>
</div>

<div class="card vip-card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover table-vip align-middle mb-0">
            <thead>
                <tr>
                    <th class="text-center ps-4" style="width: 60px;">#</th>
                    <th>Tên loại</th>
                    <th>Tên loại không dấu</th>
                    <th class="text-center" style="width: 110px;">Sửa</th>
                    <th class="text-center pe-4" style="width: 110px;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loaisanpham as $loai)
                    <tr>
                        <td class="text-center ps-4">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $loai->tenloai }}</td>
                        <td class="text-muted small">{{ $loai->tenloai_slug ?? '' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.loaisanpham.sua', ['id' => $loai->id]) }}" class="btn btn-sm btn-outline-gold px-3">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('admin.loaisanpham.xoa', ['id' => $loai->id]) }}" class="btn btn-sm btn-soft-danger px-3" onclick="return confirm('Bạn chắc chắn muốn xóa?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Không có dữ liệu</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection