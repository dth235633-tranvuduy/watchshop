<?php

namespace App\Http\Controllers;

use App\Models\HangSanXuat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HangSanXuatController extends Controller
{
    public function getDanhSach()
    {
        $hangsanxuat = HangSanXuat::all();

        return view('admin.hangsanxuat.danhsach', compact('hangsanxuat'));
    }

    public function getThem()
    {
        return view('admin.hangsanxuat.them');
    }

    public function postThem(Request $request)
    {
        $request->validate([
            'tenhang' => 'required|unique:hangsanxuat,tenhang',
            'hinhanh' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hinhanh = null;
        if ($request->hasFile('hinhanh')) {
            $hinhanh = $request->file('hinhanh')->store('hangsanxuat', 'private');
        }

        HangSanXuat::create([
            'tenhang' => $request->tenhang,
            'tenhang_slug' => Str::slug($request->tenhang, '-'),
            'hinhanh' => $hinhanh,
        ]);

        return redirect()->route('admin.hangsanxuat.danhsach')->with('success', 'Thêm hãng sản xuất thành công.');
    }

    public function getSua(int $id)
    {
        $hangsanxuat = HangSanXuat::findOrFail($id);

        return view('admin.hangsanxuat.sua', compact('hangsanxuat'));
    }

    public function postSua(Request $request, int $id)
    {
        $hangsanxuat = HangSanXuat::findOrFail($id);

        $request->validate([
            'tenhang' => 'required|unique:hangsanxuat,tenhang,'.$id,
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $hangsanxuat->tenhang = $request->tenhang;
        $hangsanxuat->tenhang_slug = Str::slug($request->tenhang, '-');

        if ($request->hasFile('hinhanh')) {
            $hangsanxuat->hinhanh = $request->file('hinhanh')->store('hangsanxuat', 'private');
        }

        $hangsanxuat->save();

        return redirect()->route('admin.hangsanxuat.danhsach')->with('success', 'Cập nhật hãng sản xuất thành công.');
    }

    public function getXoa(int $id)
    {
        $hangsanxuat = HangSanXuat::findOrFail($id);
        $hangsanxuat->delete();

        return redirect()->route('admin.hangsanxuat.danhsach')->with('success', 'Xóa hãng sản xuất thành công.');
    }
}
