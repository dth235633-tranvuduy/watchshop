<?php

namespace App\Console\Commands;

use App\Models\SanPham;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportProductImages extends Command
{
    protected $signature = 'sanpham:import-images
                            {folder=import/anh-san-pham : Thư mục chứa ảnh, tính từ storage/app/private}
                            {--dry-run : Chỉ in ra kết quả, không ghi vào database}';

    protected $description = 'Gán ảnh cho các sản phẩm đang thiếu ảnh, dựa trên tên file trùng với slug sản phẩm';

    public function handle(): int
    {
        $disk = Storage::disk('private');
        $folder = trim((string) $this->argument('folder'), '/');
        $dryRun = (bool) $this->option('dry-run');

        if (! $disk->exists($folder)) {
            $this->error("Không tìm thấy thư mục: storage/app/private/{$folder}");
            $this->line('Hãy tạo thư mục này và chép ảnh vào đó, mỗi ảnh đặt tên theo slug sản phẩm.');

            return self::FAILURE;
        }

        $files = collect($disk->files($folder))
            ->filter(fn (string $file): bool => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['webp', 'jpg', 'jpeg', 'png'], true))
            ->values();

        if ($files->isEmpty()) {
            $this->error("Không có file ảnh nào trong storage/app/private/{$folder}");

            return self::FAILURE;
        }

        $this->info("Tìm thấy {$files->count()} file ảnh. Bắt đầu ghép với sản phẩm thiếu ảnh...");

        $missing = SanPham::query()
            ->where(fn ($query) => $query->whereNull('hinhanh')->orWhere('hinhanh', ''))
            ->get();

        if ($missing->isEmpty()) {
            $this->info('Không có sản phẩm nào thiếu ảnh. Không cần làm gì.');

            return self::SUCCESS;
        }

        $matched = 0;
        $unmatched = [];

        foreach ($missing as $sanpham) {
            $target = $this->findFileForSlug($files, $sanpham->tensanpham_slug);

            if ($target === null) {
                $unmatched[] = $sanpham->tensanpham_slug;

                continue;
            }

            $this->line(($dryRun ? '[DRY] ' : '')."#{$sanpham->id} {$sanpham->tensanpham} <- ".basename($target));

            if (! $dryRun) {
                $sanpham->hinhanh = $target;
                $sanpham->save();
            }

            $matched++;
        }

        $this->newLine();
        $this->info(($dryRun ? '[DRY RUN] ' : '')."Đã gán {$matched}/{$missing->count()} sản phẩm.");

        if ($unmatched !== []) {
            $this->warn('Sản phẩm chưa có ảnh phù hợp ('.count($unmatched).'):');
            foreach ($unmatched as $slug) {
                $this->line("  - {$slug}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Tìm file ảnh khớp với slug sản phẩm (so khớp chính xác hoặc chứa slug).
     *
     * @param  Collection<int, string>  $files
     */
    private function findFileForSlug($files, string $slug): ?string
    {
        $normalizedSlug = Str::slug($slug);

        $exact = $files->first(fn (string $file): bool => Str::slug(pathinfo($file, PATHINFO_FILENAME)) === $normalizedSlug);
        if ($exact !== null) {
            return $exact;
        }

        return $files->first(fn (string $file): bool => str_contains(Str::slug(pathinfo($file, PATHINFO_FILENAME)), $normalizedSlug));
    }
}
