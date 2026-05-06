# Stage 1: Install PHP dependencies
FROM composer:latest AS php-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --ignore-platform-reqs --no-scripts --optimize-autoloader

# Stage 2: Build assets
FROM node:20-slim AS assets-builder
WORKDIR /app
RUN npm install -g pnpm
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile
COPY . .
# Copy vendor from php-builder stage because Ziggy is imported from vendor
COPY --from=php-builder /app/vendor ./vendor
RUN pnpm build

# Stage 3: Production image
FROM php:8.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# Install Composer (just in case, but we use pre-installed vendor)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Apache configuration
RUN a2enmod rewrite remoteip
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure RemoteIP
RUN echo 'RemoteIPHeader X-Forwarded-For' > /etc/apache2/conf-available/remoteip.conf && \
    echo 'RemoteIPTrustedProxy 10.0.0.0/8' >> /etc/apache2/conf-available/remoteip.conf && \
    echo 'RemoteIPTrustedProxy 172.16.0.0/12' >> /etc/apache2/conf-available/remoteip.conf && \
    echo 'RemoteIPTrustedProxy 192.168.0.0/16' >> /etc/apache2/conf-available/remoteip.conf && \
    a2enconf remoteip

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . .

# Copy vendor from php-builder and build assets from assets-builder
COPY --from=php-builder /app/vendor ./vendor
COPY --from=assets-builder /app/public/build ./public/build

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
