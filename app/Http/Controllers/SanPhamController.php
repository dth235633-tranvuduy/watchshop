<?php

namespace App\Http\Controllers;

use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SanPhamController extends Controller
{
    public function getDanhSach()
    {
        $sanpham = SanPham::all();

        return view('admin.sanpham.danhsach', compact('sanpham'));
    }

    public function getThem()
    {
        $loaisanpham = LoaiSanPham::all();
        $hangsanxuat = HangSanXuat::all();

        return view('admin.sanpham.them', compact('loaisanpham', 'hangsanxuat'));
    }

    public function postThem(Request $request)
    {
        $request->validate([
            'loaisanpham_id' => 'required|exists:loaisanpham,id',
            'hangsanxuat_id' => 'required|exists:hangsanxuat,id',
            'tensanpham' => 'required|string|max:255|unique:sanpham,tensanpham',
            'soluong' => 'required|integer|min:0',
            'dongia' => 'required|numeric|min:0',
            'hinhanh' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'giamgia' => ['required', 'integer', 'between:0,100'],
            'danhgia' => ['required', 'numeric', 'between:0,5'],
            'chatlieuday' => ['nullable', 'string', 'max:255'],
            'matkinh' => ['nullable', 'string', 'max:255'],
            'dochieunuoc' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('hinhanh');
        $loaisanpham = LoaiSanPham::find($request->loaisanpham_id);
        $directory = $loaisanpham->tenloai_slug;
        $filename = Str::slug($request->tensanpham, '-').'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'private');

        SanPham::create([
            'loaisanpham_id' => $request->loaisanpham_id,
            'hangsanxuat_id' => $request->hangsanxuat_id,
            'tensanpham' => $request->tensanpham,
            'tensanpham_slug' => Str::slug($request->tensanpham, '-'),
            'soluong' => $request->soluong,
            'dongia' => $request->dongia,
            'hinhanh' => $path,
            'motasanpham' => $request->motasanpham,
            'giamgia' => $request->giamgia,
            'danhgia' => $request->danhgia,
            'chatlieuday' => $request->chatlieuday,
            'matkinh' => $request->matkinh,
            'dochieunuoc' => $request->dochieunuoc,
        ]);

        return redirect()->route('admin.sanpham.danhsach')->with('success', 'Thêm sản phẩm thành công.');
    }

    public function getSua(int $id)
    {
        $sanpham = SanPham::findOrFail($id);
        $loaisanpham = LoaiSanPham::all();
        $hangsanxuat = HangSanXuat::all();

        return view('admin.sanpham.sua', compact('sanpham', 'loaisanpham', 'hangsanxuat'));
    }

    public function postSua(Request $request, int $id)
    {
        $sanpham = SanPham::findOrFail($id);

        $request->validate([
            'loaisanpham_id' => 'required|exists:loaisanpham,id',
            'hangsanxuat_id' => 'required|exists:hangsanxuat,id',
            'tensanpham' => 'required|string|max:255|unique:sanpham,tensanpham,'.$id,
            'soluong' => 'required|integer|min:0',
            'dongia' => 'required|numeric|min:0',
            'hinhanh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'giamgia' => ['required', 'integer', 'between:0,100'],
            'danhgia' => ['required', 'numeric', 'between:0,5'],
            'chatlieuday' => ['nullable', 'string', 'max:255'],
            'matkinh' => ['nullable', 'string', 'max:255'],
            'dochieunuoc' => ['nullable', 'string', 'max:255'],
        ]);

        $sanpham->loaisanpham_id = $request->loaisanpham_id;
        $sanpham->hangsanxuat_id = $request->hangsanxuat_id;
        $sanpham->tensanpham = $request->tensanpham;
        $sanpham->tensanpham_slug = Str::slug($request->tensanpham, '-');
        $sanpham->soluong = $request->soluong;
        $sanpham->dongia = $request->dongia;
        $sanpham->motasanpham = $request->motasanpham;
        $sanpham->giamgia = $request->giamgia;
        $sanpham->danhgia = $request->danhgia;
        $sanpham->chatlieuday = $request->chatlieuday;
        $sanpham->matkinh = $request->matkinh;
        $sanpham->dochieunuoc = $request->dochieunuoc;

        if ($request->hasFile('hinhanh')) {
            if (! empty($sanpham->hinhanh)) {
                Storage::disk('private')->delete($sanpham->hinhanh);
            }

            $file = $request->file('hinhanh');
            $loaisanpham = LoaiSanPham::find($request->loaisanpham_id);
            $directory = $loaisanpham->tenloai_slug;
            $filename = Str::slug($request->tensanpham, '-').'.'.$file->getClientOriginalExtension();
            $sanpham->hinhanh = $file->storeAs($directory, $filename, 'private');
        }

        $sanpham->save();

        return redirect()->route('admin.sanpham.danhsach')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function getXoa(int $id)
    {
        $sanpham = SanPham::findOrFail($id);

        if (! empty($sanpham->hinhanh)) {
            Storage::disk('private')->delete($sanpham->hinhanh);
        }

        $sanpham->delete();

        return redirect()->route('admin.sanpham.danhsach')->with('success', 'Xóa sản phẩm thành công.');
    }
}
