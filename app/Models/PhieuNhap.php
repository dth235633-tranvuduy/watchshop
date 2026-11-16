<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhieuNhap extends Model
{
    protected $table = 'phieunhap';

    protected $fillable = ['nhacungcap_id', 'user_id', 'ma_phieu', 'ngaynhap', 'tongtien', 'ghichu'];

    protected function casts(): array
    {
        return ['ngaynhap' => 'datetime', 'tongtien' => 'decimal:2'];
    }

    public function NhaCungCap(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'nhacungcap_id');
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ChiTiet(): HasMany
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'phieunhap_id');
    }
}
