<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SanPham extends Model
{
    protected $table = 'sanpham';

    protected $fillable = [
        'loaisanpham_id',
        'hangsanxuat_id',
        'tensanpham',
        'tensanpham_slug',
        'soluong',
        'dongia',
        'giamgia',
        'danhgia',
        'hinhanh',
        'motasanpham',
        'chatlieuday',
        'matkinh',
        'dochieunuoc',
    ];

    protected function casts(): array
    {
        return [
            'dongia' => 'decimal:2',
            'giamgia' => 'integer',
            'danhgia' => 'decimal:1',
        ];
    }

    public function LoaiSanPham(): BelongsTo
    {
        return $this->belongsTo(LoaiSanPham::class, 'loaisanpham_id');
    }

    public function HangSanXuat(): BelongsTo
    {
        return $this->belongsTo(HangSanXuat::class, 'hangsanxuat_id');
    }

    public function DonHang_ChiTiet(): HasMany
    {
        return $this->hasMany(DonHang_ChiTiet::class, 'sanpham_id');
    }
}
