<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HangSanXuat extends Model
{
    protected $table = 'hangsanxuat';

    public function SanPham(): HasMany
    {
        return $this->hasMany(SanPham::class, 'hangsanxuat_id');
    }
}
