<?php

namespace App\Imports;

use App\Models\SanPham;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PhieuNhapImport implements ToArray, WithHeadingRow
{
    public array $items = [];

    public array $errors = [];

    public function array(array $rows): void
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2; // header là dòng 1

            $tenSanPham = trim((string) ($row['tensanpham'] ?? $row['ten san pham'] ?? $row['ten_san_pham'] ?? $row['ten_sp'] ?? $row['product_name'] ?? ''));
            $soLuong = trim((string) ($row['soluong'] ?? $row['so luong'] ?? $row['so_luong'] ?? $row['quantity'] ?? $row['qty'] ?? '0'));
            $donGia = trim((string) ($row['dongianhap'] ?? $row['don gia'] ?? $row['don_gia'] ?? $row['price'] ?? $row['dongia nhap'] ?? '0'));

            if ($tenSanPham === '') {
                $this->errors[] = "Dòng {$line}: Tên sản phẩm không được để trống.";

                continue;
            }

            $product = SanPham::where('tensanpham', $tenSanPham)->first();

            if (! $product) {
                $this->errors[] = "Dòng {$line}: Sản phẩm \"{$tenSanPham}\" chưa tồn tại trong hệ thống.";

                continue;
            }

            $quantity = (int) preg_replace('/[^0-9]/', '', $soLuong);
            $price = (float) preg_replace('/[^0-9,.]/', '', str_replace('.', '', $donGia));

            if ($quantity < 1) {
                $this->errors[] = "Dòng {$line}: Số lượng phải lớn hơn 0.";

                continue;
            }

            if ($price < 0) {
                $this->errors[] = "Dòng {$line}: Đơn giá nhập không hợp lệ.";

                continue;
            }

            $this->items[] = [
                'line' => $line,
                'sanpham_id' => $product->id,
                'tensanpham' => $product->tensanpham,
                'soluong' => $quantity,
                'dongianhap' => $price,
                'thanhtien' => $quantity * $price,
            ];
        }
    }
}
