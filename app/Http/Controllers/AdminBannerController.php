<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminBannerController extends Controller
{
    public function index(): View
    {
        $banners = Banner::orderBy('thu_tu')->latest()->paginate(15);

        return view('admin.banner.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banner.form', ['banner' => new Banner]);
    }

    public function store(Request $request)
    {
        Banner::create($this->validated($request));

        return redirect()->route('admin.banner.danhsach')->with('success', 'Đã thêm banner.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banner.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $banner->update($this->validated($request, $banner));

        return redirect()->route('admin.banner.danhsach')->with('success', 'Đã cập nhật banner.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->hinh_anh && ! str_starts_with($banner->hinh_anh, 'http')) {
            Storage::disk('private')->delete($banner->hinh_anh);
        }

        $banner->delete();

        return back()->with('success', 'Đã xóa banner.');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['trang_thai' => ! $banner->trang_thai]);

        return back()->with(
            'success',
            $banner->trang_thai ? 'Đã bật banner.' : 'Đã tắt banner.',
        );
    }

    private function validated(Request $request, ?Banner $banner = null): array
    {
        $rules = [
            'tieu_de' => ['required', 'string', 'max:255'],
            'tieu_de_phu' => ['nullable', 'string', 'max:255'],
            'mo_ta' => ['nullable', 'string', 'max:1000'],
            'duong_dan' => ['nullable', 'string', 'max:500'],
            'thu_tu' => ['nullable', 'integer', 'min:0'],
            'trang_thai' => ['nullable', 'boolean'],
            'hinh_anh' => [
                $banner ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ];
        $data = $request->validate($rules);
        if ($request->hasFile('hinh_anh')) {
            if ($banner?->hinh_anh && ! str_starts_with($banner->hinh_anh, 'http')) {
                Storage::disk('private')->delete($banner->hinh_anh);
            }
            $data['hinh_anh'] = $request->file('hinh_anh')->store('banners', 'private');
        } elseif ($banner) {
            $data['hinh_anh'] = $banner->hinh_anh;
        }
        $data['trang_thai'] = $request->boolean('trang_thai');

        return $data;
    }
}
