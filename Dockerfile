############################################
# Base Image
############################################
FROM serversideup/php:8.4-fpm-nginx as base

USER root

RUN install-php-extensions curl intl bcmath soap gd sockets

RUN apt-get update \
    && apt-get install -y curl gnupg default-mysql-client \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

############################################
# Development Image
############################################
FROM base AS development

# We can pass USER_ID and GROUP_ID as build arguments
# to ensure the www-data user has the same UID and GID
# as the user running Docker.
ARG USER_ID
ARG GROUP_ID

USER root

# Update the image www-data UID/GID to match host UID/GID
RUN if [ -n "$USER_ID" ] && [ -n "$GROUP_ID" ]; then \
        docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
        docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID; \
    else \
        echo "⚠ USER_ID or GROUP_ID not set, skipping permissions setup"; \
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
    && usermod -aG www-data $USERNAME

RUN docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID

USER $USERNAME

############################################
# Build Image
############################################
FROM base AS build

USER root

COPY . /var/www/html

RUN --mount=type=cache,target=/root/.composer/cache \
    --mount=type=secret,id=COMPOSER_AUTH,env=COMPOSER_AUTH \
	composer install --no-interaction --prefer-dist --optimize-autoloader

RUN --mount=type=cache,target=/root/.npm \
    npm install && \
    npm run build && \
    rm -rf node_modules

USER www-data

############################################
# CI Image
############################################
FROM build AS ci

# Sometimes CI images need to run as root
USER root

COPY --from=build --chown=www-data:www-data /var/www/html /var/www/html

############################################
# Production Image
############################################
FROM build AS production

USER root

COPY --chmod=755 ./docker/entrypoint.d/production/ /etc/entrypoint.d/

COPY --from=build --chown=www-data:www-data /var/www/html /var/www/html

RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

USER www-data
