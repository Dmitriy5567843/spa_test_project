FROM docker.io/library/ubuntu:22.04


WORKDIR /var/www/html


# Установка зависимостей
RUN ln -snf /usr/share/zoneinfo/UTC /etc/localtime && echo UTC > /etc/timezone \
    && apt-get update && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 dnsutils \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x4f4ea0aae5267a6c' | gpg --dearmor | tee /usr/share/keyrings/php-archive-keyring.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/php-archive-keyring.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list \
    && apt-get update && apt-get install -y php8.2-cli \
&& apt-get update && apt-get install -y php8.1-cli \
       php8.1 \
       php8.1-common \
       php8.1-bcmath \
       php8.1-bz2 \
       php8.1-curl \
       php8.1-dba \
       php8.1-gd \
       php8.1-geoip \
       php8.1-gmp \
       php8.1-imap \
       php8.1-intl \
       php8.1-ldap \
       php8.1-mbstring \
       php8.1-mysql \
       php8.1-opcache \
       php8.1-pgsql \
       php8.1-pspell \
       php8.1-readline \
       php8.1-recode \
       php8.1-snmp \
       php8.1-soap \
       php8.1-sqlite3 \
       php8.1-sybase \
       php8.1-tidy \
       php8.1-xml \
       php8.1-xmlrpc \
       php8.1-xsl \
       php8.1-zip \
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
