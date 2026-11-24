@extends('layouts.app')

@section('title', 'Chất liệu dây - Watch Shop Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-link me-2" style="color:var(--ws-gold);"></i>Chất liệu dây</h2>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.chatlieuday.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-9">
                <label for="tenchatlieu" class="form-label fw-semibold">Tên chất liệu dây</label>
                <input id="tenchatlieu" name="tenchatlieu" type="text" maxlength="255" class="form-control"
                       value="{{ old('tenchatlieu') }}" placeholder="Ví dụ: Dây da, Dây cao su, Thép không gỉ..." required>
                @error('tenchatlieu')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-gold w-100"><i class="fas fa-plus me-1"></i>Thêm mới</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm overflow-hidden">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width:70px;">#</th>
                <th>Chất liệu dây</th>
                <th>Tên không dấu</th>
                <th class="text-center" style="width:150px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.chatlieuday.update', $item) }}" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <input name="tenchatlieu" type="text" maxlength="255" class="form-control form-control-sm"
                                   value="{{ $item->tenchatlieu }}" required>
                            <button type="submit" class="btn btn-sm btn-primary text-nowrap">Cập nhật</button>
                        </form>
                    </td>
                    <td class="text-muted">{{ $item->tenchatlieu_slug }}</td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.chatlieuday.destroy', $item) }}"
                              onsubmit="return confirm('Xóa chất liệu dây này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash me-1"></i>Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có chất liệu dây</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
