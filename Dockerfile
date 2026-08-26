# =============================================================================
# SIAKAD NUJA — Production image (Railway-ready)
#
# Arsitektur single-container yang berisi:
#   - PHP 8.3 + ekstensi produksi (pdo_mysql, gd, zip, intl, opcache, pcntl, posix)
#   - Node.js 20  -> runtime WhatsApp sidecar (whatsapp-web.js)
#   - Chromium    -> browser headless untuk Puppeteer
#   - Aset frontend hasil `npm run build`
#   - Dependency composer --no-dev
#
# Proses yang dijalankan entrypoint:
#   web server (:PORT) + queue worker + sidecar WA + SSE listener
#
# Build:  docker build -t siakad-nuja .
# =============================================================================

# ---------------------------------------------------------------------------
# Stage 1 — build aset frontend (Vite + Tailwind v4)
# ---------------------------------------------------------------------------
FROM node:20-bookworm-slim AS assets

WORKDIR /build

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js ./
COPY resources ./resources

RUN npm run build


# ---------------------------------------------------------------------------
# Stage 2 — dependency composer (tanpa dev, autoloader teroptimasi)
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# --no-scripts: post-autoload-dump butuh artisan yang belum ada di stage ini;
# package manifest akan dibuat otomatis saat container start.
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts \
    && rm -rf /root/.composer/cache


# ---------------------------------------------------------------------------
# Stage 3 — image produksi
# ---------------------------------------------------------------------------
FROM php:8.3-cli-bookworm AS production

ARG TZ=Asia/Jakarta

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=${TZ}

# --- Paket sistem: Chromium (sidecar), tini (init/zombie reaper), libs ext PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
        chromium \
        tini \
        git unzip curl ca-certificates tzdata \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        zip \
        intl \
        pdo_mysql \
        bcmath \
        opcache \
        pcntl \
        posix \
    && cp /usr/share/zoneinfo/${TZ} /etc/localtime \
    && echo ${TZ} > /etc/timezone \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# --- Node.js runtime untuk sidecar (disalin dari stage assets, base Debian sama)
COPY --from=assets /usr/local/bin/node /usr/local/bin/node
COPY --from=assets /usr/local/lib/node_modules/npm /usr/local/lib/node_modules/npm
RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && node --version && npm --version

# --- Puppeteer: pakai Chromium sistem, jangan unduh browser sendiri
ENV PUPPETEER_SKIP_DOWNLOAD=true \
    PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_CACHE_DIR=/tmp/puppeteer-cache \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium

WORKDIR /var/www/html

# --- Vendor composer + aset frontend + source code
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /build/public/build ./public/build
COPY . .

# --- Install dependency sidecar WA di dalam vendor (tanpa unduh Chromium)
RUN cd vendor/kstmostofa/laravel-whatsapp/sidecar \
    && npm ci --omit=dev --no-audit --no-fund \
    && rm -rf /root/.npm /tmp/puppeteer-cache

# --- Konfigurasi PHP produksi
COPY docker/php.ini "$PHP_INI_DIR"/conf.d/99-siakad.ini

# --- Hak akses: www-data menulis ke storage/bootstrap/cache/public
RUN chown -R www-data:www-data storage bootstrap/cache public \
    && find storage bootstrap/cache -type d -exec chmod ug+rwx,o-rwx {} + \
    && find storage bootstrap/cache -type f -exec chmod ug+rw,o-rwx {} +

USER www-data

# Railway menyuntikkan $PORT dinamis; ini hanya fallback lokal
ENV PORT=8080 \
    PHP_CLI_SERVER_WORKERS=8 \
    TZ=${TZ}

EXPOSE 8080

# tini = PID 1: meneruskan sinyal & memungut zombie Chromium
ENTRYPOINT ["/usr/bin/tini", "--"]
CMD ["bash", "docker/entrypoint.sh"]
