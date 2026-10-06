#!/bin/sh
set -e

if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] Instalando dependências PHP..."
    composer install --no-interaction --prefer-dist
fi

if [ ! -d node_modules ]; then
    echo "[entrypoint] Instalando dependências JS..."
    npm install
fi

if [ ! -f .env ]; then
    echo "[entrypoint] Criando .env a partir do .env.example..."
    cp .env.example .env
    php artisan key:generate --force
fi

echo "[entrypoint] Rodando migrations..."
php artisan migrate --force

echo "[entrypoint] Populando o banco (ignorado se já houver dados)..."
php artisan db:seed --force

exec "$@"
