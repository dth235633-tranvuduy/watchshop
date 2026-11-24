<?php

namespace Tests\Feature;

use App\Models\DanhGia;
use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use App\Models\TinhTrang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonHangThaoTacTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(): SanPham
    {
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);

        return SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Action Watch',
            'tensanpham_slug' => 'action-watch',
            'soluong' => 5,
            'dongia' => 1000000,
        ]);
    }

    private function createOrder(User $user, SanPham $product, string $statusName): DonHang
    {
        $status = TinhTrang::firstOrCreate(['tinhtrang' => $statusName]);

        $order = new DonHang;
        $order->user_id = $user->id;
        $order->tinhtrang_id = $status->id;
        $order->tennguoinhan = 'Test Customer';
        $order->dienthoaigiaohang = '0900000000';
        $order->diachigiaohang = 'Test address';
        $order->phuongthucthanhtoan = 'cod';
        $order->tongtien = 1100000;
        $order->save();

        $detail = new DonHang_ChiTiet;
        $detail->donhang_id = $order->id;
        $detail->sanpham_id = $product->id;
        $detail->tensanpham_snapshot = $product->tensanpham;
        $detail->soluongban = 1;
        $detail->dongiaban = 1000000;
        $detail->save();

        return $order;
    }

    public function test_pending_order_shows_cancel_action_with_reason_form(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $this->createOrder($user, $product, 'Chờ xác nhận');

        $this->actingAs($user)
            ->get(route('user.donhang'))
            ->assertOk()
            ->assertSee('Thao tác')
            ->assertSee('Hủy đơn')
            ->assertSee('Lý do hủy đơn');
    }

    public function test_delivered_order_lets_customer_rate_the_product(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $this->createOrder($user, $product, 'Đã giao');

        $this->actingAs($user)
            ->get(route('user.donhang'))
            ->assertOk()
            ->assertSee('Đánh giá sản phẩm')
            ->assertDontSee('Hủy đơn');

        $this->actingAs($user)
            ->post(route('user.danhgia.luu', $product->id), [
                'sosao' => 5,
                'binhluan' => 'Đồng hồ rất đẹp, giao hàng nhanh.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('danhgia', [
            'sanpham_id' => $product->id,
            'user_id' => $user->id,
            'sosao' => 5,
            'trangthai' => 'hien_thi',
        ]);

        $this->actingAs($user)
            ->get(route('user.donhang'))
            ->assertOk()
            ->assertSee('Đánh giá đang hiển thị trên trang sản phẩm')
            ->assertDontSee('Đánh giá sản phẩm');
    }

    public function test_admin_can_hide_and_show_a_review_immediately(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $review = DanhGia::create([
            'sanpham_id' => $product->id,
            'user_id' => $user->id,
            'sosao' => 4,
            'binhluan' => 'Sản phẩm tốt.',
            'trangthai' => 'hien_thi',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.danhgia.danhsach'))
            ->assertOk()
            ->assertSee('Đang hiển thị');

        $this->actingAs($admin)
            ->patch(route('admin.danhgia.capnhat', $review), ['trangthai' => 'an'])
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertDatabaseHas('danhgia', ['id' => $review->id, 'trangthai' => 'an']);

        $this->actingAs($admin)
            ->patch(route('admin.danhgia.capnhat', $review), ['trangthai' => 'hien_thi'])
            ->assertRedirect();
        $this->assertDatabaseHas('danhgia', ['id' => $review->id, 'trangthai' => 'hien_thi']);

        $this->actingAs($admin)
            ->patch(route('admin.danhgia.capnhat', $review), ['trangthai' => 'cho_duyet'])
            ->assertSessionHasErrors('trangthai');
    }

    public function test_shipping_order_shows_neither_cancel_nor_review_action(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $this->createOrder($user, $product, 'Đang giao');

        $this->actingAs($user)
            ->get(route('user.donhang'))
            ->assertOk()
            ->assertDontSee('Hủy đơn')
            ->assertDontSee('Đánh giá sản phẩm');
    }
}
