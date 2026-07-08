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
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:warga,umkm,pembeli'],
        ]);

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
