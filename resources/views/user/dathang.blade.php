@extends('layouts.frontend')

@section('title', 'Đặt hàng - Watch Shop')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Thông tin đặt hàng</h2>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="vip-card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('user.dathang') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="tennguoinhan" class="form-label fw-semibold">Họ tên người nhận</label>
                            <input id="tennguoinhan" name="tennguoinhan" type="text"
                                   value="{{ old('tennguoinhan', $user->name) }}"
                                   class="form-control bg-light @error('tennguoinhan') is-invalid @enderror"
                                   readonly required>
                            @error('tennguoinhan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="dienthoaigiaohang" class="form-label fw-semibold">
                                Điện thoại giao hàng <span class="text-danger">*</span>
                            </label>
                            <input id="dienthoaigiaohang" name="dienthoaigiaohang" type="tel"
                                   value="{{ old('dienthoaigiaohang') }}"
                                   class="form-control @error('dienthoaigiaohang') is-invalid @enderror"
                                   maxlength="255" required>
                            @error('dienthoaigiaohang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="diachigiaohang" class="form-label fw-semibold">
                                Địa chỉ giao hàng <span class="text-danger">*</span>
                            </label>
                            <textarea id="diachigiaohang" name="diachigiaohang" rows="4"
                                      class="form-control @error('diachigiaohang') is-invalid @enderror"
                                      maxlength="255" required>{{ old('diachigiaohang') }}</textarea>
                            @error('diachigiaohang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="ghichu" class="form-label fw-semibold">Ghi chú</label>
                            <textarea id="ghichu" name="ghichu" rows="3"
                                      class="form-control @error('ghichu') is-invalid @enderror"
                                      maxlength="1000">{{ old('ghichu') }}</textarea>
                            @error('ghichu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <fieldset class="mb-4">
                            <legend class="form-label fw-semibold">Phương thức thanh toán</legend>
                            <div class="d-grid gap-2">
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-2">
                                    <input type="radio" name="phuongthucthanhtoan" value="cod"
                                           @checked(old('phuongthucthanhtoan', 'cod') === 'cod') required>
                                    <span><strong>Thanh toán khi nhận hàng (COD)</strong><br><small class="text-muted">Thanh toán khi shipper giao hàng</small></span>
                                </label>
                                <label class="border rounded-3 p-3 d-flex align-items-center gap-2">
                                    <input type="radio" name="phuongthucthanhtoan" value="bank"
                                           @checked(old('phuongthucthanhtoan') === 'bank')>
                                    <span><strong>Chuyển khoản ngân hàng</strong><br><small class="text-muted">Thanh toán sau khi xác nhận đơn hàng</small></span>
                                </label>
                            </div>
                            @error('phuongthucthanhtoan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </fieldset>

                        @php
                            $bank = config('payment.bank');
                            $bankQrUrl = $bank['account_no']
                                ? 'https://img.vietqr.io/image/' . urlencode($bank['bank_id']) . '-' . urlencode($bank['account_no']) . '-' . urlencode($bank['template']) . '.png?'
                                    . 'amount=' . (int) round($total)
                                    . '&addInfo=' . urlencode('Thanh toan don hang')
                                    . '&accountName=' . urlencode($bank['account_name'])
                                : null;
                        @endphp
                        <div id="bank-payment-panel" class="border rounded-3 p-3 mb-4 d-none">
                            <div class="row align-items-center g-3">
                                <div class="col-sm-5 text-center">
                                    @if($bankQrUrl)
                                        <img src="{{ $bankQrUrl }}" alt="Mã QR thanh toán VietQR"
                                             class="img-fluid rounded border bg-white"
                                             style="max-width:240px;">
                                    @else
                                        <div class="alert alert-warning mb-0">Chưa cấu hình tài khoản ngân hàng.</div>
                                    @endif
                                </div>
                                <div class="col-sm-7">
                                    <h6 class="fw-bold mb-2">Quét mã để chuyển khoản</h6>
                                    @if($bank['account_no'])
                                        <p class="mb-1"><strong>Ngân hàng:</strong> {{ $bank['bank_id'] }}</p>
                                        <p class="mb-1"><strong>Số tài khoản:</strong> {{ $bank['account_no'] }}</p>
                                        @if($bank['account_name'])
                                            <p class="mb-1"><strong>Chủ tài khoản:</strong> {{ $bank['account_name'] }}</p>
                                        @endif
                                    @endif
                                    <p class="mb-0"><strong>Số tiền:</strong>
                                        <span class="text-gold fs-5">{{ number_format($total, 0, ',', '.') }} đ</span>
                                    </p>
                                    <small class="text-muted">Nội dung: Thanh toán đơn hàng</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-gold">
                                <i class="fas fa-check me-1"></i>Đặt hàng
                            </button>
                            <a href="{{ route('frontend.giohang') }}" class="btn btn-outline-secondary">Quay lại giỏ hàng</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="vip-card p-4 sticky-top" style="top:100px;">
                <h5 class="mb-3">Đơn hàng của bạn</h5>

                @foreach($cart as $item)
                    <div class="d-flex justify-content-between gap-3 border-bottom py-2">
                        <span>{{ $item['name'] }} × {{ $item['qty'] }}</span>
                        <span class="text-nowrap fw-semibold">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }} đ</span>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('user.voucher.apdung') }}" class="input-group mt-3">
                    @csrf
                    <input name="code" class="form-control" placeholder="Nhập mã giảm giá"
                           value="{{ $voucher['code'] ?? '' }}" maxlength="50">
                    <button class="btn btn-outline-gold" type="submit">Áp dụng</button>
                </form>

                <div class="d-flex justify-content-between mt-3">
                    <span>Tổng tiền</span>
                    <strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
                </div>
                @if($voucher)
                    <div class="d-flex justify-content-between mt-2 text-success">
                        <span>Giảm giá ({{ $voucher['code'] }})</span>
                        <strong>−{{ number_format($discount, 0, ',', '.') }} đ</strong>
                    </div>
                @endif
                <div class="d-flex justify-content-between mt-2">
                    <span>Thuế VAT (10%)</span>
                    <strong>{{ number_format($vat, 0, ',', '.') }} đ</strong>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Phí vận chuyển</span>
                    <strong class="text-success">Miễn phí</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Tổng ước tính</span>
                    <span class="text-gold">{{ number_format($total, 0, ',', '.') }} đ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bankPanel = document.getElementById('bank-payment-panel');
    const paymentInputs = document.querySelectorAll('input[name="phuongthucthanhtoan"]');

    function toggleBankPanel() {
        const selected = document.querySelector('input[name="phuongthucthanhtoan"]:checked');
        bankPanel?.classList.toggle('d-none', !selected || selected.value !== 'bank');
    }

    paymentInputs.forEach(function (input) {
        input.addEventListener('change', toggleBankPanel);
    });
    toggleBankPanel();
});
</script>
@endpush
