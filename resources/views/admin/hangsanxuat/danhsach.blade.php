@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-building me-2"></i>Danh sách Hãng Sản Xuất</h2>
            <a href="{{ route('admin.hangsanxuat.them') }}" class="btn btn-warning">
                <i class="fas fa-plus me-1"></i>Thêm mới
            </a>
        </div>

        <table class="table table-bordered table-hover bg-white shadow">
            <thead class="bg-info text-dark">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th class="text-center" style="width: 120px;">Hình ảnh</th>
                    <th>Tên hãng</th>
                    <th>Tên hãng không dấu</th>
                    <th class="text-center" style="width: 120px;">Sửa</th>
                    <th class="text-center" style="width: 120px;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @if($hangsanxuat->count() > 0)
                    @php $stt = 1; @endphp
                    @foreach($hangsanxuat as $value)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td class="text-center">
                            @if(!empty($value->hinhanh))
                                <img src="{{ route('frontend.hinhanh', ['path' => $value->hinhanh]) }}" alt="{{ $value->tenhang }}" 
                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <span class="text-muted small">Không có ảnh</span>
                            @endif
                        </td>
                        <td>{{ $value->tenhang }}</td>
                        <td>{{ $value->tenhang_slug ?? '' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.hangsanxuat.sua', ['id' => $value->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.hangsanxuat.xoa', ['id' => $value->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    </div>
@endsection