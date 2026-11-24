<?php

namespace Database\Seeders;

use App\Models\HangSanXuat;
use App\Models\SanPham;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemoveImageLessBrandsSeeder extends Seeder
{
    private const MERGE_TARGETS = [
        'Citizen Tsuyosa' => 'Citizen',
        'Tissot Chemin Des Tourelles' => 'Tissot',
        'Tissot PRX' => 'Tissot',
        'Tissot Ballade' => 'Tissot',
        'Tissot Le Locle' => 'Tissot',
        'Mido Multifort' => 'Mido',
        'Seiko 5 Sports' => 'Seiko',
        'Hamilton Khaki Field' => 'Hamilton',
        'Longines Master' => 'Longines',
        'Longines Hydroconquest' => 'Longines',
    ];

    public function run(): void
    {
        DB::transaction(function (): void {
            foreach (self::MERGE_TARGETS as $oldName => $targetName) {
                $oldBrand = HangSanXuat::where('tenhang', $oldName)->first();
                $targetBrand = HangSanXuat::where('tenhang', $targetName)->first();

                if (! $oldBrand) {
                    continue;
                }
                if (! $targetBrand) {
                    continue;
                }

                SanPham::where('hangsanxuat_id', $oldBrand->id)
                    ->update(['hangsanxuat_id' => $targetBrand->id]);
                $oldBrand->delete();
            }

            HangSanXuat::where(function ($query): void {
                $query->whereNull('hinhanh')
                    ->orWhere('hinhanh', '');
            })->each(function (HangSanXuat $brand): void {
                if ($brand->SanPham()->doesntExist()) {
                    $brand->delete();
                }
            });
        });
    }
}
