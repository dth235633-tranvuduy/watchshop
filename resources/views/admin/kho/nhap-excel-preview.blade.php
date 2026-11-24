@extends('layouts.app')

@section('title', 'Xem trước nhập phiếu từ Excel - Watch Shop Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted small text-uppercase fw-bold mb-1">Kho hàng</p>
            <h1 class="mb-0">Xem trước nhập phiếu từ Excel</h1>
        </div>
        <a href="{{ route('admin.kho.them') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại tạo phiếu</a>
    </div>

    @if($data['errors'] && count($data['errors']) > 0)
        <div class="alert alert-danger">
            <strong>Có lỗi trong file:</strong>
            <ul class="mb-0 mt-2">
                @foreach($data['errors'] as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="vip-card">
        <div class="card-header">
            <i class="fas fa-file-excel me-2"></i>Thông tin phiếu nhập
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.kho.xac-nhan-nhap') }}">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nhà cung cấp</label>
                        <input type="text" class="form-control" value="{{ $selectedSupplier?->tennhacungcap ?? $data['nhacungcap_id'] }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ngày nhập</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($data['ngaynhap'])->format('d/m/Y H:i') }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <input type="text" class="form-control" value="{{ $data['ghichu'] ?? '-' }}" readonly>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vip mb-0">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th>Sản phẩm</th>
                                <th class="text-center" style="width: 120px;">Số lượng</th>
                                <th class="text-end" style="width: 160px;">Đơn giá nhập</th>
                                <th class="text-end" style="width: 170px;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $stt = 1; $tongTien = 0; @endphp
                            @foreach($data['items'] as $item)
                                <tr>
                                    <td class="text-center">{{ $stt++ }}</td>
                                    <td><strong>{{ $item['tensanpham'] }}</strong></td>
                                    <td class="text-center">{{ $item['soluong'] }}</td>
                                    <td class="text-end">{{ number_format((float) $item['dongianhap'], 0, ',', '.') }} đ</td>
                                    <td class="text-end">{{ number_format($item['thanhtien'], 0, ',', '.') }} đ</td>
                                    @php $tongTien += $item['thanhtien']; @endphp
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Tổng tiền:</td>
                                <td class="text-end fw-bold">{{ number_format($tongTien, 0, ',', '.') }} đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.kho.huy-nhap-excel') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Hủy nhập Excel
                    </a>
                    <button type="submit" class="btn btn-gold">
                        <i class="fas fa-check me-1"></i>Xác nhận nhập hàng vào kho
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection