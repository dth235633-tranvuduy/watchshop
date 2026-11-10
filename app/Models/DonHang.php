<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonHang extends Model
{
    protected $table = 'donhang';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function DonHang_ChiTiet(): HasMany
    {
        return $this->hasMany(DonHang_ChiTiet::class, 'donhang_id');
    }
}
