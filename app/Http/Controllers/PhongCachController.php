<?php

namespace App\Http\Controllers;

use App\Models\PhongCach;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhongCachController extends Controller
{
    public function index()
    {
        $items = PhongCach::orderBy('id')->get();

        return view('admin.phongcach.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenphongcach' => ['required', 'string', 'max:255', 'unique:phongcach,tenphongcach'],
        ]);
        PhongCach::create([
            'tenphongcach' => $validated['tenphongcach'],
            'tenphongcach_slug' => Str::slug($validated['tenphongcach']),
        ]);

        return back()->with('success', 'Đã thêm phong cách.');
    }

    public function update(Request $request, PhongCach $phongcach)
    {
        $validated = $request->validate([
            'tenphongcach' => ['required', 'string', 'max:255', 'unique:phongcach,tenphongcach,'.$phongcach->id],
        ]);
        $phongcach->update([
            'tenphongcach' => $validated['tenphongcach'],
            'tenphongcach_slug' => Str::slug($validated['tenphongcach']),
        ]);

        return back()->with('success', 'Đã cập nhật phong cách.');
    }

    public function destroy(PhongCach $phongcach)
    {
        $phongcach->delete();

        return back()->with('success', 'Đã xóa phong cách.');
    }
}
