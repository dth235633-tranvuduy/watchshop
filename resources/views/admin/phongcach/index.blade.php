@extends('layouts.app')

@section('title', 'Phong cách - Watch Shop Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-palette me-2" style="color:var(--ws-gold);"></i>Phong cách</h2>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.phongcach.store') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-9">
                <label for="tenphongcach" class="form-label fw-semibold">Tên phong cách</label>
                <input id="tenphongcach" name="tenphongcach" type="text" maxlength="255" class="form-control"
                       value="{{ old('tenphongcach') }}" placeholder="Ví dụ: Sang trọng, Thể thao, Quân đội..." required>
                @error('tenphongcach')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
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
                <th>Phong cách</th>
                <th>Tên không dấu</th>
                <th class="text-center" style="width:150px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.phongcach.update', $item) }}" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <input name="tenphongcach" type="text" maxlength="255" class="form-control form-control-sm"
                                   value="{{ $item->tenphongcach }}" required>
                            <button type="submit" class="btn btn-sm btn-primary text-nowrap">Cập nhật</button>
                        </form>
                    </td>
                    <td class="text-muted">{{ $item->tenphongcach_slug }}</td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.phongcach.destroy', $item) }}"
                              onsubmit="return confirm('Xóa phong cách này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash me-1"></i>Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Chưa có phong cách</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
