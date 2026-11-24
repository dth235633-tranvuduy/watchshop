<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use App\Models\TinhTrang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DonHangLichSuTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(int $stock = 5): SanPham
    {
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);

        return SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Timeline Watch',
            'tensanpham_slug' => 'timeline-watch',
            'soluong' => $stock,
            'dongia' => 1000000,
        ]);
    }

    private function placeOrder(User $user, SanPham $product, string $paymentMethod = 'cod'): DonHang
    {
        $this->actingAs($user)
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
                'phuongthucthanhtoan' => $paymentMethod,
            ])
            ->assertRedirect(route('user.dathangthanhcong'));

        return DonHang::latest('id')->firstOrFail();
    }

    public function test_placing_an_order_records_the_first_history_entry(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();

        $order = $this->placeOrder($user, $product);

        $this->assertDatabaseHas('donhang_lichsu', [
            'donhang_id' => $order->id,
            'ten_trang_thai' => 'Chờ xác nhận',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('donhang', [
            'id' => $order->id,
            'da_thanh_toan' => false,
        ]);
        $this->assertSame(1, $order->LichSu()->count());
    }

    public function test_admin_status_change_records_history_and_unchanged_status_does_not(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $order = $this->placeOrder($user, $product);

        $shipping = TinhTrang::firstOrCreate(['tinhtrang' => 'Đang giao']);

        $this->actingAs($admin)
            ->patch(route('admin.donhang.trangthai', $order->id), ['tinhtrang_id' => $shipping->id])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('donhang_lichsu', [
            'donhang_id' => $order->id,
            'ten_trang_thai' => 'Đang giao',
            'user_id' => $admin->id,
        ]);
        $this->assertSame(2, $order->LichSu()->count());

        $this->actingAs($admin)
            ->patch(route('admin.donhang.trangthai', $order->id), ['tinhtrang_id' => $shipping->id])
            ->assertRedirect();

        $this->assertSame(2, $order->LichSu()->count());
    }

    public function test_customer_cancellation_records_history_with_reason(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $order = $this->placeOrder($user, $product);

        $this->actingAs($user)
            ->patch(route('user.donhang.huy', $order->id), ['lydo_huy' => 'Đặt nhầm sản phẩm'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('donhang_lichsu', [
            'donhang_id' => $order->id,
            'ten_trang_thai' => 'Đã hủy',
            'user_id' => $user->id,
            'ghichu' => 'Khách hàng hủy đơn. Lý do: Đặt nhầm sản phẩm',
        ]);
    }

    public function test_order_pages_still_work_before_the_history_table_exists(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $order = $this->placeOrder($user, $product);

        Schema::drop('donhang_lichsu');

        $this->assertFalse(DonHang::coBangLichSu());
        $this->assertNull($order->ghiLichSu($user->id));
        $this->assertTrue($order->lichSuTrangThai()->isEmpty());

        $this->actingAs($user)
            ->get(route('user.donhang'))
            ->assertOk()
            ->assertSee('Theo dõi trạng thái đơn hàng');

        $this->actingAs($admin)
            ->get(route('admin.donhang.sua', $order->id))
            ->assertOk()
            ->assertSee('php artisan migrate');
    }

    public function test_customer_order_page_shows_status_timeline(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $order = $this->placeOrder($user, $product);

        $delivered = TinhTrang::firstOrCreate(['tinhtrang' => 'Đã giao']);
        $order->update(['tinhtrang_id' => $delivered->id]);
        $order->ghiLichSu($user->id, 'Quản trị viên cập nhật trạng thái đơn hàng.');

        $this->actingAs($user)
            ->get(route('user.donhang'))
            ->assertOk()
            ->assertSee('Theo dõi trạng thái đơn hàng')
            ->assertSee('Chờ xác nhận')
            ->assertSee('Đã giao');
    }

    public function test_bank_payment_marks_order_paid_without_changing_delivery_status(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $order = $this->placeOrder($user, $product, 'bank');

        $this->actingAs($user)
            ->post(route('user.donhang.thanhtoan', $order->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertTrue($order->da_thanh_toan);
        $this->assertSame('Chờ xác nhận', $order->TinhTrang->tinhtrang);
        $this->assertDatabaseHas('donhang_lichsu', [
            'donhang_id' => $order->id,
            'ten_trang_thai' => 'Đã thanh toán',
            'user_id' => $user->id,
        ]);
    }

    public function test_paid_order_cannot_be_cancelled_by_customer(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $order = $this->placeOrder($user, $product, 'bank');
        $order->update(['da_thanh_toan' => true]);

        $this->actingAs($user)
            ->patch(route('user.donhang.huy', $order->id), ['lydo_huy' => 'Đổi ý'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('Chờ xác nhận', $order->refresh()->TinhTrang->tinhtrang);
    }

    public function test_admin_order_page_offers_the_delivery_status_flow(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['is_active' => true]);
        $product = $this->createProduct();
        $order = $this->placeOrder($user, $product);

        $response = $this->actingAs($admin)
            ->get(route('admin.donhang.sua', $order->id))
            ->assertOk();

        foreach (DonHang::LUONG_TRANG_THAI as $status) {
            $response->assertSee($status);
        }
    }
}
