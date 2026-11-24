<?php

namespace App\Http\Controllers;

use App\Imports\SanPhamImport;
use App\Models\HangSanXuat;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use App\Services\StockReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    public function getNhap(): View
    {
        return view('admin.sanpham.nhap');
    }

    public function getMauNhap(): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SanPham');

        $headers = [
            'Tên sản phẩm',
            'Loại sản phẩm',
            'Hãng sản xuất',
            'Số lượng',
            'Đơn giá',
            'Giảm giá (%)',
            'Đánh giá',
            'Mô tả',
            'Chất liệu dây',
            'Mặt kính',
            'Độ chịu nước',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE3C766');

        $tenloai = LoaiSanPham::query()->orderBy('tenloai')->value('tenloai') ?? 'Đồng hồ nam';
        $tenhang = HangSanXuat::query()->orderBy('tenhang')->value('tenhang') ?? 'Rolex';

        $sheet->fromArray([
            'Rolex Submariner Date',
            $tenloai,
            $tenhang,
            10,
            250000000,
            0,
            5,
            'Mô tả sản phẩm',
            'Thép không gỉ',
            'Sapphire',
            '300m',
        ], null, 'A2');

        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'mau-nhap-san-pham-');

        (new XlsxWriter($spreadsheet))->save($tempFile);
        $spreadsheet->disconnectWorksheets();

        return response()->download($tempFile, 'mau-nhap-san-pham.xlsx')->deleteFileAfterSend(true);
    }

    public function postNhap(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
        ], [
            'file.required' => 'Vui lòng chọn file cần nhập.',
            'file.mimes' => 'File phải có định dạng XLSX hoặc XLS.',
            'file.max' => 'File không được vượt quá 20MB.',
        ]);

        try {
            $import = new SanPhamImport(app(StockReceiptService::class));
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $exception) {
            return back()->with('error', 'Không đọc được file: '.$exception->getMessage());
        }

        if ($import->imported === 0 && $import->skipped === 0) {
            return back()->with('error', 'File không có dòng dữ liệu nào để nhập.');
        }

        $message = "Đã nhập {$import->imported} sản phẩm.";
        if ($import->skipped > 0) {
            $message .= " Bỏ qua {$import->skipped} dòng trùng tên hoặc thiếu loại/hãng.";
        }

        return redirect()
            ->route('admin.sanpham.nhap')
            ->with('success', $message);
    }
}
