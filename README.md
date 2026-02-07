# E-zone_ecom

E-zone_ecom is a Laravel-based seller/admin e-commerce platform with modular code organization for products, inventory, orders, and storefront.

## Project Quickstart

Prerequisites:
- PHP 8.x
- Composer
- Node.js & npm
- MySQL (or compatible DB)

Setup (development):

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
# update DB credentials in .env
php artisan migrate --seed
npm run dev
php artisan serve --host=127.0.0.1 --port=8000
```

Run tests:

```bash
php artisan test
```

Useful commands:
- `php artisan migrate` — run migrations
- `php artisan db:seed` — seed the DB
- `php artisan queue:work` — process jobs
- `npm run build` — build assets for production
