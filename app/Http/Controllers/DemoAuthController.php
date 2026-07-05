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
        $role = $request->input('role', 'warga');

        $user = User::where('role', $role)->first();

        if ($user) {
            Auth::login($user);
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
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
