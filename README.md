# Content Management System

Small Laravel CMS for managing jobs, articles, media, users, and multi-site content.

## Stack
- Laravel
- PHP
- Blade
- PostgreSQL
- Docker

## Local Setup
1. Copy `.env.example` to `.env`
2. Start Docker
3. Install dependencies
4. Generate app key
5. Run migrations

## Commands
- `docker compose up -d`
- `php artisan key:generate`
- `php artisan migrate`
