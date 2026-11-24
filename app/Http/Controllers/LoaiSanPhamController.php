<?php

namespace App\Http\Controllers;

use App\Exports\LoaiSanPhamExport;
use App\Imports\LoaiSanPhamImport;
use App\Models\LoaiSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

        return redirect()->route('admin.loaisanpham.danhsach')->with('success', 'Thêm loại máy thành công.');
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

        return redirect()->route('admin.loaisanpham.danhsach')->with('success', 'Cập nhật loại máy thành công.');
    }

    public function getXoa(int $id)
    {
        $loaisanpham = LoaiSanPham::findOrFail($id);
        $loaisanpham->delete();

        return redirect()->route('admin.loaisanpham.danhsach')->with('success', 'Xóa loại sản phẩm thành công.');
    }

    public function postNhap(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ], [
            'file.required' => 'Vui lòng chọn file cần nhập.',
            'file.mimes' => 'File phải có định dạng XLSX hoặc XLS.',
            'file.max' => 'File không được vượt quá 20MB.',
        ]);

        try {
            $import = new LoaiSanPhamImport;
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $exception) {
            return back()->with('error', 'Không đọc được file: '.$exception->getMessage());
        }

        $message = "Đã nhập {$import->imported} loại máy.";
        if ($import->skipped > 0) {
            $message .= " Bỏ qua {$import->skipped} dòng trùng tên.";
        }

        return back()->with('success', $message);
    }

    public function getXuat(): BinaryFileResponse
    {
        return Excel::download(new LoaiSanPhamExport, 'danh-sach-loai-san-pham.xlsx');
    }
}
