<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DanhGia extends Model
{
    protected $table = 'danhgia';

    protected $fillable = ['sanpham_id', 'user_id', 'sosao', 'binhluan', 'trangthai', 'phanhoi_admin', 'phanhoi_luc'];

    protected function casts(): array
    {
        return ['phanhoi_luc' => 'datetime'];
    }

    public function SanPham(): BelongsTo
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
