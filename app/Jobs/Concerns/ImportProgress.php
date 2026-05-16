<?php

namespace App\Jobs\Concerns;

use Illuminate\Support\Facades\Cache;

trait ImportProgress
{
    private function updateProgress($key, $status, $total, $processed, $success, $failed, $errors): void
    {
        $percent = ($total > 0) ? round(($processed / $total) * 100) : 0;

        Cache::put($key, [
            'status' => $status,
            'percent' => $percent,
            'progress' => $percent,
            'total' => $total,
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ], 3600);
    }

    private function startProgress(string $cacheKey): void
    {
        $this->updateProgress($cacheKey, 'processing', 0, 0, 0, 0, []);
    }

    private function finalizeProgress(string $cacheKey, array $result): void
    {
        $this->updateProgress(
            $cacheKey,
            'completed',
            $result['total'],
            $result['processed'],
            $result['success'],
            $result['failed'],
            $result['errors']
        );
    }
}
