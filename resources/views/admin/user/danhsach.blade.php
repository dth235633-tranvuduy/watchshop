@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users me-2\"></i>Danh sách Tài Khoản</h2>
            <a href="{{ route('admin.user.them') }}" class="btn btn-warning">
                <i class="fas fa-plus me-1\"></i>Thêm mới
            </a>
        </div>

        <table class="table table-bordered table-hover bg-white shadow">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th class="text-center" style="width: 130px;">Quyền hạn</th>
                    <th class="text-center" style="width: 120px;">Sửa</th>
                    <th class="text-center" style="width: 120px;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @if($users->count() > 0)
                    @php $stt = 1; @endphp
                    @foreach($users as $value)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->email }}</td>
                        <td class="text-center">
                            <span class="badge {{ $value->role == 'admin' ? 'bg-danger' : 'bg-info' }}">
                                {{ $value->role == 'admin' ? 'Quản trị' : 'Người dùng' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.user.sua', ['id' => $value->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit\"></i> Sửa
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.user.xoa', ['id' => $value->id]) }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa tài khoản này?')">
                                <i class="fas fa-trash\"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Không có tài khoản nào</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection
