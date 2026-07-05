<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(Request $request)
    {
        // Store the intended role in session if passed via query param
        if ($request->has('role')) {
            session(['intended_role' => $request->input('role')]);
        }
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        try {
            // Disable SSL verification for local development (cURL error 60 workaround on XAMPP)
            $httpClient = new \GuzzleHttp\Client(['verify' => false]);
            $googleUser = Socialite::driver('google')->stateless()->setHttpClient($httpClient)->user();
            
            // Find existing user by google_id or email
            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if (!$user) {
                // Determine role
                $role = session('intended_role', 'warga');
                
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'role' => $role,
                    // Password can be null as per migration
                    'password' => null,
                ]);
            } else {
                // If user exists but google_id is empty, update it
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            // Log the user in
            Auth::login($user);

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
            Log::error('Google Socialite Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal login melalui Google.');
        }
    }
}
