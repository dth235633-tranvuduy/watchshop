<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
use App\Models\DanhGia;
use App\Models\SanPham;
use App\Models\TinhTrang;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class KhachHangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getHome()
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $user->load('DonHang');

        return view('user.home', compact('user'));
    }

    public function getHoSo()
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $user->load('DonHang');

        return view('user.home', compact('user'));
    }

    public function getDoiMatKhau()
    {
        return view('user.doimatkhau');
    }

    public function getDonHang()
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $user->load(['DonHang' => function ($query): void {
            $query->with(['TinhTrang', 'DonHang_ChiTiet.SanPham'])
                ->latest();
        }]);

        return view('user.donhang', compact('user'));
    }

    public function getDatHang()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('frontend.giohang')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        /** @var User $user */
        $user = Auth::user();
        $subtotal = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
        $voucher = session('voucher');
        $discount = $voucher ? (float) ($voucher['discount'] ?? 0) : 0;
        $total = max(0, $subtotal - $discount);

        return view('user.dathang', compact('cart', 'subtotal', 'discount', 'total', 'voucher', 'user'));
    }

    public function postApDungVoucher(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);
        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
        $voucher = Voucher::where('code', strtoupper(trim($request->string('code')->toString())))->first();

        if (! $voucher || ! $voucher->isUsable($subtotal)) {
            return back()->with('error', 'Mã giảm giá không hợp lệ, đã hết hạn hoặc chưa đủ điều kiện.');
        }

        session()->put('voucher', [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'discount' => $voucher->discountFor($subtotal),
        ]);

        return back()->with('success', 'Đã áp dụng mã giảm giá.');
    }

    public function postDanhGia(Request $request, int $sanphamId)
    {
        $validated = $request->validate([
            'sosao' => ['required', 'integer', 'between:1,5'],
            'binhluan' => ['nullable', 'string', 'max:2000'],
        ]);
        DanhGia::create([
            'sanpham_id' => $sanphamId,
            'user_id' => Auth::id(),
            'sosao' => $validated['sosao'],
            'binhluan' => $validated['binhluan'] ?? null,
            'trangthai' => 'cho_duyet',
        ]);

        return back()->with('success', 'Đánh giá đã được gửi và đang chờ duyệt.');
    }

    public function postDatHang(Request $request)
    {
        $validated = $request->validate([
            'tennguoinhan' => ['required', 'string', 'max:255'],
            'dienthoaigiaohang' => ['required', 'string', 'max:20'],
            'diachigiaohang' => ['required', 'string', 'max:255'],
            'ghichu' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('frontend.giohang')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        try {
            DB::beginTransaction();

            $donhang = new DonHang;
            $donhang->user_id = Auth::id();
            $status = TinhTrang::query()->orderBy('id')->first()
                ?? TinhTrang::create(['tinhtrang' => 'Chờ duyệt']);
            $donhang->tinhtrang_id = $status->id;
            $voucherData = session('voucher');
            $voucher = $voucherData ? Voucher::lockForUpdate()->find($voucherData['id']) : null;
            $subtotal = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
            $discount = $voucher && $voucher->isUsable($subtotal) ? $voucher->discountFor($subtotal) : 0;
            $donhang->voucher_id = $voucher?->id;
            $donhang->tiengiam = $discount;
            $donhang->tongtien = max(0, $subtotal - $discount);
            $donhang->tennguoinhan = $validated['tennguoinhan'];
            $donhang->dienthoaigiaohang = $validated['dienthoaigiaohang'];
            $donhang->diachigiaohang = $validated['diachigiaohang'];
            $donhang->ghichu = $validated['ghichu'] ?? null;
            $donhang->save();

            foreach ($cart as $item) {
                $soluong = (int) ($item['qty'] ?? 0);
                $sanpham = SanPham::whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (! $sanpham || $soluong < 1 || $sanpham->soluong < $soluong) {
                    throw new \RuntimeException(
                        'Sản phẩm "'.($item['name'] ?? 'không xác định').'" không đủ số lượng tồn kho.'
                    );
                }

                $chitiet = new DonHang_ChiTiet;
                $chitiet->donhang_id = $donhang->id;
                $chitiet->sanpham_id = $sanpham->id;
                $chitiet->soluongban = $soluong;
                $chitiet->dongiaban = round((float) $sanpham->dongia * (100 - (int) $sanpham->giamgia) / 100, 2);
                $chitiet->save();

                $sanpham->decrement('soluong', $soluong);
            }

            session()->forget('cart');
            if ($voucher) {
                $voucher->increment('daludung');
            }
            session()->forget('voucher');
            DB::commit();

            return redirect()->route('user.dathangthanhcong');
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return back()->withInput()->with('error', 'Đặt hàng không thành công. Vui lòng thử lại.');
        }
    }

    public function getDatHangThanhCong()
    {
        return view('user.dathangthanhcong');
    }

    public function postHoSo(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('user.home')->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function postDoiMatKhau(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Mật khẩu cũ không chính xác.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('user.home')->with('success', 'Đổi mật khẩu thành công.');
    }

    public function postDangXuat(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home')->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
