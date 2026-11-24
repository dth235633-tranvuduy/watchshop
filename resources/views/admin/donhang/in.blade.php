<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hóa đơn #{{ $donhang->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; margin: 0; background: #fff; }
        .invoice { max-width: 900px; margin: 24px auto; padding: 32px; }
        .header { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #c9a227; padding-bottom: 16px; }
        .brand { color: #c9a227; font-size: 26px; font-weight: 700; }
        .muted { color: #666; }
        .invoice-info { text-align: right; }
        .section { margin-top: 20px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .box { border: 1px solid #ddd; border-radius: 6px; padding: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #ccc; padding: 9px; }
        th { background: #f3f3f3; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .summary { width: 380px; margin-left: auto; margin-top: 16px; }
        .summary td { border: 0; padding: 5px 8px; }
        .summary .grand td { border-top: 2px solid #222; font-size: 18px; font-weight: 700; }
        .actions { max-width: 900px; margin: 16px auto 0; text-align: right; }
        .btn { border: 0; border-radius: 4px; cursor: pointer; padding: 8px 14px; background: #c9a227; color: #fff; }
        @media print {
            .actions { display: none; }
            .invoice { margin: 0; padding: 12px; }
        }
        @page { size: A4; margin: 10mm; }
    </style>
</head>
@php
    $subtotal = $donhang->DonHang_ChiTiet->sum(fn ($detail) => $detail->soluongban * $detail->dongiaban);
    $discount = (float) $donhang->tiengiam;
    $vat = round(max(0, $subtotal - $discount) * 0.10, 2);
    $shipping = 0;
@endphp
<body>
    <div class="actions">
        <button type="button" class="btn" onclick="window.print()">In hóa đơn</button>
    </div>

    <main class="invoice">
        <div class="header">
            <div>
                <div class="brand">WatchShop</div>
                <div class="muted">HÓA ĐƠN BÁN HÀNG</div>
            </div>
            <div class="invoice-info">
                <div><strong>Mã đơn:</strong> #{{ $donhang->id }}</div>
                <div><strong>Ngày đặt:</strong> {{ optional($donhang->created_at)->format('d/m/Y H:i') }}</div>
                <div><strong>Tình trạng:</strong> {{ optional($donhang->TinhTrang)->tinhtrang }}</div>
            </div>
        </div>

        <div class="section grid">
            <div class="box">
                <strong>Khách hàng</strong>
                <div>{{ optional($donhang->User)->name }}</div>
            </div>
            <div class="box">
                <strong>Thông tin nhận hàng</strong>
                <div>Người nhận: {{ $donhang->tennguoinhan ?: optional($donhang->User)->name }}</div>
                <div>Điện thoại: {{ $donhang->dienthoaigiaohang }}</div>
                <div>Địa chỉ: {{ $donhang->diachigiaohang }}</div>
                <div>Thanh toán: {{ $donhang->phuongthucthanhtoan === 'bank' ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)' }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width:50px;">#</th>
                    <th>Tên sản phẩm</th>
                    <th class="text-center" style="width:90px;">Số lượng</th>
                    <th class="text-end" style="width:150px;">Đơn giá</th>
                    <th class="text-end" style="width:170px;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donhang->DonHang_ChiTiet as $detail)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $detail->tensanpham_snapshot ?: optional($detail->SanPham)->tensanpham ?: 'Sản phẩm đã xóa' }}</td>
                        <td class="text-center">{{ $detail->soluongban }}</td>
                        <td class="text-end">{{ number_format((float) $detail->dongiaban, 0, ',', '.') }} đ</td>
                        <td class="text-end">{{ number_format($detail->soluongban * (float) $detail->dongiaban, 0, ',', '.') }} đ</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center muted">Không có sản phẩm</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="summary">
            <tbody>
                <tr>
                    <td>Tổng tiền hàng</td>
                    <td class="text-end">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                </tr>
                <tr>
                    <td>Giảm giá</td>
                    <td class="text-end">-{{ number_format($discount, 0, ',', '.') }} đ</td>
                </tr>
                <tr>
                    <td>Thuế VAT (10%)</td>
                    <td class="text-end">{{ number_format($vat, 0, ',', '.') }} đ</td>
                </tr>
                <tr>
                    <td>Phí vận chuyển</td>
                    <td class="text-end">Miễn phí</td>
                </tr>
                <tr class="grand">
                    <td>Tổng thanh toán</td>
                    <td class="text-end">{{ number_format((float) $donhang->tongtien, 0, ',', '.') }} đ</td>
                </tr>
            </tbody>
        </table>

        @if($donhang->ghichu)
            <div class="section"><strong>Ghi chú:</strong> {{ $donhang->ghichu }}</div>
        @endif
        @if($donhang->lydo_huy)
            <div class="section"><strong>Lý do hủy:</strong> {{ $donhang->lydo_huy }}</div>
        @endif
    </main>
</body>
</html>
