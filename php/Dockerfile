FROM php:8.2-fpm

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        sendmail \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip



# FROM node:16-alpine
# WORKDIR /var/www/html
# COPY package*.json ./
# RUN npm install
# COPY . .
# CMD ["npm", "start"]
# Copy the shell script file into the container
# CMD sudo chmod +x setup-cron.sh
# RUN chmod -R setup-cron.sh /usr/local/bin/




# Set the shell script as the entry point
# ENTRYPOINT ["/usr/local/bin/setup-cron.sh"]


