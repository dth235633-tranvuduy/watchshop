<?php

namespace App\Http\Controllers;

use App\Models\LoaiSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoaiSanPhamController extends Controller
{
    public function getDanhSach()
    {
        $loaisanpham = LoaiSanPham::all();

        return view('admin.loaisanpham.danhsach', compact('loaisanpham'));
    }

    public function getThem()
    {
        return view('admin.loaisanpham.them');
    }

    public function postThem(Request $request)
    {
        $request->validate([
            'tenloai' => 'required|unique:loaisanpham,tenloai',
        ]);

        LoaiSanPham::create([
            'tenloai' => $request->tenloai,
            'tenloai_slug' => Str::slug($request->tenloai, '-'),
        ]);

        return redirect()->route('admin.loaisanpham.danhsach')->with('success', 'Thêm loại sản phẩm thành công.');
    }

    public function getSua(int $id)
    {
        $loaisanpham = LoaiSanPham::findOrFail($id);

        return view('admin.loaisanpham.sua', compact('loaisanpham'));
    }

    public function postSua(Request $request, int $id)
    {
        $loaisanpham = LoaiSanPham::findOrFail($id);

        $request->validate([
            'tenloai' => 'required|unique:loaisanpham,tenloai,'.$id,
        ]);

        $loaisanpham->tenloai = $request->tenloai;
        $loaisanpham->tenloai_slug = Str::slug($request->tenloai, '-');
        $loaisanpham->save();

        return redirect()->route('admin.loaisanpham.danhsach')->with('success', 'Cập nhật loại sản phẩm thành công.');
    }

    public function getXoa(int $id)
    {
        $loaisanpham = LoaiSanPham::findOrFail($id);
        $loaisanpham->delete();

        return redirect()->route('admin.loaisanpham.danhsach')->with('success', 'Xóa loại sản phẩm thành công.');
    }
}
