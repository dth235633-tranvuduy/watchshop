@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-tag me-2"></i>Danh sách Sản Phẩm</h2>
            <a href="{{ route('admin.sanpham.them') }}" class="btn btn-warning">
                <i class="fas fa-plus me-1"></i>Thêm mới
            </a>
        </div>

        <table class="table table-bordered table-hover bg-white shadow">
            <thead class="bg-warning text-dark">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th class="text-center" style="width: 100px;">Hình ảnh</th>
                    <th>Loại sản phẩm</th>
                    <th>Hãng sản xuất</th>
                    <th>Tên sản phẩm</th>
                    <th class="text-center" style="width: 80px;">SL</th>
                    <th class="text-end" style="width: 120px;">Đơn giá</th>
                    <th class="text-center" style="width: 100px;">Sửa</th>
                    <th class="text-center" style="width: 100px;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @if($sanpham->count() > 0)
                    @php $stt = 1; @endphp
                    @foreach($sanpham as $value)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td class="text-center">
                            @if(!empty($value->hinhanh))
                                <img src="{{ route('frontend.hinhanh', ['path' => $value->hinhanh]) }}" alt="{{ $value->tensanpham }}" 
                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <span class="text-muted small">Không có ảnh</span>
                            @endif
                        </td>
                        <td>{{ optional($value->LoaiSanPham)->tenloai }}</td>
                        <td>{{ optional($value->HangSanXuat)->tenhang }}</td>
                        <td>{{ $value->tensanpham }}</td>
                        <td class="text-center">{{ $value->soluong }}</td>
                        <td class="text-end">{{ number_format($value->dongia, 0, ',', '.') }} đ</td>
                        <td class="text-center">
                            <a href="{{ route('admin.sanpham.sua', ['id' => $value->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.sanpham.xoa', ['id' => $value->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection