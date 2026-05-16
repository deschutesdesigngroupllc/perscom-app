# syntax=docker/dockerfile:1.7

############################################
# Base FPM/Nginx Image
############################################
FROM serversideup/php:8.4-fpm-nginx AS base

USER root

RUN install-php-extensions curl intl bcmath soap gd sockets \
    && apt-get update \
    && apt-get install -y --no-install-recommends gnupg ca-certificates default-mysql-client \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && mkdir -p /etc/mysql \
    && printf '[client]\nskip-ssl=true\n' > /etc/my.cnf \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

############################################
# CLI Image
############################################
FROM serversideup/php:8.4-cli AS cli

USER root

RUN install-php-extensions intl bcmath soap gd sockets gmp imap \
    && apt-get update \
    && apt-get install -y --no-install-recommends curl ca-certificates gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Cache PHP dependencies separately from source.
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache composer install \
        --no-interaction --prefer-dist --no-scripts --no-autoloader --no-progress

# Cache JS dependencies separately from source.
COPY --chown=www-data:www-data package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci --no-audit --no-fund

COPY --chown=www-data:www-data . /var/www/html

RUN composer dump-autoload --optimize --no-interaction \
    && npm run build \
    && rm -rf node_modules

USER www-data

############################################
# Development Image
############################################
FROM base AS development

# Match container www-data UID/GID to the host user so bind-mounts stay writable.
ARG USER_ID
ARG GROUP_ID

USER root

COPY --chmod=755 ./docker/entrypoint.d/development/ /etc/entrypoint.d/

RUN if [ -n "$USER_ID" ] && [ -n "$GROUP_ID" ]; then \
        docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
        docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID; \
    else \
        echo "USER_ID or GROUP_ID not set, skipping permissions setup"; \
    fi

USER www-data

############################################
# Devcontainer Image
############################################
FROM base AS devcontainer

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USERNAME=vscode

USER root

RUN addgroup --gid $GROUP_ID $USERNAME || echo "Group exists" \
    && adduser --uid $USER_ID --gid $GROUP_ID --disabled-password --gecos "" $USERNAME \
    && usermod -aG www-data $USERNAME \
    && docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID

USER $USERNAME

############################################
# Build Image (shared by CI and Production)
############################################
FROM base AS build

USER root

ARG VITE_CLOUDFLARE_TURNSTILE_SITE_KEY
ARG VITE_PUSHER_APP_KEY
ARG VITE_PUSHER_APP_CLUSTER

ENV VITE_CLOUDFLARE_TURNSTILE_SITE_KEY=${VITE_CLOUDFLARE_TURNSTILE_SITE_KEY}
ENV VITE_PUSHER_APP_KEY=${VITE_PUSHER_APP_KEY}
ENV VITE_PUSHER_APP_CLUSTER=${VITE_PUSHER_APP_CLUSTER}

WORKDIR /var/www/html

# Install PHP dependencies first to leverage layer cache.
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache composer install \
        --no-interaction --prefer-dist --no-scripts --no-autoloader --no-progress

# Install JS dependencies first to leverage layer cache.
COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci --no-audit --no-fund

# Copy the rest of the source and finalize the build.
COPY . /var/www/html

RUN composer dump-autoload --optimize --no-interaction \
    && npm run build \
    && rm -rf node_modules

USER www-data

############################################
# CI Image
############################################
FROM build AS ci

USER root

############################################
# Production Image
############################################
FROM base AS production

USER root

WORKDIR /var/www/html

COPY --chmod=755 ./docker/entrypoint.d/production/ /etc/entrypoint.d/

# Pull the built application from the build stage (already includes vendor/ and public/build/).
COPY --from=build --chown=www-data:www-data /var/www/html /var/www/html

# Strip dev dependencies and re-optimize the autoloader for production.
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache composer install \
        --no-interaction --prefer-dist --no-dev --no-scripts --no-progress --optimize-autoloader \
    && composer clear-cache

USER www-data
