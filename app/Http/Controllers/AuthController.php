<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }

        $request->session()->regenerate();

        ActivityLog::record(Auth::user(), 'login', 'Đăng nhập thành công', 'login.submit', $request->method(), $request->fullUrl(), $request->ip(), $request->userAgent());

        $homeRoute = Auth::user()->role === 'admin' ? 'admin.home' : 'user.home';

        return redirect()->intended(route($homeRoute));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'privacy' => ['accepted'],
        ], [
            'privacy.accepted' => 'Bạn cần đồng ý với Chính sách bảo mật để tiếp tục.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::record($user, 'register', 'Tạo tài khoản mới', 'register.submit', $request->method(), $request->fullUrl(), $request->ip(), $request->userAgent());

        return redirect()->route('user.home');
    }

    public function logout(Request $request)
    {
        ActivityLog::record($request->user(), 'logout', 'Đăng xuất', 'logout', $request->method(), $request->fullUrl(), $request->ip(), $request->userAgent());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('frontend.home');
    }
}
