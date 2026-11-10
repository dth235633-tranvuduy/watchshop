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
        $donhang = DonHang::with(['User', 'TinhTrang', 'DonHang_ChiTiet.SanPham'])
            ->latest()
            ->get();

        return view('admin.donhang.danhsach', compact('donhang'));
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
        $tinhtrang = TinhTrang::all();

        return view('admin.donhang.sua', compact('donhang', 'tinhtrang'));
    }

    public function postSua(Request $request, int $id)
    {
        $donhang = DonHang::findOrFail($id);

        $validated = $request->validate([
            'dienthoaigiaohang' => ['required', 'string', 'max:20'],
            'diachigiaohang' => ['required', 'string', 'max:255'],
            'tinhtrang_id' => ['required', 'integer', 'exists:tinhtrang,id'],
            'tennguoinhan' => ['required', 'string', 'max:255'],
            'ghichu' => ['nullable', 'string', 'max:1000'],
        ]);

        $donhang->tennguoinhan = $validated['tennguoinhan'];
        $donhang->dienthoaigiaohang = $validated['dienthoaigiaohang'];
        $donhang->diachigiaohang = $validated['diachigiaohang'];
        $donhang->tinhtrang_id = $validated['tinhtrang_id'];
        $donhang->ghichu = $validated['ghichu'] ?? null;
        $donhang->save();

        return redirect()->route('admin.donhang.danhsach')->with('success', 'Cập nhật đơn hàng thành công.');
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
