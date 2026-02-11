<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class PasswordResetService
{
    public function generateResetToken($model, $email)
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
            $user->password_reset_token = $token;
            $user->password_reset_expires_at = Carbon::now()->addMinutes(5);
            $user->password_reset_requested_at = Carbon::now();
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

    public function sendResetEmail($user, $token, $guardType = 'user')
    {
        try {
            $routeName = $guardType === 'admin' ? 'admin.password.reset' : 'password.reset';
            $payload = Crypt::encryptString(json_encode([
                'token' => $token,
                'email' => $user->email,
            ]));
            $resetUrl = route($routeName, ['payload' => $payload]);

            // Send email logic here both for user and admin
            if ($guardType === 'admin') {
                Mail::send('admin.emails.admin_password_reset', ['resetUrl' => $resetUrl, 'user' => $user], function ($message) use ($user) {
                    $message->to($user->email)->subject('Admin Password Reset Request');
                });
            } else {
                Mail::send('client.emails.password_reset', ['resetUrl' => $resetUrl, 'user' => $user], function ($message) use ($user) {
                    $message->to($user->email)->subject('Password Reset Request');
                });
            }
        } catch (\Exception $e) {
            Log::error('Error sending reset email: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    public function validateResetToken($token, $email, $guardType = 'user')
    {
        $user = $this->findUser($guardType, $email);

        if (!$user || $user->password_reset_token !== $token) {
            return ['valid' => false, 'message' => 'Reset link is invalid or already used.'];
        }

        if (Carbon::parse($user->password_reset_expires_at)->isPast()) {
            return ['valid' => false, 'message' => 'Reset link has expired.'];
        }

        return ['valid' => true, 'user' => $user];
    }

    public function resetPassword($token, $email, $newPassword, $guardType = 'user')
    {
        try {
            DB::beginTransaction();

            $user = $this->validateResetToken($token, $email, $guardType);

            if (!$user['valid']) {
                return false;
            }

            $user = $user['user'];
            $user->password = Hash::make($newPassword);
            $user->password_reset_token = null;
            $user->password_reset_expires_at = null;
            $user->password_reset_requested_at = null;
            $user->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            DB::rollBack();
            return false;
        }
    }
}
