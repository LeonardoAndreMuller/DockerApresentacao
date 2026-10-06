# Imagem de desenvolvimento do app: PHP 8.5 (Laravel) + Node (Vite com HMR).
# O código NÃO é copiado para a imagem: ele entra por bind mount no compose.yaml,
# então qualquer alteração no host aparece na hora dentro do container.
FROM php:8.5-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pgsql zip pcntl \
    && rm -rf /var/lib/apt/lists/*

# Composer e Node vêm de imagens oficiais (multi-stage copy).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:24-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:24-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# O container roda com o UID do host; HOME gravável para caches do composer/npm.
ENV HOME=/tmp

WORKDIR /var/www/html

EXPOSE 8000 5173

ENTRYPOINT ["entrypoint"]
# Servidor embutido do PHP com o router do Laravel (o mesmo usado pelo `artisan serve`).
# Usamos `php -S` direto porque o `artisan serve` descarta as variáveis definidas no .env,
# o que faria o DB_CONNECTION do compose ser ignorado.
CMD ["npx", "concurrently", "-k", "-n", "server,vite", "-c", "green,gray", "cd public && php -S 0.0.0.0:8000 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php", "npm run dev"]
