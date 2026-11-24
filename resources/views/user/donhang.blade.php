@extends('layouts.frontend')

@section('title', 'Đơn Hàng Của Tôi - Watch Shop')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="section-title mb-0">Đơn Hàng Của Tôi</h2>
    <a href="{{ route('user.home') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i>Trang cá nhân</a>
</div>

@php
    $danhGiaTheoSanPham = $user->DanhGia->keyBy('sanpham_id');
@endphp

@if($user->DonHang->count() > 0)
    <div class="table-responsive card border-0 shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:var(--ws-black);color:#fff;">
                <tr>
                    <th class="text-center" style="width:60px;">#</th>
                    <th>Mã đơn</th>
                    <th class="text-center" style="width:150px;">Ngày đặt</th>
                    <th class="text-end" style="width:150px;">Tổng tiền</th>
                    <th class="text-center" style="width:190px;">Tình trạng</th>
                    <th class="text-center" style="width:110px;">Chi tiết</th>
                    <th class="text-center" style="width:130px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user->DonHang as $dh)
                    @php
                        $tong = $dh->DonHang_ChiTiet->sum(fn ($ct) => $ct->soluongban * $ct->dongiaban);
                        $statusName = optional($dh->TinhTrang)->tinhtrang;
                        $statusClass = match ($statusName) {
                            'Đã giao' => 'success',
                            'Đã hủy' => 'danger',
                            'Đang giao' => 'primary',
                            'Đã xác nhận' => 'info',
                            default => 'warning',
                        };
                        $paymentMethodName = $dh->phuongthucthanhtoan === 'bank'
                            ? 'Thanh toán chuyển khoản'
                            : 'Thanh toán khi nhận hàng (COD)';
                        $coTheHuy = in_array($statusName, ['Chờ xác nhận', 'Đã xác nhận'], true) && ! $dh->da_thanh_toan;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><strong>#{{ $dh->id }}</strong></td>
                        <td class="text-center">{{ optional($dh->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-end fw-semibold">{{ number_format($tong, 0, ',', '.') }} đ</td>
                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <span class="badge bg-{{ $statusClass }}">{{ optional($dh->TinhTrang)->tinhtrang }}</span>
                                <span class="small text-muted">{{ $paymentMethodName }}</span>
                                @if($dh->da_thanh_toan)
                                    <span class="small fw-semibold text-success"><i class="fas fa-check-circle me-1"></i>Đã thanh toán</span>
                                @elseif($dh->phuongthucthanhtoan === 'bank' && $statusName !== 'Đã hủy')
                                    <span class="small fw-semibold text-warning"><i class="fas fa-clock me-1"></i>Chưa thanh toán</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-outline-gold btn-sm" data-bs-toggle="collapse" data-bs-target="#chiTiet{{ $dh->id }}"><i class="fas fa-eye"></i></button>
                        </td>
                        <td class="text-center">
                            @if($coTheHuy)
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#huyDon{{ $dh->id }}">
                                    <i class="fas fa-times me-1"></i>Hủy đơn
                                </button>
                            @elseif($statusName === 'Đã hủy')
                                <span class="small text-danger fw-semibold">Đã hủy</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" class="p-0 border-0">
                            <div class="collapse" id="chiTiet{{ $dh->id }}">
                                <div class="p-3" style="background:var(--ws-cream);">
                                    <p class="mb-2 small"><strong>Người nhận:</strong> {{ $dh->tennguoinhan ?: $user->name }} · <strong>SĐT:</strong> {{ $dh->dienthoaigiaohang }}</p>
                                    <p class="mb-3 small"><strong>Địa chỉ:</strong> {{ $dh->diachigiaohang }} @if($dh->ghichu) · <strong>Ghi chú:</strong> {{ $dh->ghichu }} @endif</p>
                                    @if($dh->lydo_huy)
                                        <p class="mb-3 small text-danger"><strong>Lý do hủy:</strong> {{ $dh->lydo_huy }}</p>
                                    @endif
                                    @if($dh->phuongthucthanhtoan === 'bank' && ! $dh->da_thanh_toan && $statusName !== 'Đã hủy')
                                        @php
                                            $bank = config('payment.bank');
                                            $bankAmount = (int) round((float) $dh->tongtien);
                                            $bankTransferInfo = 'Thanh toan don hang #' . $dh->id;
                                            $bankQrUrl = $bank['account_no']
                                                ? 'https://img.vietqr.io/image/' . urlencode($bank['bank_id']) . '-' . urlencode($bank['account_no']) . '-' . urlencode($bank['template']) . '.png?'
                                                    . 'amount=' . $bankAmount
                                                    . '&addInfo=' . urlencode($bankTransferInfo)
                                                    . '&accountName=' . urlencode($bank['account_name'])
                                                : null;
                                        @endphp
                                        <div class="border rounded bg-white p-3 mb-3">
                                            <div class="row align-items-center g-3">
                                                <div class="col-sm-4 text-center">
                                                    @if($bankQrUrl)
                                                        <img src="{{ $bankQrUrl }}" alt="Mã QR VietQR đơn hàng #{{ $dh->id }}"
                                                             class="img-fluid rounded border" style="max-width:180px;">
                                                    @endif
                                                </div>
                                                <div class="col-sm-8">
                                                    <strong>Thanh toán chuyển khoản đơn #{{ $dh->id }}</strong>
                                                    @if($bank['account_no'])
                                                        <div class="small mt-1">Ngân hàng: {{ $bank['bank_id'] }} · STK: {{ $bank['account_no'] }}</div>
                                                    @endif
                                                    <div class="small">Số tiền: <strong class="text-gold">{{ number_format($bankAmount, 0, ',', '.') }} đ</strong></div>
                                                    <div class="small">Nội dung: {{ $bankTransferInfo }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
                                                        <td>
                                                            {{ $ct->tensanpham_snapshot ?: optional($ct->SanPham)->tensanpham ?: 'Sản phẩm đã xóa' }}
                                                            @if($statusName === 'Đã giao' && $ct->sanpham_id)
                                                                @php $dg = $danhGiaTheoSanPham->get($ct->sanpham_id); @endphp
                                                                <div class="mt-1">
                                                                    @if($dg)
                                                                        <span class="small">
                                                                            @for($sao = 1; $sao <= 5; $sao++)
                                                                                <i class="fas fa-star" style="color:{{ $sao <= $dg->sosao ? 'var(--ws-gold)' : '#ddd6c4' }};"></i>
                                                                            @endfor
                                                                        </span>
                                                                        <span class="small text-muted">
                                                                            · {{ $dg->trangthai === 'hien_thi' ? 'Đánh giá đã hiển thị' : ($dg->trangthai === 'an' ? 'Đánh giá đã bị ẩn' : 'Đánh giá đang chờ duyệt') }}
                                                                        </span>
                                                                    @else
                                                                        <button type="button" class="btn btn-sm btn-outline-gold"
                                                                                data-bs-toggle="modal" data-bs-target="#danhGia{{ $dh->id }}_{{ $ct->sanpham_id }}">
                                                                            <i class="fas fa-star me-1"></i>Đánh giá sản phẩm
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </td>
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
                                    @php
                                        $mocThoiGian = collect();
                                        $lichSuDon = $dh->lichSuTrangThai();
                                        if ($lichSuDon->isEmpty()) {
                                            $mocThoiGian->push(['ten' => 'Đơn hàng được tạo', 'luc' => $dh->created_at, 'ghichu' => null]);
                                            if ($statusName && $dh->updated_at && $dh->updated_at->gt($dh->created_at)) {
                                                $mocThoiGian->push(['ten' => $statusName, 'luc' => $dh->updated_at, 'ghichu' => null]);
                                            }
                                        } else {
                                            foreach ($lichSuDon as $ls) {
                                                $mocThoiGian->push(['ten' => $ls->ten_trang_thai, 'luc' => $ls->created_at, 'ghichu' => $ls->ghichu]);
                                            }
                                        }
                                    @endphp
                                    @if($statusName === 'Đã hủy')
                                        <div class="alert alert-danger mt-3 mb-0 py-2 small"><i class="fas fa-ban me-1"></i>Đơn hàng này đã bị hủy.</div>
                                    @else
                                        @php
                                            $cacBuoc = ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Đã giao'];
                                            $buocTimThay = array_search($statusName, $cacBuoc, true);
                                            $buocHienTai = $buocTimThay === false ? 0 : $buocTimThay;
                                        @endphp
                                        <div class="bg-white border rounded mt-3 px-3 pt-3 pb-2">
                                            <div class="d-flex">
                                                @foreach($cacBuoc as $i => $tenBuoc)
                                                    <div class="flex-fill text-center position-relative">
                                                        @if($i > 0)
                                                            <span class="position-absolute" style="top:11px; left:-50%; width:100%; border-top:2px solid {{ $i <= $buocHienTai ? 'var(--ws-gold)' : 'var(--ws-line)' }};"></span>
                                                        @endif
                                                        <span class="d-inline-flex justify-content-center align-items-center rounded-circle position-relative" style="width:24px; height:24px; background:{{ $i <= $buocHienTai ? 'var(--ws-gold)' : '#e9e5da' }}; color:{{ $i <= $buocHienTai ? '#fff' : '#8a8578' }};">
                                                            @if($i < $buocHienTai)
                                                                <i class="fas fa-check" style="font-size:11px;"></i>
                                                            @else
                                                                <span style="font-size:11px;">{{ $i + 1 }}</span>
                                                            @endif
                                                        </span>
                                                        <div class="small {{ $i === $buocHienTai ? 'fw-bold' : 'text-muted' }} mt-1">{{ $tenBuoc }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if($statusName === 'Đang giao')
                                                <p class="small text-center mb-1 mt-1" style="color:var(--ws-gold);"><i class="fas fa-truck me-1"></i>Đơn hàng đang trên đường giao — bạn để ý điện thoại để nhận hàng nhé.</p>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="mt-3 bg-white border rounded p-3">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-route me-1" style="color:var(--ws-gold);"></i>Theo dõi trạng thái đơn hàng</h6>
                                        <div class="ps-4" style="border-left:2px solid var(--ws-line); margin-left:6px;">
                                            @foreach($mocThoiGian as $moc)
                                                <div class="position-relative {{ $loop->last ? '' : 'pb-3' }}">
                                                    <span class="position-absolute rounded-circle" style="left:-30px; top:4px; width:12px; height:12px; background:{{ $loop->last ? 'var(--ws-gold)' : '#c8c2b4' }};"></span>
                                                    <div class="fw-semibold {{ $loop->last ? '' : 'text-muted' }}">{{ $moc['ten'] }}</div>
                                                    <div class="small text-muted">{{ optional($moc['luc'])->format('H:i · d/m/Y') }}</div>
                                                    @if($moc['ghichu'])
                                                        <div class="small">{{ $moc['ghichu'] }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
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

@foreach($user->DonHang as $dh)
    @if(in_array(optional($dh->TinhTrang)->tinhtrang, ['Chờ xác nhận', 'Đã xác nhận'], true) && ! $dh->da_thanh_toan)
        <div class="modal fade" id="huyDon{{ $dh->id }}" tabindex="-1"
             aria-labelledby="huyDonLabel{{ $dh->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('user.donhang.huy', $dh->id) }}" class="modal-content">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="huyDonLabel{{ $dh->id }}">Hủy đơn hàng #{{ $dh->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <label for="lydo_huy{{ $dh->id }}" class="form-label fw-semibold">
                            Lý do hủy đơn <span class="text-danger">*</span>
                        </label>
                        <textarea id="lydo_huy{{ $dh->id }}" name="lydo_huy" rows="4"
                                  class="form-control" maxlength="1000" required
                                  placeholder="Ví dụ: đặt nhầm sản phẩm, đổi ý, thay đổi địa chỉ...">{{ old('lydo_huy') }}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Không hủy</button>
                        <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach

<style>
    .star-rating .star-label { font-size: 30px; line-height: 1; color: #ddd6c4; cursor: pointer; transition: color .15s; }
    .star-rating input:checked ~ .star-label,
    .star-rating .star-label:hover,
    .star-rating .star-label:hover ~ .star-label { color: var(--ws-gold); }
</style>

@foreach($user->DonHang as $dh)
    @if(optional($dh->TinhTrang)->tinhtrang === 'Đã giao')
        @foreach($dh->DonHang_ChiTiet as $ct)
            @if($ct->sanpham_id && ! $danhGiaTheoSanPham->has($ct->sanpham_id))
                <div class="modal fade" id="danhGia{{ $dh->id }}_{{ $ct->sanpham_id }}" tabindex="-1"
                     aria-labelledby="danhGiaLabel{{ $dh->id }}_{{ $ct->sanpham_id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" action="{{ route('user.danhgia.luu', $ct->sanpham_id) }}" class="modal-content">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="danhGiaLabel{{ $dh->id }}_{{ $ct->sanpham_id }}">Đánh giá sản phẩm</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                            </div>
                            <div class="modal-body">
                                <p class="fw-semibold mb-2">{{ $ct->tensanpham_snapshot ?: optional($ct->SanPham)->tensanpham }}</p>
                                <label class="form-label fw-semibold d-block text-center">Bạn chấm mấy sao cho sản phẩm này?</label>
                                <div class="star-rating d-flex flex-row-reverse justify-content-center gap-1 mb-3">
                                    @for($sao = 5; $sao >= 1; $sao--)
                                        <input type="radio" class="btn-check" name="sosao"
                                               id="sao{{ $dh->id }}_{{ $ct->sanpham_id }}_{{ $sao }}"
                                               value="{{ $sao }}" {{ $sao === 5 ? 'checked' : '' }}>
                                        <label class="star-label" for="sao{{ $dh->id }}_{{ $ct->sanpham_id }}_{{ $sao }}" title="{{ $sao }} sao"><i class="fas fa-star"></i></label>
                                    @endfor
                                </div>
                                <label for="binhluan{{ $dh->id }}_{{ $ct->sanpham_id }}" class="form-label fw-semibold">Nhận xét của bạn</label>
                                <textarea id="binhluan{{ $dh->id }}_{{ $ct->sanpham_id }}" name="binhluan" rows="3"
                                          class="form-control" maxlength="2000"
                                          placeholder="Chia sẻ cảm nhận về sản phẩm (không bắt buộc)..."></textarea>
                                <p class="small text-muted mt-2 mb-0">Đánh giá sẽ được gửi cho quản trị viên duyệt trước khi hiển thị trên trang sản phẩm.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Để sau</button>
                                <button type="submit" class="btn btn-gold">Gửi đánh giá</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
@endforeach
@endsection
