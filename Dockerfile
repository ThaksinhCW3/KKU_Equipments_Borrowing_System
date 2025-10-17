FROM php:8.3-fpm AS base

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev zip curl unzip git nano libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Copy application files first
COPY . .

# Then install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

RUN composer require --dev kitloong/laravel-migrations-generator

RUN php artisan storage:link \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

FROM base AS node-build

RUN apt-get update && apt-get install -y curl gnupg lsb-release ca-certificates \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY package*.json ./

RUN npm install --verbose \
    && npm run build

FROM php:8.3-fpm AS final

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev zip curl unzip git nano libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer from official image
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Copy everything from base including vendor directory
COPY --from=base /var/www/html /var/www/html

# Copy built assets from node-build
COPY --from=node-build /var/www/html/public /var/www/html/public
COPY --from=node-build /var/www/html/resources /var/www/html/resources

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
