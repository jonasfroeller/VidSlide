FROM php:8.1.17-apache

# update/upgrade
RUN apt-get update && apt-get upgrade -y

# zip/unzip
RUN apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip && docker-php-ext-install zip

# GD (Image Processing)
RUN apt-get install -y \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libavif-dev \
    libtiff5-dev \
    librsvg2-dev \
    libgif-dev \
    libgd-dev

RUN apt-get install -y libfreetype6-dev

RUN docker-php-ext-install gd
RUN docker-php-ext-enable gd

# FFMPEG (Video Processing)
RUN apt-get install -y ffmpeg
RUN ln -s /usr/bin/ffmpeg /usr/local/bin/ffmpeg
RUN ln -s /usr/bin/ffprobe /usr/local/bin/ffprobe

# MySQLi (Database Connection)
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

# Composer (PHP Package Manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Composer Dependencies JWT (Authentication), DOTENV (Environment Variables), FFMPEG (Video Processing)
RUN composer require firebase/php-jwt vlucas/phpdotenv php-ffmpeg/php-ffmpeg

# Sources:
# https://getcomposer.org/download/ // Composer: Dependency Management
# https://github.com/firebase/php-jwt // JWT: Authentication
# https://github.com/vlucas/phpdotenv // DotEnv: Environment Variables
# https://github.com/PHP-FFMpeg/PHP-FFMpeg // FFMpeg: Video Processing
# https://www.php.net/manual/de/intro.image.php // GD: Image Processing
