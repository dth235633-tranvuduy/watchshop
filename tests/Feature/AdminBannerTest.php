<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_toggle_and_delete_banner(): void
    {
        Storage::fake('private');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->post('/admin/banner', [
            'tieu_de' => 'Bộ sưu tập mới',
            'mo_ta' => 'Đồng hồ cao cấp',
            'duong_dan' => '/san-pham',
            'thu_tu' => 1,
            'trang_thai' => 1,
            'hinh_anh' => UploadedFile::fake()->image('banner.jpg', 1600, 600),
        ])->assertRedirect(route('admin.banner.danhsach'));

        $banner = Banner::where('tieu_de', 'Bộ sưu tập mới')->firstOrFail();
        $this->assertTrue(Storage::disk('private')->exists($banner->hinh_anh));

        $this->actingAs($admin)->patch("/admin/banner/{$banner->id}/trang-thai")
            ->assertRedirect();

        $this->assertFalse($banner->fresh()->trang_thai);

        $this->actingAs($admin)->put("/admin/banner/{$banner->id}", [
            'tieu_de' => 'Bộ sưu tập 2026',
            'mo_ta' => 'Nội dung đã cập nhật',
            'duong_dan' => '/san-pham',
            'thu_tu' => 2,
            'trang_thai' => 1,
        ])->assertRedirect(route('admin.banner.danhsach'));

        $banner->refresh();
        $this->assertSame('Bộ sưu tập 2026', $banner->tieu_de);
        $this->assertTrue($banner->trang_thai);

        $this->actingAs($admin)->delete("/admin/banner/{$banner->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    public function test_frontend_displays_only_enabled_banners_in_order(): void
    {
        Banner::create([
            'tieu_de' => 'Banner thứ hai',
            'hinh_anh' => 'https://example.com/banner-2.jpg',
            'thu_tu' => 2,
            'trang_thai' => true,
        ]);
        Banner::create([
            'tieu_de' => 'Banner đã tắt',
            'hinh_anh' => 'https://example.com/off.jpg',
            'thu_tu' => 0,
            'trang_thai' => false,
        ]);
        Banner::create([
            'tieu_de' => 'Banner thứ nhất',
            'hinh_anh' => 'https://example.com/banner-1.jpg',
            'thu_tu' => 1,
            'trang_thai' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Banner thứ nhất')
            ->assertSee('Banner thứ hai')
            ->assertDontSee('Banner đã tắt');
    }
}
