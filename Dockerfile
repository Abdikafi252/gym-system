FROM php:8.2-apache

# Install required PHP extensions for this app
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy application code
COPY . /var/www/html/

# Set safe ownership for Apache runtime
RUN chown -R www-data:www-data /var/www/html

# Use Render's default port 10000 if PORT is not set
ENV PORT=80
RUN sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 80
EXPOSE 10000
