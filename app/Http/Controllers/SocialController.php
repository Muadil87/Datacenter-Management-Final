<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            \Log::info("Starting {$provider} callback...");
            
            $socialUser = Socialite::driver($provider)->user();
            
            \Log::info("Social user retrieved: " . $socialUser->getEmail());

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                \Log::info("Creating new user: " . $socialUser->getEmail());
                // First-time login: create user as INACTIVE
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(), // Auto-verify email
                    'is_active' => false, // Pending admin verification
                    'role' => 'user', // Default role, admin will change to 'internal' after verification
                    'auth_provider' => $provider, // Store which provider they used
                ]);
                \Log::info("New user created (pending approval): " . $user->id);
                
                // Create notification for all admins
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'user_registration',
                        'title' => 'New User Registration',
                        'message' => "{$user->name} ({$user->email}) signed up and is waiting for approval.",
                        'related_id' => $user->id,
                        'related_type' => 'user',
                        'link' => '/admin/users',
                        'is_read' => false,
                    ]);
                }
                \Log::info("Notifications created for admins about new user");
                
                // Redirect to pending page, DON'T log them in
                return redirect('/auth/pending')->with('email', $socialUser->getEmail());
            } else {
                \Log::info("Existing user found: " . $user->id);
                
                // Update auth_provider if not already set (for users who logged in before the auth_provider field was added)
                if (empty($user->auth_provider)) {
                    $user->update(['auth_provider' => $provider]);
                    // Refresh the user to get updated data
                    $user = $user->fresh();
                }
                
                // Check if user was rejected
                if ($user->rejected_at) {
                    \Log::info("User account has been rejected");
                    return redirect('/auth/rejected')->with('email', $user->email);
                }
                
              
                if (!$user->is_active) {
                    
                    if (!$user->email_verified_at) {
                        \Log::info("User is pending approval");
                        return redirect('/auth/pending')->with('email', $user->email);
                    }
                    
                    
                    \Log::info("User account has been deactivated");
                    return redirect('/auth/deactivated')->with('email', $user->email);
                }
                
                // User is active, log them in
                Auth::login($user);
                \Log::info("User logged in: " . $user->id);
                
                // Redirect to home
                return redirect('/');
            }
        } catch (\Exception $e) {
            \Log::error("Social login error: " . $e->getMessage());
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}
