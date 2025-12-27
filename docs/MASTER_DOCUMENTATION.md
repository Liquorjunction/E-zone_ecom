# E-zone_ecom — Master Documentation

## 1. Overview

E-zone_ecom is a Laravel-based e-commerce application supporting product management, inventory, orders, admin panel, and storefront. The codebase follows a modular organization with domain models, services, repositories, DTOs, jobs, and HTTP controllers.

## 2. Architecture & Patterns

- Framework: Laravel (MVC)
- Patterns used: Repository pattern (Repositories/), Service layer (Services/), DTOs (DTOs/), Jobs & Queues (Jobs/), Resource classes for API responses (Http/Resources/)
- Background processing: Laravel queues (Jobs/), configure queue driver in `.env`
- Assets: built with Vite (`vite.config.js`) and `npm` scripts

## 3. Key Concepts

- Models: in `app/Models/` (Product, Inventory, Order, User, etc.)
- Repositories: data access abstractions in `app/Repositories/`
- Services: business logic in `app/Services/`
- DTOs: data transfer objects in `app/DTOs/` for structured imports and transformations
- Helpers: `app/Helpers/` (AdminHelper, ClientHelper, TranslationHelper)
- Jobs: queued tasks in `app/Jobs/` (e.g., `ProcessProductImport`)
- Providers: service providers in `app/Providers/` (register bindings, repository interfaces, etc.)

## 4. Directory Map (important parts)

- `app/` — main application code
  - `DTOs/` — structured data objects
  - `Exceptions/` — app exception handlers
  - `Helpers/` — helper utilities
  - `Http/Controllers/` — web & API controllers
  - `Http/Resources/` — API resource responses
  - `Jobs/` — queued jobs
  - `Models/` — Eloquent models (Product, Order, Inventory, etc.)
  - `Providers/` — service providers
  - `Repositories/` — repository implementations and interfaces
  - `Services/` — domain/business services
- `config/` — Laravel and package configuration
- `database/` — migrations, factories, seeders
- `public/` — web assets, built files
- `resources/` — CSS, JS, Blade views
- `routes/` — `web.php`, `admin.php`, `console.php`, etc.
- `tests/` — Feature and Unit tests

## 5. Environment & Configuration

Important `.env` keys (common):

- `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `BROADCAST_DRIVER`, `CACHE_DRIVER`, `QUEUE_CONNECTION`, `SESSION_DRIVER`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`

Queue and cache drivers should be configured for production (Redis recommended).

## 6. Database

- Migrations: `database/migrations/`
- Factories: `database/factories/`
- Seeders: `database/seeders/`

Typical workflow:

```bash
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
```

## 7. Development Workflow

- Local dev:
  - `composer install`
  - `npm ci` or `npm install`
  - `npm run dev`
  - `php artisan serve` or configure local Valet/Homestead
- Code style: follow PSR-12 and existing repository conventions
- Tests: `php artisan test` (runs PHPUnit)

## 8. Jobs & Queues

- Jobs live under `app/Jobs/` (e.g., `ProcessProductImport.php`).
- Run workers with `php artisan queue:work` or supervisor in production.

## 9. Common Tasks & Useful Commands

- Install dependencies: `composer install && npm ci`
- Run migrations: `php artisan migrate`
- Seed DB: `php artisan db:seed`
- Run tests: `php artisan test`
- Start queue worker: `php artisan queue:work`
- Build assets (production): `npm run build`

## 10. Deployment Notes

- Build assets: `npm run build` (or CI pipeline)
- Run migrations and seeders as needed during deployment
- Configure supervisor/systemd for `queue:work` in production
- Set `APP_ENV=production`, `APP_DEBUG=false`

## 11. Testing

- Tests are in `tests/Feature/` and `tests/Unit/`.
- Run all tests: `php artisan test`.

## 12. Contribution Guide (short)

- Fork the repo, create a feature branch, open PRs against `master` (or repo default branch).
- Run tests and linting locally before PRs.
- Describe changes and include screenshots or migration notes when relevant.

## 13. Next Steps & Optional Docs

I can add the following if you want:

- `docs/SETUP.md` — environment-specific setup steps (Docker, Windows notes)
- `docs/DEPLOYMENT.md` — step-by-step deployment (CI, server setup)
- `docs/API.md` — API endpoints, request/response shapes, authentication
- `CONTRIBUTING.md` — expanded contribution rules and code standards

---

If you'd like, I can now:
- generate `docs/SETUP.md` with Docker and Windows developer notes,
- add `docs/API.md` by scanning controllers and routes,
- or create `CONTRIBUTING.md` and `CODE_OF_CONDUCT.md`.

Tell me which next step you prefer.
