<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired verification and password reset tokens';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting token cleanup...');

        try {
            // Cleanup expired email verification tokens for users
            $expiredUserTokens = User::whereNotNull('email_verification_expires_at')
                ->where('email_verification_expires_at', '<', Carbon::now())
                ->update([
                    'email_verification_token' => null,
                    'email_verification_expires_at' => null,
                ]);

            $this->line("Cleaned up {$expiredUserTokens} expired user email verification tokens");

            // Cleanup expired password reset tokens for users
            $expiredUserResetTokens = User::whereNotNull('password_reset_expires_at')
                ->where('password_reset_expires_at', '<', Carbon::now())
                ->update([
                    'password_reset_token' => null,
                    'password_reset_expires_at' => null,
                ]);

            $this->line("Cleaned up {$expiredUserResetTokens} expired user password reset tokens");

            // Cleanup expired email verification tokens for admins
            $expiredAdminTokens = Admin::whereNotNull('email_verification_expires_at')
                ->where('email_verification_expires_at', '<', Carbon::now())
                ->update([
                    'email_verification_token' => null,
                    'email_verification_expires_at' => null,
                ]);

            $this->line("Cleaned up {$expiredAdminTokens} expired admin email verification tokens");

            // Cleanup expired password reset tokens for admins
            $expiredAdminResetTokens = Admin::whereNotNull('password_reset_expires_at')
                ->where('password_reset_expires_at', '<', Carbon::now())
                ->update([
                    'password_reset_token' => null,
                    'password_reset_expires_at' => null,
                ]);

            $this->line("Cleaned up {$expiredAdminResetTokens} expired admin password reset tokens");

            $this->info('Token cleanup completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error during token cleanup: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
