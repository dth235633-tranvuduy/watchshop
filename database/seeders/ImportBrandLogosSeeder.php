<?php

namespace Database\Seeders;

use App\Models\HangSanXuat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportBrandLogosSeeder extends Seeder
{
    private const BASE_URL = 'https://donghoduyanh.com/';

    private const BRANDS = [
        'Longines' => 'images/products/menufactories/resized/logo-longines_1617939728.png.webp',
        'Tissot' => 'images/products/menufactories/resized/logo-tissot-den_1786608382.png.webp',
        'Mido' => 'images/products/menufactories/resized/logo-mido_1617940892.png.webp',
        'Hamilton' => 'images/products/menufactories/resized/logo_1717577425.png.webp',
        'Oris' => 'images/products/menufactories/resized/oris_logo_1751853376.png.webp',
        'Maurice Lacroix' => 'images/products/menufactories/resized/mla-rectangle_1727855507.png.webp',
        'Frederique Constant' => 'images/products/menufactories/resized/fc_1579066189.png.webp',
        'Titoni' => 'images/products/menufactories/resized/titoni_1614063165.png.webp',
        'Seiko' => 'images/products/menufactories/resized/seiko_1579066795.png.webp',
        'Citizen' => 'images/products/menufactories/resized/citizen-logo_1624525206.png.webp',
        'Orient' => 'images/products/menufactories/resized/orient_1640747259.jpg.webp',
        'Orient Star' => 'images/products/menufactories/resized/orient_star-logo_1793074155.png.webp',
        'Casio' => 'images/products/menufactories/resized/casio_1579065603.png.webp',
        'Daniel Wellington' => 'images/products/menufactories/resized/dw_1579064560.png.webp',
        'RHYTHM' => 'images/products/menufactories/resized/a8589b9312a0da9af210d422a0fd4283_1785293668.jpg.webp',
        'SEIKO Đồng hồ treo tường' => 'images/products/menufactories/resized/seiko_clocks_1746605329.png.webp',
    ];

    public function run(): void
    {
        foreach (self::BRANDS as $name => $relativePath) {
            $slug = Str::slug($name);
            $brand = HangSanXuat::where('tenhang_slug', $slug)
                ->orWhere('tenhang', $name)
                ->first();

            if (! $brand) {
                $brand = new HangSanXuat([
                    'tenhang' => $name,
                    'tenhang_slug' => $slug,
                ]);
            }

            $imagePath = $this->downloadLogo($relativePath, $slug);
            if ($imagePath) {
                $brand->hinhanh = $imagePath;
            }

            $brand->save();

            $this->command?->info($name.($imagePath ? ': đã lưu ảnh' : ': chưa tải được ảnh'));
        }
    }

    private function downloadLogo(string $relativePath, string $slug): ?string
    {
        $response = Http::timeout(30)
            ->withOptions([
                'allow_redirects' => true,
                'verify' => false,
            ])
            ->get(self::BASE_URL.$relativePath);

        if (! $response->successful()) {
            return null;
        }

        $contentType = (string) $response->header('Content-Type');
        $extension = str_contains($contentType, 'jpeg') ? 'jpg'
            : (str_contains($contentType, 'png') ? 'png' : 'webp');
        $path = 'hangsanxuat/'.$slug.'.'.$extension;

        Storage::disk('private')->put($path, $response->body());

        return $path;
    }
}
