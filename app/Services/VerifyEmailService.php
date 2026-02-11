<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\ClientHelper;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class VerifyEmailService
{
    public function generateVerificationToken($model, $email)
    {
        try {
            DB::beginTransaction();

            // Find user/admin by email
            $user = $this->findUser($model, $email);

            if (!$user) {
                throw new \Exception('User not found');
            }

            // Generate reset token and set expiration
            $token = Str::random(64);
            $user->email_verification_token = $token;
            $user->email_verification_expires_at = Carbon::now()->addMinutes(5);
            $user->save();

            DB::commit();

            return $token;
        } catch (\Exception $e) {
            Log::error('Error generating reset token: ' . $e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    private function findUser($model, $email)
    {
        if ($model === 'admin') {
            return Admin::where('email', $email)->first();
        }
        return User::where('email', $email)->first();
    }

    public function sendVerificationEmail($user, $token, $guardType = 'user')
    {
        try {
            $routeName = $guardType === 'admin' ? 'admin.email.verify' : 'email.verify';
            $payload = Crypt::encryptString(json_encode([
                'token' => $token,
                'email' => $user->email,
            ]));
            $verifyUrl = route($routeName, ['payload' => $payload]);
            $siteContent = ClientHelper::getSiteContent();

            if ($guardType === 'admin') {
                Mail::send('admin.emails.admin_verify_email', ['user' => $user, 'verifyUrl' => $verifyUrl, 'siteContent' => $siteContent], function ($message) use ($user) {
                    $message->to($user->email)->subject('Verify Your Admin Email Address');
                });
            } else {
                Mail::send('client.emails.verify_email', ['user' => $user, 'verifyUrl' => $verifyUrl, 'siteContent' => $siteContent], function ($message) use ($user) {
                    $message->to($user->email)->subject('Verify Your Email Address');
                });
            }
        } catch (\Exception $e) {
            Log::error('Error sending verification email: ' . $e->getMessage());
            throw $e;
        }
    }

    public function resendVerificationEmail($user, $model)
    {
        try {
            $token = $this->generateVerificationToken($model, $user->email);
            $this->sendVerificationEmail($user, $token, $model);
        } catch (\Exception $e) {
            Log::error('Error resending verification email: ' . $e->getMessage());
            throw $e;
        }
    }

    public function validateVerificationToken($token, $email, $guardType = 'user')
    {
        try {
            $user = $this->findUser($guardType, $email);

            if (!$user) {
                return ['valid' => false, 'message' => 'User not found'];
            }

            if ($user->email_verified_at) {
                return ['valid' => false, 'message' => 'Email already verified'];
            }

            if ($user->email_verification_token !== $token) {
                return ['valid' => false, 'message' => 'Invalid verification token'];
            }

            if (Carbon::now()->greaterThan($user->email_verification_expires_at)) {
                return ['valid' => false, 'message' => 'Verification token has expired'];
            }

            return ['valid' => true, 'user' => $user];
        } catch (\Exception $e) {
            Log::error('Error validating verification token: ' . $e->getMessage());
            return ['valid' => false, 'message' => 'An error occurred while validating the token'];
        }
    }

    public function verifyEmail($token, $email, $guardType = 'user')
    {
        try {
            DB::beginTransaction();

            $user = $this->validateVerificationToken($token, $email, $guardType);

            if (!$user['valid']) {
                return false;
            }

            $user = $user['user'];
            $user->email_verified_at = Carbon::now();
            $user->email_verification_token = null;
            $user->email_verification_expires_at = null;
            $user->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error('Error verifying email: ' . $e->getMessage());
            DB::rollBack();
            return false;
        }
    }
}
