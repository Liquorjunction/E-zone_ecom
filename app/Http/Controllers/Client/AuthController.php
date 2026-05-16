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
use App\Services\CartService;
use App\Services\VerifyEmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, VerifyEmailService $verifyEmailService)
    {
        try {
            $data = $request->validated();
            $token = $request->input('token') ?? Str::random(64);

            // Clear old pending data if resubmitting
            if ($request->has('token')) {
                $this->clearPendingRegistration($token);
            }

            $this->storePendingRegistration($token, $data);

            $pendingUser = new User([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // Generate verification token and send email
            $verifyEmailService->sendVerificationEmail($pendingUser, $token, 'user');

            return redirect()->route('register.success')->with([
                'email' => $data['email'],
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            Log::error('Registration error: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors([
                'email' => statictranslations('Unable to complete registration. Please try again.'),
            ])->withInput($request->except('password'));
        }
    }

    public function editPendingRegistration(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('register')->with('error', statictranslations('Invalid session. Please register again.'));
        }

        $pending = $this->getPendingRegistration($token);

        if (!$pending) {
            return redirect()->route('register')->with('error', statictranslations('Session expired. Please register again.'));
        }

        return view('client.auth.register', [
            'token' => $token,
            'prefilled' => [
                'name' => $pending['name'],
                'email' => $pending['email'],
                'phone' => $pending['phone'],
            ],
        ]);
    }

    public function login(LoginRequest $request, AuthRepo $authRepository, CartService $cartService)
    {
        $credentials = $request->only(['email', 'password']);
        $guestSessionId = $request->session()->getId();

        $user = $authRepository->findUserByEmail($request->email);

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => statictranslations('Your account is inactive. Please contact support.'),
            ])->onlyInput('email');
        }

        if ($authRepository->authenticateUser($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            try {
                $cartService->mergeSessionCartIntoUserCart((int) Auth::id(), $guestSessionId);
            } catch (\Throwable $e) {
                Log::warning('Guest cart merge failed after login.', [
                    'user_id' => Auth::id(),
                    'session_id' => $guestSessionId,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => statictranslations('Your email or password is invalid.'),
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
                'message' => statictranslations('If your email is registered, a reset link has been sent.'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Password reset error: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'error' => statictranslations('Unable to send reset link. Please try again later.'),
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
                return redirect()->route('login')->with('error', statictranslations('Invalid password reset link.'));
            }
        }

        // Validate token and email exist
        if (!$token || !$email) {
            return redirect()->route('login')->with('error', statictranslations('Invalid password reset link.'));
        }

        $validation = $resetService->validateResetToken($token, $email, 'user');

        if (!$validation['valid']) {
            return redirect()->route('login')->with('error', $validation['message'] ?? statictranslations('Invalid password reset link.'));
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
                'email' => $validation['message'] ?? statictranslations('The reset link is invalid or has expired. Please request a new one.'),
            ])->withInput($request->only('email'));
        }

        $isReset = $resetService->resetPassword($request->token, $request->email, $request->password, 'user');

        if (!$isReset) {
            return back()->withErrors([
                'email' => 'Unable to reset password. Please try again.',
            ])->withInput($request->only('email'));
        }

        return redirect()->route('login')->with('success', statictranslations('Your password has been reset successfully. Please log in with your new password.'));
    }

    public function verifyEmail(VerifyEmailRequest $request, VerifyEmailService $verifyEmailService, AuthRepo $authRepository)
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
                return redirect()->route('login')->with('error', statictranslations('Invalid verification link.'));
            }
        }

        // Validate token and email exist
        if (!$token || !$email) {
            return redirect()->route('login')->with('error', statictranslations('Invalid verification link.'));
        }

        $pending = $this->getPendingRegistration($token);
        if ($pending) {
            if (!hash_equals($pending['email'], $email)) {
                return redirect()->route('login')->with('error', statictranslations('Invalid verification link.'));
            }

            if (User::where('email', $email)->exists()) {
                $this->clearPendingRegistration($token);
                return redirect()->route('login')->with('info', statictranslations('Account already exists. Please log in.'));
            }

            try {
                $pending['password'] = Crypt::decryptString($pending['password']);
                $user = $authRepository->createUser($pending);

                // Mark email as verified upon creation
                $user->update([
                    'email_verified_at' => now(),
                ]);

                $this->clearPendingRegistration($token);
            } catch (\Throwable $e) {
                Log::error('Pending registration verification error: ' . $e->getMessage(), [
                    'email' => $email,
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('login')->with('error', statictranslations('Unable to create account. Please try again.'));
            }

            return redirect()->route('login')->with('success', statictranslations('Email verified successfully! You can now log in.'));
        }

        // Validate the token
        $validation = $verifyEmailService->validateVerificationToken($token, $email, 'user');

        if (!$validation['valid']) {
            return redirect()->route('login')->with('error', $validation['message'] ?? statictranslations('Invalid or expired verification link.'));
        }

        // Verify the email
        $isVerified = $verifyEmailService->verifyEmail($token, $email, 'user');

        if (!$isVerified) {
            return redirect()->route('login')->with('error', statictranslations('Unable to verify email. Please try again.'));
        }

        return redirect()->route('login')->with('success', statictranslations('Email verified successfully! You can now log in.'));
    }

    private function storePendingRegistration(string $token, array $data): void
    {
        Cache::put($this->pendingRegistrationKey($token), [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Crypt::encryptString($data['password']),
        ], now()->addMinutes(5));
    }

    private function getPendingRegistration(string $token): ?array
    {
        $pending = Cache::get($this->pendingRegistrationKey($token));
        return is_array($pending) ? $pending : null;
    }

    private function clearPendingRegistration(string $token): void
    {
        Cache::forget($this->pendingRegistrationKey($token));
    }

    private function pendingRegistrationKey(string $token): string
    {
        return 'pending_registration:' . $token;
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request, VerifyEmailService $verifyEmailService)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return back()->with('info', statictranslations('Your email is already verified.'));
        }

        try {
            $token = $verifyEmailService->generateVerificationToken('user', $user->email);
            $verifyEmailService->resendVerificationEmail($user, $token, 'user');
            return back()->with('success', statictranslations('Verification email resent successfully! Please check your inbox.'));
        } catch (\Throwable $e) {
            Log::error('Resend verification email error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors([
                'email' => statictranslations('Unable to resend verification email. Please try again later.'),
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

        return view('client.partials.email-verification-pending');
    }

    public function logout(LogoutRequest $request, CartService $cartService)
    {
        try {
            $activeBuyNowCart = $cartService->getActiveBuyNowCart();

            if ($activeBuyNowCart) {
                $cartService->clearCart($activeBuyNowCart);
            }

            $cartService->clearBuyNowFlowState();
            $request->session()->forget('checkout_mode');
        } catch (\Throwable $e) {
            Log::warning('Failed to clear transient checkout state during logout.', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
