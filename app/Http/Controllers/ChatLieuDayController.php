<?php

namespace App\Http\Controllers;

use App\Models\ChatLieuDay;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatLieuDayController extends Controller
{
    public function index()
    {
        $items = ChatLieuDay::orderBy('id')->get();

        return view('admin.chatlieuday.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenchatlieu' => ['required', 'string', 'max:255', 'unique:chatlieuday,tenchatlieu'],
        ]);
        ChatLieuDay::create([
            'tenchatlieu' => $validated['tenchatlieu'],
            'tenchatlieu_slug' => Str::slug($validated['tenchatlieu']),
        ]);

        return back()->with('success', 'Đã thêm chất liệu dây.');
    }

    public function update(Request $request, ChatLieuDay $chatlieuday)
    {
        $validated = $request->validate([
            'tenchatlieu' => ['required', 'string', 'max:255', 'unique:chatlieuday,tenchatlieu,'.$chatlieuday->id],
        ]);
        $chatlieuday->update([
            'tenchatlieu' => $validated['tenchatlieu'],
            'tenchatlieu_slug' => Str::slug($validated['tenchatlieu']),
        ]);

        return back()->with('success', 'Đã cập nhật chất liệu dây.');
    }

    public function destroy(ChatLieuDay $chatlieuday)
    {
        $chatlieuday->delete();

        return back()->with('success', 'Đã xóa chất liệu dây.');
    }
}
