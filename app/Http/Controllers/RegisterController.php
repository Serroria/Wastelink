<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran user baru.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'username' => ['required', 'string', 'max:255', 'unique:users,username'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //         'role' => ['required', 'string', 'in:warga,umkm,pembeli'],
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'username' => $request->username,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //         'role' => $request->role,
    //     ]);

    //     // Otomatis login setelah berhasil mendaftar
    //     Auth::login($user);

    //     // Redirect sesuai role
    //     return match ($user->role) {
    //         'warga' => redirect()->route('warga.dashboard'),
    //         'bank_sampah' => redirect()->route('bank-sampah.dashboard'),
    //         'umkm' => redirect()->route('umkm.dashboard'),
    //         'pembeli' => redirect()->route('pembeli.dashboard'),
    //         default => redirect('/'),
    //     };
    // }

    /**
     * Proses pendaftaran user baru.
     */
    public function store(Request $request)
    {
        // 1. Buat aturan validasi
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:warga,umkm,pembeli'],
        ];

        // 2. Buat pesan error kustom dalam bahasa Indonesia
        $customMessages = [
            'password.min' => 'Password minimal harus terdiri dari 8 karakter.',
            'password.confirmed' => 'Kata sandi tidak sama, silakan periksa kembali.',
            'password.required' => 'Password wajib diisi.',

            'email.unique' => 'Alamat email ini sudah terdaftar, silakan gunakan email lain.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',

            'username.unique' => 'Username ini sudah dipakai, coba variasi yang lain.',
            'username.required' => 'Username wajib diisi.',

            'name.required' => 'Nama lengkap wajib diisi.',
            'role.required' => 'Peran (Role) wajib dipilih.',
        ];

        // 3. Masukkan aturan dan pesan kustom ke dalam validator
        $request->validate($rules, $customMessages);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Otomatis login setelah berhasil mendaftar
        Auth::login($user);

        // Redirect sesuai role
        return match ($user->role) {
            'warga' => redirect()->route('warga.dashboard'),
            'bank_sampah' => redirect()->route('bank-sampah.dashboard'),
            'umkm' => redirect()->route('umkm.dashboard'),
            'pembeli' => redirect()->route('pembeli.dashboard'),
            default => redirect('/'),
        };
    }
}
