<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Auth\RegisterRequest;
use App\Http\Requests\Client\Auth\LoginRequest;
use App\Http\Requests\Client\Auth\LogoutRequest;
use App\Repositories\Client\Interfaces\AuthRepositoryInterface as AuthRepo;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request ,AuthRepo $authRepository)
    {
        $user = $authRepository->createUser($request->validated());

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Account created successfully!');
    }

    public function login(LoginRequest $request, AuthRepo $authRepository)
    {
        $credentials = $request->only(['email', 'password']);

        $user = $authRepository->findUserByEmail($request->email);

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact support.',
            ])->onlyInput('email');
        }

        if ($authRepository->authenticateUser($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.overview'));
        }

        return back()->withErrors([
            'email' => 'Your email or password is invalid.',
        ])->onlyInput('email');
    }

    public function logout(LogoutRequest $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
