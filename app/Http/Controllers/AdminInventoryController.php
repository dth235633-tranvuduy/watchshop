<?php

namespace App\Http\Controllers;

use App\Exports\PhieuNhapExport;
use App\Imports\PhieuNhapImport;
use App\Models\NhaCungCap;
use App\Models\PhieuNhap;
use App\Models\SanPham;
use App\Services\StockReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminInventoryController extends Controller
{
    public function __construct(private readonly StockReceiptService $stockReceipts) {}

    public function index(): View
    {
        $receipts = PhieuNhap::with(['NhaCungCap', 'User', 'ChiTiet.SanPham'])->latest('ngaynhap')->paginate(15);
        $lowStock = SanPham::with('HangSanXuat')->where('soluong', '<', 3)->orderBy('soluong')->get();

        return view('admin.kho.index', compact('receipts', 'lowStock'));
    }

    public function create(): View
    {
        $suppliers = NhaCungCap::where('trangthai', true)->orderBy('tennhacungcap')->get();
        $products = SanPham::orderBy('tensanpham')->get();

        return view('admin.kho.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nhacungcap_id' => ['required', 'exists:nhacungcap,id'],
            'ngaynhap' => ['required', 'date'],
            'ghichu' => ['nullable', 'string', 'max:1000'],
            'sanpham_id' => ['required', 'array', 'min:1'],
            'sanpham_id.*' => ['required', 'distinct', 'exists:sanpham,id'],
            'soluong' => ['required', 'array'],
            'soluong.*' => ['required', 'integer', 'min:1'],
            'dongianhap' => ['required', 'array'],
            'dongianhap.*' => ['required', 'numeric', 'min:0'],
        ]);

        $items = [];
        foreach ($validated['sanpham_id'] as $index => $productId) {
            $items[] = [
                'sanpham_id' => (int) $productId,
                'soluong' => (int) $validated['soluong'][$index],
                'dongianhap' => (float) $validated['dongianhap'][$index],
            ];
        }

        $this->stockReceipts->receive(
            (int) $validated['nhacungcap_id'],
            $items,
            $validated['ngaynhap'],
            $validated['ghichu'] ?? null,
        );

        return redirect()->route('admin.kho.danhsach')->with('success', 'Đã nhập kho và cập nhật tồn sản phẩm.');
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'tennhacungcap' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'sodienthoai' => ['nullable', 'string', 'max:30'],
            'diachi' => ['nullable', 'string', 'max:255'],
        ]);

        NhaCungCap::create($validated);

        return back()->with('success', 'Đã thêm nhà cung cấp.');
    }

    public function postNhapExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'nhacungcap_id' => ['required', 'exists:nhacungcap,id'],
            'ngaynhap' => ['required', 'date'],
            'ghichu' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:20480'],
        ], [
            'file.required' => 'Vui lòng chọn file cần nhập.',
            'file.mimes' => 'File phải có định dạng XLSX, XLS hoặc CSV.',
            'file.max' => 'File không được vượt quá 20MB.',
        ]);

        $import = new PhieuNhapImport;
        try {
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Không đọc được file: '.$e->getMessage())->withInput();
        }

        if (empty($import->items)) {
            $msg = $import->errors ? implode('<br>', $import->errors) : 'File không có dòng dữ liệu hợp lệ.';

            return back()->with('error', $msg)->withInput();
        }

        session([
            'kho_nhap_excel' => [
                'nhacungcap_id' => (int) $request->nhacungcap_id,
                'ngaynhap' => $request->ngaynhap,
                'ghichu' => $request->ghichu ?? null,
                'items' => $import->items,
                'errors' => $import->errors,
            ],
        ]);

        return redirect()->route('admin.kho.nhap-excel-preview');
    }

    public function getNhapExcelPreview()
    {
        $data = session('kho_nhap_excel');

        if (! $data || empty($data['items'])) {
            return redirect()->route('admin.kho.them')->with('error', 'Không có dữ liệu nhập để xác nhận.');
        }

        $suppliers = NhaCungCap::where('trangthai', true)->orderBy('tennhacungcap')->get();
        $selectedSupplier = $suppliers->where('id', $data['nhacungcap_id'])->first();

        return view('admin.kho.nhap-excel-preview', compact('data', 'suppliers', 'selectedSupplier'));
    }

    public function postXacNhanNhap(Request $request): RedirectResponse
    {
        $data = session('kho_nhap_excel');

        if (! $data || empty($data['items'])) {
            return redirect()->route('admin.kho.danhsach')->with('error', 'Không có dữ liệu nhập để xác nhận.');
        }

        $items = array_map(fn ($item) => [
            'sanpham_id' => $item['sanpham_id'],
            'soluong' => $item['soluong'],
            'dongianhap' => $item['dongianhap'],
        ], $data['items']);

        try {
            $this->stockReceipts->receive(
                $data['nhacungcap_id'],
                $items,
                $data['ngaynhap'],
                $data['ghichu'],
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Lỗi khi nhập kho: '.$e->getMessage());
        }

        session()->forget('kho_nhap_excel');

        return redirect()->route('admin.kho.danhsach')->with('success', 'Đã nhập kho từ file Excel và cập nhật tồn sản phẩm.');
    }

    public function getXuat(): BinaryFileResponse
    {
        return Excel::download(new PhieuNhapExport, 'danh-sach-phieu-nhap.xlsx');
    }

    public function huyNhapExcel(): RedirectResponse
    {
        session()->forget('kho_nhap_excel');

        return redirect()->route('admin.kho.them')->with('success', 'Đã hủy nhập Excel.');
    }
}
