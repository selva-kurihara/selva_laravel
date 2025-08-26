<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdministerRequest;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('administers.auth.login');
    }

    public function login(AdministerRequest $request)
    {

        if (Auth::guard('admin')->attempt(
            ['login_id' => $request->login_id, 'password' => $request->password],
        )) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.top'));
        }

        return back()->withErrors(['login_id' => 'ログインIDまたはパスワードが違います。'])->onlyInput('login_id');
    }

    public function top()
    {
        return view('administers.auth.top');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
