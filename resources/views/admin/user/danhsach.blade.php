@extends('layouts.app')

@section('title', 'Tài khoản - Watch Shop Admin')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h2 class="mb-0">
            <i class="fas fa-users me-2" style="color:var(--ws-gold);"></i>Danh sách Tài Khoản
        </h2>
        <a href="{{ route('admin.nguoidung.them') }}" class="btn btn-gold px-4 py-2">
            <i class="fas fa-plus me-1"></i>Thêm mới
        </a>
    </div>

    <div class="card vip-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover table-vip align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center ps-4" style="width:60px;">#</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th class="text-center" style="width:130px;">Quyền hạn</th>
                        <th class="text-center" style="width:150px;">Trạng thái</th>
                        <th class="text-center" style="width:110px;">Sửa</th>
                        <th class="text-center pe-4" style="width:110px;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="text-center ps-4">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-center">
                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-info text-dark' }}">
                                    {{ $user->role === 'admin' ? 'Quản trị' : 'Người dùng' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($user->id === auth()->id())
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $user->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.nguoidung.trangthai', ['id' => $user->id]) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                        <button type="submit"
                                                class="btn btn-sm {{ $user->is_active ? 'btn-soft-danger' : 'btn-outline-gold' }}"
                                                onclick="return confirm('{{ $user->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }} tài khoản này?')">
                                            {{ $user->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.nguoidung.sua', ['id' => $user->id]) }}" class="btn btn-sm btn-outline-gold px-3">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </td>
                            <td class="text-center pe-4">
                                @if($user->id === auth()->id())
                                    <button type="button" class="btn btn-sm btn-soft-danger px-3" disabled title="Không thể xóa tài khoản đang đăng nhập">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <a href="{{ route('admin.nguoidung.xoa', ['id' => $user->id]) }}"
                                       class="btn btn-sm btn-soft-danger px-3"
                                       onclick="return confirm('Bạn chắc chắn muốn xóa tài khoản này?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Không có tài khoản nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
