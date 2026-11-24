<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLieuDay extends Model
{
    protected $table = 'chatlieuday';

    protected $fillable = ['tenchatlieu', 'tenchatlieu_slug'];
}
