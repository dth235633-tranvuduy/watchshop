<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['tieu_de', 'mo_ta', 'hinh_anh', 'duong_dan', 'thu_tu', 'trang_thai'];

    protected function casts(): array
    {
        return ['trang_thai' => 'boolean'];
    }
}
