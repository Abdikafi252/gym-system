FROM php:8.2-apache

# Install required PHP extensions for this app
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy application code
COPY . /var/www/html/

# Set safe ownership for Apache runtime
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
