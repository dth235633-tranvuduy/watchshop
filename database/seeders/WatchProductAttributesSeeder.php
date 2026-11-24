<?php

namespace Database\Seeders;

use App\Models\ChatLieuDay;
use App\Models\PhongCach;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WatchProductAttributesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Dây da',
            'Dây kim loại',
            'Thép không gỉ',
            'Thép không gỉ 316L',
            'Thép không gỉ dạng lưới',
            'Thép không gỉ mạ vàng',
            'Thép không gỉ mạ vàng hồng',
            'Thép không gỉ mạ PVD',
            'Thép không gỉ 316L Ceramic',
            'Thép không gỉ mặt Ceramic',
            'Dây vải',
            'Dây cao su',
        ] as $name) {
            ChatLieuDay::firstOrCreate(
                ['tenchatlieu_slug' => Str::slug($name)],
                ['tenchatlieu' => $name]
            );
        }

        foreach ([
            'Sang trọng',
            'Thể thao',
            'Thể thao sang trọng',
            'Quân đội',
            'Thời trang',
            'Du lịch',
            'Tráng gương',
        ] as $name) {
            PhongCach::firstOrCreate(
                ['tenphongcach_slug' => Str::slug($name)],
                ['tenphongcach' => $name]
            );
        }
    }
}
