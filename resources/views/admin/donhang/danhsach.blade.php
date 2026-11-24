@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-shopping-cart me-2"></i>Danh sách Đơn Hàng</h2>
            <a href="{{ route('admin.donhang.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <form method="GET" action="{{ route('admin.donhang.danhsach') }}" class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label for="from_date" class="form-label">Từ ngày</label>
                        <input id="from_date" name="from_date" type="date" class="form-control"
                               value="{{ $filters['from_date'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="to_date" class="form-label">Đến ngày</label>
                        <input id="to_date" name="to_date" type="date" class="form-control"
                               value="{{ $filters['to_date'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Tình trạng</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Tất cả tình trạng</option>
                            @foreach($tinhtrang as $tt)
                                <option value="{{ $tt->id }}" @selected(($filters['status'] ?? null) == $tt->id)>
                                    {{ $tt->tinhtrang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment" class="form-label">Phương thức thanh toán</label>
                        <select id="payment" name="payment" class="form-select">
                            <option value="">Tất cả phương thức</option>
                            <option value="cod" @selected(($filters['payment'] ?? null) === 'cod')>Thanh toán khi nhận hàng (COD)</option>
                            <option value="bank" @selected(($filters['payment'] ?? null) === 'bank')>Chuyển khoản ngân hàng</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-gold flex-grow-1">
                            <i class="fas fa-filter me-1"></i>Lọc
                        </button>
                        <a href="{{ route('admin.donhang.danhsach') }}" class="btn btn-outline-secondary" title="Xóa lọc">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.donhang.xuat', $filters) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i>Xuất Excel
            </a>
        </div>

        <table class="table table-bordered table-hover bg-white shadow">
            <thead class="bg-success text-dark">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Khách hàng</th>
                    <th>Thông tin giao hàng</th>
                    <th class="text-center" style="width: 140px;">Ngày đặt</th>
                    <th class="text-center" style="width: 130px;">Tình trạng</th>
                    <th class="text-center" style="width: 100px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if($donhang->count() > 0)
                    @php $stt = 1; @endphp
                    @foreach($donhang as $value)
                        <tr>
                            <td class="text-center">{{ $stt++ }}</td>
                            <td>{{ optional($value->User)->name }}</td>
                            <td>
                                <div><strong>Người nhận:</strong> {{ $value->tennguoinhan ?: optional($value->User)->name }}</div>
                                <div><strong>Điện thoại:</strong> {{ $value->dienthoaigiaohang }}</div>
                                <div><strong>Địa chỉ:</strong> {{ $value->diachigiaohang }}</div>
                                <div><strong>Thanh toán:</strong> {{ $value->phuongthucthanhtoan === 'bank' ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)' }}</div>
                                @if($value->ghichu)<div><strong>Ghi chú:</strong> {{ $value->ghichu }}</div>@endif
                                @if($value->lydo_huy)<div class="text-danger"><strong>Lý do hủy:</strong> {{ $value->lydo_huy }}</div>@endif
                            </td>
                            <td class="text-center">{{ optional($value->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ optional($value->TinhTrang)->tinhtrang == 'Đã giao' ? 'success' : (optional($value->TinhTrang)->tinhtrang == 'Đang giao' ? 'warning' : 'secondary') }}">
                                    {{ optional($value->TinhTrang)->tinhtrang }}
                                </span>
                            </td>
                            <td class="text-center" style="width: 100px;">
                                <div class="d-grid gap-1 mx-auto" style="width: 82px;">
                                    <a href="{{ route('admin.donhang.in', ['id' => $value->id]) }}" class="btn btn-sm btn-outline-dark" title="In hóa đơn">
                                        <i class="fas fa-print"></i> In
                                    </a>
                                    <a href="{{ route('admin.donhang.sua', ['id' => $value->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <a href="{{ route('admin.donhang.xoa', ['id' => $value->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa đơn hàng này? Chi tiết đơn hàng cũng sẽ bị xóa.')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <!-- Bảng chi tiết đơn hàng lồng bên dưới -->
                        <tr>
                            <td colspan="6" class="p-0 bg-light">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">#</th>
                                            <th>Tên sản phẩm</th>
                                            <th class="text-center" style="width: 100px;">Số lượng</th>
                                            <th class="text-end" style="width: 130px;">Đơn giá</th>
                                            <th class="text-end" style="width: 140px;">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $tongTien = 0; @endphp
                                        @foreach((optional($value->DonHang_ChiTiet)) as $ct)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $ct->tensanpham_snapshot ?: optional($ct->SanPham)->tensanpham ?: 'Sản phẩm đã xóa' }}</td>
                                                <td class="text-center">{{ $ct->soluongban }}</td>
                                                <td class="text-end">{{ number_format($ct->dongiaban, 0, ',', '.') }} đ</td>
                                                <td class="text-end">{{ number_format($ct->soluongban * $ct->dongiaban, 0, ',', '.') }} đ</td>
                                                @php $tongTien += $ct->soluongban * $ct->dongiaban; @endphp
                                            </tr>
                                        @endforeach
                                        @if(optional($value->DonHang_ChiTiet)->count() > 0)
                                            <tr class="fw-bold bg-white">
                                                <td colspan="4" class="text-end">Tổng tiền đơn hàng:</td>
                                                <td class="text-end">{{ number_format((float) $value->tongtien, 0, ',', '.') }} đ</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-2">Không có chi tiết sản phẩm</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Không có đơn hàng nào</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection
