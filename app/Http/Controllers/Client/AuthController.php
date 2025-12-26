<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    // Handle Registration Form Submission
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Add 'phone' validation here if you add a phone field to your register form
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true, // Default to active
        ]);

        // Auto-login after registration
        Auth::login($user);

        $request->session()->regenerate();
        if (config('session.driver') === 'database') {
            DB::table('sessions')
                ->where('id', $request->session()->getId())
                ->update(['user_id' => $user->id]);
        }
        return redirect()->route('home')->with('success', 'Account created successfully!');
    }

    // Handle Login Form Submission
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to log the user in
        // We also check 'is_active' to prevent banned users from logging in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => 1], $request->boolean('remember'))) {
            $request->session()->regenerate();

                if (config('session.driver') === 'database') {
                    DB::table('sessions')
                        ->where('id', $request->session()->getId())
                        ->update(['user_id' => Auth::id()]);
                }
            return redirect()->intended(route('dashboard.overview'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
