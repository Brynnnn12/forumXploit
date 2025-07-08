<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // BRUTE FORCE ATTACK - No rate limiting or account lockout
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // VULNERABILITY: No brute force protection
        // VULNERABILITY: No rate limiting
        // VULNERABILITY: No account lockout after failed attempts
        // VULNERABILITY: No CAPTCHA after multiple failures

        // Insecure plaintext password comparison
        $user = User::where('email', $email)->where('password', $password)->first();

        if ($user) {
            Auth::login($user);

            // VULNERABILITY: Session fixation - no session regeneration
            // VULNERABILITY: Weak session management

            return redirect()->route('home');
        }

        // VULNERABILITY: Information disclosure in error messages
        if (User::where('email', $email)->exists()) {
            return back()->with('error', 'Password salah untuk email: ' . $email);
        } else {
            return back()->with('error', 'Email tidak terdaftar: ' . $email);
        }
    }

    public function register(Request $request)
    {
        // A09: No logging of user registration
        // A09: No monitoring of suspicious activities

        // No validation - vulnerability
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'), // Stored as plaintext - insecure
            'role' => $request->input('role', 'user'), // Role can be set by user - vulnerability
        ]);

        // A09: No logging of successful registration
        // A09: No alert for admin role creation

        Auth::login($user);
        return redirect()->route('home');
    }

    // A09: No failed login attempt logging
    public function loginFailure(Request $request)
    {
        // This method demonstrates lack of logging
        // In a secure system, failed attempts should be logged

        return back()->with('error', 'Login failed - but this is not logged anywhere');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('home');
    }

    // BROKEN AUTHENTICATION - Weak session management
    public function showProfile($id)
    {
        // VULNERABILITY: Insecure Direct Object Reference (IDOR)
        // No authorization check - any user can view any profile
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('home')->with('error', 'User not found');
        }

        return view('auth.profile', compact('user'));
    }

    // BROKEN SESSION MANAGEMENT
    public function changePassword(Request $request)
    {
        $userId = $request->input('user_id');
        $newPassword = $request->input('password');

        // VULNERABILITY: No authentication check
        // VULNERABILITY: No authorization check
        // VULNERABILITY: Insecure Direct Object Reference

        $user = User::find($userId);
        if ($user) {
            $user->password = $newPassword; // Plaintext password
            $user->save();

            return back()->with('success', 'Password changed successfully');
        }

        return back()->with('error', 'User not found');
    }
}
