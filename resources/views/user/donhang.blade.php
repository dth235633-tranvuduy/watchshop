@extends('layouts.frontend')

@section('title', 'Đơn Hàng Của Tôi - Watch Shop')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">Đơn Hàng Của Tôi</h2>
    <a href="{{ route('user.home') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i>Trang cá nhân</a>
</div>

@if($user->DonHang->count() > 0)
    <div class="table-responsive card border-0 shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:var(--ws-black);color:#fff;">
                <tr>
                    <th class="text-center" style="width:60px;">#</th>
                    <th>Mã đơn</th>
                    <th class="text-center" style="width:150px;">Ngày đặt</th>
                    <th class="text-end" style="width:150px;">Tổng tiền</th>
                    <th class="text-center" style="width:140px;">Tình trạng</th>
                    <th class="text-center" style="width:110px;">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->DonHang as $dh)
                    @php
                        $tong = $dh->DonHang_ChiTiet->sum(fn ($ct) => $ct->soluongban * $ct->dongiaban);
                        $statusClass = optional($dh->TinhTrang)->tinhtrang === 'Đã giao' ? 'success' : (optional($dh->TinhTrang)->tinhtrang === 'Đang giao' ? 'warning' : 'secondary');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><strong>#{{ $dh->id }}</strong></td>
                        <td class="text-center">{{ optional($dh->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-end fw-semibold">{{ number_format($tong, 0, ',', '.') }} đ</td>
                        <td class="text-center"><span class="badge bg-{{ $statusClass }}">{{ optional($dh->TinhTrang)->tinhtrang }}</span></td>
                        <td class="text-center">
                            <button class="btn btn-outline-gold btn-sm" data-bs-toggle="collapse" data-bs-target="#chiTiet{{ $dh->id }}"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="p-0 border-0">
                            <div class="collapse" id="chiTiet{{ $dh->id }}">
                                <div class="p-3" style="background:var(--ws-cream);">
                                    <p class="mb-2 small"><strong>Người nhận:</strong> {{ $dh->tennguoinhan ?: $user->name }} · <strong>SĐT:</strong> {{ $dh->dienthoaigiaohang }}</p>
                                    <p class="mb-3 small"><strong>Địa chỉ:</strong> {{ $dh->diachigiaohang }} @if($dh->ghichu) · <strong>Ghi chú:</strong> {{ $dh->ghichu }} @endif</p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered bg-white mb-0">
                                            <thead style="background:#fff;">
                                                <tr>
                                                    <th class="text-center" style="width:50px;">#</th>
                                                    <th>Tên sản phẩm</th>
                                                    <th class="text-center" style="width:100px;">SL</th>
                                                    <th class="text-end" style="width:130px;">Đơn giá</th>
                                                    <th class="text-end" style="width:140px;">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dh->DonHang_ChiTiet as $ct)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td>{{ optional($ct->SanPham)->tensanpham ?? 'Sản phẩm đã xóa' }}</td>
                                                        <td class="text-center">{{ $ct->soluongban }}</td>
                                                        <td class="text-end">{{ number_format($ct->dongiaban, 0, ',', '.') }} đ</td>
                                                        <td class="text-end">{{ number_format($ct->soluongban * $ct->dongiaban, 0, ',', '.') }} đ</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="fw-bold">
                                                    <td colspan="4" class="text-end">Tổng tiền:</td>
                                                    <td class="text-end">{{ number_format($tong, 0, ',', '.') }} đ</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
        <h4>Bạn chưa có đơn hàng nào</h4>
        <p class="text-muted">Hãy chọn sản phẩm yêu thích và đặt hàng ngay hôm nay.</p>
        <a href="{{ route('frontend.sanpham') }}" class="btn btn-gold"><i class="fas fa-tag me-1"></i>Xem sản phẩm</a>
    </div>
@endif
@endsection