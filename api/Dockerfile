FROM php:8.2

# Instalación de dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Establecer el directorio de trabajo
WORKDIR /var/www/symfony

# Copiar los archivos de la aplicación
COPY . .

EXPOSE 58000

CMD ["php", "-S", "0.0.0.0:58000", "-t", "public"]