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
        $columns = ['city', 'state', 'country'];
        $showrooms = DB::table('showrooms')->get();

        foreach ($showrooms as $showroom) {
            $updates = [];
            foreach ($columns as $column) {
                $rawVal = $showroom->{$column} ?? null;
                if ($rawVal !== null && $rawVal !== '') {
                    $decoded = json_decode($rawVal, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $plainVal = $decoded['en'] ?? ($decoded['fr'] ?? ($decoded['ar'] ?? reset($decoded)));
                        if (is_string($plainVal) && str_starts_with(trim($plainVal), '{')) {
                            $nested = json_decode($plainVal, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($nested)) {
                                $plainVal = $nested['en'] ?? ($nested['fr'] ?? ($nested['ar'] ?? reset($nested)));
                            }
                        }
                        $updates[$column] = $plainVal;
                    }
                }
            }

            if (!empty($updates)) {
                DB::table('showrooms')
                    ->where('id', $showroom->id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = ['city', 'state', 'country'];
        $showrooms = DB::table('showrooms')->get();

        foreach ($showrooms as $showroom) {
            $updates = [];
            foreach ($columns as $column) {
                $rawVal = $showroom->{$column} ?? null;
                if ($rawVal !== null && $rawVal !== '') {
                    $decoded = json_decode($rawVal, true);
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                        $jsonVal = json_encode([
                            'en' => $rawVal,
                            'fr' => $rawVal,
                            'ar' => $rawVal,
                        ], JSON_UNESCAPED_UNICODE);
                        $updates[$column] = $jsonVal;
                    }
                }
            }

            if (!empty($updates)) {
                DB::table('showrooms')
                    ->where('id', $showroom->id)
                    ->update($updates);
            }
        }
    }
};
