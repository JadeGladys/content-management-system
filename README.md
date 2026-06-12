# Content Management System

Laravel CMS for managing careers, articles, media, users, categories, tags, and website content.

This CMS can run on its own or as part of the full ISCO platform.

## Stack

* Laravel
* PHP
* Blade
* PostgreSQL
* Docker
* Mailpit
* Vite
* Node.js

## What This CMS Does

The CMS is used to manage content that can be displayed on one or more public websites.

It supports:

* Admin login
* User management
* Articles
* Jobs/careers
* Media uploads
* Categories
* Tags
* Public API endpoints
* Password setup and reset emails

## Running the CMS Only

Use this mode when you want to work on the CMS by itself.

From the `Content-Management-system` folder, run:

```bash
docker compose up
```

Then open:

```txt
http://localhost:8002
```

Example CMS URLs:

```txt
http://localhost:8002/admin/login
http://localhost:8002/api/articles
```

Mailpit:

```txt
http://localhost:8025
```

## Running the CMS Inside the Full Platform

The CMS can also run inside the full platform from the parent `isco-platform` repository.

In platform mode, the public entry point is:

```txt
http://localhost:8000
```

CMS routes are accessed through Apache:

```txt
http://localhost:8000/admin/login
http://localhost:8000/api/articles
```

In platform mode, the CMS is not exposed directly with its own browser port. Apache forwards `/admin`, `/api`, `/build`, `/storage`, and `/admin-assets` to the CMS container internally.

## Why the CMS Can Run Alone

The CMS is built as its own project because it should not depend completely on one website.

This is useful because the CMS can still be used even when the website is being changed.

For example:

If the public ISCO Security website is being redesigned, replaced, or temporarily disconnected, the CMS can still run alone at:

```txt
http://localhost:8002
```

Admins and developers can still:

* Add articles
* Update jobs
* Upload media
* Manage users
* Edit categories and tags
* Test CMS features

Later, a website can connect to the same CMS data using the public API.

This also makes it possible to attach a different website to the same CMS in the future.

## Database

The CMS uses PostgreSQL through Docker.

The database volume is:

```txt
contentmanagementsystem_postgres_data
```

This volume stores the actual database data, including users, articles, jobs, media records, categories, and tags.

Because the data is stored in a Docker volume, it stays available even if the containers are stopped or recreated.

The Docker Compose file has a fixed project name:

```yaml
name: contentmanagementsystem
```

This keeps the database volume name stable even if the folder name changes.

## Shared Database With Platform Mode

The CMS-only setup and the platform setup can use the same database volume:

```txt
contentmanagementsystem_postgres_data
```

This means content created in CMS-only mode can also appear when the full platform is running.

Example:

1. Run CMS-only mode.
2. Create and publish an article.
3. Stop CMS-only mode.
4. Run platform mode from `isco-platform`.
5. The website can load the article through:

```txt
http://localhost:8000/api/articles
```

## Important Database Rule

Do not run CMS-only mode and platform mode at the same time if both are using the same database volume.

Before starting platform mode, stop CMS-only mode:

```bash
docker compose down
```

Before starting CMS-only mode again, stop platform mode from the parent repository:

```bash
docker compose down
```

Only one PostgreSQL container should use the same database volume at a time.

## Local Setup

1. Copy `.env.example` to `.env`.

```bash
cp .env.example .env
```

2. Start Docker containers.

```bash
docker compose up
```

3. Install PHP dependencies if needed.

```bash
docker compose exec app composer install
```

4. Generate the Laravel app key.

```bash
docker compose exec app php artisan key:generate
```

5. Run migrations.

```bash
docker compose exec app php artisan migrate
```

6. Run seeders.

```bash
docker compose exec app php artisan db:seed
```

Or run migrations and seeders together:

```bash
docker compose exec app php artisan migrate --seed
```

## Commands

Start containers:

```bash
docker compose up
```

Start containers in the background:

```bash
docker compose up -d
```

Stop containers:

```bash
docker compose down
```

View logs:

```bash
docker compose logs -f
```

Generate app key:

```bash
docker compose exec app php artisan key:generate
```

Run migrations:

```bash
docker compose exec app php artisan migrate
```

Run seeders:

```bash
docker compose exec app php artisan db:seed
```

Run migrations with seeders:

```bash
docker compose exec app php artisan migrate --seed
```

Clear cache:

```bash
docker compose exec app php artisan optimize:clear
```

Build frontend assets:

```bash
docker compose exec node npm run build
```

Run Vite dev server:

```bash
docker compose exec node npm run dev -- --host 0.0.0.0
```

## Ports

CMS-only mode:

```txt
http://localhost:8002       → CMS app
http://localhost:5173       → Vite dev server
http://localhost:8025       → Mailpit
localhost:5432              → PostgreSQL
```

Platform mode:

```txt
http://localhost:8000       → Full platform through Apache
http://localhost:8026       → Platform Mailpit
```

## Notes About Vite

If the CMS admin page loads without styling, Laravel may still be trying to use the Vite dev server.

Check if this file exists:

```txt
public/hot
```

If platform mode is using built assets, remove it:

```bash
rm -f public/hot
php artisan optimize:clear
```

Inside Docker:

```bash
docker compose exec app rm -f public/hot
docker compose exec app php artisan optimize:clear
```

## Git Workflow

Create feature branches for CMS work.

Example:

```bash
git checkout development
git checkout -b feature/CMS-53-create-public-articles-api
```

Commit CMS changes inside this repository:

```bash
git add .
git commit -m "CMS-53 Create public articles API"
git push -u origin feature/CMS-53-create-public-articles-api
```

Platform-level Docker or Apache changes should be committed in the parent `isco-platform` repository, not here.
