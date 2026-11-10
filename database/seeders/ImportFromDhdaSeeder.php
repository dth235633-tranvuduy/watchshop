<?php

namespace Database\Seeders;

use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ImportFromDhdaSeeder extends Seeder
{
    public function run(): void
    {
        $htmlPath = storage_path('app/dhda_category.html');

        if (! is_file($htmlPath)) {
            $this->command?->error('Không tìm thấy file: '.$htmlPath);

            return;
        }

        $html = file_get_contents($htmlPath);

        // Mỗi card bắt đầu bằng <div class="frame_inner"> và kết thúc ở <!-- end .frame_inner -->
        preg_match_all(
            '/<div class="frame_inner">(.*?)<!-- end \.frame_inner -->/s',
            $html,
            $matches
        );

        $cards = $matches[1] ?? [];
        $this->command?->info('Tìm thấy '.count($cards).' card sản phẩm.');

        $inserted = 0;
        $skipped = 0;

        foreach ($cards as $card) {
            $product = $this->parseCard($card);

            if ($product === null) {
                $skipped++;

                continue;
            }

            // Loại sản phẩm
            $loai = LoaiSanPham::firstOrCreate(
                ['tenloai_slug' => Str::slug($product['loai'])],
                ['tenloai' => $product['loai']]
            );

            // Hãng sản xuất
            $hang = HangSanXuat::firstOrCreate(
                ['tenhang_slug' => Str::slug($product['hang'])],
                ['tenhang' => $product['hang']]
            );

            // Cập nhật nếu slug đã tồn tại (để fix dữ liệu cũ), nếu chưa có thì tạo mới
            $sanpham = SanPham::firstOrNew(['tensanpham_slug' => $product['slug']]);
            $sanpham->fill([
                'loaisanpham_id' => $loai->id,
                'hangsanxuat_id' => $hang->id,
                'tensanpham' => $product['name'],
                'tensanpham_slug' => $product['slug'],
                'soluong' => 10,
                'dongia' => $product['price_old'],
                'giamgia' => $product['discount'],
                'danhgia' => 0,
                'hinhanh' => $product['image'],
                'motasanpham' => null,
                'chatlieuday' => null,
                'matkinh' => null,
                'dochieunuoc' => null,
            ]);
            $sanpham->save();

            $inserted++;
        }

        $this->command?->info("Đã chèn {$inserted} sản phẩm, bỏ qua {$skipped}.");
    }

    /**
     * @return array{name: string, slug: string, image: ?string, price_old: float, discount: int, loai: string, hang: string}|null
     */
    private function parseCard(string $card): ?array
    {
        if (! preg_match('/<h3\s*>\s*<a[^>]+href="([^"]+)"[^>]+title\s*=\s*"([^"]*)"[^>]*>(.*?)<\/a>/s', $card, $m)) {
            return null;
        }

        $href = $m[1];
        $name = html_entity_decode(trim($m[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $inner = $m[3];

        $slug = $this->slugFromHref($href);
        if ($slug === '' || $name === '') {
            return null;
        }

        // Hình ảnh
        $image = null;
        if (preg_match('/<img[^>]+src\s*=\s*"([^"]+)"/i', $card, $mi)) {
            $image = $mi[1];
        }

        // Tên loại + hãng lấy từ .cat_name, ví dụ "Đồng hồ nam Daniel Wellington"
        $loai = 'Đồng hồ nam';
        $hang = 'Khác';
        if (preg_match('/<span class="cat_name">([^<]+)<\/span>/i', $inner, $mc)) {
            $cat = html_entity_decode(trim($mc[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $loai = $cat !== '' ? $cat : $loai;

            $hangName = preg_replace('/^Đồng hồ (nam|nữ)\s*/iu', '', $cat);
            if (is_string($hangName) && trim($hangName) !== '') {
                $hang = trim($hangName);
            }
        }

        // Giá gốc
        $priceOld = 0.0;
        if (preg_match('/<div[^>]+class=\'price_old\'[^>]*>.*?<span>([^<]+)<\/span>/is', $card, $mp)) {
            $priceOld = $this->parsePrice($mp[1]);
        }

        // Nếu không có giá cũ (hết hàng hoặc format khác), lấy giá hiện tại
        if ($priceOld <= 0 && preg_match('/<div[^>]+class=\'price_current\'[^>]*>.*?([\d\.]+)\s*₫/is', $card, $mc)) {
            $priceOld = $this->parsePrice($mc[1]);
        }

        // % giảm giá
        $discount = 0;
        if (preg_match('/class=\'discount\'>\s*<span>-?(\d+)%<\/span>/i', $card, $md)) {
            $discount = (int) $md[1];
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'image' => $image,
            'price_old' => $priceOld,
            'discount' => $discount,
            'loai' => $loai,
            'hang' => $hang,
        ];
    }

    private function slugFromHref(string $href): string
    {
        $path = parse_url($href, PHP_URL_PATH) ?: $href;
        $base = basename($path);

        return Str::slug(preg_replace('/\.html$/i', '', $base) ?? $base);
    }

    private function parsePrice(string $raw): float
    {
        // "7.129.000₫" -> 7129000
        $digits = preg_replace('/[^\d]/', '', $raw);

        return $digits === '' ? 0.0 : (float) $digits;
    }
}
