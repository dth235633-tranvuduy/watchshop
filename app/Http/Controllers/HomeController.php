<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\DanhGia;
use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\PhongCach;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function getHome()
    {
        $sanpham = SanPham::with(['LoaiSanPham', 'HangSanXuat'])
            ->whereNotNull('hinhanh')
            ->where('hinhanh', '!=', '')
            ->latest()
            ->take(8)
            ->get();
        $hangsanxuat = HangSanXuat::all();
        $loaisanpham = LoaiSanPham::all();
        $banners = Banner::where('trang_thai', true)->orderBy('thu_tu')->get();

        return view('frontend.home', compact('sanpham', 'hangsanxuat', 'loaisanpham', 'banners'));
    }

    public function getSanPham(Request $request, ?string $tenloai_slug = null)
    {
        $query = SanPham::with(['LoaiSanPham', 'HangSanXuat']);

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($productQuery) use ($search): void {
                $productQuery->where('tensanpham', 'like', '%'.$search.'%')
                    ->orWhere('motasanpham', 'like', '%'.$search.'%');
            });
        }

        $categorySlug = $tenloai_slug ?: trim((string) $request->query('category', ''));
        if ($categorySlug !== '') {
            $query->whereHas('LoaiSanPham', function ($categoryQuery) use ($categorySlug): void {
                $categoryQuery->where('tenloai_slug', $categorySlug);
            });
        }

        $hangsanxuatId = $request->integer('hangsanxuat_id');
        if ($hangsanxuatId > 0) {
            $query->where('hangsanxuat_id', $hangsanxuatId);
        }

        $priceMin = max(0, (float) $request->query('price_min', 0));
        $priceMax = max(0, (float) $request->query('price_max', 0));
        if ($priceMin > 0) {
            $query->where('dongia', '>=', $priceMin);
        }
        if ($priceMax > 0) {
            $query->where('dongia', '<=', $priceMax);
        }

        $chatlieuday = trim((string) $request->query('chatlieuday', ''));
        if ($chatlieuday !== '') {
            $query->where('chatlieuday', $chatlieuday);
        }

        $phongcach = trim((string) $request->query('phongcach', ''));
        if ($phongcach !== '') {
            $query->where('phongcach', $phongcach);
        }

        $sanpham = $query->orderByRaw("CASE WHEN hinhanh IS NULL OR hinhanh = '' THEN 1 ELSE 0 END")
            ->latest()
            ->paginate(12)
            ->withQueryString();
        $loaisanpham = LoaiSanPham::all();
        $hangsanxuat = HangSanXuat::all();
        $phongcachList = PhongCach::orderBy('tenphongcach')->get();

        return view('frontend.sanpham', [
            'sanpham' => $sanpham,
            'loaisanpham' => $loaisanpham,
            'hangsanxuat' => $hangsanxuat,
            'phongcach' => $phongcachList,
            'search' => $search,
            'tenloai_slug' => $categorySlug,
            'hangsanxuatId' => $hangsanxuatId,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'chatlieudayFilter' => $chatlieuday,
            'phongcachFilter' => $phongcach,
        ]);
    }

    public function getSanPham_ChiTiet(string $tenloai_slug, string $tensanpham_slug)
    {
        $sanpham = SanPham::with(['LoaiSanPham', 'HangSanXuat'])
            ->where('tensanpham_slug', $tensanpham_slug)
            ->firstOrFail();
        $related = SanPham::with(['LoaiSanPham', 'HangSanXuat'])
            ->where('loaisanpham_id', $sanpham->loaisanpham_id)
            ->where('id', '!=', $sanpham->id)
            ->latest()
            ->take(4)
            ->get();
        $reviews = DanhGia::with('User')
            ->where('sanpham_id', $sanpham->id)
            ->where('trangthai', 'hien_thi')
            ->latest()
            ->get();

        return view('frontend.sanpham_chitiet', compact('sanpham', 'related', 'reviews'));
    }

    public function getHinhAnh(string $path)
    {
        $disk = Storage::disk('private');

        if (! $disk->exists($path)) {
            return $this->placeholderImage();
        }

        $fullPath = $disk->path($path);
        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        if (! str_starts_with($mimeType, 'image/')) {
            return $this->placeholderImage();
        }

        return response()->file($fullPath, ['Content-Type' => $mimeType]);
    }

    /**
     * Trả về ảnh placeholder dạng SVG khi ảnh gốc không tồn tại hoặc không hợp lệ.
     */
    private function placeholderImage()
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="250" viewBox="0 0 300 250">'
            .'<rect width="300" height="250" fill="#f5f1e8"/>'
            .'<text x="150" y="125" font-family="sans-serif" font-size="16" fill="#b0a68f" text-anchor="middle" dominant-baseline="middle">No Image</text>'
            .'</svg>';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function getGioHang()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
        $voucher = session('voucher');
        $discount = $voucher ? (float) ($voucher['discount'] ?? 0) : 0;
        $shipping = 0;
        $vat = round(max(0, $total - $discount) * 0.10, 2);
        $grandTotal = max(0, $total - $discount + $shipping + $vat);

        if ($cart === []) {
            return view('frontend.giohangrong');
        }

        return view('frontend.giohang', compact('cart', 'total', 'discount', 'shipping', 'vat', 'grandTotal', 'voucher'));
    }

    public function getGioHang_Them(Request $request, string $tensanpham_slug)
    {
        $sanpham = SanPham::where('tensanpham_slug', $tensanpham_slug)->firstOrFail();
        $price = round((float) $sanpham->dongia * (100 - (int) $sanpham->giamgia) / 100, 2);
        if ($sanpham->soluong < 1) {
            return redirect()->route('frontend.giohang')
                ->with('error', 'Sản phẩm hiện đã hết hàng.');
        }

        $quantity = max(1, min($request->integer('quantity', 1), 20, $sanpham->soluong));

        $cart = session()->get('cart', []);

        if (isset($cart[$sanpham->id])) {
            if ($cart[$sanpham->id]['qty'] + $quantity > $sanpham->soluong) {
                return redirect()->route('frontend.giohang')
                    ->with('error', 'Số lượng sản phẩm trong kho không đủ.');
            }

            $cart[$sanpham->id]['qty'] += $quantity;
        } else {
            if ($sanpham->soluong < 1) {
                return redirect()->route('frontend.giohang')
                    ->with('error', 'Sản phẩm hiện đã hết hàng.');
            }

            $cart[$sanpham->id] = [
                'product_id' => $sanpham->id,
                'name' => $sanpham->tensanpham,
                'slug' => $sanpham->tensanpham_slug,
                'price' => $price,
                'qty' => $quantity,
                'image' => $sanpham->hinhanh,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('frontend.giohang')->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function getGioHang_Xoa(int $row_id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$row_id]);
        session()->put('cart', $cart);

        return redirect()->route('frontend.giohang')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function getGioHang_Giam(int $row_id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$row_id])) {
            $currentQuantity = (int) $cart[$row_id]['qty'];
            if ($currentQuantity <= 1) {
                session()->flash('error', 'Số lượng tối thiểu của sản phẩm là 1.');
            } else {
                $cart[$row_id]['qty'] = $currentQuantity - 1;
                session()->flash('success', 'Đã giảm số lượng sản phẩm.');
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('frontend.giohang');
    }

    public function getGioHang_Tang(int $row_id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$row_id])) {
            $sanpham = DB::transaction(function () use ($row_id): ?SanPham {
                return SanPham::whereKey($row_id)->lockForUpdate()->first();
            });

            if (! $sanpham || $cart[$row_id]['qty'] >= $sanpham->soluong) {
                return redirect()->route('frontend.giohang')
                    ->with('error', 'Số lượng sản phẩm trong kho không đủ.');
            }

            if ($cart[$row_id]['qty'] >= 20) {
                return redirect()->route('frontend.giohang')
                    ->with('error', 'Mỗi sản phẩm chỉ được đặt tối đa 20.');
            }

            $cart[$row_id]['qty']++;
            session()->flash('success', 'Đã tăng số lượng sản phẩm.');
        }

        session()->put('cart', $cart);

        return redirect()->route('frontend.giohang');
    }

    public function getTuyenDung()
    {
        return view('frontend.tuyendung');
    }

    public function getLienHe()
    {
        return view('frontend.lienhe');
    }
}
