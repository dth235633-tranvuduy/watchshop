<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(): View
    {
        $reviews = DanhGia::with(['SanPham', 'User'])->latest()->paginate(20);

        return view('admin.danhgia.index', compact('reviews'));
    }

    public function update(Request $request, DanhGia $danhGia)
    {
        $validated = $request->validate([
            'trangthai' => ['required', 'in:hien_thi,an'],
            'phanhoi_admin' => ['nullable', 'string', 'max:2000'],
        ]);
        $danhGia->update([
            ...$validated,
            'phanhoi_luc' => filled($validated['phanhoi_admin'] ?? null) ? now() : $danhGia->phanhoi_luc,
        ]);

        return back()->with('success', 'Đã cập nhật đánh giá.');
    }
}
