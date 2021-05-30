FROM php:7.3.6-fpm-alpine3.10

RUN apk update && apk add --no-cache shadow openssl \
    bash \
    mysql-client \
    nodejs \
    npm \
    git \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    tzdata \
    python3 \
    py3-crcmod \
    py3-openssl \
    libc6-compat \
    openssh-client \
    gnupg \
    $PHPIZE_DEPS

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-configure gd \
    --with-gd \
    --with-freetype-dir=/usr/include/ \
    --with-png-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ && \
    NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
    docker-php-ext-install -j${NPROC} gd 

#Timezone
RUN echo "America/Bahia" > /etc/TZ && cp /usr/share/zoneinfo/America/Bahia /etc/localtime && echo "America/Bahia" > /etc/timezone && \
    sed "s/;date.timezone =/date.timezone = America\/Bahia/g" /usr/local/etc/php/php.ini-development  > /usr/local/etc/php/php.ini && \
    sed -i "s/expose_php = On/expose_php = Off/g" /usr/local/etc/php/php.ini


RUN touch /home/www-data/.bashrc | echo "PS1='\w\$ '" >> /home/www-data/.bashrc

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalação de google cloud sdk
RUN curl -O https://dl.google.com/dl/cloudsdk/channels/rapid/downloads/google-cloud-sdk-335.0.0-linux-x86_64.tar.gz && \
    tar xzf google-cloud-sdk-335.0.0-linux-x86_64.tar.gz && \
    rm google-cloud-sdk-335.0.0-linux-x86_64.tar.gz && \
    mv google-cloud-sdk /opt/.

ENV PATH /opt/google-cloud-sdk/bin:$PATH

RUN npm config set cache /var/www/.npm-cache --global && npm install yarn -g

RUN usermod -u 1000 www-data

WORKDIR /var/www

RUN rm -rf /var/www/html && ln -s public html

USER www-data

RUN gcloud config set core/disable_usage_reporting true && \
    gcloud config set component_manager/disable_update_check true && \
    gcloud config set metrics/environment github_docker_image && \
    gcloud --version

RUN composer config -g cache-dir "/var/www/.composer/cache" && \
    composer config -g data-dir "/var/www/.composer"

RUN yarn config set cache-folder /var/www/.cache/yarn/v6

EXPOSE 9000
