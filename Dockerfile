FROM php:8.3-cli

COPY --from=composer:lts /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/

EXPOSE 8000

CMD [ "php", "artisan", "serve", "--host=0.0.0.0", "--port=8000" ]
# CMD ["php", "artisan", "serve", "--host=$APP_HOST", "--port=$APP_PORT"]
