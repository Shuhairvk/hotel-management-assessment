# Appcom Hotel Management (Laravel 12 + Vue 3)

Admin and staff login, staff permissions, guests, rooms and bookings, with search, filters and pagination.

## Requirements
- PHP 8.2+ and Composer
- Node.js 18+ and npm
- SQLite (default, no setup) or MySQL

## Installation
```bash
git clone https://github.com/Shuhairvk/hotel-management-assessment.git
cd hotel-management-assessment
composer install
npm install
```

## Environment configuration
```bash
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
```
The default database is SQLite and needs no setup. To use MySQL instead, create an empty database and set these in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel
DB_USERNAME=root
DB_PASSWORD=
```

## Database and storage
```bash
php artisan migrate --seed
php artisan storage:link
```
If asked to create the SQLite database file, answer yes.

## Build the front-end
```bash
npm run build
```
(The compiled assets are already committed, so this step is only needed after changing files in `resources/`.)

## Run the project
```bash
php artisan serve
```
Open http://127.0.0.1:8000

For front-end development, run `npm run dev` in a second terminal, and still use http://127.0.0.1:8000 (not port 5173).

## Login
- Admin: `admin@appcom.com` / `password123` (select the **Admin** tab)
- Staff: create accounts from **Staff Users** as admin, then sign in with the **Staff** tab

## Troubleshooting
- "Vite manifest not found": run `npm run build`.
- Blank page or old styles: run `php artisan optimize:clear`.
- Photos not showing: run `php artisan storage:link`.
