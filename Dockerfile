FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user=www-data
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    mariadb-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js (v20 LTS) & NPM
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Configure user matching host UID
RUN if [ "$user" = "www-data" ]; then \
      usermod -u $uid $user; \
    else \
      useradd -G www-data,root -u $uid -d /home/$user $user; \
    fi \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

# Set git safe directory to avoid dubious ownership
RUN git config --system --add safe.directory '*'

USER $user

EXPOSE 9000

CMD ["php-fpm"]
