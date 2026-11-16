<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietPhieuNhap extends Model
{
    protected $table = 'chitietphieunhap';

    protected $fillable = ['phieunhap_id', 'sanpham_id', 'soluong', 'dongianhap'];

    protected function casts(): array
    {
        return ['dongianhap' => 'decimal:2'];
    }

    public function PhieuNhap(): BelongsTo
    {
        return $this->belongsTo(PhieuNhap::class, 'phieunhap_id');
    }

    public function SanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }
}
