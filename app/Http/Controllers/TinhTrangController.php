<?php

namespace App\Http\Controllers;

use App\Models\TinhTrang;
use Illuminate\Http\Request;

class TinhTrangController extends Controller
{
    public function getDanhSach()
    {
        $tinhtrang = TinhTrang::all();

        return view('admin.tinhtrang.danhsach', compact('tinhtrang'));
    }

    public function getThem()
    {
        return view('admin.tinhtrang.them');
    }

    public function postThem(Request $request)
    {
        $request->validate([
            'tinhtrang' => 'required|string|max:255',
        ]);

        TinhTrang::create([
            'tinhtrang' => $request->tinhtrang,
        ]);

        return redirect()->route('admin.tinhtrang.danhsach')->with('success', 'Thêm tình trạng thành công.');
    }

    public function getSua(int $id)
    {
        $tinhtrang = TinhTrang::findOrFail($id);

        return view('admin.tinhtrang.sua', compact('tinhtrang'));
    }

    public function postSua(Request $request, int $id)
    {
        $tinhtrang = TinhTrang::findOrFail($id);

        $request->validate([
            'tinhtrang' => 'required|string|max:255',
        ]);

        $tinhtrang->tinhtrang = $request->tinhtrang;
        $tinhtrang->save();

        return redirect()->route('admin.tinhtrang.danhsach')->with('success', 'Cập nhật tình trạng thành công.');
    }

    public function getXoa(int $id)
    {
        $tinhtrang = TinhTrang::findOrFail($id);
        $tinhtrang->delete();

        return redirect()->route('admin.tinhtrang.danhsach')->with('success', 'Xóa tình trạng thành công.');
    }
}
