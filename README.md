# Example Vue App

A modern full-stack application built with Laravel 12, Vue 3, and Inertia.js.

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (default) or other database

## Setup

```bash
# Setup environment, database, and build assets
composer run setup

# Seed database with sample data
php artisan db:seed
```

**Default user credentials:**
- Email: `test@example.com`
- Password: `password`

## Development

```bash
# Start development server (Laravel + Vite + Queue + Logs)
composer run dev

# Or with SSR support
composer run dev:ssr
```

## Database

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Reset and seed
composer run reset:dev
```

## Testing

```bash
# Run all tests
composer run test

# Run specific test
php artisan test --filter=ArtworkTest
```

## Tech Stack

**Backend:** Laravel 12, PHP 8.2+, SQLite
**Frontend:** Vue 3, TypeScript, Inertia.js, Tailwind CSS v4, shadcn-vue
**Testing:** Pest PHP
**Auth:** Laravel Fortify (with 2FA)
