<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
use App\Models\SanPham;
use App\Models\TinhTrang;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

        $user->load(['DonHang' => function ($query): void {
            $query->with(['TinhTrang', 'DonHang_ChiTiet.SanPham'])->latest();
        }]);

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
            $query->with(['TinhTrang', 'DonHang_ChiTiet.SanPham', 'LichSu'])
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
        if (! $user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt hàng.');
        }

        $subtotal = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
        $voucher = session('voucher');
        $discount = $voucher ? (float) ($voucher['discount'] ?? 0) : 0;
        $shipping = 0;
        $vat = round(max(0, $subtotal - $discount) * 0.10, 2);
        $total = max(0, $subtotal - $discount + $shipping + $vat);

        return view('user.dathang', compact('cart', 'subtotal', 'discount', 'shipping', 'vat', 'total', 'voucher', 'user'));
    }

    public function postApDungVoucher(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);
        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
        $voucher = Voucher::where('code', strtoupper(trim($request->string('code')->toString())))->first();

        if (! $voucher) {
            return back()->with('error', 'Không tìm thấy mã giảm giá này.');
        }
        if (! $voucher->isUsable($subtotal)) {
            return back()->with('error', $voucher->unavailableReason($subtotal) ?? 'Mã giảm giá chưa thể áp dụng.');
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
            'dienthoaigiaohang' => ['required', 'string', 'max:255'],
            'diachigiaohang' => ['required', 'string', 'max:255'],
            'ghichu' => ['nullable', 'string', 'max:1000'],
            'phuongthucthanhtoan' => ['required', 'string', 'in:cod,bank'],
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
            $status = TinhTrang::firstOrCreate(['tinhtrang' => 'Chưa thanh toán']);
            $donhang->tinhtrang_id = $status->id;
            $voucherData = session('voucher');
            $voucher = $voucherData ? Voucher::lockForUpdate()->find($voucherData['id']) : null;
            $subtotal = collect($cart)->sum(fn (array $item): float => $item['price'] * $item['qty']);
            $discount = $voucher && $voucher->isUsable($subtotal) ? $voucher->discountFor($subtotal) : 0;
            $vat = round(max(0, $subtotal - $discount) * 0.10, 2);
            $shipping = 0;
            $donhang->voucher_id = $voucher?->id;
            $donhang->tiengiam = $discount;
            $donhang->tongtien = max(0, $subtotal - $discount + $vat + $shipping);
            $donhang->tennguoinhan = $validated['tennguoinhan'];
            $donhang->dienthoaigiaohang = $validated['dienthoaigiaohang'];
            $donhang->diachigiaohang = $validated['diachigiaohang'];
            $donhang->ghichu = $validated['ghichu'] ?? null;
            $donhang->phuongthucthanhtoan = $validated['phuongthucthanhtoan'];
            $donhang->save();
            $donhang->ghiLichSu(Auth::id(), 'Đơn hàng được tạo.');

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
                $chitiet->tensanpham_snapshot = $sanpham->tensanpham;
                $chitiet->soluongban = $soluong;
                $chitiet->dongiaban = round((float) $sanpham->dongia * (100 - (int) $sanpham->giamgia) / 100, 2);
                $chitiet->save();

                $sanpham->decrement('soluong', $soluong);
            }

            DB::commit();
            session()->forget('cart');
            session()->forget('voucher');
            session()->flash('latest_order_id', $donhang->id);

            return redirect()->route('user.dathangthanhcong');
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return back()->withInput()->with('error', 'Đặt hàng không thành công. Vui lòng thử lại.');
        }
    }

    public function getDatHangThanhCong()
    {
        session()->forget('cart');
        session()->forget('voucher');

        $orderId = session('latest_order_id');
        $order = $orderId
            ? DonHang::with('TinhTrang')->where('user_id', Auth::id())->find($orderId)
            : DonHang::with('TinhTrang')
                ->where('user_id', Auth::id())
                ->where('phuongthucthanhtoan', 'bank')
                ->latest('id')
                ->first();

        return view('user.dathangthanhcong', compact('order'));
    }

    public function postThanhToan(int $id)
    {
        try {
            DB::transaction(function () use ($id): void {
                $order = DonHang::where('user_id', Auth::id())
                    ->with('TinhTrang')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($order->TinhTrang?->tinhtrang === 'Đã hủy') {
                    abort(422, 'Đơn hàng đã hủy không thể thanh toán.');
                }

                if ($order->TinhTrang?->tinhtrang === 'Đã thanh toán') {
                    return;
                }

                if ($order->voucher_id) {
                    $voucher = Voucher::lockForUpdate()->find($order->voucher_id);
                    $subtotal = $order->DonHang_ChiTiet()->sum(DB::raw('soluongban * dongiaban'));

                    if (! $voucher || ! $voucher->isUsable((float) $subtotal)) {
                        abort(422, 'Mã giảm giá của đơn hàng không còn đủ điều kiện thanh toán.');
                    }

                    $voucher->increment('daludung');
                }

                $paidStatus = TinhTrang::firstOrCreate(['tinhtrang' => 'Đã thanh toán']);
                $order->update(['tinhtrang_id' => $paidStatus->id]);
                $order->ghiLichSu(Auth::id(), 'Khách hàng đã thanh toán đơn hàng.');
            });
        } catch (Throwable $exception) {
            if ($exception instanceof HttpExceptionInterface) {
                return back()->with('error', $exception->getMessage());
            }

            report($exception);

            return back()->with('error', 'Thanh toán không thành công. Vui lòng thử lại.');
        }

        return back()->with('success', 'Thanh toán thành công.');
    }

    public function postHuyDon(Request $request, int $id)
    {
        $validated = $request->validate([
            'lydo_huy' => ['required', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($id, $validated): void {
                $order = DonHang::where('user_id', Auth::id())
                    ->with('TinhTrang')
                    ->with('DonHang_ChiTiet')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($order->TinhTrang?->tinhtrang === 'Đã thanh toán') {
                    abort(422, 'Đơn hàng đã thanh toán không thể hủy.');
                }
                if ($order->TinhTrang?->tinhtrang === 'Đã hủy') {
                    return;
                }

                $cancelledStatus = TinhTrang::firstOrCreate(['tinhtrang' => 'Đã hủy']);
                foreach ($order->DonHang_ChiTiet as $detail) {
                    SanPham::whereKey($detail->sanpham_id)
                        ->lockForUpdate()
                        ->increment('soluong', $detail->soluongban);
                }

                $order->update([
                    'tinhtrang_id' => $cancelledStatus->id,
                    'lydo_huy' => trim($validated['lydo_huy']),
                ]);
                $order->ghiLichSu(Auth::id(), 'Khách hàng hủy đơn. Lý do: '.trim($validated['lydo_huy']));
            });
        } catch (Throwable $exception) {
            if ($exception instanceof HttpExceptionInterface) {
                return back()->with('error', $exception->getMessage());
            }

            report($exception);

            return back()->with('error', 'Hủy đơn hàng không thành công. Vui lòng thử lại.');
        }

        return back()->with('success', 'Đã hủy đơn hàng và hoàn lại tồn kho.');
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
