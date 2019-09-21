FROM yiisoftware/yii2-php:7.2-apache

# Install PHP Extensions (igbinary & memcached)
RUN apt-get update
#RUN apt-get install -y apt-utils libmemcached-dev libmemcached11 git build-essential

#RUN pecl install memcached-3.0.4

#RUN apt-get update && apt-get install --no-install-recommends -y libzip-dev zlibc zlib1g && docker-php-ext-configure zip --with-libzip && docker-php-ext-install zip
#RUN git clone https://github.com/php-memcached-dev/php-memcached /usr/src/php/ext/memcached \
#  && cd /usr/src/php/ext/memcached && git checkout -b php7 origin/php7 \
#  && docker-php-ext-configure memcached \
#  && docker-php-ext-install memcached

# Memcached
RUN apt-get -y install libz-dev libmemcached-dev libmemcached11 libmemcachedutil2 build-essential \
    && pecl install memcached \
    && echo extension=memcached.so >> /usr/local/etc/php/conf.d/memcached.ini \
    && apt-get remove -y build-essential libmemcached-dev libz-dev \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /tmp/pear


RUN dir /usr/local/etc/php/conf.d/

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/backend/web|g' /etc/apache2/sites-available/000-default.conf
