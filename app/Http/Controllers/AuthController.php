<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            ActivityLog::log('login', 'auth', 'User logged in');

            $user = Auth::user();
            
            if ($user->level == 1) {
                return redirect()->route('dashboard');
            } elseif ($user->level == 2) {
                return redirect()->route('dashboard');
            } elseif ($user->level == 3) {
                return redirect()->route('dashboard');
            } elseif ($user->level == 4) {
                return redirect()->route('dashboard');
            } elseif ($user->level == 5) {
                return redirect()->route('dashboard');
            }
        }

        ActivityLog::log('login_failed', 'auth', 'Failed login attempt for email: ' . $request->email);

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'auth', 'User logged out');
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $oldData = $user->only(['name', 'email', 'phone', 'address']);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $newData = $user->only(['name', 'email', 'phone', 'address']);

        ActivityLog::log('update', 'profile', 'Updated profile information', $oldData, $newData);

        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log('update', 'password', 'Changed password');

        return redirect()->route('profile')->with('success', 'Password changed successfully');
    }
}
