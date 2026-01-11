<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // محاولة تسجيل الدخول
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // توجيه المستخدم بناءً على نوعه
            $user = Auth::user();
            
            if ($user->type == 'admin' || $user->type == 'employee') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended('/');
        }

        // إذا فشل تسجيل الدخول
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * عرض صفحة التسجيل (إذا كنت تحتاجها)
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * معالجة التسجيل (إذا كنت تحتاجها)
     */
    public function register(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'type' => 'customer', // نوع افتراضي
            'status' => 'active',
        ]);

        // تسجيل الدخول تلقائياً بعد التسجيل
        Auth::login($user);

        return redirect('/');
    }
}