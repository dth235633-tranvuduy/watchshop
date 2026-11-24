<?php

namespace Database\Seeders;

use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportWatchMachineTypesSeeder extends Seeder
{
    private const MACHINE_TYPES = [
        'Automatic (Máy cơ tự động)',
        'Quartz (Máy pin - Điện tử)',
        'Eco-Drive (Năng lượng ánh sáng)',
        'Solar (Năng lượng ánh sáng)',
        'Chronograph (Đồng hồ bấm giờ thể thao)',
        'COSC (Chuẩn đồng hồ cơ tự động)',
        'Kính Sapphire',
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            $defaultType = null;

            foreach (self::MACHINE_TYPES as $name) {
                $type = LoaiSanPham::firstOrCreate(
                    ['tenloai_slug' => Str::slug($name)],
                    ['tenloai' => $name]
                );

                if ($name === 'Automatic (Máy cơ tự động)') {
                    $defaultType = $type;
                }
            }

            if (! $defaultType) {
                return;
            }

            $oldTypeIds = LoaiSanPham::whereNotIn(
                'tenloai',
                self::MACHINE_TYPES
            )->pluck('id');

            SanPham::whereIn('loaisanpham_id', $oldTypeIds)
                ->update(['loaisanpham_id' => $defaultType->id]);

            LoaiSanPham::whereIn('id', $oldTypeIds)->delete();
        });
    }
}
