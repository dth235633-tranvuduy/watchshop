<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonHang extends Model
{
    protected $table = 'donhang';

    protected $fillable = ['user_id', 'voucher_id', 'tinhtrang_id', 'tennguoinhan', 'dienthoaigiaohang', 'diachigiaohang', 'ghichu', 'lydo_huy', 'phuongthucthanhtoan', 'tiengiam', 'tongtien'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'tiengiam' => 'decimal:2',
            'tongtien' => 'decimal:2',
        ];
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function TinhTrang(): BelongsTo
    {
        return $this->belongsTo(TinhTrang::class, 'tinhtrang_id');
    }

    public function Voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function DonHang_ChiTiet(): HasMany
    {
        return $this->hasMany(DonHang_ChiTiet::class, 'donhang_id');
    }
}
