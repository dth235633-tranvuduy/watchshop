<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use App\Models\PhieuNhap;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminInventoryController extends Controller
{
    public function index(): View
    {
        $receipts = PhieuNhap::with(['NhaCungCap', 'User'])->latest('ngaynhap')->paginate(15);
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

        DB::transaction(function () use ($validated): void {
            $receipt = PhieuNhap::create([
                'nhacungcap_id' => $validated['nhacungcap_id'],
                'user_id' => auth()->id(),
                'ma_phieu' => 'PN-'.now()->format('YmdHis'),
                'ngaynhap' => $validated['ngaynhap'],
                'ghichu' => $validated['ghichu'] ?? null,
                'tongtien' => 0,
            ]);
            $total = 0;

            foreach ($validated['sanpham_id'] as $index => $productId) {
                $quantity = (int) $validated['soluong'][$index];
                $unitPrice = (float) $validated['dongianhap'][$index];
                $receipt->ChiTiet()->create([
                    'sanpham_id' => $productId,
                    'soluong' => $quantity,
                    'dongianhap' => $unitPrice,
                ]);
                SanPham::whereKey($productId)->lockForUpdate()->increment('soluong', $quantity);
                $total += $quantity * $unitPrice;
            }

            $receipt->update(['tongtien' => $total]);
        });

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
}
