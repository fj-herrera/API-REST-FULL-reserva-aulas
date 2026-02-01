FROM php:8.2-apache

# Extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Activar mod_rewrite (usas .htaccess)
RUN a2enmod rewrite headers

# Permitir uso de .htaccess
RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Forzar a Apache a usar UTF-8 a nivel de configuración interna
RUN echo "AddDefaultCharset UTF-8" >> /etc/apache2/conf-enabled/charset.conf

WORKDIR /var/www/html