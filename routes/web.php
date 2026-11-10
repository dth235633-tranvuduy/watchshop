<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\HangSanXuatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\LoaiSanPhamController;
use App\Http\Controllers\QuanTriVienController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\TinhTrangController;
use App\Http\Controllers\TuyenDungController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Đăng ký các route xác thực mặc định (Login, Register, Password...)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ============================================================================
// NHÓM 1: ROUTES KHÁCH HÀNG (Frontend) - Không cần đăng nhập
// ============================================================================
Route::name('frontend.')->group(function () {
    Route::get('/', [HomeController::class, 'getHome'])->name('home');
    Route::get('/home', [HomeController::class, 'getHome'])->name('home');

    Route::get('/san-pham', [HomeController::class, 'getSanPham'])->name('sanpham');
    Route::get('/san-pham/{tenloai_slug}', [HomeController::class, 'getSanPham'])->name('sanpham.phanloai');
    Route::get('/san-pham/{tenloai_slug}/{tensanpham_slug}', [HomeController::class, 'getSanPham_ChiTiet'])->name('sanpham.chitiet');
    Route::get('/hinh-anh/{path}', [HomeController::class, 'getHinhAnh'])->where('path', '.*')->name('hinhanh');

    Route::get('/gio-hang', [HomeController::class, 'getGioHang'])->name('giohang');
    Route::get('/gio-hang/them/{tensanpham_slug}', [HomeController::class, 'getGioHang_Them'])->name('giohang.them');
    Route::get('/gio-hang/xoa/{row_id}', [HomeController::class, 'getGioHang_Xoa'])->name('giohang.xoa');
    Route::get('/gio-hang/giam/{row_id}', [HomeController::class, 'getGioHang_Giam'])->name('giohang.giam');
    Route::get('/gio-hang/tang/{row_id}', [HomeController::class, 'getGioHang_Tang'])->name('giohang.tang');

    Route::get('/tuyen-dung', [HomeController::class, 'getTuyenDung'])->name('tuyendung');
    Route::get('/lien-he', [HomeController::class, 'getLienHe'])->name('lienhe');
});

// ============================================================================
// NHÓM 2: ROUTES NGƯỜI DÙNG (User) - Cần đăng nhập
// ============================================================================
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/', [KhachHangController::class, 'getHome'])->name('home');
    Route::get('/home', [KhachHangController::class, 'getHome'])->name('home');

    Route::get('/ho-so', [KhachHangController::class, 'getHoSo'])->name('hoso');
    Route::post('/ho-so', [KhachHangController::class, 'postHoSo'])->name('hoso');

    Route::get('/doi-mat-khau', [KhachHangController::class, 'getDoiMatKhau'])->name('doimatkhau');
    Route::post('/doi-mat-khau', [KhachHangController::class, 'postDoiMatKhau'])->name('doimatkhau');

    Route::get('/don-hang', [KhachHangController::class, 'getDonHang'])->name('donhang');
    Route::get('/dat-hang', [KhachHangController::class, 'getDatHang'])->name('dathang');
    Route::post('/dat-hang', [KhachHangController::class, 'postDatHang'])->name('dathang');
    Route::get('/dat-hang-thanh-cong', [KhachHangController::class, 'getDatHangThanhCong'])->name('dathangthanhcong');

    Route::post('/dang-xuat', [KhachHangController::class, 'postDangXuat'])->name('dangxuat');
});

// Route alias hỗ trợ trang đăng nhập user
Route::get('/user/dang-nhap', [AuthController::class, 'showLogin'])->name('user.dangnhap');

