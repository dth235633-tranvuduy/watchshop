<?php

namespace App\Http\Controllers;

use App\Exports\NhaCungCapExport;
use App\Imports\NhaCungCapImport;
use App\Models\NhaCungCap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NhaCungCapController extends Controller
{
    public function getDanhSach(): View
    {
        $nhacungcap = NhaCungCap::orderBy('tennhacungcap')->paginate(15);

        return view('admin.nhacungcap.danhsach', compact('nhacungcap'));
    }

    public function postNhap(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ], [
            'file.required' => 'Vui lòng chọn file cần nhập.',
            'file.mimes' => 'File phải có định dạng XLSX, XLS hoặc CSV.',
            'file.max' => 'File không được vượt quá 20MB.',
        ]);

        try {
            $import = new NhaCungCapImport;
            Excel::import($import, $request->file('file'));

            $message = 'Đã nhập '.$import->imported.' nhà cung cấp.';
            if ($import->skipped > 0) {
                $message .= ' Bỏ qua '.$import->skipped.' dòng trùng tên.';
            }

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->with('error', 'Không đọc được file: '.$e->getMessage());
        }
    }

    public function getXuat(): BinaryFileResponse
    {
        return Excel::download(new NhaCungCapExport, 'danh-sach-nha-cung-cap.xlsx');
    }
}
