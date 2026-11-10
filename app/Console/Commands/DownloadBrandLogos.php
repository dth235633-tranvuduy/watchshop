<?php

namespace App\Console\Commands;

use App\Models\HangSanXuat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadBrandLogos extends Command
{
    protected $signature = 'brands:download-logos';

    protected $description = 'Tải logo thương hiệu từ Clearbit và cập nhật đường dẫn nội bộ';

    public function handle(): int
    {
        // Mapping tên hãng -> domain để lấy logo
        $domains = [
            'daniel-wellington' => 'danielwellington.com',
            'daniel-wellington-iconic-link' => 'danielwellington.com',
            'seiko-prospex' => 'seikowatches.com',
            'orient-star' => 'orientwatchusa.com',
            'hamilton-khaki-field' => 'hamiltonwatch.com',
            'seiko-5-sports' => 'seikowatches.com',
            'tissot-prx' => 'tissotwatches.com',
            'frederique-constant-classics' => 'frederiqueconstant.com',
            'frederique-constant-manufacture' => 'frederiqueconstant.com',
        ];

        $disk = Storage::disk('private');
        $brands = HangSanXuat::whereNull('hinhanh')->orWhere('hinhanh', 'like', 'http%')->get();

        if ($brands->isEmpty()) {
            $this->info('Không có thương hiệu nào cần tải logo.');

            return self::SUCCESS;
        }

        $this->info("Bắt đầu tải logo cho {$brands->count()} thương hiệu...");
        $success = 0;
        $failed = 0;

        foreach ($brands as $brand) {
            $slug = $brand->tenhang_slug;
            $domain = $domains[$slug] ?? null;

            if (! $domain) {
                $this->warn("✗ Không tìm thấy domain cho hãng: {$brand->tenhang} (slug: {$slug})");
                $failed++;

                continue;
            }

            $url = "https://www.google.com/s2/favicons?domain={$domain}&sz=128";
            $path = 'thuong-hieu/'.$slug.'.png';

            try {
                $response = Http::withOptions([
                    'verify' => false,
                ])->timeout(15)->get($url);

                if (! $response->successful()) {
                    $this->warn("✗ [{$brand->id}] HTTP {$response->status()} - {$brand->tenhang}");
                    $failed++;

                    continue;
                }

                $disk->put($path, $response->body());
                $brand->hinhanh = $path;
                $brand->save();

                $this->line("✓ [{$brand->id}] {$brand->tenhang} -> {$path}");
                $success++;
            } catch (\Throwable $e) {
                $this->error("✗ [{$brand->id}] {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Hoàn tất: {$success} thành công, {$failed} lỗi.");

        return self::SUCCESS;
    }
}
