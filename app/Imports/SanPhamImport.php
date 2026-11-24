<?php

namespace App\Imports;

use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use App\Services\StockReceiptService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SanPhamImport implements ToModel, WithHeadingRow
{
    public int $imported = 0;

    public int $skipped = 0;

    public function __construct(private readonly StockReceiptService $stockReceipts) {}

    public function model(array $row): ?SanPham
    {
        $name = trim((string) ($row['tensanpham'] ?? $row['ten_san_pham'] ?? $row['name'] ?? $row['product_name'] ?? ''));

        if ($name === '' || SanPham::where('tensanpham', $name)->exists()) {
            $this->skipped++;

            return null;
        }

        $loai = $this->findOrCreateCategory($row);
        $hang = $this->findOrCreateBrand($row);

        if (! $loai || ! $hang) {
            $this->skipped++;

            return null;
        }

        $quantity = max(0, (int) $this->parseNumber($row['soluong'] ?? $row['so_luong'] ?? $row['quantity'] ?? $row['qty'] ?? 0));
        $price = max(0, $this->parseNumber($row['dongia'] ?? $row['don_gia'] ?? $row['price'] ?? 0));
        $discount = max(0, min(100, (int) $this->parseNumber($row['giamgia'] ?? $row['giam_gia'] ?? $row['discount'] ?? 0)));
        $rating = max(0, min(5, $this->parseNumber($row['danhgia'] ?? $row['danh_gia'] ?? $row['rating'] ?? 0)));

        $product = null;

        DB::transaction(function () use (&$product, $name, $loai, $hang, $quantity, $price, $discount, $rating, $row): void {
            $product = new SanPham([
                'loaisanpham_id' => $loai->id,
                'hangsanxuat_id' => $hang->id,
                'tensanpham' => $name,
                'tensanpham_slug' => Str::slug($name),
                'soluong' => 0,
                'dongia' => $price,
                'giamgia' => $discount,
                'danhgia' => $rating,
                'motasanpham' => $this->stringValue($row['motasanpham'] ?? $row['mo_ta'] ?? $row['description'] ?? null),
                'chatlieuday' => $this->stringValue($row['chatlieuday'] ?? $row['chat_lieu_day'] ?? $row['strap'] ?? null),
                'matkinh' => $this->stringValue($row['matkinh'] ?? $row['mat_kinh'] ?? $row['glass'] ?? null),
                'dochieunuoc' => $this->stringValue($row['dochieunuoc'] ?? $row['do_chiu_nuoc'] ?? $row['water_resistance'] ?? null),
                'hinhanh' => null,
            ]);
            $product->save();

            if ($quantity > 0) {
                $this->stockReceipts->receive(
                    $this->stockReceipts->defaultExcelSupplier()->id,
                    [['sanpham_id' => $product->id, 'soluong' => $quantity, 'dongianhap' => $price]],
                    now()->toDateTimeString(),
                    'Nhập sản phẩm từ file Excel',
                );
            }
        });

        $this->imported++;

        return $product;
    }

    private function findOrCreateCategory(array $row): ?LoaiSanPham
    {
        $value = trim((string) ($row['loaisanpham'] ?? $row['loai_san_pham'] ?? $row['loaimay'] ?? $row['loai_may'] ?? $row['loai'] ?? $row['category'] ?? $row['machine_type'] ?? ''));

        if ($value === '') {
            return null;
        }

        $byId = ctype_digit($value) ? LoaiSanPham::find((int) $value) : null;

        if ($byId) {
            return $byId;
        }

        $slug = Str::slug($value);

        return LoaiSanPham::where('tenloai_slug', $slug)->orWhere('tenloai', $value)->first()
            ?? LoaiSanPham::create(['tenloai' => $value, 'tenloai_slug' => $slug]);
    }

    private function findOrCreateBrand(array $row): ?HangSanXuat
    {
        $value = trim((string) ($row['hangsanxuat'] ?? $row['hang_san_xuat'] ?? $row['thuonghieu'] ?? $row['thuong_hieu'] ?? $row['hang'] ?? $row['brand'] ?? ''));

        if ($value === '') {
            return null;
        }

        $byId = ctype_digit($value) ? HangSanXuat::find((int) $value) : null;

        if ($byId) {
            return $byId;
        }

        $slug = Str::slug($value);

        return HangSanXuat::where('tenhang_slug', $slug)->orWhere('tenhang', $value)->first()
            ?? HangSanXuat::create(['tenhang' => $value, 'tenhang_slug' => $slug, 'hinhanh' => null]);
    }

    private function stringValue(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function parseNumber(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $value = trim((string) $value);
        $value = str_replace(["\u{00A0}", ' ', '₫', 'đ'], '', $value);
        $value = preg_replace('/[^0-9,.]/u', '', $value) ?? '';

        if ($value === '') {
            return 0;
        }

        $lastDot = strrpos($value, '.');
        $lastComma = strrpos($value, ',');

        if ($lastDot !== false && $lastComma !== false) {
            $value = $lastDot > $lastComma
                ? str_replace(',', '', $value)
                : str_replace('.', '', str_replace(',', '.', $value));
        } elseif ($lastComma !== false) {
            $value = substr_count($value, ',') === 1 && strlen($value) - $lastComma - 1 <= 2
                ? str_replace(',', '.', $value)
                : str_replace(',', '', $value);
        } elseif (substr_count($value, '.') > 1) {
            $value = str_replace('.', '', $value);
        } elseif ($lastDot !== false && strlen($value) - $lastDot - 1 === 3) {
            $value = str_replace('.', '', $value);
        }

        return is_numeric($value) ? (float) $value : 0;
    }
}
