#!/bin/sh
set -e

# Executa como www-data (mesmo UID do host) para não criar arquivos de root no volume.
as_www() {
    su -p -s /bin/sh www-data -c "$1"
}

if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] Instalando dependências PHP..."
    as_www "composer install --no-interaction --prefer-dist"
fi

if [ ! -f .env ]; then
    echo "[entrypoint] Criando .env a partir do .env.example..."
    as_www "cp .env.example .env && php artisan key:generate --force"
fi

echo "[entrypoint] Rodando migrations..."
as_www "php artisan migrate --force"

echo "[entrypoint] Populando o banco (ignorado se já houver dados)..."
as_www "php artisan db:seed --force"

exec "$@"
