<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
 public function show(){return view('auth.login');}
 public function login(Request $r){$data=$r->validate(['username'=>'required|string','password'=>'required|string']); if(Auth::attempt($data,$r->boolean('remember'))){$r->session()->regenerate(); if(!Auth::user()->is_active){Auth::logout();return back()->withErrors(['username'=>'حساب کاربری غیرفعال است.']);} return redirect()->intended(route('dashboard'));} return back()->withErrors(['username'=>'نام کاربری یا رمز عبور اشتباه است.'])->onlyInput('username');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('login');}
}
