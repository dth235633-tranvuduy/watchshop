@extends('layouts.app')

@section('title', 'Đơn tuyển dụng - Watch Shop Admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h3 class="mb-0" style="font-family:'Playfair Display',serif; font-weight:800;">Đơn ứng tuyển</h3>
    <span class="badge rounded-pill fs-6 px-3 py-2" style="background:rgba(201,162,39,.12); color:#c9a227;">
        {{ $donUngTuyen->where('trangthai', 'cho_duyet')->count() }} chờ duyệt
    </span>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#faf8f3;">
                <tr>
                    <th class="ps-4">Ứng viên</th>
                    <th>Vị trí</th>
                    <th>Ngày nộp</th>
                    <th>Hồ sơ</th>
                    <th class="text-end pe-4">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donUngTuyen as $don)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $don->hoten }}</div>
                            <div class="small text-muted">{{ $don->email }}</div>
                        </td>
                        <td>{{ $don->vitri }}</td>
                        <td class="small text-muted">{{ $don->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.tuyendung.cv', $don->id) }}" target="_blank" rel="noopener" class="small text-decoration-none" style="color:#c9a227;">
                                <i class="fas fa-file-pdf me-1"></i>Xem CV
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <form method="POST" action="{{ route('admin.tuyendung.capnhat', $don->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="trangthai" onchange="this.form.submit()"
                                        class="form-select form-select-sm rounded-pill d-inline-block w-auto fw-semibold
                                        @if($don->trangthai === 'cho_duyet') text-warning border-warning
                                        @elseif($don->trangthai === 'da_duyet') text-success border-success
                                        @else text-danger border-danger @endif">
                                    <option value="cho_duyet" @selected($don->trangthai === 'cho_duyet')>Chờ duyệt</option>
                                    <option value="da_duyet" @selected($don->trangthai === 'da_duyet')>Đã duyệt</option>
                                    <option value="tu_choi" @selected($don->trangthai === 'tu_choi')>Từ chối</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Chưa có đơn ứng tuyển nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection