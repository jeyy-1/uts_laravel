<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
        ], [
            'email.required' => 'Email wajib diisi untuk masuk.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi / password wajib diisi.',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password yang Anda masukkan tidak sesuai dengan data kami.',
                ])
                ->with('error', 'Login gagal! Silakan periksa kembali email dan password Anda.')
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Arahkan sesuai peran masing-masing
        if ($user->role === 'user') {
            return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang di Sistem Inventory & Penjualan Ritel, ' . $user->name . ' (' . ucfirst($user->role) . ').');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user', // Default pendaftaran publik adalah Pelanggan (User)
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran akun berhasil! Selamat berbelanja di RetailSmart.');
    }

    public function logout(Request $request)
    {
        $name = Auth::user()?->name;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah berhasil logout. Sampai jumpa kembali, ' . $name . '!');
    }
}
