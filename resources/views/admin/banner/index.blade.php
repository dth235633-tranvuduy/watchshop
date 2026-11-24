@extends('layouts.app')

@section('title', 'Banner trang chủ - Watch Shop Admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <p class="text-muted small text-uppercase fw-bold mb-1">Marketing</p>
        <h1 class="mb-0">Banner trang chủ</h1>
    </div>
    <a href="{{ route('admin.banner.them') }}" class="btn btn-gold px-4 py-2">
        <i class="fas fa-plus me-2"></i>Thêm banner
    </a>
</div>

<div class="vip-card">
    <div class="table-responsive">
        <table class="table table-vip align-middle mb-0">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Liên kết</th>
                    <th class="text-center">Thứ tự</th>
                    <th class="text-center">Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $banner)
                    <tr>
                        <td>
                            <img src="{{ str_starts_with($banner->hinh_anh, 'http') ? $banner->hinh_anh : route('frontend.hinhanh', ['path' => $banner->hinh_anh]) }}"
                                 alt="{{ $banner->tieu_de }}"
                                 class="rounded-3"
                                 style="width:150px;height:64px;object-fit:cover;">
                        </td>
                        <td>
                            @if($banner->tieu_de_phu)
                                <small class="text-uppercase fw-bold" style="color:var(--ws-gold, #b8860b);">{{ $banner->tieu_de_phu }}</small><br>
                            @endif
                            <strong>{{ $banner->tieu_de }}</strong>
                            @if($banner->mo_ta)
                                <br><small class="text-muted">{{ $banner->mo_ta }}</small>
                            @endif
                        </td>
                        <td>
                            @if($banner->duong_dan)
                                <a href="{{ $banner->duong_dan }}" target="_blank" class="text-decoration-none">{{ $banner->duong_dan }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $banner->thu_tu }}</td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.banner.trangthai', $banner) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="btn btn-sm {{ $banner->trang_thai ? 'btn-success' : 'btn-outline-secondary' }}"
                                        onclick="return confirm('{{ $banner->trang_thai ? 'Tắt' : 'Bật' }} banner này?')">
                                    {{ $banner->trang_thai ? 'Đang bật' : 'Đã tắt' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end text-nowrap pe-4">
                            <a href="{{ route('admin.banner.sua', $banner) }}" class="btn btn-sm btn-outline-gold px-3" title="Sửa banner">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form class="d-inline" method="POST" action="{{ route('admin.banner.xoa', $banner) }}" onsubmit="return confirm('Xóa banner này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-soft-danger px-3" title="Xóa banner">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Chưa có banner.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($banners->hasPages())
        <div class="card-body">{{ $banners->links() }}</div>
    @endif
</div>
@endsection
