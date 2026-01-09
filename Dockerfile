FROM php:8.2-apache

# Install extensions and required tools
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip \
 && docker-php-ext-install pdo pdo_mysql mysqli \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Create custom Apache configuration
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
 && a2enconf custom

WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80