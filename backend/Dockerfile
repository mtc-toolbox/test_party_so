FROM yiisoftware/yii2-php:7.2-apache
# memcache
ENV MEMCACHED_DEPS libmemcached-dev git
RUN set -xe \
 && apt-get update \
 && apt-get install -y $MEMCACHED_DEPS \
 && curl https://codeload.github.com/php-memcached-dev/php-memcached/zip/php7 -o /tmp/memcached.zip \
 && mkdir -p /usr/src/php/ext \
 && unzip /tmp/memcached.zip -d /usr/src/php/ext \
 && docker-php-ext-configure /usr/src/php/ext/php-memcached-php7 \
     --disable-memcached-sasl \
 && docker-php-ext-install /usr/src/php/ext/php-memcached-php7 \
 && rm -rf /usr/src/php/ext/php-memcached-php7 /tmp/memcached.zip
# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/backend/web|g' /etc/apache2/sites-available/000-default.conf
