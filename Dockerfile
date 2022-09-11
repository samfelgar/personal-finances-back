FROM php:8-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install bcmath pdo_mysql pcntl

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]