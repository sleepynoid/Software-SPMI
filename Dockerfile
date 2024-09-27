FROM ubuntu/apache2

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install PHP and extensions
RUN apt-get update && apt-get install -y \
    php \
    libapache2-mod-php \
    php-mysql \
    php-xml \
    php-mbstring \
    php-curl \
    php-zip \
    git \
    unzip \
    curl

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]
