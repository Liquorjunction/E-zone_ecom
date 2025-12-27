## Translation System — E-zone_ecom

This document describes the translation architecture, responsibilities, and flows used across the codebase. It is intended for developers and maintainers who will extend, operate, or harden the translation functionality.

Contents
- Purpose
- Components
- Data flows (admin create/update, runtime rendering, batch import)
- Helper behavior and cache
- Implementation examples
- Risks, mitigations and production recommendations
- Quick operational checklist
- Where to find the code

---

Purpose
-------
Provide automated and persistent localization for site content so admin users can create content in English and quickly obtain French/Arabic translations, while ensuring translated content is stored and served efficiently to clients.

Components
----------
- `app/Helpers/TranslationHelper.php` — runtime translation helpers: `gtrans()` and `gtrans_html()`.
- `app/Repositories/Admin/Repositories/TranslationRepository.php` — batch translation helpers used by controllers and jobs.
- Eloquent models (e.g., `app/Models/Brand.php`, `Product.php`) using Spatie `HasTranslations` for persistent per-field translations.
- Blade views and controllers that use `gtrans()` for UI literals and model `getTranslation()` for persisted content.

Data flows
----------
1) Admin create/update (persisted translations — recommended default)

- Admin submits localized form fields (e.g., `name[en]`, `name[fr]`, `name[ar]`).
- Controller validates input and runs "Smart Auto-Translate": for any empty locale field, call `gtrans($enValue, $locale)` or `gtrans_html($html, $locale)` for HTML content.
- Controller writes the completed translations into model attributes and persists via repository or model. Spatie stores translations in JSON columns.

Outcome: translated content is persisted and served from DB for all future requests in that locale.

2) Runtime rendering (on-demand translation for UI strings)

- Blade templates may call `gtrans('Some English label')` for UI literals that are not stored in models.
- `gtrans()` returns cached translation when available, otherwise calls the external translator, caches, and returns the result.

Outcome: immediate UI translation with caching to avoid repeated external calls.

3) Batch imports / background translation

- Large import or content migration jobs call `TranslationRepository::translateBatch()` or `translateAll()`.
- Jobs run in queue workers to avoid blocking HTTP requests and to apply rate limits/retries.

Outcome: bulk persistence of translations into models using Spatie, performed reliably in background workers.

Helper behavior (detailed)
-------------------------
- `gtrans(string $text, ?string $targetLocale = null): string`
  - Uses `App::getLocale()` if `$targetLocale` is null.
  - If locale is `'en'` or text is empty, returns original quickly.
  - Builds a cache key: `trans_{locale}_{md5(text)}`.
  - Uses `Cache::remember($cacheKey, 60 * 24 * 30, $callback)` to cache result ~30 days.
  - Translates using `Stichoza\GoogleTranslate\GoogleTranslate` by default (source `'en'`).
  - Logs successes (`Log::info`) and failures (`Log::error`) and returns original on error.

- `gtrans_html(string $html, ?string $targetLocale = null): string`
  - Same locale/caching logic with cache key `trans_html_{locale}_{md5(html)}`.
  - Parses HTML via `DOMDocument`, extracts text nodes using `DOMXPath`, translates each text node with `gtrans()`, and reconstructs HTML.
  - Catches exceptions, logs, and falls back to original HTML if translation fails.

Notes
-----
- Helper assumes English source. If you accept non-English sources, update helper to detect or accept a `source` parameter.
- `stichoza/google-translate-php` is a community library that scrapes Google Translate; it is convenient but not reliable for production at scale.

Implementation examples
-----------------------
Controller snippet (auto-fill translation):

```php
// inside Admin controller store/update
foreach (['name', 'description'] as $field) {
    if (! isset($data[$field]['en'])) continue;
    if (empty($data[$field]['fr'])) $data[$field]['fr'] = gtrans($data[$field]['en'], 'fr');
    if (empty($data[$field]['ar'])) $data[$field]['ar'] = gtrans($data[$field]['en'], 'ar');
}
// save via repo or model

```

Repository example:

```php
// TranslationRepository::translateAll($text, ['fr','ar'], $isHtml)
$translations = $translationRepo->translateAll($text, ['fr', 'ar'], $isHtml);
// $translations['fr'] and $translations['ar'] contain translated strings
```

Caching and operational notes
-----------------------------
- Cache store: use Redis for production to ensure performance and TTL reliability.
- TTL: current helpers use ~30 days. Invalidate caches when source text changes (consider storing a hash or version metadata).
- Logging: translation successes and failures are logged. Monitor logs for repeated errors.

Risks, mitigations & recommendations
-----------------------------------
- Provider reliability & compliance:
  - Risk: `stichoza` scrapes Google Translate and may break or violate terms.
  - Mitigation: implement a provider adapter interface and add an official provider implementation (Google Cloud Translate / Microsoft / DeepL).

- Performance & blocking requests:
  - Risk: synchronous translation in controllers can slow requests.
  - Mitigation: perform batch translations in queued jobs; for single-item translation prefer persisting by background job or async UI pattern.

- Incorrect translations or placeholder corruption:
  - Risk: placeholders like `{name}` or `%s` may be translated.
  - Mitigation: pre-process and protect placeholders before translation and restore afterwards.

- Security & privacy:
  - Risk: sending sensitive content to external translation services.
  - Mitigation: avoid translating PII and use official paid APIs with contractual privacy guarantees if necessary.

Recommended roadmap (practical steps)
------------------------------------
1. Add `TranslationProviderInterface` and a `StichozaProvider` adapter to wrap the current client.
2. Implement `GoogleCloudProvider` using Google Cloud Translate (requires credentials and billing).
3. Move bulk and import translations into queue jobs (use `TranslationRepository` inside jobs).
4. Add translation metadata table or JSON metadata (provider, timestamp, confidence, hash) for audit and rollback.
5. Build a small admin UI to review/approve/override machine translations.

Quick operational checklist
--------------------------
- Install translator (dev): `composer require stichoza/google-translate-php` (or configure official provider).
- Ensure cache is configured (Redis recommended): set `CACHE_DRIVER=redis` in `.env`.
- Run queue workers for background jobs: `php artisan queue:work`.

Where to look in the codebase
-----------------------------
- Helper: `app/Helpers/TranslationHelper.php`
- Repository: `app/Repositories/Admin/Repositories/TranslationRepository.php`
- Controllers: `app/Http/Controllers/Admin/*Controller.php` (search `gtrans(`)
- Models: `app/Models/*` (look for `HasTranslations` / `public $translatable`)
- Views: `resources/views/**/*` (search `gtrans(` and `gtrans_html(`)
- Jobs: `app/Jobs/` (import/processing jobs that call translation repository)

---

If you want, I can:
- generate a sequence diagram PNG (`docs/TRANSLATION-diagram.png`) showing Admin -> Controller -> Helper/Repo -> Model flows, or
- scaffold a `TranslationProviderInterface` and a `GoogleCloudProvider` implementation with queued examples.

Tell me which follow-up you want next.
