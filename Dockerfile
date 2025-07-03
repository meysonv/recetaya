# Usa PHP con FPM
FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Instala Node.js (para compilar el frontend Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia todo el proyecto
COPY . .

# Instala dependencias de backend
RUN composer install --no-dev --optimize-autoloader

# Instala dependencias y compila frontend
RUN npm install && npm run build

# Da permisos a Laravel
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www/storage

# Usa el servidor de Laravel para producción
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
