<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'users' => User::orderBy('name')->paginate(15)
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:owner,staff,user'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Peran pengguna wajib dipilih.',
            'role.in' => 'Peran harus salah satu dari: owner, staff, user.',
        ]);

        User::create($data);

        return redirect()->route('users.index')->with('success', "Pengguna {$data['name']} dengan peran {$data['role']} berhasil ditambahkan.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.']);
        }

        if ($user->sales()->exists()) {
            return back()->withErrors(['delete' => "Pengguna {$user->name} memiliki riwayat transaksi penjualan dan tidak dapat dihapus."]);
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "Pengguna {$userName} berhasil dihapus.");
    }
}
