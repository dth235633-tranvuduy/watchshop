@extends('layouts.frontend')

@section('title', 'Đặt Hàng Thành Công - Watch Shop')

@section('content')
<div class="text-center py-5">
    <i class="fas fa-check-circle fa-4x mb-3" style="color:var(--ws-gold);"></i>
    <h2 class="mb-2">Đặt hàng thành công</h2>
    <p class="text-muted mb-4">Cảm ơn bạn đã mua hàng. Đơn hàng đang được xử lý và sẽ sớm được giao đến bạn.</p>

    @if($order && $order->phuongthucthanhtoan === 'bank')
        @php
            $bank = config('payment.bank');
            $amount = (int) round((float) $order->tongtien);
            $transferInfo = 'Thanh toan don hang #' . $order->id;
            $bankQrUrl = $bank['account_no']
                ? 'https://img.vietqr.io/image/' . urlencode($bank['bank_id']) . '-' . urlencode($bank['account_no']) . '-' . urlencode($bank['template']) . '.png?'
                    . 'amount=' . $amount
                    . '&addInfo=' . urlencode($transferInfo)
                    . '&accountName=' . urlencode($bank['account_name'])
                : null;
        @endphp
        <div class="vip-card mx-auto p-4 mb-4 text-start" style="max-width:720px;">
            <h5 class="text-center mb-3">Quét mã VietQR để thanh toán đơn hàng #{{ $order->id }}</h5>
            <div class="row align-items-center g-3">
                <div class="col-sm-5 text-center">
                    @if($bankQrUrl)
                        <img src="{{ $bankQrUrl }}" alt="Mã QR thanh toán đơn hàng #{{ $order->id }}"
                             class="img-fluid rounded border bg-white"
                             style="max-width:240px;">
                    @else
                        <div class="alert alert-warning mb-0">Chưa cấu hình tài khoản ngân hàng.</div>
                    @endif
                </div>
                <div class="col-sm-7">
                    @if($bank['account_no'])
                        <p class="mb-1"><strong>Ngân hàng:</strong> {{ $bank['bank_id'] }}</p>
                        <p class="mb-1"><strong>Số tài khoản:</strong> {{ $bank['account_no'] }}</p>
                        @if($bank['account_name'])
                            <p class="mb-1"><strong>Chủ tài khoản:</strong> {{ $bank['account_name'] }}</p>
                        @endif
                    @endif
                    <p class="mb-1"><strong>Số tiền:</strong>
                        <span class="text-gold fs-5">{{ number_format($amount, 0, ',', '.') }} đ</span>
                    </p>
                    <p class="mb-0"><strong>Nội dung:</strong> {{ $transferInfo }}</p>
                </div>
            </div>
        </div>
    @endif

    <a href="{{ route('user.donhang') }}" class="btn btn-gold me-2"><i class="fas fa-shopping-bag me-1"></i>Xem đơn hàng</a>
    <a href="{{ route('frontend.sanpham') }}" class="btn btn-outline-dark">Tiếp tục mua sắm</a>
</div>
@endsection