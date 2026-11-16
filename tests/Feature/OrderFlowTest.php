<?php

namespace Tests\Feature;

use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use App\Models\TinhTrang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creates_default_pending_status_when_status_table_is_empty(): void
    {
        $user = User::factory()->create();
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);
        $product = SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Test Watch',
            'tensanpham_slug' => 'test-watch',
            'soluong' => 5,
            'dongia' => 1000000,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [
                $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->tensanpham,
                    'price' => 1000000,
                    'qty' => 1,
                ],
            ]])
            ->post('/user/dat-hang', [
                'tennguoinhan' => 'Test Customer',
                'dienthoaigiaohang' => '0900000000',
                'diachigiaohang' => 'Test address',
                'ghichu' => null,
            ]);

        $response->assertRedirect(route('user.dathangthanhcong'));
        $this->assertDatabaseHas('tinhtrang', ['tinhtrang' => 'Chờ duyệt']);
        $this->assertDatabaseHas('donhang', ['user_id' => $user->id, 'tinhtrang_id' => TinhTrang::first()->id]);
        $this->assertDatabaseHas('donhang_chitiet', ['sanpham_id' => $product->id, 'soluongban' => 1]);
        $this->assertDatabaseHas('sanpham', ['id' => $product->id, 'soluong' => 4]);
    }
}
