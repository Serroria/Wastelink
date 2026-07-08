<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(Request $request)
    {
        // Store the intended role in session if passed via query param
        if (in_array($request->input('role'), ['warga', 'umkm'], true)) {
            session(['intended_role' => $request->input('role')]);
        }

        if ($request->has('redirect') && str_starts_with($request->input('redirect'), '/')) {
            session(['intended_redirect' => $request->input('redirect')]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            $httpClient = new Client(['verify' => ! app()->environment('local')]);
            $googleUser = Socialite::driver('google')->stateless()->setHttpClient($httpClient)->user();

            // Find existing user by google_id or email
            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if (! $user) {
                // Determine role
                $role = session('intended_role', 'warga');

                // Create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role' => $role,
                    'password' => Hash::make(Str::random(32)),
                ]);
            } else {
                // If user exists but google_id is empty, update it
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            // Log the user in
            Auth::login($user);

            if (session()->has('intended_redirect')) {
                return redirect(session()->pull('intended_redirect'));
            }

            // Redirect based on role
            switch ($user->role) {
                case 'warga':
                    return redirect()->route('warga.dashboard');
                case 'bank_sampah':
                    return redirect()->route('bank-sampah.dashboard');
                case 'umkm':
                    return redirect()->route('umkm.dashboard');
                case 'pembeli':
                    return redirect()->route('pembeli.dashboard');
                default:
                    return redirect()->route('home');
            }

        } catch (\Exception $e) {
            Log::error('Google Socialite Login Error: '.$e->getMessage());

            return redirect()->route('login')->with('error', 'Gagal login melalui Google.');
        }
    }
}
