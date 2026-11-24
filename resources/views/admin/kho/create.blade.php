@extends('layouts.app')

@section('title', 'Tạo phiếu nhập - Watch Shop Admin')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4"><div><p class="text-muted small text-uppercase fw-bold mb-1">Kho hàng</p><h1 class="mb-0">Tạo phiếu nhập</h1></div><div class="d-flex gap-2"><a href="{{ route('admin.kho.danhsach') }}" class="btn btn-outline-secondary">Quay lại</a> <button type="button" class="btn btn-outline-gold" data-bs-toggle="modal" data-bs-target="#importExcelModal"><i class="fas fa-file-import me-1"></i>Nhập Excel</button></div></div>

<!-- Modal nhập Excel cho phiếu nhập -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.kho.nhap-excel') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importExcelModalLabel">Nhập phiếu từ Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nhà cung cấp</label>
                        <select name="nhacungcap_id" class="form-select" required>
                            <option value="">Chọn nhà cung cấp</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->tennhacungcap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngày nhập</label>
                        <input type="datetime-local" name="ngaynhap" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <input name="ghichu" class="form-control" placeholder="Ghi chú nhập kho">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn file Excel</label>
                        <input name="file" type="file" accept=".xlsx,.xls,.csv" class="form-control" required>
                        <div class="form-text">Cột bắt buộc: <strong>Tên sản phẩm</strong>, <strong>Số lượng</strong>, <strong>Đơn giá nhập</strong>. Tên sản phẩm phải đã tồn tại trong hệ thống.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-file-import me-1"></i>Xem trước nhập</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="vip-card"><div class="card-body"><form method="POST" action="{{ route('admin.kho.luu') }}">@csrf<div class="row g-3 mb-4"><div class="col-md-5"><label class="form-label fw-semibold">Nhà cung cấp</label><select name="nhacungcap_id" class="form-select" required><option value="">Chọn nhà cung cấp</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->tennhacungcap }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label fw-semibold">Ngày nhập</label><input type="datetime-local" name="ngaynhap" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required></div><div class="col-md-3"><label class="form-label fw-semibold">Ghi chú</label><input name="ghichu" class="form-control"></div></div><div class="table-responsive"><table class="table align-middle" id="receiptItems"><thead><tr><th>Sản phẩm</th><th style="width:150px">Số lượng</th><th style="width:200px">Đơn giá nhập</th><th style="width:50px"></th></tr></thead><tbody><tr><td><select name="sanpham_id[]" class="form-select" required><option value="">Chọn sản phẩm</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->tensanpham }} (tồn {{ $product->soluong }})</option>@endforeach</select></td><td><input type="text" inputmode="numeric" data-thousands data-decimals="0" name="soluong[]" class="form-control" min="1" value="1" required></td><td><input type="text" inputmode="decimal" data-thousands data-decimals="2" name="dongianhap[]" class="form-control" min="0" step="0.01" value="0" required></td><td><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="fas fa-trash"></i></button></td></tr></tbody></table></div><div class="d-flex justify-content-between mt-3"><button type="button" class="btn btn-outline-gold" id="addItem"><i class="fas fa-plus me-1"></i>Thêm sản phẩm</button><button class="btn btn-gold" type="submit">Lưu phiếu nhập</button></div></form></div></div>
@endsection

@push('scripts')
<script>document.getElementById('addItem').addEventListener('click',function(){const first=document.querySelector('#receiptItems tbody tr');const row=first.cloneNode(true);row.querySelectorAll('input').forEach(i=>{i.value=i.name.startsWith('soluong')?'1':'0';delete i.dataset.groupedInitialized;});row.querySelector('select').selectedIndex=0;document.querySelector('#receiptItems tbody').appendChild(row);if(window.watchShopInitGroupedNumbers)window.watchShopInitGroupedNumbers(row);});document.addEventListener('click',function(e){const button=e.target.closest('.remove-row');if(button&&document.querySelectorAll('#receiptItems tbody tr').length>1)button.closest('tr').remove();});</script>
@endpush
