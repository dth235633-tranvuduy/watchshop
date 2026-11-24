<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_the_current_slides_as_database_banners(): void
    {
        $this->assertSame(3, Banner::count());

        $this->get('/')
            ->assertOk()
            ->assertSee('photo-1523275335684-37898b6baf30')
            ->assertSee('photo-1579586337278-3befd40fd17a')
            ->assertSee('photo-1548135170-77b9e51226a9')
            ->assertSee('Đồng Hồ Nam Sang Trọng')
            ->assertSee('Bộ sưu tập 2025');
    }

    public function test_admin_can_add_a_banner_and_it_appears_on_homepage(): void
    {
        Storage::fake('private');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.banner.luu'), [
                'tieu_de' => 'Khuyến mãi cuối năm',
                'tieu_de_phu' => 'Sale lớn',
                'mo_ta' => 'Giảm sâu toàn bộ cửa hàng',
                'duong_dan' => '/san-pham',
                'thu_tu' => 0,
                'trang_thai' => '1',
                'hinh_anh' => UploadedFile::fake()->image('banner.jpg', 1600, 600),
            ])
            ->assertRedirect(route('admin.banner.danhsach'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('banners', ['tieu_de' => 'Khuyến mãi cuối năm', 'trang_thai' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Khuyến mãi cuối năm')
            ->assertSee('Sale lớn');
    }

    public function test_turned_off_banner_is_hidden_from_homepage_until_toggled_on(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $banner = Banner::create([
            'tieu_de' => 'Banner đang tắt',
            'hinh_anh' => 'https://example.com/banner.jpg',
            'thu_tu' => 9,
            'trang_thai' => false,
        ]);

        $this->get('/')->assertOk()->assertDontSee('Banner đang tắt');

        $this->actingAs($admin)
            ->patch(route('admin.banner.trangthai', $banner))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->get('/')->assertOk()->assertSee('Banner đang tắt');
    }

    public function test_homepage_has_no_hero_slider_when_there_are_no_banners(): void
    {
        Banner::query()->delete();

        $this->get('/')
            ->assertOk()
            ->assertDontSee('heroCarousel');
    }
}
