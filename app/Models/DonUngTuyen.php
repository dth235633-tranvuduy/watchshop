<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonUngTuyen extends Model
{
    protected $table = 'donungtuyen';

    protected $fillable = [
        'hoten',
        'email',
        'sodienthoai',
        'vitri',
        'cv_path',
        'trangthai',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}