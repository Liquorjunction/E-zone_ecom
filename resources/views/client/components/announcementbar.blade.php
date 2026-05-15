@php
    $announcement = ['text' => '', 'url' => ''];
    $rawInfoText = $siteContent->infotext ?? [];
    $currentLocale = app()->getLocale();

    if (is_string($rawInfoText)) {
        $announcement['text'] = trim($rawInfoText);
    } elseif (is_array($rawInfoText)) {
        if (array_key_exists('text', $rawInfoText) || array_key_exists('url', $rawInfoText)) {
            $rawText = $rawInfoText['text'] ?? '';

            if (is_array($rawText)) {
                $announcement['text'] = trim((string) (
                    $rawText[$currentLocale]
                    ?? $rawText['en']
                    ?? $rawText['fr']
                    ?? $rawText['ar']
                    ?? ''
                ));
            } else {
                $announcement['text'] = trim((string) $rawText);
            }

            $announcement['url']  = trim((string) ($rawInfoText['url']  ?? ''));
        } else {
            foreach ($rawInfoText as $value) {
                if (is_string($value) && trim($value) !== '') {
                    $announcement['text'] = trim($value);
                    break;
                }
            }
        }
    }

    $hasAnnouncement = $announcement['text'] !== '';
@endphp

@if ($hasAnnouncement)


<div class="relative overflow-hidden bg-linear-to-r from-primary-dark via-primary to-primary-dark"
     aria-label="Announcements" aria-live="polite">

    <div class="ann-stripe-bg absolute inset-0"></div>

    <div class="flex items-center justify-center overflow-hidden py-1.5 px-4 relative z-10" id="ann-inner">
        <div class="flex whitespace-nowrap" id="ann-track"></div>
    </div>
</div>

@push('scripts')
    <script>
        // Set global data before module loads
        window.__announcementData = {
            text: @json($announcement['text']),
            url: @json($announcement['url'])
        };
    </script>
@endpush
@endif
