<?php

namespace Tests\Feature;

use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\NhaCungCap;
use App\Models\SanPham;
use App\Models\User;
use App\Services\StockReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Tests\TestCase;

class StockReceiptPriceTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(int $stock = 2, float $price = 5000000): SanPham
    {
        $category = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $brand = HangSanXuat::create(['tenhang' => 'Watch Brand', 'tenhang_slug' => 'watch-brand']);

        return SanPham::create([
            'loaisanpham_id' => $category->id,
            'hangsanxuat_id' => $brand->id,
            'tensanpham' => 'Receipt Watch',
            'tensanpham_slug' => 'receipt-watch',
            'soluong' => $stock,
            'dongia' => $price,
        ]);
    }

    private function createSupplier(): NhaCungCap
    {
        return NhaCungCap::create(['tennhacungcap' => 'Supplier A', 'trangthai' => true]);
    }

    public function test_selling_price_formula_adds_vat_and_service_fee(): void
    {
        $this->assertSame(11500000.0, StockReceiptService::giaBanTuGiaNhap(10000000));
        $this->assertSame(10914000.0, StockReceiptService::giaBanTuGiaNhap(9490000));
    }

    public function test_receipt_adds_stock_and_recalculates_selling_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supplier = $this->createSupplier();
        $product = $this->createProduct(stock: 2, price: 5000000);

        $this->actingAs($admin)
            ->post(route('admin.kho.luu'), [
                'nhacungcap_id' => $supplier->id,
                'ngaynhap' => now()->format('Y-m-d H:i:s'),
                'sanpham_id' => [$product->id],
                'soluong' => [3],
                'dongianhap' => [10000000],
            ])
            ->assertRedirect(route('admin.kho.danhsach'))
            ->assertSessionHas('success');

        $product->refresh();
        $this->assertSame(5, $product->soluong);
        $this->assertSame(11500000.0, (float) $product->dongia);
        $this->assertDatabaseHas('phieunhap', ['nhacungcap_id' => $supplier->id, 'tongtien' => 30000000]);
    }

    public function test_receipt_with_zero_import_price_keeps_selling_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $supplier = $this->createSupplier();
        $product = $this->createProduct(stock: 1, price: 7000000);

        $this->actingAs($admin)
            ->post(route('admin.kho.luu'), [
                'nhacungcap_id' => $supplier->id,
                'ngaynhap' => now()->format('Y-m-d H:i:s'),
                'sanpham_id' => [$product->id],
                'soluong' => [2],
                'dongianhap' => [0],
            ])
            ->assertRedirect(route('admin.kho.danhsach'));

        $product->refresh();
        $this->assertSame(3, $product->soluong);
        $this->assertSame(7000000.0, (float) $product->dongia);
    }

    public function test_receipt_page_prefills_products_from_low_stock_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $product = $this->createProduct(stock: 1);

        $this->actingAs($admin)
            ->get(route('admin.kho.them', ['sanpham' => [$product->id]]))
            ->assertOk()
            ->assertSee('Giá bán dự kiến')
            ->assertSee('Receipt Watch');
    }

    public function test_product_excel_import_keeps_excel_price_as_selling_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        HangSanXuat::create(['tenhang' => 'Rolex', 'tenhang_slug' => 'rolex']);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['Tên sản phẩm', 'Loại sản phẩm', 'Hãng sản xuất', 'Số lượng', 'Đơn giá', 'Giảm giá (%)', 'Đánh giá'],
            ['Rolex Submariner', 'Đồng hồ nam', 'Rolex', 5, 25000000, 0, 5],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'san-pham-');
        (new XlsxWriter($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        try {
            $this->actingAs($admin)
                ->post(route('admin.sanpham.postnhap'), [
                    'file' => new UploadedFile($path, 'san-pham.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
                ])
                ->assertRedirect(route('admin.sanpham.nhap'));
        } finally {
            @unlink($path);
        }

        $this->assertDatabaseHas('sanpham', [
            'tensanpham' => 'Rolex Submariner',
            'soluong' => 5,
            'dongia' => 25000000,
        ]);
    }
}
