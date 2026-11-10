@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-list me-2"></i>Danh sách Tình Trạng</h2>
            <a href="{{ route('admin.tinhtrang.them') }}" class="btn btn-warning">
                <i class="fas fa-plus me-1"></i>Thêm mới
            </a>
        </div>

        <table class="table table-bordered table-hover bg-white shadow">
            <thead class="bg-info text-dark">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Tình trạng</th>
                    <th class="text-center" style="width: 120px;">Sửa</th>
                    <th class="text-center" style="width: 120px;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @if($tinhtrang->count() > 0)
                    @php $stt = 1; @endphp
                    @foreach($tinhtrang as $item)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>{{ $item->tinhtrang }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.tinhtrang.sua', ['id' => $item->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.tinhtrang.xoa', ['id' => $item->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Không có dữ liệu</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection