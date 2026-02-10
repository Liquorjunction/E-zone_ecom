<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Auth\ForgotPassRequest;
use App\Http\Requests\Client\Auth\RegisterRequest;
use App\Http\Requests\Client\Auth\LoginRequest;
use App\Http\Requests\Client\Auth\LogoutRequest;
use App\Http\Requests\Client\Auth\ResetPasswordRequest;
use App\Http\Requests\Client\Auth\ShowResetFormRequest;
use App\Repositories\Client\Interfaces\AuthRepositoryInterface as AuthRepo;
use App\Services\PasswordResetService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function sendResetLink(ForgotPassRequest $request, PasswordResetService $resetService): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if ($user) {
                $token = $resetService->generateResetToken('user', $request->email);
                $resetService->sendResetEmail($user, $token, 'user');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'If your email is registered, a reset link has been sent.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Password reset error: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'error' => 'Unable to send reset link. Please try again later.',
            ], 500);
        }
    }

    public function showResetForm(ShowResetFormRequest $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        // Validate token and email exist
        if (!$token || !$email) {
            return redirect()->route('login')->with('error', 'Invalid password reset link.');
        }

        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, PasswordResetService $resetService)
    {
        $isReset = $resetService->resetPassword($request->token, $request->email, $request->password, 'user');

        if (!$isReset) {
            return back()->withErrors([
                'email' => 'The reset link is invalid or has expired. Please request a new one.',
            ])->withInput($request->only('email'));
        }

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please log in with your new password.');
    }

    public function logout(LogoutRequest $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
