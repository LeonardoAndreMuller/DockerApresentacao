# Imagem de desenvolvimento do app: Apache + PHP 8.5 (mod_php) no mesmo container.
# O código NÃO é copiado para a imagem: ele entra por bind mount no compose.yaml,
# então qualquer alteração no host aparece na hora dentro do container.
FROM php:8.5-apache

ARG UID=1000
ARG GID=1000

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pgsql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache servindo a pasta public/ do Laravel, com mod_rewrite para as rotas.
COPY docker/app/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

# Configuração do PHP para desenvolvimento (OPcache revalidando a cada requisição).
COPY docker/app/php.ini /usr/local/etc/php/conf.d/zz-dev.ini

# O Apache roda como www-data; damos a ele o mesmo UID/GID do usuário do host
# para conseguir gravar em storage/ no volume montado (e os arquivos continuarem seus).
RUN groupmod -o -g ${GID} www-data && usermod -o -u ${UID} -g ${GID} www-data

COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# HOME gravável para o cache do composer.
ENV HOME=/tmp

WORKDIR /var/www/html

EXPOSE 80

ENTRYPOINT ["entrypoint"]
CMD ["apache2-foreground"]
