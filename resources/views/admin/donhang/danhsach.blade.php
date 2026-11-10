@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-shopping-cart me-2"></i>Danh sách Đơn Hàng</h2>
            <a href="{{ route('admin.donhang.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
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
                    <th class="text-center" style="width: 100px;">Sửa</th>
                    <th class="text-center" style="width: 100px;">Xóa</th>
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
                                @if($value->ghichu)<div><strong>Ghi chú:</strong> {{ $value->ghichu }}</div>@endif
                            </td>
                            <td class="text-center">{{ optional($value->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ optional($value->TinhTrang)->tinhtrang == 'Đã giao' ? 'success' : (optional($value->TinhTrang)->tinhtrang == 'Đang giao' ? 'warning' : 'secondary') }}">
                                    {{ optional($value->TinhTrang)->tinhtrang }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.donhang.sua', ['id' => $value->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.donhang.xoa', ['id' => $value->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa đơn hàng này? Chi tiết đơn hàng cũng sẽ bị xóa.')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                        <!-- Bảng chi tiết đơn hàng lồng bên dưới -->
                        <tr>
                            <td colspan="7" class="p-0 bg-light">
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
                                                <td>{{ optional($ct->SanPham)->tensanpham ?? 'Sản phẩm đã xóa' }}</td>
                                                <td class="text-center">{{ $ct->soluongban }}</td>
                                                <td class="text-end">{{ number_format($ct->dongiaban, 0, ',', '.') }} đ</td>
                                                <td class="text-end">{{ number_format($ct->soluongban * $ct->dongiaban, 0, ',', '.') }} đ</td>
                                                @php $tongTien += $ct->soluongban * $ct->dongiaban; @endphp
                                            </tr>
                                        @endforeach
                                        @if(optional($value->DonHang_ChiTiet)->count() > 0)
                                            <tr class="fw-bold bg-white">
                                                <td colspan="4" class="text-end">Tổng tiền đơn hàng:</td>
                                                <td class="text-end">{{ number_format($tongTien, 0, ',', '.') }} đ</td>
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
