@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-edit me-2\"></i>Sửa Đơn Hàng</h2>
            <a href="{{ route('admin.donhang.danhsach') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1\"></i>Quay lại
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.donhang.postsua', ['id' => $donhang->id]) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="tennguoinhan" class="form-label">Họ tên người nhận <span class="text-danger">*</span></label>
                        <input type="text" name="tennguoinhan" id="tennguoinhan" class="form-control @error('tennguoinhan') is-invalid @enderror" value="{{ old('tennguoinhan', $donhang->tennguoinhan) }}" required>
                        @error('tennguoinhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dienthoaigiaohang" class="form-label">Điện thoại giao hàng <span class="text-danger">*</span></label>
                        <input type="text" name="dienthoaigiaohang" id="dienthoaigiaohang" class="form-control @error('dienthoaigiaohang') is-invalid @enderror" value="{{ old('dienthoaigiaohang') ?? $donhang->dienthoaigiaohang }}" placeholder="Nhập điện thoại giao hàng" required>
                        @error('dienthoaigiaohang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="ghichu" class="form-label">Ghi chú</label>
                        <textarea name="ghichu" id="ghichu" rows="3" class="form-control @error('ghichu') is-invalid @enderror">{{ old('ghichu', $donhang->ghichu) }}</textarea>
                        @error('ghichu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="diachigiaohang" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                        <textarea name="diachigiaohang" id="diachigiaohang" class="form-control @error('diachigiaohang') is-invalid @enderror" rows="3" placeholder="Nhập địa chỉ giao hàng" required>{{ old('diachigiaohang') ?? $donhang->diachigiaohang }}</textarea>
                        @error('diachigiaohang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.donhang.danhsach') }}" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1\"></i>Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Trạng thái đơn hàng</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.donhang.trangthai', ['id' => $donhang->id]) }}" class="row g-2 align-items-end">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-8">
                        <label for="tinhtrang_id" class="form-label">Tình trạng hiện tại</label>
                        <select name="tinhtrang_id" id="tinhtrang_id" class="form-select @error('tinhtrang_id') is-invalid @enderror">
                            @foreach($tinhtrang as $tt)
                                <option value="{{ $tt->id }}" {{ $donhang->tinhtrang_id == $tt->id ? 'selected' : '' }}>{{ $tt->tinhtrang }}</option>
                            @endforeach
                        </select>
                        @error('tinhtrang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync-alt me-1"></i>Cập nhật trạng thái</button>
                    </div>
                </form>
                <p class="small text-muted mt-2 mb-0">Mỗi lần đổi trạng thái, hệ thống sẽ ghi lại một mốc vào lịch sử bên dưới và khách hàng sẽ thấy mốc đó trong trang đơn hàng của họ.</p>
            </div>
        </div>

        <div class="card shadow mt-4 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử trạng thái</h5>
            </div>
            <div class="card-body">
                @php $lichSuDon = $donhang->lichSuTrangThai(); @endphp
                @if($lichSuDon->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($lichSuDon->sortByDesc('id') as $ls)
                            <li class="list-group-item px-0">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <span class="badge bg-primary">{{ $ls->ten_trang_thai }}</span>
                                    <span class="small text-muted">{{ optional($ls->created_at)->format('H:i · d/m/Y') }}</span>
                                </div>
                                <div class="small mt-1">
                                    Người thực hiện: <strong>{{ optional($ls->User)->name ?? 'Hệ thống' }}</strong>
                                    @if($ls->ghichu)
                                        <span class="text-muted">· {{ $ls->ghichu }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    @if(\App\Models\DonHang::coBangLichSu())
                        <p class="text-muted mb-0">Đơn hàng này được tạo trước khi có tính năng theo dõi trạng thái nên chưa có mốc lịch sử nào. Các thay đổi trạng thái từ bây giờ sẽ được ghi lại tại đây.</p>
                    @else
                        <p class="text-muted mb-0">Bảng lịch sử trạng thái chưa được tạo. Hãy chạy lệnh <code>php artisan migrate</code> để bật tính năng theo dõi trạng thái.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
