<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Tampilkan halaman pengaturan & edit profil (universal, semua role).
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('settings', compact('user'));
    }

    /**
     * Simpan perubahan profil & password (universal, semua role).
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Validate basic fields
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_profile_photo' => 'nullable|boolean',
        ];

        // If trying to change password
        if ($request->filled('current_password') || $request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // Verify current password if changing password
        if ($request->filled('current_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        if ($request->boolean('remove_profile_photo')) {
            $this->deleteProfilePhoto($user);
            $user->profile_photo = null;
        }

        if ($request->hasFile('profile_photo')) {
            $this->deleteProfilePhoto($user);
            $user->profile_photo = $request->file('profile_photo')->store('profiles', 'public');
        }

        // Update fields
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->save();

        Auth::setUser($user->fresh());

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    private function deleteProfilePhoto(User $user): void
    {
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }
    }
}
