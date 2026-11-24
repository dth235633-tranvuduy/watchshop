<?php

namespace Tests\Feature;

use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\NhaCungCap;
use App\Models\SanPham;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Tests\TestCase;

class ProductImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_products_from_xlsx_and_create_stock_receipt(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $loai = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $hang = HangSanXuat::create(['tenhang' => 'Rolex', 'tenhang_slug' => 'rolex']);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['Tên sản phẩm', 'Loại sản phẩm', 'Hãng sản xuất', 'Số lượng', 'Đơn giá', 'Giảm giá (%)', 'Đánh giá'],
            ['Rolex Submariner', 'Đồng hồ nam', 'Rolex', 5, 25000000, 10, 4.5],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'san-pham-');
        (new XlsxWriter($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        try {
            $response = $this->actingAs($admin)->post('/admin/sanpham/nhap', [
                'file' => new UploadedFile($path, 'san-pham.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
            ]);

            $response->assertRedirect(route('admin.sanpham.nhap'));
        } finally {
            @unlink($path);
        }

        $this->assertDatabaseHas('sanpham', [
            'tensanpham' => 'Rolex Submariner',
            'loaisanpham_id' => $loai->id,
            'hangsanxuat_id' => $hang->id,
            'soluong' => 5,
            'giamgia' => 10,
            'hinhanh' => null,
        ]);
        $this->assertDatabaseHas('nhacungcap', ['tennhacungcap' => 'Nhà cung cấp nhập Excel']);
        $this->assertDatabaseCount('phieunhap', 1);
        $this->assertDatabaseCount('chitietphieunhap', 1);
    }

    public function test_admin_can_import_products_from_xlsx(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        LoaiSanPham::create(['tenloai' => 'Đồng hồ nữ', 'tenloai_slug' => 'dong-ho-nu']);
        HangSanXuat::create(['tenhang' => 'Casio', 'tenhang_slug' => 'casio']);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Tên sản phẩm', 'Loại sản phẩm', 'Hãng sản xuất', 'Số lượng', 'Đơn giá'],
            ['Casio Sheen', 'Đồng hồ nữ', 'Casio', 3, 4500000],
        ]);

        $path = tempnam(sys_get_temp_dir(), 'san-pham-');
        (new XlsxWriter($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        try {
            $response = $this->actingAs($admin)->post('/admin/sanpham/nhap', [
                'file' => new UploadedFile(
                    $path,
                    'san-pham.xlsx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    null,
                    true
                ),
            ]);

            $response->assertRedirect(route('admin.sanpham.nhap'));

            $this->assertDatabaseHas('sanpham', [
                'tensanpham' => 'Casio Sheen',
                'soluong' => 3,
            ]);
        } finally {
            @unlink($path);
        }
    }

    public function test_import_skips_duplicate_product_names(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        HangSanXuat::create(['tenhang' => 'Rolex', 'tenhang_slug' => 'rolex']);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['Tên sản phẩm', 'Loại sản phẩm', 'Hãng sản xuất', 'Số lượng', 'Đơn giá'],
            ['Rolex Submariner', 'Đồng hồ nam', 'Rolex', 5, 25000000],
            ['Rolex Submariner', 'Đồng hồ nam', 'Rolex', 8, 26000000],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'san-pham-');
        (new XlsxWriter($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        try {
            $response = $this->actingAs($admin)->post('/admin/sanpham/nhap', [
                'file' => new UploadedFile($path, 'san-pham.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
            ]);
            $response->assertRedirect(route('admin.sanpham.nhap'));
            $response->assertSessionHas('success');
        } finally {
            @unlink($path);
        }

        $this->assertDatabaseCount('sanpham', 1);
    }

    public function test_admin_can_import_categories_and_brands(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $loaiFile = tempnam(sys_get_temp_dir(), 'loai-');
        $loaiSheet = new Spreadsheet;
        $loaiSheet->getActiveSheet()->fromArray([
            ['Tên loại'],
            ['Đồng hồ thể thao'],
        ]);
        (new XlsxWriter($loaiSheet))->save($loaiFile);
        $loaiSheet->disconnectWorksheets();

        $hangFile = tempnam(sys_get_temp_dir(), 'hang-');
        $hangSheet = new Spreadsheet;
        $hangSheet->getActiveSheet()->fromArray([
            ['Tên hãng'],
            ['Seiko'],
        ]);
        (new XlsxWriter($hangSheet))->save($hangFile);
        $hangSheet->disconnectWorksheets();

        try {
            $this->actingAs($admin)->post('/admin/loaisanpham/nhap', [
                'file' => new UploadedFile($loaiFile, 'loai.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
            ])->assertRedirect();

            $this->actingAs($admin)->post('/admin/hangsanxuat/nhap', [
                'file' => new UploadedFile($hangFile, 'hang.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true),
            ])->assertRedirect();
        } finally {
            @unlink($loaiFile);
            @unlink($hangFile);
        }

        $this->assertDatabaseHas('loaisanpham', ['tenloai' => 'Đồng hồ thể thao', 'tenloai_slug' => 'dong-ho-the-thao']);
        $this->assertDatabaseHas('hangsanxuat', ['tenhang' => 'Seiko', 'tenhang_slug' => 'seiko']);
    }

    public function test_inventory_receipt_uses_shared_stock_logic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $loai = LoaiSanPham::create(['tenloai' => 'Đồng hồ nam', 'tenloai_slug' => 'dong-ho-nam']);
        $hang = HangSanXuat::create(['tenhang' => 'Rolex', 'tenhang_slug' => 'rolex']);
        $supplier = NhaCungCap::create(['tennhacungcap' => 'Nhà cung cấp A', 'trangthai' => true]);
        $product = SanPham::create([
            'loaisanpham_id' => $loai->id,
            'hangsanxuat_id' => $hang->id,
            'tensanpham' => 'Rolex Test',
            'tensanpham_slug' => 'rolex-test',
            'soluong' => 2,
            'dongia' => 1000000,
        ]);

        $response = $this->actingAs($admin)->post('/admin/kho/nhap', [
            'nhacungcap_id' => $supplier->id,
            'ngaynhap' => now()->format('Y-m-d\TH:i'),
            'ghichu' => 'Test nhập kho',
            'sanpham_id' => [$product->id],
            'soluong' => [3],
            'dongianhap' => [900000],
        ]);

        $response->assertRedirect(route('admin.kho.danhsach'));
        $this->assertDatabaseHas('sanpham', ['id' => $product->id, 'soluong' => 5]);
        $this->assertDatabaseCount('phieunhap', 1);
        $this->assertDatabaseHas('chitietphieunhap', [
            'sanpham_id' => $product->id,
            'soluong' => 3,
            'dongianhap' => 900000,
        ]);
    }
}
