FROM php:8.2-apache

# Habilita mod_rewrite si lo usas
RUN a2enmod rewrite

# Copia tu código fuente a la carpeta pública de Apache
COPY public/ /var/www/html/

# Opcional: Si tienes config de Apache personalizada (.htaccess)
# COPY public/.htaccess /var/www/html/.htaccess

# Da permisos a uploads (si tu app lo necesita)
RUN chown -R www-data:www-data /var/www/html/uploads
RUN chmod -R 755 /var/www/html/uploads

EXPOSE 8080

CMD ["apache2-foreground"]
