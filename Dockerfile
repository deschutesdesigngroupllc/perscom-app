FROM serversideup/php:8.4-fpm-nginx

USER root

RUN install-php-extensions curl intl bcmath soap gd sockets

RUN apt-get update \
    && apt-get install -y curl gnupg default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
RUN apt-get install -y nodejs

COPY --chmod=755 ./docker/entrypoint.d/ /etc/entrypoint.d/

USER www-data
