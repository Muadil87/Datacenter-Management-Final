<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestLogin extends Command
{
    protected $signature = 'test:login';
    protected $description = 'Test login functionality';

    public function handle()
    {
        $this->info('=== TESTING LOGIN AFTER APP_KEY FIX ===');
        
        // Check 1: APP_KEY loaded
        $appKey = config('app.key');
        $this->info('1. APP_KEY Status: ' . ($appKey ? '✓ LOADED' : '✗ NOT LOADED'));
        if ($appKey) {
            $this->line('   Key (first 30 chars): ' . substr($appKey, 0, 30) . '...');
        }
        
        // Check 2: User exists
        $this->info('\n2. Checking if admin@admin.com exists...');
        $user = User::where('email', 'admin@admin.com')->first();
        if ($user) {
            $this->line('   ✓ User found');
            $this->line('   ID: ' . $user->id);
            $this->line('   Role: ' . $user->role);
            $this->line('   Active: ' . ($user->is_active ? 'yes' : 'no'));
        } else {
            $this->error('   ✗ User NOT found');
            $this->info('\n   Available users:');
            User::all()->each(function($u) {
                $this->line('   - ' . $u->email . ' (' . $u->role . ')');
            });
            return 1;
        }
        
        // Check 3: Password test
        $this->info('\n3. Testing password verification...');
        $passwordTest = Hash::check('admin123', $user->password);
        $this->line('   Password "admin123" matches: ' . ($passwordTest ? '✓ YES' : '✗ NO'));
        
        // Check 4: Auth attempt
        $this->info('\n4. Testing Auth::attempt()...');
        $result = Auth::attempt([
            'email' => 'admin@admin.com',
            'password' => 'admin123'
        ]);
        
        if ($result) {
            $this->info('   ✓ LOGIN SUCCESSFUL!');
            $authenticated = Auth::user();
            $this->line('   Authenticated as: ' . $authenticated->email . ' (ID: ' . $authenticated->id . ')');
            Auth::logout();
            $this->info('   ✓ Session logout successful');
        } else {
            $this->error('   ✗ LOGIN FAILED');
            $this->info('\n   Debugging info:');
            $this->line('   - Checking if credentials are correct...');
            $testHash = Hash::make('admin123');
            $this->line('   - Test hash check: ' . (Hash::check('admin123', $testHash) ? 'works' : 'broken'));
        }
        
        $this->info('\n=== TEST COMPLETE ===');
        return 0;
    }
}
