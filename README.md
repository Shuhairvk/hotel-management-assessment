# Appcom Hotel Management (Laravel + Vue)

## Setup
```
composer install
npm install
cp .env.example .env      # set DB_* values
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build             # or: npm run dev
php artisan serve
```
Admin login: admin@appcom.com / password123 (choose "Admin" on the login screen).
Staff accounts are created by the admin under "Staff Users"; log in with the "Staff" role.
