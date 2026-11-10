<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getDanhSach()
    {
        $users = User::all();

        return view('admin.user.danhsach', compact('users'));
    }

    public function getThem()
    {
        return view('admin.user.them');
    }

    public function postThem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,user',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.user.danhsach')->with('success', 'Thêm tài khoản thành công.');
    }

    public function getSua(int $id)
    {
        $user = User::findOrFail($id);

        return view('admin.user.sua', compact('user'));
    }

    public function postSua(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Chỉ mã hóa và cập nhật mật khẩu khi admin thực sự nhập mật khẩu mới
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.user.danhsach')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function getXoa(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.danhsach')->with('success', 'Xóa tài khoản thành công.');
    }
}
