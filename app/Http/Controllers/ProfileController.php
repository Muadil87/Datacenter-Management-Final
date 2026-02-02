<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show() {
        // Get fresh user data from database to ensure auth_provider is loaded
        $user = Auth::user()->fresh();
        return view('auth.profile', ['user' => $user]);
    }

    public function update(Request $request) {
        $user = Auth::user();

        // Build validation rules based on whether user is OAuth or not
        $rules = [
            'name' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notification_email' => 'nullable|boolean',
            'notification_incidents' => 'nullable|boolean',
        ];

        // Only validate email and password for non-OAuth users
        if (empty($user->auth_provider)) {
            $rules['email'] = 'required|email|unique:users,email,'.$user->id;
            $rules['password'] = 'nullable|min:6|confirmed';
        }

        $request->validate($rules);

        // Profile photo upload
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        // Update personal info
        $user->name = $request->name;
        
        // Only update email for non-OAuth users
        if (empty($user->auth_provider)) {
            $user->email = $request->email;
        }
        
        // Update notification preferences
        $user->notification_email = $request->has('notification_email') ? 1 : 0;
        $user->notification_incidents = $request->has('notification_incidents') ? 1 : 0;

        // Update password if provided (only for non-OAuth users)
        if (empty($user->auth_provider) && $request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès !');
    }
}