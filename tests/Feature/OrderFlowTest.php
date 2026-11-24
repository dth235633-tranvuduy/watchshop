<?php

namespace Tests\Feature;

use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
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

    public function test_empty_cart_opens_empty_cart_view(): void
    {
        $this->get('/gio-hang')
            ->assertOk()
            ->assertViewIs('frontend.giohangrong');
    }

    public function test_checkout_rolls_back_when_stock_is_not_enough(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);
        $product = SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Limited Watch',
            'tensanpham_slug' => 'limited-watch',
            'soluong' => 1,
            'dongia' => 1000000,
        ]);

        $this->actingAs($user)
            ->withSession(['cart' => [
                $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->tensanpham,
                    'price' => 1000000,
                    'qty' => 2,
                ],
            ]])
            ->post('/user/dat-hang', [
                'tennguoinhan' => 'Test Customer',
                'dienthoaigiaohang' => '0900000000',
                'diachigiaohang' => 'Test address',
                'phuongthucthanhtoan' => 'cod',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('sanpham', ['id' => $product->id, 'soluong' => 1]);
        $this->assertDatabaseCount('donhang', 0);
    }

    public function test_order_creates_default_pending_status_when_status_table_is_empty(): void
    {
        $user = User::factory()->create(['is_active' => true]);
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
                'phuongthucthanhtoan' => 'cod',
            ]);

        $response->assertRedirect(route('user.dathangthanhcong'));
        $this->assertDatabaseHas('tinhtrang', ['tinhtrang' => 'Chưa thanh toán']);
        $this->assertDatabaseHas('donhang', ['user_id' => $user->id, 'tinhtrang_id' => TinhTrang::first()->id]);
        $this->assertDatabaseHas('donhang_chitiet', ['sanpham_id' => $product->id, 'soluongban' => 1]);
        $this->assertDatabaseHas('sanpham', ['id' => $product->id, 'soluong' => 4]);
        $this->assertDatabaseHas('donhang', ['phuongthucthanhtoan' => 'cod']);
    }

    public function test_bank_order_renders_vietqr_with_the_calculated_total(): void
    {
        config([
            'payment.bank.bank_id' => 'MB',
            'payment.bank.account_no' => '123456789',
            'payment.bank.account_name' => 'WATCH SHOP',
            'payment.bank.template' => 'compact2',
        ]);

        $user = User::factory()->create(['is_active' => true]);
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);
        $product = SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Bank Payment Watch',
            'tensanpham_slug' => 'bank-payment-watch',
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
                'phuongthucthanhtoan' => 'bank',
            ]);

        $response->assertRedirect(route('user.dathangthanhcong'));
        $this->assertDatabaseHas('donhang', [
            'user_id' => $user->id,
            'phuongthucthanhtoan' => 'bank',
            'tongtien' => 1100000,
        ]);

        $this->actingAs($user)
            ->get('/user/dat-hang-thanh-cong')
            ->assertOk()
            ->assertSee('amount=1100000')
            ->assertSee('Thanh toan don hang #')
            ->assertSee('1.100.000 đ');
    }

    public function test_customer_can_cancel_pending_order_with_reason_and_stock_is_restored(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);
        $product = SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Cancelable Watch',
            'tensanpham_slug' => 'cancelable-watch',
            'soluong' => 2,
            'dongia' => 500000,
        ]);

        $pendingStatus = TinhTrang::create(['tinhtrang' => 'Chưa thanh toán']);
        $order = new DonHang;
        $order->user_id = $user->id;
        $order->tinhtrang_id = $pendingStatus->id;
        $order->tennguoinhan = 'Test Customer';
        $order->dienthoaigiaohang = '0900000000';
        $order->diachigiaohang = 'Test address';
        $order->phuongthucthanhtoan = 'cod';
        $order->tongtien = 550000;
        $order->save();

        $detail = new DonHang_ChiTiet;
        $detail->donhang_id = $order->id;
        $detail->sanpham_id = $product->id;
        $detail->soluongban = 1;
        $detail->dongiaban = 500000;
        $detail->save();

        $this->actingAs($user)
            ->patch('/user/don-hang/'.$order->id.'/huy', [
                'lydo_huy' => 'Đặt nhầm địa chỉ giao hàng',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('donhang', [
            'id' => $order->id,
            'lydo_huy' => 'Đặt nhầm địa chỉ giao hàng',
        ]);
        $this->assertDatabaseHas('sanpham', [
            'id' => $product->id,
            'soluong' => 3,
        ]);

        $this->actingAs($user)
            ->get('/user/don-hang')
            ->assertOk()
            ->assertDontSee('>Thanh toán<')
            ->assertSee('Thanh toán khi nhận hàng (COD)')
            ->assertSee('Đặt nhầm địa chỉ giao hàng');
    }

    public function test_dashboard_excludes_cancelled_orders_from_order_count_and_revenue(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $customer = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);
        $product = SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Dashboard Watch',
            'tensanpham_slug' => 'dashboard-watch',
            'soluong' => 5,
            'dongia' => 500000,
        ]);

        $pendingStatus = TinhTrang::create(['tinhtrang' => 'Chưa thanh toán']);
        $cancelledStatus = TinhTrang::create(['tinhtrang' => 'Đã hủy']);

        $activeOrder = new DonHang;
        $activeOrder->user_id = $customer->id;
        $activeOrder->tinhtrang_id = $pendingStatus->id;
        $activeOrder->tennguoinhan = 'Active Customer';
        $activeOrder->dienthoaigiaohang = '0900000001';
        $activeOrder->diachigiaohang = 'Active Address';
        $activeOrder->phuongthucthanhtoan = 'cod';
        $activeOrder->tongtien = 550000;
        $activeOrder->save();

        $activeDetail = new DonHang_ChiTiet;
        $activeDetail->donhang_id = $activeOrder->id;
        $activeDetail->sanpham_id = $product->id;
        $activeDetail->soluongban = 1;
        $activeDetail->dongiaban = 500000;
        $activeDetail->save();

        $cancelledOrder = new DonHang;
        $cancelledOrder->user_id = $customer->id;
        $cancelledOrder->tinhtrang_id = $cancelledStatus->id;
        $cancelledOrder->tennguoinhan = 'Cancelled Customer';
        $cancelledOrder->dienthoaigiaohang = '0900000002';
        $cancelledOrder->diachigiaohang = 'Cancelled Address';
        $cancelledOrder->phuongthucthanhtoan = 'cod';
        $cancelledOrder->tongtien = 1000000;
        $cancelledOrder->save();

        $cancelledDetail = new DonHang_ChiTiet;
        $cancelledDetail->donhang_id = $cancelledOrder->id;
        $cancelledDetail->sanpham_id = $product->id;
        $cancelledDetail->soluongban = 1;
        $cancelledDetail->dongiaban = 1000000;
        $cancelledDetail->save();

        $this->actingAs($admin)
            ->get('/admin/home')
            ->assertOk()
            ->assertViewHas('stats.donhang', 1)
            ->assertViewHas('stats.donhang_moi', 1)
            ->assertViewHas('stats.doanhthu', 500000.0);
    }

    public function test_admin_can_print_complete_order_invoice(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $customer = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);
        $product = SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Invoice Watch',
            'tensanpham_slug' => 'invoice-watch',
            'soluong' => 5,
            'dongia' => 750000,
        ]);
        $pendingStatus = TinhTrang::create(['tinhtrang' => 'Chưa thanh toán']);
        $order = new DonHang;
        $order->user_id = $customer->id;
        $order->tinhtrang_id = $pendingStatus->id;
        $order->tennguoinhan = 'Invoice Customer';
        $order->dienthoaigiaohang = '0900000001';
        $order->diachigiaohang = 'Invoice Address';
        $order->phuongthucthanhtoan = 'cod';
        $order->tongtien = 825000;
        $order->save();

        $detail = new DonHang_ChiTiet;
        $detail->donhang_id = $order->id;
        $detail->sanpham_id = $product->id;
        $detail->soluongban = 1;
        $detail->dongiaban = 750000;
        $detail->save();

        $this->actingAs($admin)
            ->get('/admin/donhang/'.$order->id.'/in')
            ->assertOk()
            ->assertSee('HÓA ĐƠN BÁN HÀNG')
            ->assertSee('Invoice Watch')
            ->assertSee('750.000 đ')
            ->assertSee('825.000 đ');
    }

    public function test_deleted_product_keeps_original_name_and_price_in_order_invoice(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
        $customer = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);
        $product = SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Original Watch Name',
            'tensanpham_slug' => 'original-watch-name',
            'soluong' => 5,
            'dongia' => 900000,
        ]);
        $status = TinhTrang::create(['tinhtrang' => 'Chưa thanh toán']);
        $order = new DonHang;
        $order->user_id = $customer->id;
        $order->tinhtrang_id = $status->id;
        $order->tennguoinhan = 'Snapshot Customer';
        $order->dienthoaigiaohang = '0900000001';
        $order->diachigiaohang = 'Snapshot Address';
        $order->phuongthucthanhtoan = 'cod';
        $order->tongtien = 990000;
        $order->save();

        $detail = new DonHang_ChiTiet;
        $detail->donhang_id = $order->id;
        $detail->sanpham_id = $product->id;
        $detail->tensanpham_snapshot = 'Original Watch Name';
        $detail->soluongban = 1;
        $detail->dongiaban = 900000;
        $detail->save();

        $product->delete();

        $this->assertSoftDeleted('sanpham', ['id' => $product->id]);
        $this->actingAs($admin)
            ->get('/admin/donhang/'.$order->id.'/in')
            ->assertOk()
            ->assertSee('Original Watch Name')
            ->assertSee('900.000 đ')
            ->assertDontSee('Sản phẩm đã xóa');
    }
}
