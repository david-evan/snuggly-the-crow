# Raccourci pour lancer l'application en local
cp .env.local .env

composer install -n
php artisan key:generate
php artisan migrate:fresh --force --seed
