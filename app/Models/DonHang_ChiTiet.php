<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonHang_ChiTiet extends Model
{
    protected $table = 'donhang_chitiet';

    protected $fillable = [
        'donhang_id',
        'sanpham_id',
        'tensanpham_snapshot',
        'soluongban',
        'dongiaban',
    ];

    public function DonHang(): BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'donhang_id');
    }

    public function SanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }
}
