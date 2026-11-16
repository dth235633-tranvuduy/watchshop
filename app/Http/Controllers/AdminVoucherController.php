<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminVoucherController extends Controller
{
    public function index(): View
    {
        $vouchers = Voucher::latest()->paginate(15);

        return view('admin.voucher.index', compact('vouchers'));
    }

    public function create(): View
    {
        return view('admin.voucher.form', ['voucher' => new Voucher]);
    }

    public function store(Request $request)
    {
        Voucher::create($this->validated($request));

        return redirect()->route('admin.voucher.danhsach')->with('success', 'Đã tạo mã giảm giá.');
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.voucher.form', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $voucher->update($this->validated($request, $voucher->id));

        return redirect()->route('admin.voucher.danhsach')->with('success', 'Đã cập nhật mã giảm giá.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return back()->with('success', 'Đã xóa mã giảm giá.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:vouchers,code,'.($id ?? 'NULL').',id'],
            'loai_giam' => ['required', 'in:phantram,giatri'],
            'giatrigiam' => ['required', 'numeric', 'min:0'],
            'dieukien_toithieu' => ['nullable', 'numeric', 'min:0'],
            'ngay_batdau' => ['nullable', 'date'],
            'ngay_ketthuc' => ['nullable', 'date', 'after_or_equal:ngay_batdau'],
            'soluong' => ['nullable', 'integer', 'min:1'],
            'trangthai' => ['nullable', 'boolean'],
        ]);
        $data['code'] = strtoupper($data['code']);
        $data['trangthai'] = $request->boolean('trangthai');
        $data['dieukien_toithieu'] = $data['dieukien_toithieu'] ?? 0;

        if ($data['loai_giam'] === 'phantram' && (float) $data['giatrigiam'] > 100) {
            abort(422, 'Mức giảm phần trăm không được vượt quá 100.');
        }

        return $data;
    }
}
