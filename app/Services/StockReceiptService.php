<?php

namespace App\Services;

use App\Models\NhaCungCap;
use App\Models\PhieuNhap;
use App\Models\SanPham;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockReceiptService
{
    /**
     * Hệ số tính giá bán từ đơn giá nhập: cộng 10% VAT và 5% phí dịch vụ.
     */
    public const HE_SO_GIA_BAN = 1.15;

    /**
     * Giá bán cho khách, suy ra từ đơn giá nhập và làm tròn đến nghìn đồng.
     */
    public static function giaBanTuGiaNhap(float $giaNhap): float
    {
        return round($giaNhap * self::HE_SO_GIA_BAN / 1000) * 1000;
    }

    /**
     * @param  array<int, array{sanpham_id: int, soluong: int, dongianhap: float}>  $items
     */
    public function receive(
        int $supplierId,
        array $items,
        ?string $receivedAt = null,
        ?string $note = null,
        bool $capNhatGiaBan = true,
    ): PhieuNhap {
        return DB::transaction(function () use ($supplierId, $items, $receivedAt, $note, $capNhatGiaBan): PhieuNhap {
            $receipt = PhieuNhap::create([
                'nhacungcap_id' => $supplierId,
                'user_id' => auth()->id(),
                'ma_phieu' => 'PN-'.now()->format('YmdHis').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
                'ngaynhap' => $receivedAt ?: now(),
                'ghichu' => $note,
                'tongtien' => 0,
            ]);

            $total = 0;

            foreach ($items as $item) {
                $product = SanPham::whereKey($item['sanpham_id'])->lockForUpdate()->first();

                if (! $product) {
                    throw new RuntimeException("Sản phẩm ID {$item['sanpham_id']} không tồn tại.");
                }

                $product->increment('soluong', $item['soluong']);

                if ($capNhatGiaBan && $item['dongianhap'] > 0) {
                    $product->update(['dongia' => self::giaBanTuGiaNhap($item['dongianhap'])]);
                }

                $receipt->ChiTiet()->create([
                    'sanpham_id' => $item['sanpham_id'],
                    'tensanpham_snapshot' => $product->tensanpham,
                    'soluong' => $item['soluong'],
                    'dongianhap' => $item['dongianhap'],
                ]);

                $total += $item['soluong'] * $item['dongianhap'];
            }

            $receipt->update(['tongtien' => $total]);

            return $receipt;
        });
    }

    public function defaultExcelSupplier(): NhaCungCap
    {
        return NhaCungCap::firstOrCreate(
            ['tennhacungcap' => 'Nhà cung cấp nhập Excel'],
            ['trangthai' => true],
        );
    }
}
