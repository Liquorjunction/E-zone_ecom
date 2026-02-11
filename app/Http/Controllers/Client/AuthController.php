<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Auth\ForgotPassRequest;
use App\Http\Requests\Client\Auth\RegisterRequest;
use App\Http\Requests\Client\Auth\LoginRequest;
use App\Http\Requests\Client\Auth\LogoutRequest;
use App\Http\Requests\Client\Auth\ResetPasswordRequest;
use App\Http\Requests\Client\Auth\ShowResetFormRequest;
use App\Http\Requests\Client\Auth\VerifyEmailRequest;
use App\Http\Requests\Client\Auth\ResendVerificationEmailRequest;
use App\Repositories\Client\Interfaces\AuthRepositoryInterface as AuthRepo;
use App\Services\PasswordResetService;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\VerifyEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthRepo $authRepository, VerifyEmailService $verifyEmailService)
    {
        try {
            $user = $authRepository->createUser($request->validated());

            // Generate verification token and send email
            $token = $verifyEmailService->generateVerificationToken('user', $user->email);
            $verifyEmailService->sendVerificationEmail($user, $token, 'user');

            return redirect()->route('login')->with('success', 'Account created successfully! Please check your email to verify your address.');
        } catch (\Throwable $e) {
            Log::error('Registration error: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors([
                'email' => 'Unable to complete registration. Please try again.',
            ])->withInput($request->except('password'));
        }
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

    public function showResetForm(ShowResetFormRequest $request, PasswordResetService $resetService)
    {
        $email = $request->query('email');
        $token = $request->query('token');
        $payload = $request->query('payload');

        if ($payload) {
            try {
                $decoded = json_decode(Crypt::decryptString($payload), true);
                $email = $decoded['email'] ?? null;
                $token = $decoded['token'] ?? null;
            } catch (\Throwable $e) {
                return redirect()->route('login')->with('error', 'Invalid password reset link.');
            }
        }

        // Validate token and email exist
        if (!$token || !$email) {
            return redirect()->route('login')->with('error', 'Invalid password reset link.');
        }

        $validation = $resetService->validateResetToken($token, $email, 'user');

        if (!$validation['valid']) {
            return redirect()->route('login')->with('error', $validation['message'] ?? 'Invalid password reset link.');
        }

        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, PasswordResetService $resetService)
    {
        $validation = $resetService->validateResetToken($request->token, $request->email, 'user');

        if (!$validation['valid']) {
            return back()->withErrors([
                'email' => $validation['message'] ?? 'The reset link is invalid or has expired. Please request a new one.',
            ])->withInput($request->only('email'));
        }

        $isReset = $resetService->resetPassword($request->token, $request->email, $request->password, 'user');

        if (!$isReset) {
            return back()->withErrors([
                'email' => 'Unable to reset password. Please try again.',
            ])->withInput($request->only('email'));
        }

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please log in with your new password.');
    }

    public function verifyEmail(VerifyEmailRequest $request, VerifyEmailService $verifyEmailService)
    {
        $payload = $request->query('payload');
        $email = $request->query('email');
        $token = $request->query('token');

        // Decrypt payload if provided
        if ($payload) {
            try {
                $decoded = json_decode(Crypt::decryptString($payload), true);
                $email = $decoded['email'] ?? null;
                $token = $decoded['token'] ?? null;
            } catch (\Throwable $e) {
                return redirect()->route('login')->with('error', 'Invalid verification link.');
            }
        }

        // Validate token and email exist
        if (!$token || !$email) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        // Validate the token
        $validation = $verifyEmailService->validateVerificationToken($token, $email, 'user');

        if (!$validation['valid']) {
            return redirect()->route('login')->with('error', $validation['message'] ?? 'Invalid or expired verification link.');
        }

        // Verify the email
        $isVerified = $verifyEmailService->verifyEmail($token, $email, 'user');

        if (!$isVerified) {
            return redirect()->route('login')->with('error', 'Unable to verify email. Please try again.');
        }

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now log in.');
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request, VerifyEmailService $verifyEmailService)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return back()->with('info', 'Your email is already verified.');
        }

        try {
            $verifyEmailService->resendVerificationEmail($user, 'user');
            return back()->with('success', 'Verification email resent successfully! Please check your inbox.');
        } catch (\Throwable $e) {
            Log::error('Resend verification email error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors([
                'email' => 'Unable to resend verification email. Please try again later.',
            ]);
        }
    }

    public function pendingVerification(Request $request, VerifyEmailService $verifyEmailService)
    {
        $user = $request->user();

        // Redirect to dashboard if email is already verified
        if ($user && $user->email_verified_at) {
            return redirect()->route('dashboard.overview');
        }

        // Automatically send verification email if not sent in last 2 minutes (rate limiting)
        $lastSent = $request->session()->get('verification_email_sent_at');
        $shouldSendEmail = !$lastSent || now()->diffInMinutes($lastSent) >= 2;

        if ($user && $shouldSendEmail) {
            try {

                $token = $verifyEmailService->generateVerificationToken('user', $user->email);
                $verifyEmailService->sendVerificationEmail($user, $token, 'user');

                $request->session()->put('verification_email_sent_at', now());
                $request->session()->flash('email_sent', true);
            } catch (\Throwable $e) {
                Log::error('Auto-send verification email error: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return view('client.pages.email-verification-pending');
    }

    public function logout(LogoutRequest $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
