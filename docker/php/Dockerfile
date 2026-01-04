
FROM php:8.3-apache

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install system dependencies, PHP extensions, and required tools
RUN apt-get update && \
    apt-get install -y \
        wget \
        gnupg2 \
        libpq-dev \
        git \
        unzip \
        libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install PostgreSQL client
RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ jammy-pgdg main 15.0" > /etc/apt/sources.list.d/pgdg.list
RUN wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/postgresql.gpg
RUN apt-get update
RUN apt-get install -y postgresql-client-15

# Copy helper scripts from the docker folder
COPY docker/php/wait-for-postgres.sh /usr/local/bin/wait-for-postgres.sh
RUN chmod +x /usr/local/bin/wait-for-postgres.sh

# Change Apache document root to the public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the entire project
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader