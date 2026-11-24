<?php

namespace Database\Seeders;

use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportMensWatchSampleSeeder extends Seeder
{
    private const HTML_FILES = [
        'dhda_mens.html',
        'dhda_mens_page2.html',
        'dhda_mens_page3.html',
        'dhda_mens_page4.html',
    ];

    private const BRAND_NAMES = [
        'Longines',
        'Tissot',
        'Mido',
        'Hamilton',
        'Oris',
        'Maurice Lacroix',
        'Frederique Constant',
        'Titoni',
        'Seiko',
        'Citizen',
        'Orient Star',
        'Orient',
        'Daniel Wellington',
        'Casio',
        'RHYTHM',
    ];

    public function run(): void
    {
        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        foreach (self::HTML_FILES as $fileName) {
            $htmlPath = storage_path('app/'.$fileName);
            if (! is_file($htmlPath)) {
                $this->command?->warn("Không thấy {$fileName}");

                continue;
            }

            $html = (string) file_get_contents($htmlPath);
            preg_match_all('/<div class="frame_inner">(.*?)<!-- end \.frame_inner -->/s', $html, $matches);

            foreach ($matches[1] ?? [] as $card) {
                $product = $this->parseCard($card);
                if (! $product) {
                    $skipped++;

                    continue;
                }

                $machineType = LoaiSanPham::query()
                    ->get()
                    ->first(fn (LoaiSanPham $type): bool => stripos($type->tenloai, $product['machine']) !== false);

                if (! $machineType) {
                    $machineType = LoaiSanPham::firstOrCreate(
                        ['tenloai_slug' => Str::slug($product['machine'])],
                        ['tenloai' => $product['machine']]
                    );
                }

                $brand = $this->findOrCreateBrand($product['brand']);
                $imagePath = $this->downloadImage($product['image'], $product['slug']);
                $price = $product['price'] > 0
                    ? $product['price']
                    : $this->fallbackPrice($product['brand']);

                $sanpham = SanPham::firstOrNew(['tensanpham_slug' => $product['slug']]);
                $isNew = ! $sanpham->exists;
                $sanpham->fill([
                    'loaisanpham_id' => $machineType->id,
                    'hangsanxuat_id' => $brand->id,
                    'tensanpham' => $product['name'],
                    'tensanpham_slug' => $product['slug'],
                    'soluong' => 10,
                    'dongia' => $price,
                    'giamgia' => $product['discount'],
                    'danhgia' => 0,
                    'hinhanh' => $imagePath ?: $product['image'],
                    'motasanpham' => null,
                    'chatlieuday' => null,
                    'phongcach' => null,
                    'matkinh' => null,
                    'dochieunuoc' => null,
                ]);
                $sanpham->save();

                $isNew ? $inserted++ : $updated++;
            }
        }

        $this->command?->info("Đã thêm {$inserted} sản phẩm, cập nhật {$updated}, bỏ qua {$skipped}.");
    }

    /**
     * @return array{name: string, slug: string, image: ?string, price: float, discount: int, machine: string, brand: string}|null
     */
    private function parseCard(string $card): ?array
    {
        if (! preg_match('/<h3[\s\S]*?<a[^>]+href="([^"]+)"[^>]+title\s*=\s*[\'"]([^\'"]+)[\'"][\s\S]*?<span class="cat_name">([^<]+)<\/span>/is', $card, $titleMatch)) {
            if (! preg_match('/<h3[\s\S]*?<a[^>]+title\s*=\s*[\'"]([^\'"]+)[\'"][^>]+href="([^"]+)"/is', $card, $fallbackMatch)) {
                return null;
            }

            $name = html_entity_decode(trim($fallbackMatch[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $href = $fallbackMatch[2];
            $catName = $name;
        } else {
            $href = $titleMatch[1];
            $name = html_entity_decode(trim($titleMatch[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $catName = html_entity_decode(trim($titleMatch[3]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $slug = $this->slugFromHref($href);
        if ($slug === '') {
            return null;
        }

        $image = null;
        if (preg_match('/<img[^>]+(?:src|data-src)\s*=\s*[\'"]([^\'"]+)[\'"]/is', $card, $imageMatch)) {
            $image = trim($imageMatch[1]);
            if (! str_starts_with($image, 'http')) {
                $image = 'https://donghoduyanh.com/'.ltrim($image, '/');
            }
        }

        $machine = 'Automatic';
        if (preg_match("/<span[^>]*class=['\"]loai_may['\"][^>]*>([^<]+)<\/span>/is", $card, $machineMatch)) {
            $machine = trim(strip_tags(html_entity_decode($machineMatch[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }
        $machine = match (true) {
            stripos($machine, 'quartz') !== false => 'Quartz',
            stripos($machine, 'eco') !== false => 'Eco-Drive',
            stripos($machine, 'solar') !== false => 'Solar',
            stripos($machine, 'chrono') !== false => 'Chronograph',
            stripos($machine, 'cosc') !== false => 'COSC',
            stripos($machine, 'automatic') !== false || stripos($machine, 'cơ') !== false => 'Automatic',
            default => 'Automatic',
        };

        $discount = 0;
        if (preg_match("/<div[^>]*class=['\"]discount['\"][^>]*>\s*<span>-?(\d+)%<\/span>/is", $card, $discountMatch)) {
            $discount = (int) $discountMatch[1];
        }

        $price = 0.0;
        if (preg_match('/<div[^>]+class=[\'"]price_current[\'"][^>]*>(.*?<span>)?([^<\r\n]+?)(?:<|$)/is', $card, $currentMatch)) {
            $price = $this->parsePrice($currentMatch[2]);
        }
        if ($price <= 0 && preg_match('/<div[^>]+class=[\'"]price_old[\'"][^>]*>.*?<span>([^<]+)<\/span>/is', $card, $oldMatch)) {
            $price = $this->parsePrice($oldMatch[1]);
        }

        $brand = 'Khác';
        foreach (self::BRAND_NAMES as $brandName) {
            if (stripos($catName, $brandName) !== false || stripos($name, $brandName) !== false) {
                $brand = $brandName;

                break;
            }
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'image' => $image,
            'price' => $price,
            'discount' => $discount,
            'machine' => $machine,
            'brand' => $brand,
        ];
    }

    private function findOrCreateBrand(string $name): HangSanXuat
    {
        $slug = Str::slug($name);
        $brand = HangSanXuat::where('tenhang_slug', $slug)
            ->orWhere('tenhang', $name)
            ->first();

        if ($brand) {
            return $brand;
        }

        return HangSanXuat::create([
            'tenhang' => $name,
            'tenhang_slug' => $slug,
            'hinhanh' => null,
        ]);
    }

    private function downloadImage(?string $url, string $slug): ?string
    {
        if (! $url) {
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->withOptions(['allow_redirects' => true, 'verify' => false])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $contentType = (string) $response->header('Content-Type');
            $extension = str_contains($contentType, 'jpeg') ? 'jpg'
                : (str_contains($contentType, 'png') ? 'png' : 'webp');
            $path = 'sanpham/'.$slug.'.'.$extension;
            Storage::disk('private')->put($path, $response->body());

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }

    private function slugFromHref(string $href): string
    {
        $path = parse_url($href, PHP_URL_PATH) ?: $href;
        $base = basename($path);

        return Str::slug(preg_replace('/\.html$/i', '', $base) ?? $base);
    }

    private function parsePrice(string $raw): float
    {
        $digits = preg_replace('/[^\d]/', '', $raw);

        return $digits === '' ? 0.0 : (float) $digits;
    }

    private function fallbackPrice(string $brand): float
    {
        return match ($brand) {
            'Frederique Constant' => 30000000,
            'Longines' => 35000000,
            'Hamilton' => 25000000,
            'Tissot', 'Mido' => 18000000,
            'Citizen', 'Orient', 'Orient Star', 'Seiko', 'Casio', 'Daniel Wellington' => 10000000,
            default => 15000000,
        };
    }
}
