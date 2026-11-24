<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhongCach extends Model
{
    protected $table = 'phongcach';

    protected $fillable = ['tenphongcach', 'tenphongcach_slug'];
}
