# ============================================
# Stage 1: Install PHP/Composer dependencies
# ============================================
FROM composer:2 AS composer-deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ============================================
# Stage 2: Build frontend assets (Vite)
# ============================================
FROM node:20-alpine AS node-build

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# ============================================
# Stage 3: Final image (Nginx + PHP-FPM)
# ============================================
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    ca-certificates \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        intl \
        bcmath \
        opcache \
    && rm -rf /var/cache/apk/*

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Set PHP upload/memory limits
RUN echo "upload_max_filesize = 10M" >> "$PHP_INI_DIR/php.ini" \
    && echo "post_max_size = 12M" >> "$PHP_INI_DIR/php.ini" \
    && echo "memory_limit = 256M" >> "$PHP_INI_DIR/php.ini" \
    && echo "max_execution_time = 60" >> "$PHP_INI_DIR/php.ini"

# Configure OPcache for production
RUN echo "opcache.enable=1" >> "$PHP_INI_DIR/php.ini" \
    && echo "opcache.memory_consumption=128" >> "$PHP_INI_DIR/php.ini" \
    && echo "opcache.interned_strings_buffer=8" >> "$PHP_INI_DIR/php.ini" \
    && echo "opcache.max_accelerated_files=10000" >> "$PHP_INI_DIR/php.ini" \
    && echo "opcache.validate_timestamps=0" >> "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Copy application + built assets
COPY --from=composer-deps /app /var/www/html
COPY --from=node-build /app/public/build /var/www/html/public/build

# Copy Nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy deploy script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/app/public \
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Create supervisor config
RUN mkdir -p /etc/supervisor.d
COPY docker/supervisord.ini /etc/supervisor.d/supervisord.ini

# Expose port (Render uses PORT env variable, default 10000)
EXPOSE 10000

CMD ["/start.sh"]
