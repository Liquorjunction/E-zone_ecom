<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Client\Auth\RegisterRequest;
use App\Http\Requests\Client\Auth\LoginRequest;
use App\Http\Requests\Client\Auth\LogoutRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    // Handle Registration Form Submission
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
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
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact support.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (config('session.driver') === 'database') {
                DB::table('sessions')
                    ->where('id', $request->session()->getId())
                    ->update(['user_id' => Auth::id()]);
            }
            return redirect()->intended(route('dashboard.overview'));
        }

        return back()->withErrors([
            'email' => 'Your email or password is invalid.',
        ])->onlyInput('email');
    }

    // Handle Logout
    public function logout(LogoutRequest $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
