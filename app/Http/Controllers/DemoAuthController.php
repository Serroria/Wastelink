<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoAuthController extends Controller
{
    /**
     * Langsung login sebagai user demo berdasarkan role.
     */
   public function switchRole(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:warga,bank_sampah,umkm,pembeli'],
            'redirect' => ['nullable', 'string'],
        ]);

        $role = $validated['role'];
        // $user = User::where('role', $role)->first();
        $user = User::where('role', $role)->latest()->first();

        if ($user) {

            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // LOGIN SEBAGAI USER BARU
            Auth::login($user);

            // Generate sesi baru setelah berhasil login
            $request->session()->regenerate();
        }

        if ($request->filled('redirect') && str_starts_with($request->input('redirect'), '/')) {
            return redirect($request->input('redirect'));
        }

        // Redirect ke dashboard sesuai role
        return match ($role) {
            'warga' => redirect()->route('warga.dashboard'),
            'bank_sampah' => redirect()->route('bank-sampah.dashboard'),
            'umkm' => redirect()->route('umkm.dashboard'),
            'pembeli' => redirect()->route('pembeli.dashboard'),
            default => redirect('/'),
        };
    }

    /**
     * Logout dan kembali ke halaman utama.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
