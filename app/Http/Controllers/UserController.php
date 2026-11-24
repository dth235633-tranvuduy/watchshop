<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            'is_active' => ['required', 'boolean'],
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->boolean('is_active'),
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.nguoidung.danhsach')->with('success', 'Thêm tài khoản thành công.');
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
            'is_active' => ['required', 'boolean'],
            'doimatkhau' => ['nullable', 'boolean'],
            'current_password' => [Rule::requiredIf($request->boolean('doimatkhau')), 'nullable', 'string'],
            'password' => [
                Rule::requiredIf($request->boolean('doimatkhau')),
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        if ($request->boolean('doimatkhau')
            && ! Hash::check((string) $request->current_password, $request->user()->password)) {
            return back()->withInput()->withErrors([
                'current_password' => 'Mật khẩu quản trị hiện tại không chính xác.',
            ]);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->is_active = $request->boolean('is_active');

        // Chỉ mã hóa và cập nhật mật khẩu khi admin thực sự nhập mật khẩu mới
        if ($request->boolean('doimatkhau') && $request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.nguoidung.danhsach')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function getXoa(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập.');
        }

        $user->delete();

        return redirect()->route('admin.nguoidung.danhsach')->with('success', 'Xóa tài khoản thành công.');
    }

    public function postTrangThai(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể vô hiệu hóa tài khoản đang đăng nhập.');
        }

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update(['is_active' => $validated['is_active']]);

        return back()->with(
            'success',
            $user->is_active ? 'Đã kích hoạt tài khoản.' : 'Đã vô hiệu hóa tài khoản.',
        );
    }
}
