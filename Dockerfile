FROM docker.io/library/ubuntu:22.04


WORKDIR /var/www/html


# Установка зависимостей
RUN ln -snf /usr/share/zoneinfo/UTC /etc/localtime && echo UTC > /etc/timezone \
    && apt-get update && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 dnsutils \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x4f4ea0aae5267a6c' | gpg --dearmor | tee /usr/share/keyrings/php-archive-keyring.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/php-archive-keyring.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list \
    && apt-get update && apt-get install -y php8.2-cli \
       php8.2 \
       php8.2-common \
       php8.2-bcmath \
       php8.2-bz2 \
       php8.2-curl \
       php8.2-dba \
       php8.2-gd \
       php8.2-geoip \
       php8.2-gmp \
       php8.2-imap \
       php8.2-intl \
       php8.2-ldap \
       php8.2-mbstring \
       php8.2-mysql \
       php8.2-opcache \
       php8.2-pgsql \
       php8.2-pspell \
       php8.2-readline \
       php8.2-recode \
       php8.2-snmp \
       php8.2-soap \
       php8.2-sqlite3 \
       php8.2-sybase \
       php8.2-tidy \
       php8.2-xml \
       php8.2-xmlrpc \
       php8.2-xsl \
       php8.2-zip \
       php-apcu \
       php-apcu-bc \
       php-imagick \
       php-memcached \
       php-memcache \
       php-redis \
       php-xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Установка зависимостей Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка зависимостей приложения
COPY composer.json composer.lock /var/www/html/
RUN composer install --no-scripts --no-autoloader && rm -rf /root/.composer

# Настройка среды
COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.2/cli/conf.d/99-sail.ini
COPY www.conf /etc/php/8.2/cli/conf.d/zzz-www.conf

#Добавление пользователя www-data
ARG WWWUSER
ARG WWWGROUP
RUN if [ -z "$WWWUSER" ]; then adduser --no-create-home --uid 1000 --disabled-password --gecos "" www-data; else adduser --no-create-home --uid $WWWUSER --disabled-password --gecos "" www-data; fi
RUN if [ -z "$WWWGROUP" ]; then groupadd --force -g 1000 sail; else groupadd --force -g $WWWGROUP sail; fi
RUN usermod -a -G www-data,sail www-data

#Установка прав на исполняемые файлы
RUN chmod +x /usr/local/bin/docker-entrypoint.sh && chmod +x /usr/local/bin/wait-for-it.sh && chmod +x /usr/local/bin/sail

#Назначение рабочей директории
WORKDIR /var/www/html

#Копирование конфигурационных файлов
COPY ./docker/supervisor/*.conf /etc/supervisor/conf.d/

#Копирование миграций
COPY ./database/migrations /var/www/html/database/migrations

#Открытие портов
EXPOSE 80 443

#Запуск supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
