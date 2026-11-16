<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhaCungCap extends Model
{
    protected $table = 'nhacungcap';

    protected $fillable = ['tennhacungcap', 'email', 'sodienthoai', 'diachi', 'trangthai'];

    protected function casts(): array
    {
        return ['trangthai' => 'boolean'];
    }

    public function PhieuNhap(): HasMany
    {
        return $this->hasMany(PhieuNhap::class, 'nhacungcap_id');
    }
}
