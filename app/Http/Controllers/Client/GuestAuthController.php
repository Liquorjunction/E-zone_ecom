<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Auth\CheckExistingUserRequest;
use App\Http\Requests\Client\Auth\InlineLoginRequest;
use App\Repositories\Client\Interfaces\AuthRepositoryInterface as AuthRepo;
use App\Repositories\Client\Interfaces\GuestUserRepositoryInterface as GuestUserRepo;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GuestAuthController extends Controller
{
    /**
     * SOW #11: Check whether an email or phone number is registered to an existing account.
     */
    public function checkExistingUser(CheckExistingUserRequest $request, AuthRepo $authRepository): JsonResponse
    {
        try {
            $data  = $request->validated();
            $email = !empty($data['email']) ? strtolower(trim($data['email'])) : null;
            $phone = !empty($data['phone']) ? trim($data['phone']) : null;

            $emailExists = $email ? (bool) $authRepository->findUserByEmail($email) : false;
            $phoneExists = $phone ? (bool) $authRepository->findUserByPhone($phone) : false;
            $exists      = $emailExists || $phoneExists;

            $matchedBy = null;
            if ($emailExists && $phoneExists) {
                $matchedBy = 'both';
            } elseif ($emailExists) {
                $matchedBy = 'email';
            } elseif ($phoneExists) {
                $matchedBy = 'phone';
            }

            $message = null;
            if ($matchedBy === 'both') {
                $message = statictranslations('This email address or mobile number is already registered. Please log in using your existing account to continue.');
            } elseif ($matchedBy === 'email') {
                $message = statictranslations('You are already registered. Please log in to your account.');
            } elseif ($matchedBy === 'phone') {
                $message = statictranslations('This phone number is already registered. Please log in or use a different phone number.');
            }

            return response()->json([
                'exists'        => $exists,
                'is_registered' => $exists,
                'email_exists'  => $emailExists,
                'phone_exists'  => $phoneExists,
                'matched_by'    => $matchedBy,
                'email'         => $emailExists ? $email : null,
                'phone'         => $phoneExists ? $phone : null,
                'message'       => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error checking existing user during guest checkout: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'exists'        => false,
                'is_registered' => false,
                'email_exists'  => false,
                'phone_exists'  => false,
                'matched_by'    => null,
                'message'       => null,
            ]);
        }
    }

    /**
     * Inline login from SOW #11 checkout modal.
     */
    public function inlineLogin(
        InlineLoginRequest $request,
        AuthRepo $authRepository,
        GuestUserRepo $guestUserRepository,
        CartService $cartService
    ): JsonResponse {
        try {
            $data = $request->validated();
            $credentials = [
                'email'    => strtolower(trim($data['email'])),
                'password' => $data['password'],
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

                // SOW #8: Link previous guest orders if user's email is verified
                if ($user && $user->email_verified_at) {
                    try {
                        $guestUserRepository->linkGuestOrdersToUser($user);
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
        } catch (\Throwable $e) {
            Log::error('Inline login error: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => statictranslations('Something went wrong. Please try again.'),
            ], 500);
        }
    }
}
