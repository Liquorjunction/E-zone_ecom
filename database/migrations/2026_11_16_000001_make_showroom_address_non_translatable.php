<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $showrooms = DB::table('showrooms')->get();

        foreach ($showrooms as $showroom) {
            $rawAddress = $showroom->address;
            if ($rawAddress !== null && $rawAddress !== '') {
                $decoded = json_decode($rawAddress, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $plainAddress = $decoded['en'] ?? ($decoded['fr'] ?? ($decoded['ar'] ?? reset($decoded)));
                    if (is_string($plainAddress) && str_starts_with(trim($plainAddress), '{')) {
                        $nested = json_decode($plainAddress, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($nested)) {
                            $plainAddress = $nested['en'] ?? ($nested['fr'] ?? ($nested['ar'] ?? reset($nested)));
                        }
                    }
                    DB::table('showrooms')
                        ->where('id', $showroom->id)
                        ->update(['address' => $plainAddress]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $showrooms = DB::table('showrooms')->get();

        foreach ($showrooms as $showroom) {
            $rawAddress = $showroom->address;
            if ($rawAddress !== null && $rawAddress !== '') {
                $decoded = json_decode($rawAddress, true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                    $jsonAddress = json_encode([
                        'en' => $rawAddress,
                        'fr' => $rawAddress,
                        'ar' => $rawAddress,
                    ], JSON_UNESCAPED_UNICODE);
                    DB::table('showrooms')
                        ->where('id', $showroom->id)
                        ->update(['address' => $jsonAddress]);
                }
            }
        }
    }
};
