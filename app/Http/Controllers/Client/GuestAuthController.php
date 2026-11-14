<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\GuestOtpMail;
use App\Models\User;
use App\Repositories\Client\Interfaces\AuthRepositoryInterface as AuthRepo;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GuestAuthController extends Controller
{
    /**
     * Send a 6-digit OTP to the guest's email address.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($request->input('email')));

        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success'       => false,
                'is_registered' => true,
                'message'       => statictranslations('You are already registered. Please log in to your account.'),
            ], 422);
        }

        $emailHash = sha1($email);
        $cooldownKey = 'guest_otp_cooldown:' . $emailHash;

        if (Cache::has($cooldownKey)) {
            $remaining = (int) Cache::get($cooldownKey) - time();
            return response()->json([
                'success'  => false,
                'message'  => statictranslations('Please wait before requesting another code.'),
                'cooldown' => max(1, $remaining),
            ], 429);
        }

        $code = (string) random_int(100000, 999999);
        $otpKey = 'guest_otp:' . $emailHash;
        $attemptsKey = 'guest_otp_attempts:' . $emailHash;

        Cache::put($otpKey, Hash::make($code), now()->addMinutes(10));
        Cache::put($attemptsKey, 0, now()->addMinutes(10));
        Cache::put($cooldownKey, time() + 60, 60);

        try {
            Mail::to($email)->queue(new GuestOtpMail($code, $email));
        } catch (\Throwable $e) {
            Log::error('Failed to queue GuestOtpMail: ' . $e->getMessage(), ['email' => $email]);
            return response()->json([
                'success' => false,
                'message' => statictranslations('Unable to send verification email. Please try again later.'),
            ], 500);
        }

        return response()->json([
            'success'  => true,
            'message'  => statictranslations('A 6-digit verification code has been sent to your email address.'),
            'cooldown' => 60,
        ]);
    }

    /**
     * Verify the 6-digit OTP submitted by the guest.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'otp'   => ['required', 'string', 'size:6'],
        ]);

        $email = strtolower(trim($request->input('email')));

        if (User::where('email', $email)->exists()) {
            return response()->json([
                'success'       => false,
                'is_registered' => true,
                'message'       => statictranslations('You are already registered. Please log in to your account.'),
            ], 422);
        }

        $emailHash = sha1($email);
        $otpKey = 'guest_otp:' . $emailHash;
        $attemptsKey = 'guest_otp_attempts:' . $emailHash;

        $attempts = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= 5) {
            Cache::forget($otpKey);
            return response()->json([
                'success' => false,
                'locked'  => true,
                'message' => statictranslations('Too many failed attempts. Please request a new verification code.'),
            ], 422);
        }

        $hashedOtp = Cache::get($otpKey);

        if (!$hashedOtp) {
            return response()->json([
                'success' => false,
                'expired' => true,
                'message' => statictranslations('The verification code has expired. Please request a new code.'),
            ], 422);
        }

        if (!Hash::check($request->input('otp'), $hashedOtp)) {
            Cache::increment($attemptsKey);
            $remaining = 5 - ($attempts + 1);

            return response()->json([
                'success'            => false,
                'remaining_attempts' => max(0, $remaining),
                'message'            => statictranslations('Invalid verification code. Attempts remaining: ') . max(0, $remaining),
            ], 422);
        }

        // Clean up OTP state upon successful verification
        Cache::forget($otpKey);
        Cache::forget($attemptsKey);

        $request->session()->put('guest_email_verified', $email);

        return response()->json([
            'success'        => true,
            'verified_email' => $email,
            'message'        => statictranslations('Email verified successfully.'),
        ]);
    }

    /**
     * SOW #11: Check whether an email is registered to an existing account.
     */
    public function checkExistingUser(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $email = $request->filled('email') ? strtolower(trim($request->input('email'))) : null;
        $exists = false;

        if ($email) {
            $exists = User::where('email', $email)->exists();
        }

        return response()->json([
            'exists'        => $exists,
            'is_registered' => $exists,
            'email'         => $email,
            'message'       => $exists
                ? statictranslations('You are already registered. Please log in to your account.')
                : null,
        ]);
    }

    /**
     * Inline login from SOW #11 checkout modal.
     */
    public function inlineLogin(Request $request, AuthRepo $authRepository, CartService $cartService): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'email'    => strtolower(trim($request->input('email'))),
            'password' => $request->input('password'),
        ];
        $guestSessionId = $request->session()->getId();

        $user = $authRepository->findUserByEmail($credentials['email']);

        if ($user && !$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => statictranslations('Your account is inactive. Please contact support.'),
            ], 403);
        }

        if ($authRepository->authenticateUser($credentials, true)) {
            $request->session()->regenerate();
            $request->session()->forget('guest_email_verified');

            // SOW #8: Link previous guest orders if user's email is verified
            if ($user && $user->email_verified_at) {
                try {
                    app(\App\Repositories\Client\Interfaces\GuestUserRepositoryInterface::class)->linkGuestOrdersToUser($user);
                } catch (\Throwable $e) {
                    Log::error('Error linking guest orders on inline login: ' . $e->getMessage());
                }
            }

            try {
                $cartService->mergeSessionCartIntoUserCart((int) Auth::id(), $guestSessionId);
            } catch (\Throwable $e) {
                Log::warning('Guest cart merge failed after inline login.', [
                    'user_id'    => Auth::id(),
                    'session_id' => $guestSessionId,
                    'error'      => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success'  => true,
                'message'  => statictranslations('Logged in successfully.'),
                'redirect' => route('checkout', ['step' => 2]),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => statictranslations('Your email or password is invalid.'),
        ], 422);
    }
}
