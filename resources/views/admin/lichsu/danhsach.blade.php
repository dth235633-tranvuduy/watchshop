@extends('layouts.app')

@section('title', 'Lịch sử hoạt động - Watch Shop Admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="text-muted small text-uppercase fw-bold mb-1">Quản trị hệ thống</p>
        <h1 class="mb-0">Lịch sử hoạt động</h1>
        <p class="text-muted mb-0 mt-2">Theo dõi các thao tác đã được thực hiện trên website.</p>
    </div>
    <form method="POST" action="{{ route('admin.lichsu.xoahet') }}" onsubmit="return confirm('Xóa toàn bộ lịch sử hoạt động?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-soft-danger" type="submit"><i class="fas fa-trash me-2"></i>Xóa toàn bộ</button>
    </form>
</div>

<div class="vip-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Mô tả, URL hoặc IP">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Phương thức</label>
                <select name="method" class="form-select">
                    <option value="">Tất cả</option>
                    @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                        <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label small fw-semibold">Từ ngày</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
            <div class="col-md-2"><label class="form-label small fw-semibold">Đến ngày</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
            <div class="col-md-2 d-flex gap-2"><button class="btn btn-gold flex-grow-1" type="submit"><i class="fas fa-filter me-1"></i>Lọc</button><a class="btn btn-outline-secondary" href="{{ route('admin.lichsu.danhsach') }}" title="Xóa lọc"><i class="fas fa-rotate-left"></i></a></div>
        </form>
    </div>
</div>

<div class="vip-card">
    <div class="card-header"><i class="fas fa-clock-rotate-left"></i>Danh sách bản ghi ({{ $logs->total() }})</div>
    <div class="table-responsive">
        <table class="table table-vip table-hover align-middle mb-0">
            <thead><tr><th>Thời gian</th><th>Người dùng</th><th>Hành động</th><th>Chi tiết</th><th>IP</th><th class="text-end">Xóa</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="small text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td><strong>{{ $log->user?->name ?? 'Không xác định' }}</strong><br><small class="text-muted">{{ $log->user?->email }}</small></td>
                        <td><span class="badge rounded-pill bg-gold-soft">{{ strtoupper($log->action) }}</span><br><small class="text-muted">{{ $log->method }}</small></td>
                        <td><div>{{ $log->description }}</div><small class="text-muted text-break">{{ $log->route_name ?? $log->url }}</small></td>
                        <td class="small">{{ $log->ip_address ?? '—' }}</td>
                        <td class="text-end"><form method="POST" action="{{ route('admin.lichsu.xoa', $log) }}" onsubmit="return confirm('Xóa bản ghi này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-soft-danger" title="Xóa"><i class="fas fa-trash"></i></button></form></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Chưa có lịch sử hoạt động.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())<div class="card-body">{{ $logs->links() }}</div>@endif
</div>
@endsection
