<?php

namespace App\Models;

use Database\Factories\DonHangLichSuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonHangLichSu extends Model
{
    /** @use HasFactory<DonHangLichSuFactory> */
    use HasFactory;

    protected $table = 'donhang_lichsu';

    protected $fillable = ['donhang_id', 'tinhtrang_id', 'ten_trang_thai', 'user_id', 'ghichu'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function DonHang(): BelongsTo
    {
        return $this->belongsTo(DonHang::class, 'donhang_id');
    }

    public function TinhTrang(): BelongsTo
    {
        return $this->belongsTo(TinhTrang::class, 'tinhtrang_id');
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
