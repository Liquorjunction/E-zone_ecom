<?php

namespace App\Jobs\Concerns;

use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

trait ImportFileLoader
{
    private function loadFile(string $filePath, string $cacheKey)
    {
        Log::info('Reading file with FastExcel...');
        try {
            $collection = (new FastExcel)->import($filePath);
        } catch (\Exception $e) {
            Log::error('File read FAILED: '.$e->getMessage());
            $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['File error: '.$e->getMessage()]);

            return null;
        }

        if ($collection->count() === 0) {
            Log::error('File is empty');
            $this->updateProgress($cacheKey, 'failed', 0, 0, 0, 0, ['File is empty.']);

            return null;
        }

        Log::info('File read SUCCESS, total rows: '.$collection->count());

        return $collection;
    }
}
