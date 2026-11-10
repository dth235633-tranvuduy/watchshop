<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
use App\Models\SanPham;
use App\Models\User;
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
        $total = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);

        return view('user.dathang', compact('cart', 'total', 'user'));
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
            $donhang->tinhtrang_id = 1;
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
