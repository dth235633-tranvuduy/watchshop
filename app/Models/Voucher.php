<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['code', 'loai_giam', 'giatrigiam', 'dieukien_toithieu', 'ngay_batdau', 'ngay_ketthuc', 'soluong', 'daludung', 'trangthai'];

    protected function casts(): array
    {
        return [
            'giatrigiam' => 'decimal:2',
            'dieukien_toithieu' => 'decimal:2',
            'ngay_batdau' => 'datetime',
            'ngay_ketthuc' => 'datetime',
            'trangthai' => 'boolean',
        ];
    }

    public function isUsable(float $subtotal): bool
    {
        $now = now();

        return $this->trangthai
            && $subtotal >= (float) $this->dieukien_toithieu
            && (! $this->ngay_batdau || $now->greaterThanOrEqualTo($this->ngay_batdau))
            && (! $this->ngay_ketthuc || $now->lessThanOrEqualTo($this->ngay_ketthuc))
            && ($this->soluong === null || $this->daludung < $this->soluong);
    }

    public function discountFor(float $subtotal): float
    {
        if ($this->loai_giam === 'phantram') {
            return min($subtotal, $subtotal * (float) $this->giatrigiam / 100);
        }

        return min($subtotal, (float) $this->giatrigiam);
    }
}
