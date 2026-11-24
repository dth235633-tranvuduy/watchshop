@extends('layouts.app')

@section('title', 'Danh sách Nhà cung cấp - Watch Shop Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted small text-uppercase fw-bold mb-1">Kho hàng</p>
            <h1 class="mb-0">Danh sách Nhà cung cấp</h1>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-gold px-4 py-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-import me-1"></i>Nhập Excel
            </button>
            <a href="{{ route('admin.nhacungcap.xuat') }}" class="btn btn-outline-gold px-4 py-2">
                <i class="fas fa-file-export me-1"></i>Xuất Excel
            </a>
            <a href="{{ route('admin.kho.danhsach') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại kho hàng</a>
        </div>
    </div>

    <!-- Modal nhập Excel -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.nhacungcap.nhap') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Nhập nhà cung cấp từ Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="nccExcelFile" class="form-label fw-semibold">Chọn file XLSX, XLS hoặc CSV</label>
                        <input id="nccExcelFile" name="file" type="file" accept=".xlsx,.xls,.csv" class="form-control" required>
                        <div class="form-text">Cột bắt buộc: <strong>Tên nhà cung cấp</strong>. Có thể dùng <strong>tennhacungcap</strong>, <strong>Ten nha cung cap</strong> hoặc <strong>name</strong>. Các cột tùy chọn: Email, Số điện thoại, Địa chỉ.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-file-import me-1"></i>Nhập file</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="vip-card">
        <div class="table-responsive">
            <table class="table table-vip mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">#</th>
                        <th>Tên nhà cung cấp</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                    </tr>
                </thead>
                <tbody>
                    @if($nhacungcap->count() > 0)
                        @php $stt = 1; @endphp
                        @foreach($nhacungcap as $value)
                            <tr>
                                <td class="text-center">{{ $stt++ }}</td>
                                <td><strong>{{ $value->tennhacungcap }}</strong></td>
                                <td>{{ $value->sodienthoai ?? '-' }}</td>
                                <td>{{ $value->email ?? '-' }}</td>
                                <td>{{ $value->diachi ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có nhà cung cấp nào.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $nhacungcap->links() }}</div>
    </div>
@endsection