<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DonHang extends Model
{
    protected $table = 'donhang';

    protected $fillable = ['user_id', 'voucher_id', 'tinhtrang_id', 'tennguoinhan', 'dienthoaigiaohang', 'diachigiaohang', 'ghichu', 'lydo_huy', 'phuongthucthanhtoan', 'tiengiam', 'tongtien'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'tiengiam' => 'decimal:2',
            'tongtien' => 'decimal:2',
        ];
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function TinhTrang(): BelongsTo
    {
        return $this->belongsTo(TinhTrang::class, 'tinhtrang_id');
    }

    public function Voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function DonHang_ChiTiet(): HasMany
    {
        return $this->hasMany(DonHang_ChiTiet::class, 'donhang_id');
    }

    public function LichSu(): HasMany
    {
        return $this->hasMany(DonHangLichSu::class, 'donhang_id')
            ->orderBy('created_at')
            ->orderBy('id');
    }

    /**
     * Bảng lịch sử chỉ tồn tại sau khi chạy migration tạo bảng donhang_lichsu.
     * Kiểm tra này giúp web không lỗi khi code mới được deploy trước migration.
     */
    public static function coBangLichSu(): bool
    {
        return Schema::hasTable('donhang_lichsu');
    }

    /**
     * Lịch sử trạng thái của đơn, hoặc danh sách rỗng khi chưa có bảng lịch sử.
     */
    public function lichSuTrangThai(): Collection
    {
        if (! self::coBangLichSu()) {
            return collect();
        }

        return $this->LichSu;
    }

    /**
     * Ghi lại một mốc lịch sử cho trạng thái hiện tại của đơn hàng.
     * Trả về null (và không ghi gì) khi bảng lịch sử chưa được tạo.
     */
    public function ghiLichSu(?int $userId = null, ?string $ghiChu = null): ?DonHangLichSu
    {
        if (! self::coBangLichSu()) {
            return null;
        }

        $tenTrangThai = $this->tinhtrang_id
            ? TinhTrang::whereKey($this->tinhtrang_id)->value('tinhtrang')
            : null;

        return $this->LichSu()->create([
            'tinhtrang_id' => $this->tinhtrang_id,
            'ten_trang_thai' => $tenTrangThai ?? 'Không rõ',
            'user_id' => $userId,
            'ghichu' => $ghiChu,
        ]);
    }
}
