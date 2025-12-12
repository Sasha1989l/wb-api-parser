#!/bin/sh
set -e

# Ждём доступности MySQL
until php -r "new PDO('mysql:host=$DB_HOST;port=$DB_PORT', '$DB_USERNAME', '$DB_PASSWORD');"; do
  echo "Waiting for database..."
  sleep 2
done

# Выполняем миграции
php artisan migrate --force

# Запускаем контейнер с командой по умолчанию
exec "$@"
