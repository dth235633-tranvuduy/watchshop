<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
use App\Models\TinhTrang;
use Illuminate\Http\Request;

class DonHangController extends Controller
{
    public function getDanhSach()
    {
        $this->ensureDefaultStatuses();
        $donhang = DonHang::with(['User', 'TinhTrang', 'DonHang_ChiTiet.SanPham'])
            ->latest()
            ->get();
        $tinhtrang = TinhTrang::orderBy('id')->get();

        return view('admin.donhang.danhsach', compact('donhang', 'tinhtrang'));
    }

    public function getThem()
    {
        abort(404);
    }

    public function postThem(Request $request)
    {
        // TODO: Xử lý thêm đơn hàng
        return redirect()->route('admin.donhang.danhsach');
    }

    public function getSua(int $id)
    {
        $donhang = DonHang::with(['User', 'TinhTrang', 'DonHang_ChiTiet.SanPham'])->findOrFail($id);
        $this->ensureDefaultStatuses();
        $tinhtrang = TinhTrang::all();

        return view('admin.donhang.sua', compact('donhang', 'tinhtrang'));
    }

    public function postSua(Request $request, int $id)
    {
        $donhang = DonHang::findOrFail($id);

        $validated = $request->validate([
            'dienthoaigiaohang' => ['required', 'string', 'max:20'],
            'diachigiaohang' => ['required', 'string', 'max:255'],
            'tennguoinhan' => ['required', 'string', 'max:255'],
            'ghichu' => ['nullable', 'string', 'max:1000'],
        ]);

        $donhang->tennguoinhan = $validated['tennguoinhan'];
        $donhang->dienthoaigiaohang = $validated['dienthoaigiaohang'];
        $donhang->diachigiaohang = $validated['diachigiaohang'];
        $donhang->ghichu = $validated['ghichu'] ?? null;
        $donhang->save();

        return redirect()->route('admin.donhang.danhsach')->with('success', 'Cập nhật đơn hàng thành công.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'tinhtrang_id' => ['required', 'integer', 'exists:tinhtrang,id'],
        ]);
        $donhang = DonHang::findOrFail($id);
        $donhang->update(['tinhtrang_id' => $validated['tinhtrang_id']]);

        return back()->with('success', 'Đã cập nhật tình trạng đơn hàng.');
    }

    private function ensureDefaultStatuses(): void
    {
        foreach (['Chưa thanh toán', 'Đã thanh toán', 'Đã hủy'] as $status) {
            TinhTrang::firstOrCreate(['tinhtrang' => $status]);
        }
    }

    public function getXoa(int $id)
    {
        $donhang = DonHang::findOrFail($id);

        // Xóa hết chi tiết đơn hàng trước để tránh lỗi ràng buộc khóa ngoại (foreign key)
        DonHang_ChiTiet::where('donhang_id', $id)->delete();

        $donhang->delete();

        return redirect()->route('admin.donhang.danhsach')->with('success', 'Xóa đơn hàng thành công.');
    }
}
