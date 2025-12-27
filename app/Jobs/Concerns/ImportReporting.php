<?php

namespace App\Jobs\Concerns;

use Illuminate\Support\Facades\Cache;

trait ImportReporting
{
    private function persistReport(array $successfulProducts, array $failedProducts, string $batchId): void
    {
        Cache::put('import_details_' . $batchId, [
            'successful' => $successfulProducts,
            'failed' => $failedProducts,
        ], 86400); // Keep report for 24 hours
    }
}
