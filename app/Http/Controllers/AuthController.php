<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    
    public function showRegister() { return view('auth.register'); }

   public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Vérification si actif
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte n\'est pas encore activé.']);
            }

            // Tous les utilisateurs vont à la home page
            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }
  public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', 
            'is_active' => false,
            'email_verified_at' => now(),
        ]);

        // Send notification to all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'user_registration',
                'title' => 'New User Registration',
                'message' => "{$newUser->name} ({$newUser->email}) registered and is waiting for approval.",
                'related_id' => $newUser->id,
                'related_type' => 'user',
                'link' => '/admin/users',
                'is_read' => false,
            ]);
        }

        return redirect('/auth/pending')->with('email', $newUser->email);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    
    public function profile() {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request) {
        $user = Auth::user();

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notification_email' => 'nullable|boolean',
            'notification_incidents' => 'nullable|boolean',
        ]);

        // Mise à jour des infos de base
        $user->name = $request->name;
        $user->email = $request->email;
        $user->notification_email = $request->has('notification_email') ? 1 : 0;
        $user->notification_incidents = $request->has('notification_incidents') ? 1 : 0;

        // Upload de la photo de profil
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('profile_photos', $filename, 'public');
            $user->profile_photo = 'profile_photos/' . $filename;
        }

        // Mise à jour du mot de passe (seulement si rempli)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès !');
    }
}

