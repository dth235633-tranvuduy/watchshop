<?php

namespace App\Console\Commands;

use App\Models\SanPham;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadProductImages extends Command
{
    protected $signature = 'products:download-images';

    protected $description = 'Tải toàn bộ ảnh sản phẩm từ URL bên ngoài về storage/app/private và cập nhật đường dẫn nội bộ';

    public function handle(): int
    {
        $disk = Storage::disk('private');
        $products = SanPham::where('hinhanh', 'like', 'http%')->get();

        if ($products->isEmpty()) {
            $this->info('Không có ảnh nào cần tải về.');

            return self::SUCCESS;
        }

        $this->info("Bắt đầu tải {$products->count()} ảnh...");
        $success = 0;
        $failed = 0;

        foreach ($products as $sp) {
            $url = $sp->hinhanh;
            $ext = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
            if ($ext === '') {
                $ext = 'jpg';
            }

            $directory = $sp->LoaiSanPham?->tenloai_slug ?? 'san-pham';
            $filename = Str::slug($sp->tensanpham, '-').'-'.$sp->id.'.'.$ext;
            $path = $directory.'/'.$filename;

            try {
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                    'Referer' => 'https://donghoduyanh.com/',
                ])->timeout(30)->get($url);

                if (! $response->successful()) {
                    $this->warn("✗ [{$sp->id}] HTTP {$response->status()} - {$sp->tensanpham}");
                    $failed++;

                    continue;
                }

                $disk->put($path, $response->body());
                $sp->hinhanh = $path;
                $sp->save();

                $this->line("✓ [{$sp->id}] {$path}");
                $success++;
            } catch (\Throwable $e) {
                $this->error("✗ [{$sp->id}] {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Hoàn tất: {$success} thành công, {$failed} lỗi.");

        return self::SUCCESS;
    }
}
