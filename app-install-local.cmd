:: Raccourci pour lancer l'application en local
copy .env.local .env

call composer install -n
php artisan key:generate
php artisan migrate:fresh --seed --force
