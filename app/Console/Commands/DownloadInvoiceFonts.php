<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Run ONCE to download Instrument Sans fonts for DomPDF:
 *
 *   php artisan invoice:download-fonts
 *
 * Saves TTF files to:  public/fonts/instrument-sans/
 */
class DownloadInvoiceFonts extends Command
{
    protected $signature   = 'invoice:download-fonts';
    protected $description = 'Download Instrument Sans TTF fonts for DomPDF invoice rendering';

    public function handle(): int
    {
        $outputDir = public_path('fonts/instrument-sans');

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $this->info('Fetching Instrument Sans CSS from Google Fonts...');

        // Use an old User-Agent → Google returns TTF (not woff2)
        // woff2 is NOT supported by DomPDF — TTF is required
        $css = Http::withHeaders([
            'User-Agent' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
        ])->get('https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700')->body();

        if (empty(trim($css))) {
            $this->error('Empty response from Google Fonts. Check your server internet connection.');
            return self::FAILURE;
        }

        $this->line('CSS fetched. Parsing @font-face blocks...');

        // Parse each @font-face block individually
        // Example block:
        //   @font-face {
        //     font-weight: 400;
        //     src: url(https://fonts.gstatic.com/...);
        //   }
        preg_match_all('/@font-face\s*\{([^}]+)\}/s', $css, $blocks);

        if (empty($blocks[1])) {
            $this->error('No @font-face blocks found in CSS response.');
            $this->line('Raw snippet: ' . substr($css, 0, 600));
            return self::FAILURE;
        }

        $downloaded = [];

        foreach ($blocks[1] as $block) {
            // Extract font-weight
            preg_match('/font-weight:\s*(\d+)/i', $block, $wm);
            $weight = isset($wm[1]) ? (int) $wm[1] : 400;

            // Skip weights we don't need
            if (! in_array($weight, [400, 500, 600, 700])) {
                continue;
            }

            // Skip if already downloaded
            if (isset($downloaded[$weight])) {
                continue;
            }

            // Extract URL — handles both url(...) and url("...")
            preg_match('/src:\s*url\([\'"]?([^\'")]+)[\'"]?\)/i', $block, $um);

            if (empty($um[1])) {
                $this->warn("  ⚠ Could not extract URL for weight {$weight}");
                continue;
            }

            $url      = trim($um[1]);
            $filename = "InstrumentSans-{$weight}.ttf";
            $filepath = $outputDir . '/' . $filename;

            if (file_exists($filepath) && filesize($filepath) > 10000) {
                $this->line("  ✓ Already exists: {$filename} (" . round(filesize($filepath) / 1024) . " KB)");
                $downloaded[$weight] = $filepath;
                continue;
            }

            $this->line("  ↓ Downloading weight {$weight} from Google Fonts...");

            try {
                $response = Http::timeout(30)->get($url);

                if (! $response->successful()) {
                    $this->warn("  ⚠ HTTP {$response->status()} for weight {$weight}");
                    continue;
                }

                $content = $response->body();

                if (strlen($content) < 5000) {
                    $this->warn("  ⚠ Response too small (" . strlen($content) . " bytes) — skipping weight {$weight}");
                    continue;
                }

                file_put_contents($filepath, $content);
                $downloaded[$weight] = $filepath;
                $this->info("  ✓ Saved: public/fonts/instrument-sans/{$filename} (" . round(strlen($content) / 1024) . " KB)");

            } catch (\Exception $e) {
                $this->warn("  ⚠ Failed weight {$weight}: " . $e->getMessage());
            }
        }

        if (empty($downloaded)) {
            $this->error('No fonts were downloaded.');
            $this->line('');
            $this->line('Debug — Raw CSS from Google:');
            $this->line(substr($css, 0, 800));
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('✅ Done! Downloaded weights: ' . implode(', ', array_keys($downloaded)));
        $this->line('   Fonts saved to: public/fonts/instrument-sans/');
        $this->line('   DomPDF will load them via file:// in invoice-pdf.blade.php');
        $this->newLine();

        // Verify files
        $this->line('File verification:');
        foreach ([400, 500, 600, 700] as $w) {
            $path   = $outputDir . "/InstrumentSans-{$w}.ttf";
            $exists = file_exists($path);
            $size   = $exists ? round(filesize($path) / 1024) . ' KB' : 'MISSING';
            $icon   = $exists ? '✓' : '✗';
            $this->line("  {$icon} InstrumentSans-{$w}.ttf — {$size}");
        }

        return self::SUCCESS;
    }
}