// ============================================================================
// NHÓM 3: ROUTES QUẢN TRỊ (Admin) - Cần đăng nhập
// ============================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/', [QuanTriVienController::class, 'getHome'])->name('home');
    Route::get('/home', [QuanTriVienController::class, 'getHome'])->name('home');

    // --- LoaiSanPham ---
    Route::get('/loaisanpham', [LoaiSanPhamController::class, 'getDanhSach'])->name('loaisanpham.danhsach');
    Route::get('/loaisanpham/them', [LoaiSanPhamController::class, 'getThem'])->name('loaisanpham.them');
    Route::post('/loaisanpham/them', [LoaiSanPhamController::class, 'postThem'])->name('loaisanpham.postthem');
    Route::get('/loaisanpham/sua/{id}', [LoaiSanPhamController::class, 'getSua'])->name('loaisanpham.sua');
    Route::post('/loaisanpham/sua/{id}', [LoaiSanPhamController::class, 'postSua'])->name('loaisanpham.postsua');
    Route::get('/loaisanpham/xoa/{id}', [LoaiSanPhamController::class, 'getXoa'])->name('loaisanpham.xoa');

    // --- HangSanXuat ---
    Route::get('/hangsanxuat', [HangSanXuatController::class, 'getDanhSach'])->name('hangsanxuat.danhsach');
    Route::get('/hangsanxuat/them', [HangSanXuatController::class, 'getThem'])->name('hangsanxuat.them');
    Route::post('/hangsanxuat/them', [HangSanXuatController::class, 'postThem'])->name('hangsanxuat.postthem');
    Route::get('/hangsanxuat/sua/{id}', [HangSanXuatController::class, 'getSua'])->name('hangsanxuat.sua');
    Route::post('/hangsanxuat/sua/{id}', [HangSanXuatController::class, 'postSua'])->name('hangsanxuat.postsua');
    Route::get('/hangsanxuat/xoa/{id}', [HangSanXuatController::class, 'getXoa'])->name('hangsanxuat.xoa');

    // --- SanPham ---
    Route::get('/sanpham', [SanPhamController::class, 'getDanhSach'])->name('sanpham.danhsach');
    Route::get('/sanpham/them', [SanPhamController::class, 'getThem'])->name('sanpham.them');
    Route::post('/sanpham/them', [SanPhamController::class, 'postThem'])->name('sanpham.postthem');
    Route::get('/sanpham/sua/{id}', [SanPhamController::class, 'getSua'])->name('sanpham.sua');
    Route::post('/sanpham/sua/{id}', [SanPhamController::class, 'postSua'])->name('sanpham.postsua');
    Route::get('/sanpham/xoa/{id}', [SanPhamController::class, 'getXoa'])->name('sanpham.xoa');

    // --- TinhTrang ---
    Route::get('/tinhtrang', [TinhTrangController::class, 'getDanhSach'])->name('tinhtrang.danhsach');
    Route::get('/tinhtrang/them', [TinhTrangController::class, 'getThem'])->name('tinhtrang.them');
    Route::post('/tinhtrang/them', [TinhTrangController::class, 'postThem'])->name('tinhtrang.postthem');
    Route::get('/tinhtrang/sua/{id}', [TinhTrangController::class, 'getSua'])->name('tinhtrang.sua');
    Route::post('/tinhtrang/sua/{id}', [TinhTrangController::class, 'postSua'])->name('tinhtrang.postsua');
    Route::get('/tinhtrang/xoa/{id}', [TinhTrangController::class, 'getXoa'])->name('tinhtrang.xoa');

    // --- DonHang ---
    Route::get('/donhang', [DonHangController::class, 'getDanhSach'])->name('donhang.danhsach');
    Route::get('/donhang/sua/{id}', [DonHangController::class, 'getSua'])->name('donhang.sua');
    Route::post('/donhang/sua/{id}', [DonHangController::class, 'postSua'])->name('donhang.postsua');
    Route::get('/donhang/xoa/{id}', [DonHangController::class, 'getXoa'])->name('donhang.xoa');

    // --- TuyenDung (Job applications) ---
    Route::get('/tuyendung', [TuyenDungController::class, 'getDanhSach'])->name('tuyendung.danhsach');
    Route::patch('/tuyendung/{id}', [TuyenDungController::class, 'postCapNhat'])->name('tuyendung.capnhat');
    Route::get('/tuyendung/{id}/cv', [TuyenDungController::class, 'getCv'])->name('tuyendung.cv');

    // --- NguoiDung (User) ---
    Route::get('/nguoidung', [UserController::class, 'getDanhSach'])->name('nguoidung.danhsach');
    Route::get('/nguoidung/them', [UserController::class, 'getThem'])->name('nguoidung.them');
    Route::post('/nguoidung/them', [UserController::class, 'postThem'])->name('nguoidung.postthem');
    Route::get('/nguoidung/sua/{id}', [UserController::class, 'getSua'])->name('nguoidung.sua');
    Route::post('/nguoidung/sua/{id}', [UserController::class, 'postSua'])->name('nguoidung.postsua');
    Route::get('/nguoidung/xoa/{id}', [UserController::class, 'getXoa'])->name('nguoidung.xoa');
});
