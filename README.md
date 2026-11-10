<div align="center">

# WatchShop

### Nền tảng thương mại điện tử kinh doanh đồng hồ trực tuyến

Ứng dụng web hỗ trợ khách hàng tìm kiếm, lựa chọn và đặt mua đồng hồ, đồng thời cung cấp hệ thống quản trị tập trung cho cửa hàng.

<p>
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Blade-Template-F7523F?style=for-the-badge&logo=laravel&logoColor=white" alt="Blade Template">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
</p>
<p>
  <img src="https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 8">
  <img src="https://img.shields.io/badge/SQLite-Database-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/Eloquent-ORM-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Eloquent ORM">
  <img src="https://img.shields.io/badge/PHPUnit-12-3C9CD7?style=for-the-badge&logo=php&logoColor=white" alt="PHPUnit 12">
</p>

</div>

---

## Mục lục

- [Tổng quan](#tổng-quan)
- [Tính năng](#tính-năng)
- [Công nghệ đã sử dụng](#công-nghệ-đã-sử-dụng)
- [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
- [Cài đặt và khởi chạy](#cài-đặt-và-khởi-chạy)
- [Tài khoản và phân quyền](#tài-khoản-và-phân-quyền)
- [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)
- [Cấu trúc dự án](#cấu-trúc-dự-án)
- [Mô hình dữ liệu](#mô-hình-dữ-liệu)
- [Các lệnh hỗ trợ dữ liệu](#các-lệnh-hỗ-trợ-dữ-liệu)
- [Kiểm thử và định dạng mã nguồn](#kiểm-thử-và-định-dạng-mã-nguồn)
- [Xử lý sự cố thường gặp](#xử-lý-sự-cố-thường-gặp)
- [Bảo mật khi triển khai](#bảo-mật-khi-triển-khai)
- [Giấy phép](#giấy-phép)

## Tổng quan

WatchShop mô phỏng quy trình bán đồng hồ trực tuyến:

1. Khách truy cập xem danh mục và thông tin chi tiết sản phẩm.
2. Khách lọc sản phẩm theo từ khóa, loại, thương hiệu và khoảng giá.
3. Khách thêm sản phẩm vào giỏ hàng, cập nhật số lượng và đặt hàng.
4. Người dùng đăng nhập để quản lý hồ sơ, đổi mật khẩu và theo dõi đơn hàng.
5. Quản trị viên quản lý dữ liệu cửa hàng và cập nhật trạng thái đơn hàng.

Ứng dụng sử dụng SQLite mặc định để đơn giản hóa việc cài đặt và phù hợp cho môi trường học tập, demo hoặc phát triển cục bộ. Có thể chuyển sang MySQL hoặc hệ quản trị cơ sở dữ liệu khác được Laravel hỗ trợ bằng cách điều chỉnh file môi trường.

## Công nghệ đã sử dụng

| Thành phần | Công nghệ |
|---|---|
| Ngôn ngữ lập trình | PHP 8.3+ |
| Backend framework | Laravel 13 |
| Giao diện | Blade Template, Tailwind CSS 4 |
| Build frontend | Vite 8 |
| Cơ sở dữ liệu | SQLite |
| ORM | Laravel Eloquent |
| Kiểm thử | PHPUnit 12 |
| Định dạng mã nguồn | Laravel Pint |
| Môi trường phát triển | Windows, WAMP/XAMPP, PHP CLI |

## Tính năng

### Dành cho khách hàng

- Trang chủ giới thiệu sản phẩm mới và thương hiệu.
- Danh sách sản phẩm có phân trang.
- Tìm kiếm theo tên và mô tả sản phẩm.
- Lọc theo loại sản phẩm, thương hiệu và khoảng giá.
- Xem thông tin chi tiết, giá, giảm giá, đánh giá và thông số sản phẩm.
- Giỏ hàng lưu trong session:
  - Thêm sản phẩm.
  - Tăng, giảm hoặc xóa sản phẩm.
  - Kiểm tra số lượng tồn kho khi cập nhật.
- Đăng ký, đăng nhập và đăng xuất.
- Cập nhật thông tin cá nhân.
- Đổi mật khẩu.
- Đặt hàng với thông tin người nhận, số điện thoại, địa chỉ và ghi chú.
- Xem lịch sử đơn hàng.
- Xem trang liên hệ và thông tin tuyển dụng.

### Dành cho quản trị viên

Khu vực quản trị nằm tại `/admin` và yêu cầu tài khoản có `role = admin`.

- Dashboard quản trị.
- CRUD loại sản phẩm.
- CRUD thương hiệu.
- CRUD sản phẩm, bao gồm hình ảnh, tồn kho, giá, giảm giá và thông số kỹ thuật.
- Quản lý trạng thái đơn hàng.
- Xem, cập nhật và xóa đơn hàng.
- Quản lý người dùng và vai trò.
- Xem hồ sơ ứng tuyển, cập nhật trạng thái và tải CV.

## Yêu cầu hệ thống

- PHP `8.3` trở lên.
- Composer.
- Node.js và npm.
- Git.
- Một trong các môi trường chạy PHP:
  - WAMP/XAMPP.
  - PHP built-in server.
  - Laravel Herd hoặc môi trường tương đương.

## Cài đặt và khởi chạy

### 1. Clone dự án

```bash
git clone https://github.com/dth235633-tranvuduy/watchshop.git
cd watchshop
```

### 2. Cài đặt dependency

```bash
composer install
npm install
```

### 3. Tạo file môi trường

Trên Windows PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Trên Command Prompt:

```bat
copy .env.example .env
php artisan key:generate
```

### 4. Chuẩn bị cơ sở dữ liệu

Dự án đã cấu hình SQLite trong `.env.example`. Đảm bảo file `database/database.sqlite` tồn tại, sau đó chạy migration:

```bash
php artisan migrate
```

Nếu muốn tạo lại toàn bộ cơ sở dữ liệu trong môi trường phát triển:

```bash
php artisan migrate:fresh --seed
```

> `DatabaseSeeder` hiện tạo tài khoản mẫu `test@example.com` với mật khẩu `password` từ factory mặc định. Tài khoản này không có quyền quản trị nếu chưa được gán `role = admin`. Không sử dụng tài khoản mẫu trong môi trường production.

### 5. Build tài nguyên frontend

```bash
npm run build
```

### 6. Chạy ứng dụng

```bash
php artisan serve
```

Mở [http://127.0.0.1:8000](http://127.0.0.1:8000) trên trình duyệt.

Trong quá trình phát triển giao diện, có thể chạy Vite ở một terminal riêng:

```bash
npm run dev
```

### Cài đặt nhanh

Composer đã cung cấp script thiết lập tự động:

```bash
composer run setup
```

Lệnh này cài dependency, tạo `.env`, sinh `APP_KEY`, chạy migration, cài package frontend và build tài nguyên.

## Tài khoản và phân quyền

Ứng dụng có hai nhóm quyền chính:

| Vai trò | Quyền |
|---|---|
| `user` | Mua hàng, quản lý hồ sơ, đổi mật khẩu và xem đơn hàng của bản thân |
| `admin` | Truy cập toàn bộ khu vực quản trị và quản lý dữ liệu hệ thống |

Middleware `role:admin` bảo vệ toàn bộ route dưới `/admin`. Không nên gán vai trò quản trị trực tiếp cho người dùng nếu chưa kiểm tra kỹ quyền truy cập.

## Hướng dẫn sử dụng

### Luồng mua hàng

1. Truy cập trang chủ hoặc `/san-pham`.
2. Tìm kiếm hoặc lọc sản phẩm.
3. Mở trang chi tiết sản phẩm.
4. Thêm sản phẩm vào `/gio-hang`.
5. Đăng nhập hoặc đăng ký tài khoản.
6. Nhập thông tin giao hàng tại `/user/dat-hang`.
7. Theo dõi đơn hàng tại `/user/don-hang`.

### Luồng quản trị

1. Đăng nhập bằng tài khoản có `role = admin`.
2. Truy cập `/admin`.
3. Khai báo loại sản phẩm và thương hiệu trước khi thêm sản phẩm.
4. Quản lý sản phẩm tại `/admin/sanpham`.
5. Theo dõi và cập nhật đơn hàng tại `/admin/donhang`.
6. Quản lý hồ sơ tuyển dụng tại `/admin/tuyendung`.

## Cấu trúc dự án

```text
app/
├── Console/Commands/       # Lệnh Artisan xử lý dữ liệu và hình ảnh
├── Http/Controllers/       # Controller frontend, user, admin và xác thực
├── Http/Middleware/        # Middleware phân quyền
└── Models/                 # Eloquent model

database/
├── migrations/             # Định nghĩa cấu trúc cơ sở dữ liệu
├── seeders/                # Dữ liệu khởi tạo và nhập dữ liệu
└── database.sqlite         # Cơ sở dữ liệu SQLite cục bộ

resources/
├── css/                    # CSS đầu vào
├── js/                     # JavaScript đầu vào
└── views/                  # Blade views frontend, user, admin và auth

routes/
└── web.php                # Route web của ứng dụng

storage/app/private/        # Hình ảnh sản phẩm, logo và CV không public
```

## Mô hình dữ liệu

Các bảng nghiệp vụ chính:

- `users`: tài khoản và vai trò người dùng.
- `loaisanpham`: loại sản phẩm.
- `hangsanxuat`: thương hiệu/nhà sản xuất.
- `sanpham`: thông tin, giá, tồn kho và hình ảnh sản phẩm.
- `tinhtrang`: danh mục trạng thái đơn hàng.
- `donhang`: thông tin đơn hàng và người nhận.
- `donhang_chitiet`: các sản phẩm thuộc đơn hàng.
- `don_ung_tuyens`: hồ sơ ứng tuyển và trạng thái xử lý.

Hình ảnh và CV được lưu trên private disk, sau đó được phục vụ qua các route có kiểm soát thay vì đặt trực tiếp trong thư mục public.

## Các lệnh hỗ trợ dữ liệu

### Gán ảnh có sẵn cho sản phẩm

Đặt ảnh vào `storage/app/private/import/anh-san-pham`, đặt tên file theo slug sản phẩm, sau đó chạy thử:

```bash
php artisan sanpham:import-images --dry-run
```

Nếu kết quả đúng, chạy lệnh ghi dữ liệu:

```bash
php artisan sanpham:import-images
```

### Tải ảnh sản phẩm từ URL

```bash
php artisan products:download-images
```

### Tải logo thương hiệu

```bash
php artisan brands:download-logos
```

Các lệnh tải dữ liệu bên ngoài cần kết nối mạng. Hãy kiểm tra nguồn ảnh, quyền sử dụng nội dung và kết quả tải trước khi dùng cho môi trường thật.

## Kiểm thử và định dạng mã nguồn

Chạy toàn bộ test:

```bash
php artisan test --compact
```

Hoặc:

```bash
composer test
```

Định dạng các file PHP đã thay đổi:

```bash
vendor/bin/pint --dirty --format agent
```

## Xử lý sự cố thường gặp

### Lỗi `No application encryption key has been specified`

Chạy:

```bash
php artisan key:generate
```

### Lỗi bảng hoặc cột chưa tồn tại

Kiểm tra cấu hình database trong `.env`, sau đó chạy:

```bash
php artisan migrate
```

Trong môi trường phát triển có thể dùng `php artisan migrate:fresh --seed`, nhưng lệnh này sẽ xóa toàn bộ dữ liệu hiện có.

### Giao diện không cập nhật hoặc lỗi manifest Vite

Chạy lại:

```bash
npm install
npm run build
```

### Hình ảnh không hiển thị

- Kiểm tra file có nằm trong `storage/app/private` hay không.
- Kiểm tra giá trị `hinhanh` trong bảng `sanpham`.
- Kiểm tra log tại `storage/logs/laravel.log`.
- Không đưa CV hoặc hình ảnh riêng tư vào thư mục public.

## Bảo mật khi triển khai

- Không commit file `.env`, `APP_KEY`, mật khẩu hoặc thông tin kết nối cơ sở dữ liệu.
- Đặt `APP_ENV=production` và `APP_DEBUG=false` trên production.
- Dùng mật khẩu mạnh cho tài khoản quản trị.
- Chỉ cấp quyền ghi cần thiết cho các thư mục `storage` và `bootstrap/cache`.
- Không sử dụng `migrate:fresh` trên production.
- Kiểm tra nguồn và giấy phép của hình ảnh tải từ bên ngoài.
- Sao lưu cơ sở dữ liệu trước các thao tác cập nhật dữ liệu lớn.

## Giấy phép

Dự án sử dụng framework Laravel, được phát hành theo [giấy phép MIT](https://opensource.org/licenses/MIT). Giấy phép và quyền sử dụng các tài nguyên sản phẩm, logo hoặc hình ảnh bên ngoài cần được kiểm tra riêng trước khi phát hành thương mại.
