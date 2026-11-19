FROM php:8.3-apache-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libonig-dev \
    && docker-php-ext-install pdo_pgsql mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY deploy/apache.conf /etc/apache2/conf-available/portal.conf
COPY deploy/php.ini /usr/local/etc/php/conf.d/portal.ini
COPY deploy/start.sh /usr/local/bin/portal-start
RUN chmod +x /usr/local/bin/portal-start
RUN a2enconf portal
WORKDIR /var/www/html
COPY . .
RUN mkdir -p app/views/admin/uploads \
    && chown -R www-data:www-data app/views/admin/uploads
EXPOSE 80
CMD ["/usr/local/bin/portal-start"]
