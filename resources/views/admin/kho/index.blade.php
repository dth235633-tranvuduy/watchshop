@extends('layouts.app')

@section('title', 'Quản lý kho - Watch Shop Admin')

@push('styles')
    <style>
        .kho-actions .btn { white-space: nowrap; }
        .kho-receipt + .kho-receipt { border-top: 1px solid var(--ws-line); }
        .kho-detail-wrap {
            border: 1px solid var(--ws-line);
            border-radius: 14px;
            overflow: hidden;
        }
        .kho-detail { font-size: .92rem; }
        .kho-detail thead th {
            background: var(--ws-cream);
            border-bottom: 1px solid var(--ws-line);
            color: #6f6f6f;
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .06em;
            padding: 10px 14px;
            text-transform: uppercase;
        }
        .kho-detail tbody td { padding: 10px 14px; }
        @media (max-width: 575.98px) {
            .kho-actions { width: 100%; }
            .kho-actions .btn { flex: 1 1 auto; }
        }
    </style>
@endpush

@section('content')
    {{-- Tiêu đề + cụm thao tác --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <p class="text-muted small text-uppercase fw-bold mb-1">Vận hành</p>
            <h1 class="mb-1">Kho hàng</h1>
            <p class="text-muted mb-0">Theo dõi tồn kho, phiếu nhập và nhà cung cấp ở cùng một nơi.</p>
        </div>
        <div class="kho-actions d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('admin.nhacungcap.danhsach') }}" class="btn btn-outline-gold px-3 py-2">
                <i class="fas fa-truck me-2"></i>Danh sách nhà cung cấp
            </a>
            <a href="{{ route('admin.kho.xuat') }}" class="btn btn-outline-gold px-3 py-2">
                <i class="fas fa-file-export me-2"></i>Xuất phiếu nhập
            </a>
            <a href="{{ route('admin.kho.them') }}" class="btn btn-gold px-3 py-2">
                <i class="fas fa-boxes-stacked me-2"></i>Tạo phiếu nhập
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Cảnh báo tồn kho thấp --}}
        <div class="col-lg-5">
            <div class="vip-card h-100">
                <div class="card-header">
                    <i class="fas fa-triangle-exclamation"></i>Cảnh báo tồn kho thấp
                </div>
                <div class="table-responsive">
                    <table class="table table-vip mb-0">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Hãng</th>
                                <th>Tồn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStock as $product)
                                <tr>
                                    <td>{{ $product->tensanpham }}</td>
                                    <td>{{ $product->HangSanXuat?->tenhang }}</td>
                                    <td><span class="badge bg-danger">{{ $product->soluong }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-circle-check text-gold d-block fs-4 mb-2"></i>
                                        Kho đang an toàn
                                        <span class="d-block small mt-1">Không có sản phẩm nào sắp hết hàng.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Thêm nhà cung cấp --}}
        <div class="col-lg-7">
            <div class="vip-card h-100">
                <div class="card-header">
                    <i class="fas fa-truck-ramp-box"></i>Thêm nhà cung cấp
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.kho.nhacungcap.luu') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="tennhacungcap" class="form-label small fw-semibold mb-1">Tên nhà cung cấp <span class="text-danger">*</span></label>
                            <input id="tennhacungcap" name="tennhacungcap" class="form-control" placeholder="Ví dụ: Công ty TNHH Đồng Hồ ABC" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-semibold mb-1">Email</label>
                            <input id="email" name="email" type="email" class="form-control" placeholder="lienhe@congty.vn">
                        </div>
                        <div class="col-md-6">
                            <label for="sodienthoai" class="form-label small fw-semibold mb-1">Số điện thoại</label>
                            <input id="sodienthoai" name="sodienthoai" class="form-control" placeholder="0900 000 000">
                        </div>
                        <div class="col-md-6">
                            <label for="diachi" class="form-label small fw-semibold mb-1">Địa chỉ</label>
                            <input id="diachi" name="diachi" class="form-control" placeholder="Số nhà, đường, quận/huyện, tỉnh/thành">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-outline-gold px-3 py-2" type="submit">
                                <i class="fas fa-floppy-disk me-2"></i>Lưu nhà cung cấp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Lịch sử nhập hàng --}}
    <div class="vip-card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span><i class="fas fa-clock-rotate-left me-2"></i>Lịch sử nhập hàng</span>
            <span class="text-muted small fw-normal">Tổng {{ $receipts->total() }} phiếu nhập</span>
        </div>

        <div>
            @forelse($receipts as $receipt)
                <section class="kho-receipt px-3 px-md-4 py-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-bold">{{ $receipt->ma_phieu }}</div>
                            <div class="text-muted small d-flex flex-wrap gap-3 mt-1">
                                <span><i class="fas fa-truck me-1 text-gold"></i>{{ $receipt->NhaCungCap?->tennhacungcap ?: '—' }}</span>
                                <span><i class="fas fa-calendar-days me-1 text-gold"></i>{{ $receipt->ngaynhap?->format('d/m/Y H:i') }}</span>
                                <span><i class="fas fa-user me-1 text-gold"></i>{{ $receipt->User?->name ?: '—' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted small text-uppercase fw-bold">Tổng tiền</div>
                            <div class="fw-bold fs-5">{{ number_format($receipt->tongtien, 0, ',', '.') }} đ</div>
                        </div>
                    </div>

                    <div class="table-responsive kho-detail-wrap mt-3">
                        <table class="table kho-detail mb-0">
                            <thead>
                                <tr>
                                    <th>Tên sản phẩm</th>
                                    <th class="text-center" style="width:120px;">Số lượng</th>
                                    <th class="text-end" style="width:170px;">Đơn giá nhập</th>
                                    <th class="text-end" style="width:170px;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receipt->ChiTiet as $detail)
                                    <tr>
                                        <td>{{ $detail->tensanpham_snapshot ?: optional($detail->SanPham)->tensanpham ?: 'Sản phẩm đã xóa' }}</td>
                                        <td class="text-center">{{ $detail->soluong }}</td>
                                        <td class="text-end">{{ number_format((float) $detail->dongianhap, 0, ',', '.') }} đ</td>
                                        <td class="text-end fw-semibold">{{ number_format($detail->soluong * (float) $detail->dongianhap, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="fas fa-inbox d-block fs-3 mb-2 text-gold"></i>
                    Chưa có phiếu nhập.
                </div>
            @endforelse
        </div>

        @if($receipts->hasPages())
            <div class="card-body border-top">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
@endsection
