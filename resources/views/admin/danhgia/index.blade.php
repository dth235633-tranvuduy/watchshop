@extends('layouts.app')

@section('title', 'Đánh giá sản phẩm - Watch Shop Admin')

@section('content')
<div class="mb-4">
    <p class="text-muted small text-uppercase fw-bold mb-1">Chăm sóc khách hàng</p>
    <h1 class="mb-0">Đánh giá sản phẩm</h1>
    <p class="text-muted small mt-2 mb-0">Đánh giá của khách hiển thị ngay trên trang sản phẩm, không cần duyệt. Khi cần, bạn chỉ việc bấm Ẩn/Hiện — thay đổi áp dụng ngay, không cần lưu.</p>
</div>

<div class="vip-card">
    <div class="table-responsive">
        <table class="table table-vip mb-0">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Đánh giá</th>
                    <th>Nội dung</th>
                    <th style="min-width:260px;">Trạng thái & phản hồi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    @php
                        $dangHien = $review->trangthai !== 'an';
                        $trangThaiForm = $review->trangthai === 'an' ? 'an' : 'hien_thi';
                    @endphp
                    <tr>
                        <td>{{ $review->User?->name }}<br><small class="text-muted">{{ $review->created_at?->format('d/m/Y H:i') }}</small></td>
                        <td>{{ $review->SanPham?->tensanpham }}</td>
                        <td class="text-warning">{{ str_repeat('★', $review->sosao) }}<br><small class="text-muted">{{ $review->sosao }}/5</small></td>
                        <td style="min-width:220px">{{ $review->binhluan ?: '—' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if($dangHien)
                                    <span class="badge bg-success">Đang hiển thị</span>
                                @else
                                    <span class="badge bg-secondary">Đang ẩn</span>
                                @endif
                                <form method="POST" action="{{ route('admin.danhgia.capnhat', $review) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="trangthai" value="{{ $dangHien ? 'an' : 'hien_thi' }}">
                                    @if($dangHien)
                                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye-slash me-1"></i>Ẩn</button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success"><i class="fas fa-eye me-1"></i>Hiện</button>
                                    @endif
                                </form>
                            </div>
                            <form method="POST" action="{{ route('admin.danhgia.capnhat', $review) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="trangthai" value="{{ $trangThaiForm }}">
                                <div class="input-group input-group-sm">
                                    <textarea name="phanhoi_admin" class="form-control" rows="2" placeholder="Phản hồi khách hàng">{{ $review->phanhoi_admin }}</textarea>
                                    <button class="btn btn-gold" type="submit">Phản hồi</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">Chưa có đánh giá.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $reviews->links() }}</div>
</div>
@endsection
